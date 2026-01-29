<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\GeneratedDocument;

class ClearDocumentArchive extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'documents:clear-archive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all document archive records (Surat & Dokumen only, excluding Member Cards and SHU Slips)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->confirm('Are you sure you want to clear all document archive records? This cannot be undone.')) {
            $this->info('Operation cancelled.');
            return 0;
        }

        // Get count before deletion
        $count = GeneratedDocument::whereNotIn('document_type', ['Member Card', 'SHU Slip'])
            ->count();

        if ($count === 0) {
            $this->info('No documents to delete.');
            return 0;
        }

        // Delete documents
        GeneratedDocument::whereNotIn('document_type', ['Member Card', 'SHU Slip'])
            ->delete();

        $this->info("Successfully deleted {$count} document archive records.");
        $this->info('Member Cards and SHU Slips were preserved.');

        return 0;
    }
}
