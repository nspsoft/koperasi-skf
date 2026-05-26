<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use App\Models\Saving;
use App\Models\JournalEntry;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\Purchase;

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

Artisan::command('journals:reclassify-consignment-expense {--dry-run}', function () {
    $fromAccount = Account::where('code', '5102')->first();
    $toAccount = Account::where('code', '5201')->first();

    if (! $fromAccount || ! $toAccount) {
        $this->error('Akun 5102 atau 5201 tidak ditemukan.');
        return;
    }

    $query = DB::table('journal_entry_lines')
        ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
        ->where('journal_entries.reference_type', \App\Models\ConsignmentSettlement::class)
        ->where('journal_entry_lines.account_id', $fromAccount->id);

    $affected = (clone $query)->count();
    if ($affected === 0) {
        $this->info('Tidak ada jurnal konsinyasi pada akun 5102.');
        return;
    }

    $dryRun = (bool) $this->option('dry-run');
    if (! $dryRun) {
        $query->update(['journal_entry_lines.account_id' => $toAccount->id]);
    }

    $this->info("Baris jurnal terdampak: {$affected}");
    if ($dryRun) {
        $this->warn('Mode dry-run aktif. Tidak ada perubahan data.');
    } else {
        $this->info('Reklasifikasi selesai: 5102 -> 5201 untuk settlement konsinyasi.');
    }
})->purpose('Reklasifikasi biaya settlement konsinyasi dari 5102 ke 5201');

Artisan::command('journals:generate-sales {--dry-run} {--from=} {--to=}', function () {
    $from = $this->option('from') ? \Carbon\Carbon::parse($this->option('from'))->startOfDay() : null;
    $to = $this->option('to') ? \Carbon\Carbon::parse($this->option('to'))->endOfDay() : null;

    $query = Transaction::query()
        ->where('status', 'completed')
        ->whereNotExists(function ($q) {
            $q->select(\DB::raw(1))
                ->from('journal_entries')
                ->whereColumn('journal_entries.reference_id', 'transactions.id')
                ->whereIn('journal_entries.reference_type', [Transaction::class, 'transaction']);
        });

    if ($from) {
        $query->whereDate('created_at', '>=', $from);
    }
    if ($to) {
        $query->whereDate('created_at', '<=', $to);
    }

    $transactions = $query->with('items.product')->get();

    if ($transactions->isEmpty()) {
        $this->info('Tidak ada transaksi tanpa jurnal.');
        return;
    }

    $dryRun = (bool) $this->option('dry-run');
    $generated = 0;
    $errors = 0;

    DB::transaction(function () use ($transactions, $dryRun, &$generated, &$errors) {
        foreach ($transactions as $trx) {   
            try {
                if (! $dryRun) {
                    \App\Services\JournalService::journalSale($trx);
                }
                $generated++;
            } catch (\Exception $e) {
                $errors++;
            }
        }
    });

    $this->info("Transaksi terdeteksi: {$transactions->count()}");
    $this->info("Jurnal " . ($dryRun ? 'akan dibuat' : 'dibuat') . ": {$generated}");
    if ($errors > 0) {
        $this->warn("Gagal diproses: {$errors}");
    }
    if ($dryRun) {
        $this->warn('Mode dry-run aktif. Tidak ada data yang disimpan.');
    }
})->purpose('Generate jurnal penjualan untuk transaksi completed yang belum memiliki jurnal');

Artisan::command('purchases:delete-cancelled {--dry-run} {--from=} {--to=}', function () {
    $from = $this->option('from') ? \Carbon\Carbon::parse($this->option('from'))->startOfDay() : null;
    $to = $this->option('to') ? \Carbon\Carbon::parse($this->option('to'))->endOfDay() : null;

    $query = Purchase::query()->where('status', 'cancelled');

    if ($from) {
        $query->whereDate('purchase_date', '>=', $from);
    }
    if ($to) {
        $query->whereDate('purchase_date', '<=', $to);
    }

    $purchases = $query->with('items')->get();
    if ($purchases->isEmpty()) {
        $this->info('Tidak ada purchase cancelled yang dapat dihapus.');
        return;
    }

    $dryRun = (bool) $this->option('dry-run');
    $deletedPurchases = 0;
    $deletedItems = 0;
    $deletedJournals = 0;
    $deletedJournalLines = 0;
    $deletedReceipts = 0;

    DB::transaction(function () use ($purchases, $dryRun, &$deletedPurchases, &$deletedItems, &$deletedJournals, &$deletedJournalLines, &$deletedReceipts) {
        foreach ($purchases as $purchase) {
            $itemsCount = $purchase->items->count();
            $deletedItems += $itemsCount;
            $deletedPurchases++;

            $journals = JournalEntry::where('reference_type', Purchase::class)
                ->where('reference_id', $purchase->id)
                ->get();
            $deletedJournals += $journals->count();
            foreach ($journals as $journal) {
                $deletedJournalLines += $journal->lines()->count();
            }

            if (! $dryRun) {
                if ($purchase->receipt_image && \Storage::disk('public')->exists($purchase->receipt_image)) {
                    \Storage::disk('public')->delete($purchase->receipt_image);
                    $deletedReceipts++;
                }

                foreach ($journals as $journal) {
                    $journal->lines()->delete();
                    $journal->delete();
                }

                $purchase->items()->delete();
                $purchase->delete();
            }
        }
    });

    $this->info("Purchase cancelled terdeteksi: {$deletedPurchases}");
    $this->info("Purchase " . ($dryRun ? 'akan dihapus' : 'dihapus') . ": {$deletedPurchases}");
    $this->info("Item purchase " . ($dryRun ? 'akan dihapus' : 'dihapus') . ": {$deletedItems}");
    $this->info("Jurnal terkait " . ($dryRun ? 'akan dihapus' : 'dihapus') . ": {$deletedJournals}");
    $this->info("Baris jurnal terkait " . ($dryRun ? 'akan dihapus' : 'dihapus') . ": {$deletedJournalLines}");
    if (! $dryRun) {
        $this->info("File bukti " . 'dihapus' . ": {$deletedReceipts}");
    } else {
        $this->warn('Mode dry-run aktif. Tidak ada data yang dihapus.');
    }
})->purpose('Hapus purchase dengan status cancelled beserta item, jurnal, dan file bukti');

\Illuminate\Support\Facades\Schedule::command('email:loan-reminders --days=3 --overdue')->dailyAt('08:00');

