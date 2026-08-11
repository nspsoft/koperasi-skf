<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Saving;
use App\Models\Loan;
use App\Models\Transaction;
use App\Models\CreditInstallment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MembersExport;
use App\Exports\SavingsExport;
use App\Exports\LoansExport;

use App\Models\Account;
use App\Models\JournalEntryLine;
use App\Models\JournalEntry;
use App\Services\JournalService;

class ReportController extends Controller
{
    /**
     * General Ledger Report (Buku Besar)
     */
    public function generalLedger(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfDay();
        $accountId = $request->account_id;
        $aggregateSavings = $request->has('aggregate_savings') ? $request->boolean('aggregate_savings') : true;
        $aggregateSales = $request->has('aggregate_sales') ? $request->boolean('aggregate_sales') : true;
        $aggregateCreditPayments = $request->has('aggregate_credit_payments') ? $request->boolean('aggregate_credit_payments') : true;

        // Simplified list for the dropdown
        $excludedCodes = [
            '1201', '1301', '2101', '2102', '2103', '4101', '4102', '5201',
        ];

        $accountsGrouped = Account::whereNotIn('code', $excludedCodes)
            ->orderBy('code')
            ->get()
            ->groupBy('type');

        $accounts = Account::orderBy('code')->get();
        $lines = collect();
        $openingBalance = 0;

        if ($accountId) {
            $selectedAccount = Account::find($accountId);
            
            // Calculate Opening Balance
            $openingBalance = JournalService::getAccountBalance($selectedAccount->code, $startDate->copy()->subDay());

            // Get Transactions
            $lines = JournalEntryLine::with(['journalEntry', 'account'])
                ->where('account_id', $accountId)
                ->whereHas('journalEntry', function($q) use ($startDate, $endDate) {
                    $q->whereBetween('transaction_date', [$startDate, $endDate])
                      ->where('status', 'posted');
                })
                ->get()
                ->sortBy(function($line) {
                    return $line->journalEntry->transaction_date . '-' . $line->created_at;
                });

            if ($aggregateSavings) {
                $summaries = [];
                $plainLines = collect();

                foreach ($lines as $line) {
                    $meta = $this->extractSavingLedgerMeta($line);
                    if (!$meta) {
                        $plainLines->push($line);
                        continue;
                    }

                    $key = implode('|', [$meta['date'], $meta['action'], $meta['type_label']]);
                    if (!isset($summaries[$key])) {
                        $summaries[$key] = [
                            'date' => $meta['date'],
                            'action' => $meta['action'],
                            'type_label' => $meta['type_label'],
                            'debit' => 0.0,
                            'credit' => 0.0,
                            'count' => 0,
                            'first_journal_number' => $line->journalEntry->journal_number,
                        ];
                    }

                    $summaries[$key]['debit'] += (float) $line->debit;
                    $summaries[$key]['credit'] += (float) $line->credit;
                    $summaries[$key]['count']++;
                }

                $summaryLines = collect($summaries)->map(function ($summary) {
                    $line = new \stdClass();
                    $line->debit = $summary['debit'];
                    $line->credit = $summary['credit'];
                    $line->description = "{$summary['count']} transaksi";
                    $line->journalEntry = (object) [
                        'transaction_date' => $summary['date'],
                        'journal_number' => $summary['first_journal_number'],
                        'description' => "{$summary['action']} Simpanan {$summary['type_label']} (Ringkas)",
                    ];
                    return $line;
                });

                $lines = $plainLines
                    ->concat($summaryLines)
                    ->sortBy(function ($line) {
                        return (string) $line->journalEntry->transaction_date . '-' . (string) $line->journalEntry->journal_number;
                    })
                    ->values();
            }

            if ($aggregateSales) {
                $summaries = [];
                $plainLines = collect();

                foreach ($lines as $line) {
                    $meta = $this->extractSalesLedgerMeta($line);
                    if (!$meta) {
                        $plainLines->push($line);
                        continue;
                    }

                    $key = $meta['date'];
                    if (!isset($summaries[$key])) {
                        $summaries[$key] = [
                            'date' => $meta['date'],
                            'debit' => 0.0,
                            'credit' => 0.0,
                            'count' => 0,
                            'first_journal_number' => $line->journalEntry->journal_number,
                        ];
                    }

                    $summaries[$key]['debit'] += (float) $line->debit;
                    $summaries[$key]['credit'] += (float) $line->credit;
                    $summaries[$key]['count']++;
                }

                $summaryLines = collect($summaries)->map(function ($summary) {
                    $line = new \stdClass();
                    $line->debit = $summary['debit'];
                    $line->credit = $summary['credit'];
                    $line->description = "{$summary['count']} transaksi";
                    $line->journalEntry = (object) [
                        'transaction_date' => $summary['date'],
                        'journal_number' => $summary['first_journal_number'],
                        'description' => 'Penjualan Harian (Ringkas)',
                    ];
                    return $line;
                });

                $lines = $plainLines
                    ->concat($summaryLines)
                    ->sortBy(function ($line) {
                        return (string) $line->journalEntry->transaction_date . '-' . (string) $line->journalEntry->journal_number;
                    })
                    ->values();
            }

            if ($aggregateCreditPayments) {
                $summaries = [];
                $plainLines = collect();

                foreach ($lines as $line) {
                    $meta = $this->extractCreditPaymentLedgerMeta($line);
                    if (!$meta) {
                        $plainLines->push($line);
                        continue;
                    }

                    $key = $meta['date'];
                    if (!isset($summaries[$key])) {
                        $summaries[$key] = [
                            'date' => $meta['date'],
                            'debit' => 0.0,
                            'credit' => 0.0,
                            'count' => 0,
                            'first_journal_number' => $line->journalEntry->journal_number,
                        ];
                    }

                    $summaries[$key]['debit'] += (float) $line->debit;
                    $summaries[$key]['credit'] += (float) $line->credit;
                    $summaries[$key]['count']++;
                }

                $summaryLines = collect($summaries)->map(function ($summary) {
                    $line = new \stdClass();
                    $line->debit = $summary['debit'];
                    $line->credit = $summary['credit'];
                    $line->description = "{$summary['count']} transaksi";
                    $line->journalEntry = (object) [
                        'transaction_date' => $summary['date'],
                        'journal_number' => $summary['first_journal_number'],
                        'description' => 'Pelunasan Kredit Harian (Ringkas)',
                    ];
                    return $line;
                });

                $lines = $plainLines
                    ->concat($summaryLines)
                    ->sortBy(function ($line) {
                        return (string) $line->journalEntry->transaction_date . '-' . (string) $line->journalEntry->journal_number;
                    })
                    ->values();
            }
        }

        return view('reports.accounting.ledger', compact(
            'accounts', 'accountsGrouped', 'lines', 'startDate', 'endDate', 'accountId', 'openingBalance', 'aggregateSavings', 'aggregateSales', 'aggregateCreditPayments'
        ));
    }

    private function extractSavingLedgerMeta($line): ?array
    {
        $referenceType = $line->journalEntry->reference_type ?? null;
        if (!in_array($referenceType, [Saving::class, 'saving'], true)) {
            return null;
        }

        $description = (string) ($line->journalEntry->description ?? '');
        if (!preg_match('/^(Setoran|Penarikan)\s+Simpanan\s+(.+?)\s*-\s*/u', $description, $m)) {
            return null;
        }

        return [
            'date' => (string) $line->journalEntry->transaction_date,
            'action' => $m[1],
            'type_label' => trim($m[2]),
        ];
    }

    private function extractSalesLedgerMeta($line): ?array
    {
        $referenceType = $line->journalEntry->reference_type ?? null;
        if (!in_array($referenceType, [Transaction::class, 'transaction'], true)) {
            return null;
        }

        $description = (string) ($line->journalEntry->description ?? '');
        if (!preg_match('/^Penjualan\s*-\s*/u', $description)) {
            return null;
        }

        return [
            'date' => (string) $line->journalEntry->transaction_date,
        ];
    }

    private function extractCreditPaymentLedgerMeta($line): ?array
    {
        $referenceType = $line->journalEntry->reference_type ?? null;
        if (!in_array($referenceType, [Transaction::class, 'transaction'], true)) {
            return null;
        }

        $description = (string) ($line->journalEntry->description ?? '');
        if (!preg_match('/^Pelunasan\s+Kredit\s*-\s*/u', $description)) {
            return null;
        }

        return [
            'date' => (string) $line->journalEntry->transaction_date,
        ];
    }

    /**
     * Balance Sheet Report (Neraca)
     */
    public function balanceSheet(Request $request)
    {
        $date = $request->date ? Carbon::parse($request->date) : Carbon::now();

        // Assets (1xxx)
        $assets = Account::where('code', 'like', '1%')->orderBy('code')->get();
        
        // Liabilities (2xxx)
        $liabilities = Account::where('code', 'like', '2%')->orderBy('code')->get();
        
        // Equity (3xxx)
        $equities = Account::where('code', 'like', '3%')->orderBy('code')->get();

        // Collect all codes to fetch in one go
        $allCodes = array_merge(
            $assets->pluck('code')->toArray(),
            $liabilities->pluck('code')->toArray(),
            $equities->pluck('code')->toArray()
        );
        
        $balances = JournalService::getBatchAccountBalances($allCodes, $date);

        // Map Balances
        foreach ($assets as $account) {
            $account->current_balance = $balances[$account->code] ?? 0;
        }
        foreach ($liabilities as $account) {
            $account->current_balance = $balances[$account->code] ?? 0;
        }
        foreach ($equities as $account) {
            $account->current_balance = $balances[$account->code] ?? 0;
        }

        // Calculate Historical Earnings (Laba Rugi Tahun-tahun Sebelumnya yang belum ditutup)
        $startOfYear = $date->copy()->startOfYear();
        $previousRevenue = JournalService::getTotalRevenue(null, $startOfYear->copy()->subDay());
        $previousExpense = JournalService::getTotalExpenses(null, $startOfYear->copy()->subDay());
        $previousEarnings = $previousRevenue - $previousExpense;

        // Calculate Current Earnings (Laba Rugi Berjalan)
        $revenue = JournalService::getTotalRevenue($startOfYear, $date);
        $expense = JournalService::getTotalExpenses($startOfYear, $date);
        $currentEarnings = $revenue - $expense;

        $totalAssets = $assets->sum('current_balance');
        $totalLiabilities = $liabilities->sum('current_balance');
        $totalEquity = $equities->sum('current_balance') + $currentEarnings + $previousEarnings; 

        return view('reports.accounting.balance_sheet', compact(
            'assets', 'liabilities', 'equities', 'currentEarnings', 'previousEarnings',
            'totalAssets', 'totalLiabilities', 'totalEquity', 'date'
        ));
    }

    /**
     * Income Statement Report (Laba Rugi)
     */
    public function incomeStatement(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfDay();

        // Revenue (4xxx)
        $revenues = Account::where('code', 'like', '4%')->orderBy('code')->get();
        // Expenses (5xxx)
        $expenses = Account::where('code', 'like', '5%')->orderBy('code')->get();

        $allAccountIds = array_merge($revenues->pluck('id')->toArray(), $expenses->pluck('id')->toArray());

        // Get movements for all relevant accounts in the period
        $movements = JournalEntryLine::whereIn('account_id', $allAccountIds)
            ->whereHas('journalEntry', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('transaction_date', [$startDate, $endDate])
                  ->where('status', 'posted');
            })
            ->select('account_id', DB::raw('SUM(debit) as total_debit'), DB::raw('SUM(credit) as total_credit'))
            ->groupBy('account_id')
            ->get()
            ->keyBy('account_id');

        // Map Balances
        foreach ($revenues as $account) {
            $mv = $movements->get($account->id);
            $account->period_balance = $mv ? ($mv->total_credit - $mv->total_debit) : 0;
        }

        foreach ($expenses as $account) {
            $mv = $movements->get($account->id);
            $account->period_balance = $mv ? ($mv->total_debit - $mv->total_credit) : 0;
        }

        $totalRevenue = $revenues->sum('period_balance');
        $totalExpense = $expenses->sum('period_balance');
        $netIncome = $totalRevenue - $totalExpense;

        return view('reports.accounting.income_statement', compact(
            'revenues', 'expenses', 'totalRevenue', 'totalExpense', 'netIncome',
            'startDate', 'endDate'
        ));
    }

    /**
     * Trial Balance Report (Neraca Saldo)
     */
    public function trialBalance(Request $request)
    {
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfDay();

        $accounts = Account::orderBy('code')->get();
        $accountCodes = $accounts->pluck('code')->toArray();
        $balances = JournalService::getBatchAccountBalances($accountCodes, $endDate);

        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($accounts as $account) {
            $balance = $balances[$account->code] ?? 0;
            
            $account->debit_balance = 0;
            $account->credit_balance = 0;

            if ($balance != 0) {
                if ($account->normal_balance == 'debit') {
                    if ($balance > 0) {
                        $account->debit_balance = $balance;
                    } else {
                        $account->credit_balance = abs($balance);
                    }
                } else { // credit normal
                    if ($balance > 0) {
                        $account->credit_balance = $balance;
                    } else {
                        $account->debit_balance = abs($balance);
                    }
                }
            }

            $totalDebit += $account->debit_balance;
            $totalCredit += $account->credit_balance;
        }

        return view('reports.accounting.trial_balance', compact('accounts', 'endDate', 'totalDebit', 'totalCredit'));
    }

    public function index()
    {
        return view('reports.index');
    }

    /**
     * Member reports.
     */
    public function members(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfYear();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfDay();

        // 1. Total Members Stats
        $totalMembers = Member::count();
        $activeMembers = Member::where('status', 'active')->count();
        $newMembers = Member::whereBetween('join_date', [$startDate, $endDate])->count();
        
        // 2. Department Distribution
        $byDepartment = Member::select('department', DB::raw('count(*) as total'))
            ->where('status', 'active')
            ->groupBy('department')
            ->orderByDesc('total')
            ->get();

        // 3. Gender Distribution
        $byGender = Member::select('gender', DB::raw('count(*) as total'))
            ->where('status', 'active')
            ->groupBy('gender')
            ->get();

        // 4. Recent Members List (for table)
        $members = Member::with('user')
            ->whereBetween('join_date', [$startDate, $endDate])
            ->latest('join_date')
            ->get();

        return view('reports.members', compact(
            'totalMembers', 'activeMembers', 'newMembers', 
            'byDepartment', 'byGender', 'members',
            'startDate', 'endDate'
        ));
    }

    /**
     * Savings reports.
     */
    public function savings(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfDay();
        $type = $request->type;

        // Base Query
        $query = Saving::whereBetween('transaction_date', [$startDate, $endDate]);
        if ($type) {
            $query->where('type', $type);
        }

        // Restriction for Member
        if (!auth()->user()->hasAdminAccess()) {
             $query->where('member_id', auth()->user()->member->id);
        }

        // 1. Summary Stats
        $totalDeposits = (clone $query)->where('transaction_type', 'deposit')->sum('amount');
        $totalWithdrawals = (clone $query)->where('transaction_type', 'withdrawal')->sum('amount');
        $netFlow = $totalDeposits - $totalWithdrawals;

        // 2. Transaction List
        $transactions = $query->with('member.user')
            ->latest('transaction_date')
            ->get();

        // 3. Balance per Type (All time)
        $balanceByType = Saving::select('type', 
                DB::raw("SUM(CASE WHEN transaction_type = 'deposit' THEN amount ELSE -amount END) as balance")
            )
            ->when(!auth()->user()->hasAdminAccess(), function($q) {
                $q->where('member_id', auth()->user()->member->id);
            })
            ->groupBy('type')
            ->get();

        return view('reports.savings', compact(
            'totalDeposits', 'totalWithdrawals', 'netFlow',
            'transactions', 'balanceByType',
            'startDate', 'endDate', 'type'
        ));
    }

    /**
     * Loans reports.
     */
    public function loans(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfYear();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfDay();
        $status = $request->status;

        // 1. Portfolio Summary (All Time Active)
        $totalActiveLoans = Loan::where('status', 'active')->count();
        $totalOutstanding = Loan::where('status', 'active')->sum('remaining_amount');
        $totalDisbursed = Loan::where('status', 'active')->sum('amount');
        
        // 2. Loan Filtering
        $query = Loan::with('member.user')
            ->whereBetween('application_date', [$startDate, $endDate]);
            
        if ($status) {
            $query->where('status', $status);
        }

        // Restriction for Member
        if (!auth()->user()->hasAdminAccess()) {
             $query->where('member_id', auth()->user()->member->id);
        }

        $loans = $query->latest('application_date')->get();

        // 3. Status Distribution
        $byStatus = Loan::select('status', DB::raw('count(*) as total'))
            ->whereBetween('application_date', [$startDate, $endDate])
            ->groupBy('status')
            ->get();

        return view('reports.loans', compact(
            'totalActiveLoans', 'totalOutstanding', 'totalDisbursed',
            'loans', 'byStatus',
            'startDate', 'endDate', 'status'
        ));
    }

    public function creditReceivables(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->addMonths(11)->endOfMonth();

        $summary = CreditInstallment::select(
                DB::raw('DATE_FORMAT(due_date, "%Y-%m-01") as month'),
                DB::raw('SUM(amount) as total')
            )
            ->whereBetween('due_date', [$startDate, $endDate])
            ->whereNotIn('status', ['paid'])
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $details = CreditInstallment::with(['transaction.user.member'])
            ->whereBetween('due_date', [$startDate, $endDate])
            ->whereNotIn('status', ['paid'])
            ->orderBy('due_date')
            ->get();

        return view('reports.credit-receivables', compact(
            'startDate',
            'endDate',
            'summary',
            'details'
        ));
    }

    /**
     * Transactions/Sales reports.
     */
    public function transactions(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfDay();
        $type = $request->type; // pos, online, or all
        $status = $request->status;
        $chartPeriod = $request->get('chart_period', 'daily');

        // Base Query
        $query = Transaction::whereBetween('created_at', [$startDate, $endDate]);
        
        if ($type) {
            $query->where('type', $type);
        }
        if ($status) {
            $query->where('status', $status);
        }

        // 1. Summary Stats
        $salesQuery = (clone $query)->whereIn('status', ['completed', 'paid', 'delivered', 'credit']);
        $totalSales = $salesQuery->sum('total_amount');
        $totalTransactions = $salesQuery->count();
        $averageTransaction = $totalTransactions > 0 ? $totalSales / $totalTransactions : 0;

        // Total COGS and Margin Calculation
        $cogsQuery = DB::table('transaction_items')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->join('products', 'transaction_items.product_id', '=', 'products.id')
            ->whereBetween('transactions.created_at', [$startDate, $endDate])
            ->whereIn('transactions.status', ['completed', 'paid', 'delivered', 'credit']);

        if ($type) {
            $cogsQuery->where('transactions.type', $type);
        }

        $totalCogs = (float) $cogsQuery->sum(DB::raw('transaction_items.quantity * (CASE WHEN products.conversion_factor > 0 THEN products.cost / products.conversion_factor ELSE products.cost END)'));
        $totalMargin = $totalSales - $totalCogs;
        $marginPercentage = $totalSales > 0 ? ($totalMargin / $totalSales) * 100 : 0;
        
        // Pending/Processing Orders
        $pendingOrders = (clone $query)->whereIn('status', ['pending', 'processing'])->count();
        
        // 2. Payment Method Distribution
        $byPaymentMethod = Transaction::whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('status', ['completed', 'paid', 'delivered', 'credit'])
            ->select('payment_method', DB::raw('count(*) as count'), DB::raw('sum(total_amount) as total'))
            ->groupBy('payment_method')
            ->get();

        // 3. Sales & Margin Chart Data Grouped by Period
        $periodSelect = match($chartPeriod) {
            'weekly' => "DATE_FORMAT(transactions.created_at, '%X-W%V') as period_key, CONCAT('Mkg ', WEEK(transactions.created_at, 1), ' ', YEAR(transactions.created_at)) as label",
            'monthly' => "DATE_FORMAT(transactions.created_at, '%Y-%m') as period_key, DATE_FORMAT(transactions.created_at, '%b %Y') as label",
            'quarterly' => "CONCAT(YEAR(transactions.created_at), '-Q', QUARTER(transactions.created_at)) as period_key, CONCAT('Q', QUARTER(transactions.created_at), ' ', YEAR(transactions.created_at)) as label",
            'semi_annually' => "CONCAT(YEAR(transactions.created_at), '-S', IF(MONTH(transactions.created_at) <= 6, 1, 2)) as period_key, CONCAT('Semester ', IF(MONTH(transactions.created_at) <= 6, 1, 2), ' ', YEAR(transactions.created_at)) as label",
            default => "DATE(transactions.created_at) as period_key, DATE_FORMAT(transactions.created_at, '%d %b') as label",
        };

        $periodGroupBy = match($chartPeriod) {
            'weekly' => [DB::raw("DATE_FORMAT(transactions.created_at, '%X-W%V')"), DB::raw("CONCAT('Mkg ', WEEK(transactions.created_at, 1), ' ', YEAR(transactions.created_at))")],
            'monthly' => [DB::raw("DATE_FORMAT(transactions.created_at, '%Y-%m')"), DB::raw("DATE_FORMAT(transactions.created_at, '%b %Y')")],
            'quarterly' => [DB::raw("CONCAT(YEAR(transactions.created_at), '-Q', QUARTER(transactions.created_at))"), DB::raw("CONCAT('Q', QUARTER(transactions.created_at), ' ', YEAR(transactions.created_at))")],
            'semi_annually' => [DB::raw("CONCAT(YEAR(transactions.created_at), '-S', IF(MONTH(transactions.created_at) <= 6, 1, 2))"), DB::raw("CONCAT('Semester ', IF(MONTH(transactions.created_at) <= 6, 1, 2), ' ', YEAR(transactions.created_at))")],
            default => [DB::raw("DATE(transactions.created_at)"), DB::raw("DATE_FORMAT(transactions.created_at, '%d %b')")],
        };

        $chartSalesQuery = Transaction::whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('status', ['completed', 'paid', 'delivered', 'credit']);
        if ($type) {
            $chartSalesQuery->where('type', $type);
        }

        $salesGrouped = $chartSalesQuery
            ->select(DB::raw($periodSelect), DB::raw('SUM(total_amount) as sales_total'))
            ->groupBy($periodGroupBy)
            ->orderBy('period_key')
            ->get()
            ->keyBy('period_key');

        $chartCogsQuery = DB::table('transaction_items')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->join('products', 'transaction_items.product_id', '=', 'products.id')
            ->whereBetween('transactions.created_at', [$startDate, $endDate])
            ->whereIn('transactions.status', ['completed', 'paid', 'delivered', 'credit']);
        if ($type) {
            $chartCogsQuery->where('transactions.type', $type);
        }

        $cogsGrouped = $chartCogsQuery
            ->select(DB::raw($periodSelect), DB::raw('SUM(transaction_items.quantity * (CASE WHEN products.conversion_factor > 0 THEN products.cost / products.conversion_factor ELSE products.cost END)) as cogs_total'))
            ->groupBy($periodGroupBy)
            ->orderBy('period_key')
            ->get()
            ->keyBy('period_key');

        $chartData = collect();
        foreach ($salesGrouped as $key => $salesRow) {
            $salesVal = (float) $salesRow->sales_total;
            $cogsVal = (float) ($cogsGrouped->get($key)->cogs_total ?? 0);
            $marginVal = $salesVal - $cogsVal;

            $chartData->push([
                'period_key' => $key,
                'label' => $salesRow->label,
                'sales' => round($salesVal, 2),
                'margin' => round($marginVal, 2)
            ]);
        }

        // Backward compatibility for daily sales
        $dailySales = $salesGrouped;

        // 4. Transaction List
        $transactions = $query->with(['user', 'cashier', 'items.product'])
            ->latest()
            ->paginate(20);

        // 5. Top Selling Products
        $topProducts = DB::table('transaction_items')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->join('products', 'transaction_items.product_id', '=', 'products.id')
            ->whereBetween('transactions.created_at', [$startDate, $endDate])
            ->whereIn('transactions.status', ['completed', 'paid', 'delivered', 'credit'])
            ->select('products.name', 'products.code', 
                DB::raw('sum(transaction_items.quantity) as total_qty'),
                DB::raw('sum(transaction_items.subtotal) as total_sales'))
            ->groupBy('products.id', 'products.name', 'products.code')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();

        return view('reports.transactions', compact(
            'totalSales', 'totalTransactions', 'averageTransaction', 'pendingOrders',
            'totalMargin', 'marginPercentage', 'chartPeriod', 'chartData',
            'byPaymentMethod', 'dailySales', 'transactions', 'topProducts',
            'startDate', 'endDate', 'type', 'status'
        ));
    }

    /**
     * Export Members Report to PDF
     */
    public function exportMembersPDF(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfYear();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfDay();

        $totalMembers = Member::count();
        $activeMembers = Member::where('status', 'active')->count();
        $newMembers = Member::whereBetween('join_date', [$startDate, $endDate])->count();
        
        $members = Member::with('user')
            ->whereBetween('join_date', [$startDate, $endDate])
            ->latest('join_date')
            ->get();

        $pdf = PDF::loadView('reports.pdf.members', compact(
            'totalMembers', 'activeMembers', 'newMembers', 'members', 'startDate', 'endDate'
        ));

        \App\Models\AuditLog::log(
            'export', 
            "Mengunduh Laporan Anggota (PDF)"
        );

        return $pdf->download('laporan-anggota-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Export Savings Report to PDF
     */
    public function exportSavingsPDF(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfDay();

        $query = Saving::whereBetween('transaction_date', [$startDate, $endDate]);

        $totalDeposits = (clone $query)->where('transaction_type', 'deposit')->sum('amount');
        $totalWithdrawals = (clone $query)->where('transaction_type', 'withdrawal')->sum('amount');
        $totalTransactions = (clone $query)->count();



        // Restriction for Member
        if (!auth()->user()->hasAdminAccess()) {
             $query->where('member_id', auth()->user()->member->id);
        }

        $savings = $query->with('member.user')->latest('transaction_date')->get();

        $pdf = PDF::loadView('reports.pdf.savings', compact(
            'totalDeposits', 'totalWithdrawals', 'totalTransactions', 'savings', 'startDate', 'endDate'
        ));

        \App\Models\AuditLog::log(
            'export', 
            "Mengunduh Laporan Simpanan (PDF)"
        );

        return $pdf->download('laporan-simpanan-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Export Loans Report to PDF
     */
    public function exportLoansPDF(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfYear();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfDay();

        $loans = Loan::with('member.user')
            ->whereBetween('application_date', [$startDate, $endDate])
            ->when(!auth()->user()->hasAdminAccess(), function($q) {
                $q->where('member_id', auth()->user()->member->id);
            })
            ->latest('application_date')
            ->get();

        $totalLoans = $loans->sum('amount');
        $totalPaid = $loans->sum('paid_amount');
        $totalRemaining = $loans->sum('remaining_amount');
        $loanCount = $loans->count();

        $pdf = PDF::loadView('reports.pdf.loans', compact(
            'totalLoans', 'totalPaid', 'totalRemaining', 'loanCount', 'loans', 'startDate', 'endDate'
        ));

        \App\Models\AuditLog::log(
            'export', 
            "Mengunduh Laporan Pinjaman (PDF)"
        );

        return $pdf->download('laporan-pinjaman-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Export Members to Excel
     */
    public function exportMembersExcel(Request $request)
    {
        \App\Models\AuditLog::log(
            'export', 
            "Mengunduh Laporan Anggota (Excel)"
        );
        return Excel::download(new MembersExport($request), 'laporan-anggota-' . now()->format('Y-m-d') . '.xlsx');
    }

    /**
     * Export Savings to Excel
     */
    public function exportSavingsExcel(Request $request)
    {
        \App\Models\AuditLog::log(
            'export', 
            "Mengunduh Laporan Simpanan (Excel)"
        );
        return Excel::download(new SavingsExport($request), 'laporan-simpanan-' . now()->format('Y-m-d') . '.xlsx');
    }

    /**
     * Export Loans to Excel
     */
    public function exportLoansExcel(Request $request)
    {
        \App\Models\AuditLog::log(
            'export', 
            "Mengunduh Laporan Pinjaman (Excel)"
        );
        return Excel::download(new LoansExport($request), 'laporan-pinjaman-' . now()->format('Y-m-d') . '.xlsx');
    }

    public function analyzeIncomeStatement(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        // Fetch Revenues and Expenses (identical calculations)
        $revenues = Account::where('code', 'like', '4%')->orderBy('code')->get();
        $expenses = Account::where('code', 'like', '5%')->orderBy('code')->get();

        $allAccountIds = array_merge($revenues->pluck('id')->toArray(), $expenses->pluck('id')->toArray());

        $movements = JournalEntryLine::whereIn('account_id', $allAccountIds)
            ->whereHas('journalEntry', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('transaction_date', [$startDate, $endDate])
                  ->where('status', 'posted');
            })
            ->select('account_id', DB::raw('SUM(debit) as total_debit'), DB::raw('SUM(credit) as total_credit'))
            ->groupBy('account_id')
            ->get()
            ->keyBy('account_id');

        $revenueList = [];
        foreach ($revenues as $account) {
            $mv = $movements->get($account->id);
            $bal = $mv ? ($mv->total_credit - $mv->total_debit) : 0;
            if ($bal != 0) {
                $revenueList[] = "- {$account->code} {$account->name}: Rp " . number_format($bal, 0, ',', '.');
            }
        }

        $expenseList = [];
        foreach ($expenses as $account) {
            $mv = $movements->get($account->id);
            $bal = $mv ? ($mv->total_debit - $mv->total_credit) : 0;
            if ($bal != 0) {
                $expenseList[] = "- {$account->code} {$account->name}: Rp " . number_format($bal, 0, ',', '.');
            }
        }

        $totalRevenue = $revenues->sum(function($account) use ($movements) {
            $mv = $movements->get($account->id);
            return $mv ? ($mv->total_credit - $mv->total_debit) : 0;
        });

        $totalExpense = $expenses->sum(function($account) use ($movements) {
            $mv = $movements->get($account->id);
            return $mv ? ($mv->total_debit - $mv->total_credit) : 0;
        });

        $netIncome = $totalRevenue - $totalExpense;

        // AI Configurations
        $config = \App\Models\AiSetting::getConfig();
        if (!$config['enabled']) {
            return response()->json([
                'success' => false,
                'error' => 'Modul Asisten AI belum diaktifkan oleh Administrator. Silakan aktifkan terlebih dahulu di menu Pengaturan AI.'
            ], 403);
        }

        $provider = $config['provider'];
        $url = $config['url'];
        $model = $config['model'];
        $apiKey = $config['apiKey'];

        // Build prompt
        $financialReportStr = "LAPORAN LABA RUGI KOPERASI\n";
        $financialReportStr .= "Periode: " . $startDate->translatedFormat('d F Y') . " s/d " . $endDate->translatedFormat('d F Y') . "\n\n";
        $financialReportStr .= "POST PENDAPATAN (REVENUE):\n" . (empty($revenueList) ? "- Tidak ada data pendapatan\n" : implode("\n", $revenueList)) . "\n";
        $financialReportStr .= "TOTAL PENDAPATAN: Rp " . number_format($totalRevenue, 0, ',', '.') . "\n\n";
        $financialReportStr .= "POST BEBAN (EXPENSES):\n" . (empty($expenseList) ? "- Tidak ada data beban\n" : implode("\n", $expenseList)) . "\n";
        $financialReportStr .= "TOTAL BEBAN: Rp " . number_format($totalExpense, 0, ',', '.') . "\n\n";
        $financialReportStr .= "LABA BERSIH / SHU BERJALAN: Rp " . number_format($netIncome, 0, ',', '.') . "\n";
        
        $systemPrompt = "Anda adalah Chief Financial Officer (CFO), auditor finansial profesional senior, dan konsultan ahli bisnis koperasi.
Tugas Anda adalah melakukan analisa audit mendalam dan kritis atas Laporan Laba Rugi Koperasi yang diberikan.

Lakukan perhitungan persentase secara presisi berdasarkan angka-angka yang diberikan.
Berikan analisis yang sangat mendalam, profesional, dan mudah dipahami dalam Bahasa Indonesia, dengan struktur Markdown yang sangat rapi dan menarik. Respons Anda harus terbagi dalam 3 bagian utama menggunakan judul Markdown yang tepat:

### 🔴 Evaluasi Kinerja (Analisis Rasio Keuangan)
- **Ringkasan Finansial**: Sajikan sebuah tabel Markdown perbandingan metrik utama yang mencakup:
  * Total Pendapatan
  * Harga Pokok Penjualan (HPP)
  * Laba Kotor (Gross Profit = Total Pendapatan - HPP)
  * Margin Laba Kotor (Gross Profit Margin %)
  * Total Beban Operasional (Total Beban - HPP)
  * Laba Bersih / SHU Berjalan
  * Margin Laba Bersih (Net Profit Margin %)
- **Analisis Kritis & Akar Masalah**: 
  * Jika HPP lebih besar dari Total Pendapatan (HPP > 100% dari Pendapatan), tandai ini sebagai **ANOMALI VITAL DAN SANGAT BERBAHAYA**. Jelaskan konsekuensinya secara mendalam (koperasi merugi secara langsung di setiap produk yang terjual bahkan sebelum menghitung biaya operasional).
  * Ulas proporsi Pendapatan vs Beban secara kritis.
  * Sorot akun pengeluaran spesifik yang paling membebani dan jelaskan rasionya terhadap pendapatan.
  * Simpulkan status kesehatan finansial koperasi saat ini secara jujur, lugas, dan terperinci.

### 🟢 Rekomendasi Aksi Operasional
- Gunakan format Callout Box jika ada hal darurat (misalnya: `> [!CAUTION]` jika HPP melebihi Pendapatan, atau `> [!WARNING]` jika biaya operasional terlalu tinggi).
- Berikan daftar rekomendasi taktis, konkret, dan dapat segera dilaksanakan oleh pengurus koperasi, terutama untuk:
  1. **Kebijakan Penetapan Harga (Pricing Strategy Overhaul)**: Aturan markup harga jual minimum (misal: Cost + 10-15%) agar tidak menjual rugi.
  2. **Audit & Manajemen Inventoris (Stocktaking & Shrinkage Control)**: Melakukan stok opname berkala untuk mendeteksi kehilangan barang atau pencatatan yang keliru.
  3. **Negosiasi Supplier & Pengadaan Barang**: Beralih ke distributor utama atau sistem konsinyasi guna memangkas harga beli pokok.
  4. **Efisiensi Beban Operasional**: Langkah spesifik memangkas pos biaya operasional yang tidak perlu.

### 🔵 Rencana Bisnis Langkah Ke Depan
- Berikan panduan langkah demi langkah (checklist checklist dengan format `- [ ]` atau `- [x]`) untuk menstabilkan arus kas, menghentikan kebocoran, dan membalikkan kerugian menjadi surplus SHU pada bulan berikutnya.
- Buat target-target pencapaian realistis per minggu untuk tim manajemen.

Format respons Anda HARUS menggunakan struktur tajuk Markdown di atas secara konsisten agar parser di frontend dapat merendernya dengan visual yang premium. Berikan detail yang lengkap, jangan ringkas atau terpotong.";

        $message = "Berikut adalah laporan laba rugi kami untuk dianalisa:\n\n" . $financialReportStr;

        try {
            if ($provider === 'ollama') {
                $response = \Illuminate\Support\Facades\Http::timeout(120)->post("{$url}/api/generate", [
                    'model' => $model,
                    'prompt' => "{$systemPrompt}\n\nUser: {$message}\nAssistant:",
                    'stream' => false
                ]);
                
                if (!$response->successful()) {
                    throw new \Exception('Ollama tidak merespons: ' . $response->status());
                }
                
                return response()->json([
                    'success' => true,
                    'response' => $response->json('response', 'Tidak ada respons')
                ]);
                
            } elseif ($provider === 'openai') {
                $response = \Illuminate\Support\Facades\Http::timeout(60)
                    ->withHeaders(['Authorization' => "Bearer {$apiKey}"])
                    ->post('https://api.openai.com/v1/chat/completions', [
                        'model' => $model,
                        'messages' => [
                            ['role' => 'system', 'content' => $systemPrompt],
                            ['role' => 'user', 'content' => $message]
                        ]
                    ]);
                
                if (!$response->successful()) {
                    throw new \Exception('OpenAI error: ' . $response->json('error.message', 'Unknown error'));
                }
                
                return response()->json([
                    'success' => true,
                    'response' => $response->json('choices.0.message.content', 'Tidak ada respons')
                ]);
                
            } elseif ($provider === 'gemini') {
                $response = \Illuminate\Support\Facades\Http::timeout(60)
                    ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", [
                        'contents' => [
                            [
                                'parts' => [
                                    ['text' => "{$systemPrompt}\n\nUser: {$message}\n\nPlease respond in Indonesian."]
                                ]
                            ]
                        ],
                        'generationConfig' => [
                            'temperature' => 0.7,
                            'maxOutputTokens' => 8192,
                        ]
                    ]);
                
                if (!$response->successful()) {
                    $errorMsg = $response->json('error.message', 'Unknown error');
                    throw new \Exception('Gemini error: ' . $errorMsg);
                }
                
                $text = $response->json('candidates.0.content.parts.0.text', 'Tidak ada respons');
                
                return response()->json([
                    'success' => true,
                    'response' => $text
                ]);
                
            } else {
                // Custom provider
                $response = \Illuminate\Support\Facades\Http::timeout(60)->post("{$url}/generate", [
                    'prompt' => "{$systemPrompt}\n\nUser: {$message}\nAssistant:"
                ]);
                
                $data = $response->json();
                return response()->json([
                    'success' => true,
                    'response' => $data['response'] ?? $data['text'] ?? $data['output'] ?? 'Tidak ada respons'
                ]);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Gagal terhubung dengan layanan AI: ' . $e->getMessage()
            ], 500);
        }
    }
}
