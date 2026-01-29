<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;

class CleanOrphanedJournals extends Command
{
    protected $signature = 'journals:clean-orphaned {--dry-run : Show what would be deleted without actually deleting}';
    protected $description = 'Clean up orphaned journal entries where the source record has been deleted';

    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        $this->info($isDryRun ? '=== DRY RUN MODE ===' : '=== CLEANING ORPHANED JOURNALS ===');
        $this->newLine();

        $orphanedTypes = [
            'App\Models\Loan' => 'loans',
            'App\Models\LoanPayment' => 'loan_payments',
            'App\Models\Saving' => 'savings',
            'App\Models\Transaction' => 'transactions',
            'App\Models\Purchase' => 'purchases',
            'App\Models\Expense' => 'expenses',
        ];

        $totalDeleted = 0;

        foreach ($orphanedTypes as $modelClass => $tableName) {
            $orphaned = JournalEntry::where('reference_type', $modelClass)
                ->whereNotExists(function($q) use ($tableName) {
                    $q->select(DB::raw(1))
                      ->from($tableName)
                      ->whereRaw("{$tableName}.id = journal_entries.reference_id");
                });

            $count = $orphaned->count();
            
            if ($count > 0) {
                $modelName = class_basename($modelClass);
                $this->warn("{$modelName}: {$count} orphaned journal(s)");

                if (!$isDryRun) {
                    // Get IDs first
                    $journalIds = $orphaned->pluck('id')->toArray();
                    
                    // Delete lines first
                    JournalEntryLine::whereIn('journal_entry_id', $journalIds)->delete();
                    
                    // Delete journals
                    JournalEntry::whereIn('id', $journalIds)->delete();
                    
                    $this->info("  ✓ Deleted {$count} journal(s) and their lines");
                }
                
                $totalDeleted += $count;
            }
        }

        $this->newLine();
        if ($totalDeleted > 0) {
            if ($isDryRun) {
                $this->info("Total orphaned journals found: {$totalDeleted}");
                $this->comment("Run without --dry-run to actually delete them.");
            } else {
                $this->info("✓ Successfully cleaned {$totalDeleted} orphaned journal entries!");
            }
        } else {
            $this->info("No orphaned journals found. Database is clean!");
        }

        return Command::SUCCESS;
    }
}
