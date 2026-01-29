<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ResetTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reset-transactions {--force : Force the operation to run when in production}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset all transaction history while preserving master data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (app()->environment('production') && !$this->option('force')) {
            $this->error('Operation not allowed in production without --force flag.');
            return 1;
        }

        if (!$this->confirm('Are you absolutely sure you want to reset ALL transaction history? This is IRREVERSIBLE.')) {
            $this->info('Operation cancelled.');
            return 0;
        }

        $this->info('Starting transaction reset...');

        // List tables to truncate in an order that respects foreign key constraints
        $tables = [
            // Items first
            'transaction_items',
            'purchase_items',
            'stock_opname_items',
            'consignment_inbound_items',
            'journal_entry_lines',
            'loan_payments',
            'withdrawal_requests',
            
            // Parent records
            'transactions',
            'purchases',
            'stock_opnames',
            'consignment_inbounds',
            'consignment_settlements',
            'journal_entries',
            'savings',
            'loans',
            'expenses',
            'bank_transactions',
            'audit_logs',
        ];

        Schema::disableForeignKeyConstraints();

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                $this->comment("Truncating table: {$table}");
                DB::table($table)->truncate();
            }
        }

        // Reset Product Stock
        $this->comment("Resetting product stocks to 0...");
        DB::table('products')->update(['stock' => 0]);

        Schema::enableForeignKeyConstraints();

        $this->info('Transaction history has been successfully reset.');
        $this->warn('Note: Member balances and inventory stocks have been reset to 0.');

        return 0;
    }
}
