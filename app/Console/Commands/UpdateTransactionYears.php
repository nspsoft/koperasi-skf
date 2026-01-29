<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateTransactionYears extends Command
{
    protected $signature = 'app:update-years-2026';
    protected $description = 'Update all transaction years to 2026';

    public function handle()
    {
        $this->info('Updating years to 2026...');

        try {
            // Update Savings
            $savingsCount = DB::table('savings')->update([
                'transaction_date' => DB::raw("DATE_FORMAT(transaction_date, '2026-%m-%d')")
            ]);
            $this->info("Updated {$savingsCount} saving records.");

            // Update Loans
            if (\Schema::hasTable('loans')) {
                $loansCount = DB::table('loans')->update([
                    'loan_date' => DB::raw("DATE_FORMAT(loan_date, '2026-%m-%d')")
                ]);
                $this->info("Updated {$loansCount} loan records.");
            }

            // Update Sales Transactions if any
            if (\Schema::hasTable('transactions')) {
                $transCount = DB::table('transactions')->update([
                    'created_at' => DB::raw("DATE_FORMAT(created_at, '2026-%m-%d %H:%i:%s')"),
                    'updated_at' => DB::raw("DATE_FORMAT(updated_at, '2026-%m-%d %H:%i:%s')"),
                ]);
                $this->info("Updated {$transCount} sales transactions.");
            }

            $this->info('Successfully updated all years to 2026.');
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
        }

        return 0;
    }
}
