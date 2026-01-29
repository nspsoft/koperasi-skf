<?php

namespace App\Services;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class JournalService
{
    /**
     * Create a journal entry from a transaction
     * 
     * @param Model $reference The source transaction model
     * @param string $description Description of the transaction
     * @param array $entries Array of ['account_code' => code, 'debit' => amount, 'credit' => amount, 'description' => text]
     * @param \DateTime|string|null $date Transaction date (defaults to today)
     * @return JournalEntry|null
     */
    public static function createJournal(
        Model $reference,
        string $description,
        array $entries,
        $date = null
    ): ?JournalEntry {
        // Validate entries balance
        $totalDebit = collect($entries)->sum('debit');
        $totalCredit = collect($entries)->sum('credit');

        if (abs($totalDebit - $totalCredit) > 0.01) {
            throw new \Exception("Journal entries are not balanced. Debit: {$totalDebit}, Credit: {$totalCredit}");
        }

        return DB::transaction(function () use ($reference, $description, $entries, $date, $totalDebit, $totalCredit) {
            // Create journal entry
            $journal = JournalEntry::create([
                'journal_number' => JournalEntry::generateJournalNumber(),
                'reference_type' => get_class($reference),
                'reference_id' => $reference->id,
                'transaction_date' => $date ?? now()->toDateString(),
                'description' => $description,
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'status' => 'posted',
                'created_by' => auth()->id() ?? 1,
                'posted_by' => auth()->id() ?? 1,
                'posted_at' => now(),
            ]);

            // Create journal lines
            foreach ($entries as $entry) {
                $account = Account::where('code', $entry['account_code'])->first();
                
                if (!$account) {
                    throw new \Exception("Account with code {$entry['account_code']} not found");
                }

                JournalEntryLine::create([
                    'journal_entry_id' => $journal->id,
                    'account_id' => $account->id,
                    'debit' => $entry['debit'] ?? 0,
                    'credit' => $entry['credit'] ?? 0,
                    'description' => $entry['description'] ?? null,
                ]);
            }

            return $journal;
        });
    }

    /**
     * Create journal for saving deposit
     * @param string $paymentMethod 'cash' or 'bank'/'transfer'
     */
    public static function journalSavingDeposit($saving, string $paymentMethod = 'cash'): ?JournalEntry
    {
        $savingType = match($saving->type) {
            'pokok' => '2101',    // Simpanan Pokok
            'wajib' => '2102',    // Simpanan Wajib
            'sukarela' => '2103', // Simpanan Sukarela
            default => '2103',
        };

        $typeLabel = match($saving->type) {
            'pokok' => 'Simpanan Pokok',
            'wajib' => 'Simpanan Wajib',
            'sukarela' => 'Simpanan Sukarela',
            default => 'Simpanan',
        };

        $memberName = $saving->member->user->name ?? 'Anggota';

        // Determine asset account based on payment method
        $isBank = in_array(strtolower($paymentMethod), ['bank', 'transfer', 'payroll']);
        $assetAccount = $isBank ? '1102' : '1101';
        $assetLabel = $isBank ? 'Bank masuk (Transfer)' : 'Kas masuk';

        return self::createJournal(
            $saving,
            "Setoran {$typeLabel} - {$memberName}",
            [
                ['account_code' => $assetAccount, 'debit' => $saving->amount, 'credit' => 0, 'description' => $assetLabel],
                ['account_code' => $savingType, 'debit' => 0, 'credit' => $saving->amount, 'description' => $typeLabel],
            ],
            $saving->transaction_date
        );
    }

    /**
     * Create journal for saving withdrawal
     * @param string $paymentMethod 'cash' or 'bank'/'transfer'
     */
    public static function journalSavingWithdrawal($saving, string $paymentMethod = 'cash'): ?JournalEntry
    {
        $savingType = match($saving->type) {
            'pokok' => '2101',
            'wajib' => '2102',
            'sukarela' => '2103',
            default => '2103',
        };

        $typeLabel = match($saving->type) {
            'pokok' => 'Simpanan Pokok',
            'wajib' => 'Simpanan Wajib',
            'sukarela' => 'Simpanan Sukarela',
            default => 'Simpanan',
        };

        $memberName = $saving->member->user->name ?? 'Anggota';

        // Determine asset account based on payment method
        $isBank = in_array(strtolower($paymentMethod), ['bank', 'transfer', 'payroll']);
        $assetAccount = $isBank ? '1102' : '1101';
        $assetLabel = $isBank ? 'Bank keluar (Transfer)' : 'Kas keluar';

        return self::createJournal(
            $saving,
            "Penarikan {$typeLabel} - {$memberName}",
            [
                ['account_code' => $savingType, 'debit' => abs($saving->amount), 'credit' => 0, 'description' => "Penarikan {$typeLabel}"],
                ['account_code' => $assetAccount, 'debit' => 0, 'credit' => abs($saving->amount), 'description' => $assetLabel],
            ],
            $saving->transaction_date
        );
    }

    /**
     * Create journal for loan disbursement
     */
    public static function journalLoanDisbursement($loan): ?JournalEntry
    {
        $memberName = $loan->member->user->name ?? 'Anggota';

        return self::createJournal(
            $loan,
            "Pencairan Pinjaman - {$memberName} ({$loan->loan_number})",
            [
                ['account_code' => '1201', 'debit' => $loan->amount, 'credit' => 0, 'description' => 'Piutang Pinjaman'],
                ['account_code' => '1101', 'debit' => 0, 'credit' => $loan->amount, 'description' => 'Kas keluar'],
            ],
            $loan->disbursement_date ?? now()->toDateString()
        );
    }

    /**
     * Create journal for loan payment (angsuran)
     */
    public static function journalLoanPayment($loanPayment, $principalAmount, $interestAmount): ?JournalEntry
    {
        $loan = $loanPayment->loan;
        $memberName = $loan->member->user->name ?? 'Anggota';
        $totalPayment = $principalAmount + $interestAmount;

        return self::createJournal(
            $loanPayment,
            "Pembayaran Angsuran - {$memberName} ({$loan->loan_number})",
            [
                ['account_code' => '1101', 'debit' => $totalPayment, 'credit' => 0, 'description' => 'Kas masuk'],
                ['account_code' => '1201', 'debit' => 0, 'credit' => $principalAmount, 'description' => 'Pokok pinjaman'],
                ['account_code' => '4101', 'debit' => 0, 'credit' => $interestAmount, 'description' => 'Pendapatan bunga'],
            ],
            $loanPayment->payment_date ?? now()->toDateString()
        );
    }

    /**
     * Create journal for POS sale
     */
    /**
     * Create journal for POS sale
     */
    public static function journalSale($transaction): ?JournalEntry
    {
        $entries = [];
        
        // Determine the correct asset account based on payment method
        // Cash → Kas (1101)
        // QRIS, Transfer, VA, saldo_sukarela → Bank (1102) 
        // Kredit → Piutang Dagang (1202)
        $bankMethods = ['qris', 'transfer', 'va', 'saldo_sukarela'];
        
        if ($transaction->payment_method === 'kredit') {
            // Credit sales go to Accounts Receivable
            $entries[] = ['account_code' => '1202', 'debit' => $transaction->total_amount, 'credit' => 0, 'description' => 'Piutang dagang'];
        } elseif (in_array($transaction->payment_method, $bankMethods)) {
            // Digital payments go to Bank account
            $entries[] = ['account_code' => '1102', 'debit' => $transaction->total_amount, 'credit' => 0, 'description' => 'Bank masuk (' . strtoupper($transaction->payment_method) . ')'];
        } else {
            // Cash and other methods go to Cash account
            $entries[] = ['account_code' => '1101', 'debit' => $transaction->total_amount, 'credit' => 0, 'description' => 'Kas masuk'];
        }
        
        // Sales revenue
        $entries[] = ['account_code' => '4102', 'debit' => 0, 'credit' => $transaction->total_amount, 'description' => 'Pendapatan penjualan'];

        // Calculate COGS (use cost_per_unit for correct calculation per sale unit)
        $cogs = 0;
        foreach ($transaction->items as $item) {
            if ($item->product) {
                // cost_per_unit = cost / conversion_factor (cost per sale unit)
                $costPerUnit = $item->product->cost_per_unit ?? $item->product->cost ?? 0;
                $cogs += $costPerUnit * $item->quantity;
            }
        }

        // COGS entries (if any)
        if ($cogs > 0) {
            $entries[] = ['account_code' => '5201', 'debit' => $cogs, 'credit' => 0, 'description' => 'Harga pokok penjualan'];
            $entries[] = ['account_code' => '1301', 'debit' => 0, 'credit' => $cogs, 'description' => 'Persediaan keluar'];
        }

        return self::createJournal(
            $transaction,
            "Penjualan - {$transaction->invoice_number}",
            $entries,
            $transaction->created_at->toDateString()
        );
    }

    /**
     * Create journal for transaction credit payment (pelunasan hutang dagang/pos)
     */
    public static function journalTransactionCreditPayment($transaction, $amount, $paymentMethod = 'cash'): ?JournalEntry
    {
        // Determine the correct asset account based on payment method
        $bankMethods = ['qris', 'transfer', 'va', 'saldo'];
        
        if (in_array($paymentMethod, $bankMethods)) {
            $assetAccount = '1102'; // Bank
            $description = 'Bank masuk (' . strtoupper($paymentMethod) . ')';
        } else {
            $assetAccount = '1101'; // Kas
            $description = 'Kas masuk';
        }
        
        return self::createJournal(
            $transaction,
            "Pelunasan Kredit - {$transaction->invoice_number}",
            [
                ['account_code' => $assetAccount, 'debit' => $amount, 'credit' => 0, 'description' => $description],
                ['account_code' => '1202', 'debit' => 0, 'credit' => $amount, 'description' => 'Pelunasan piutang'],
            ],
            now()->toDateString()
        );
    }

    /**
     * Create journal for purchase (restock)
     */
    public static function journalPurchase($purchase): ?JournalEntry
    {
        $supplierName = $purchase->supplier->name ?? 'Supplier';

        return self::createJournal(
            $purchase,
            "Pembelian Barang - {$supplierName} ({$purchase->reference_number})",
            [
                ['account_code' => '1301', 'debit' => $purchase->total_amount, 'credit' => 0, 'description' => 'Persediaan masuk'],
                ['account_code' => '1101', 'debit' => 0, 'credit' => $purchase->total_amount, 'description' => 'Kas keluar'],
            ],
            $purchase->purchase_date
        );
    }

    /**
     * Create journal for operational expense
     */
    public static function journalExpense($expense): ?JournalEntry
    {
        $categoryName = $expense->category->name ?? 'Biaya Operasional';

        return self::createJournal(
            $expense,
            "Biaya {$categoryName} - {$expense->description}",
            [
                ['account_code' => '5102', 'debit' => $expense->amount, 'credit' => 0, 'description' => $categoryName],
                ['account_code' => '1101', 'debit' => 0, 'credit' => $expense->amount, 'description' => 'Kas keluar'],
            ],
            $expense->expense_date
        );
    }

    /**
     * Get account balances for multiple accounts in a single query
     */
    public static function getBatchAccountBalances($accountCodes, $upToDate = null): array
    {
        $accounts = Account::whereIn('code', $accountCodes)->get();
        $accountIds = $accounts->pluck('id')->toArray();
        $accountMap = $accounts->keyBy('id');

        $query = JournalEntryLine::whereIn('account_id', $accountIds)
            ->whereHas('journalEntry', function ($q) {
                $q->where('status', 'posted');
            });

        if ($upToDate) {
            $query->whereHas('journalEntry', function ($q) use ($upToDate) {
                $q->where('transaction_date', '<=', $upToDate);
            });
        }

        $sums = $query->select('account_id', DB::raw('SUM(debit) as total_debit'), DB::raw('SUM(credit) as total_credit'))
            ->groupBy('account_id')
            ->get()
            ->keyBy('account_id');

        $balances = [];
        foreach ($accounts as $account) {
            $sum = $sums->get($account->id);
            $totalDebit = $sum ? $sum->total_debit : 0;
            $totalCredit = $sum ? $sum->total_credit : 0;

            if ($account->normal_balance === 'debit') {
                $balances[$account->code] = $totalDebit - $totalCredit;
            } else {
                $balances[$account->code] = $totalCredit - $totalDebit;
            }
        }

        // Ensure all requested codes are in the result
        foreach ($accountCodes as $code) {
            if (!isset($balances[$code])) {
                $balances[$code] = 0;
            }
        }

        return $balances;
    }

    /**
     * Get account balance for a specific account
     */
    public static function getAccountBalance($accountCode, $upToDate = null): float
    {
        $balances = self::getBatchAccountBalances([$accountCode], $upToDate);
        return $balances[$accountCode] ?? 0;
    }

    /**
     * Get total revenue for a period
     */
    public static function getTotalRevenue($startDate, $endDate): float
    {
        return JournalEntryLine::whereHas('account', function ($q) {
                $q->where('type', 'revenue');
            })
            ->whereHas('journalEntry', function ($q) use ($startDate, $endDate) {
                $q->where('status', 'posted')
                    ->whereBetween('transaction_date', [$startDate, $endDate]);
            })
            ->sum('credit');
    }

    /**
     * Get total expenses for a period
     */
    public static function getTotalExpenses($startDate, $endDate): float
    {
        return JournalEntryLine::whereHas('account', function ($q) {
                $q->where('type', 'expense');
            })
            ->whereHas('journalEntry', function ($q) use ($startDate, $endDate) {
                $q->where('status', 'posted')
                    ->whereBetween('transaction_date', [$startDate, $endDate]);
            })
            ->sum('debit');
    }

    /**
     * Get net income (SHU) for a period
     */
    public static function getNetIncome($startDate, $endDate): float
    {
        return self::getTotalRevenue($startDate, $endDate) - self::getTotalExpenses($startDate, $endDate);
    }
    /**
     * Create journal for Consignment Settlement (Payout)
     */
    public static function journalConsignmentSettlement($settlement, string $paymentMethod = 'cash'): ?JournalEntry
    {
        // 1. Decouple Component: What are we paying?
        // We are paying the "HPP" part to the Partner. 
        // In standard accounting, when we sold the item, we recognized Revenue.
        // If we didn't recognize Cost at point of sale, we recognize it NOW as Expense + PayOut.
        // Entry: Debit Cost of Consignment (Expense), Credit Cash.
        
        // Account Mapping (Assumed Standard COA)
        // 510x : COGS / HPP
        // 1101 : Cash
        // 1102 : Bank

        $expenseAccount = '5102'; // HPP Konsinyasi (Adjust code as needed)
        
        $isBank = in_array(strtolower($paymentMethod), ['bank', 'transfer']);
        $assetAccount = $isBank ? '1102' : '1101'; // Bank vs Cash

        // If payment method was 'savings', we treat it as Cash Out (virtual) -> Savings In.
        // So we still Credit 1101 (Cash) here to balance the Debit 1101 in journalSavingDeposit.
        // Net effect: 1101 cancels out. Dr HPP, Cr Savings.
        
        return self::createJournal(
            $settlement,
            "Settlement Konsinyasi #{$settlement->transaction_number} - {$settlement->consignor->name}",
            [
                [
                    'account_code' => $expenseAccount, // Dr HPP
                    'debit' => $settlement->total_payable_amount,
                    'credit' => 0,
                    'description' => "HPP Konsinyasi ({$settlement->consignor->name})"
                ],
                [
                    'account_code' => $assetAccount, // Cr Cash/Bank
                    'debit' => 0,
                    'credit' => $settlement->total_payable_amount,
                    'description' => "Pembayaran Settlement"
                ]
            ],
            $settlement->paid_at
        );
    }
}
