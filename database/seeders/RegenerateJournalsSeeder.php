<?php

namespace Database\Seeders;

use App\Models\Loan;
use App\Models\LoanPayment;
use App\Models\Purchase;
use App\Models\Saving;
use App\Services\JournalService;
use Illuminate\Database\Seeder;

class RegenerateJournalsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $year = 2025;
        $this->command->info("Regenerating Journal Entries for Year $year...");

        // 1. PURCHASES
        // ------------
        $purchases = Purchase::whereYear('purchase_date', $year)
            ->where('status', 'completed')
            ->doesntHave('journalEntry') // Check relationship
            ->get();

        $count = 0;
        foreach ($purchases as $purchase) {
            JournalService::journalPurchase($purchase);
            $count++;
        }
        $this->command->info("Processed $count Purchases.");

        // 2. SAVINGS
        // ----------
        $savings = Saving::whereYear('transaction_date', $year)
            ->doesntHave('journalEntry')
            ->get();

        $count = 0;
        foreach ($savings as $saving) {
            if ($saving->transaction_type === 'deposit') {
                JournalService::journalSavingDeposit($saving);
            } else {
                JournalService::journalSavingWithdrawal($saving);
            }
            $count++;
        }
        $this->command->info("Processed $count Saving Transactions.");

        // 3. LOAN DISBURSEMENTS
        // ---------------------
        $loans = Loan::whereYear('disbursement_date', $year)
            ->whereIn('status', ['active', 'completed', 'defaulted'])
            ->doesntHave('journalEntry')
            ->get();

        $count = 0;
        foreach ($loans as $loan) {
            JournalService::journalLoanDisbursement($loan);
            $count++;
        }
        $this->command->info("Processed $count Loan Disbursements.");

        // 4. LOAN PAYMENTS
        // ----------------
        // Need to check specific table for payments
        $payments = LoanPayment::whereYear('payment_date', $year)
            ->where('status', 'paid')
            ->doesntHave('journalEntry') // Assuming LoanPayment has morphTo journalEntry or we check manually
            ->get();

        // Note: LoanPayment model might not have 'journalEntry' relation defined in the file I saw earlier (Purchase had it).
        // Let's assume standard polymorphic 'reference' relationship on JournalEntry.
        
        $count = 0;
        foreach ($payments as $payment) {
            // Re-calculate interest/principal split if needed, but model usually has them.
            $principal = $payment->principal_amount;
            $interest = $payment->interest_amount;
            
            JournalService::journalLoanPayment($payment, $principal, $interest);
            $count++;
        }
        $this->command->info("Processed $count Loan Payments.");
    }
}
