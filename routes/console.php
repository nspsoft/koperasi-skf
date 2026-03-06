<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use App\Models\Saving;
use App\Models\JournalEntry;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('journals:dedupe-savings {--dry-run}', function () {
    $morph = (new Saving())->getMorphClass();
    $types = array_unique([Saving::class, $morph, 'saving']);
    $duplicateIds = JournalEntry::whereIn('reference_type', $types)
        ->where('reference_id', '>', 0)
        ->select('reference_id', DB::raw('COUNT(*) as total'))
        ->groupBy('reference_id')
        ->havingRaw('COUNT(*) > 1')
        ->pluck('reference_id');

    if ($duplicateIds->isEmpty()) {
        $this->info('Tidak ada jurnal simpanan duplikat.');
        return;
    }

    $dryRun = (bool) $this->option('dry-run');
    $deletedJournals = 0;
    $deletedLines = 0;

    DB::transaction(function () use ($duplicateIds, $types, $dryRun, &$deletedJournals, &$deletedLines) {
        foreach ($duplicateIds as $savingId) {
            $journals = JournalEntry::whereIn('reference_type', $types)
                ->where('reference_id', $savingId)
                ->orderBy('id')
                ->get();

            if ($journals->count() <= 1) {
                continue;
            }

            $toDelete = $journals->slice(1);
            foreach ($toDelete as $journal) {
                $lineCount = $journal->lines()->count();
                $deletedLines += $lineCount;
                $deletedJournals++;

                if (! $dryRun) {
                    $journal->lines()->delete();
                    $journal->delete();
                }
            }
        }
    });

    $this->info("Duplikat saving terdeteksi: {$duplicateIds->count()}");
    $this->info("Jurnal " . ($dryRun ? 'akan dihapus' : 'dihapus') . ": {$deletedJournals}");
    $this->info("Baris jurnal " . ($dryRun ? 'akan dihapus' : 'dihapus') . ": {$deletedLines}");
    if ($dryRun) {
        $this->warn('Mode dry-run aktif. Tidak ada data yang dihapus.');
    }
})->purpose('Hapus jurnal simpanan duplikat berdasarkan reference_id');
