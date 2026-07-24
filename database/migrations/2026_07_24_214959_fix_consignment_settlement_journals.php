<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Temukan ID Akun HPP (5201) dan Persediaan (1301)
        $hppAccount = DB::table('accounts')->where('code', '5201')->first();
        $inventoryAccount = DB::table('accounts')->where('code', '1301')->first();

        if ($hppAccount && $inventoryAccount) {
            // Temukan ID dari semua Jurnal Settlement Konsinyasi
            $settlementJournals = DB::table('journal_entries')
                ->where('reference_type', 'like', '%consignment%settlement%')
                ->orWhere('description', 'like', '%Settlement Konsinyasi%')
                ->pluck('id');

            if ($settlementJournals->isNotEmpty()) {
                // Update baris jurnal (journal_entry_lines) yang salah debit 5201 menjadi 1301
                DB::table('journal_entry_lines')
                    ->whereIn('journal_entry_id', $settlementJournals)
                    ->where('account_id', $hppAccount->id)
                    ->update(['account_id' => $inventoryAccount->id]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Temukan ID Akun HPP (5201) dan Persediaan (1301)
        $hppAccount = DB::table('accounts')->where('code', '5201')->first();
        $inventoryAccount = DB::table('accounts')->where('code', '1301')->first();

        if ($hppAccount && $inventoryAccount) {
            $settlementJournals = DB::table('journal_entries')
                ->where('reference_type', 'like', '%consignment%settlement%')
                ->orWhere('description', 'like', '%Settlement Konsinyasi%')
                ->pluck('id');

            if ($settlementJournals->isNotEmpty()) {
                // Kembalikan ke 5201 jika di-rollback
                DB::table('journal_entry_lines')
                    ->whereIn('journal_entry_id', $settlementJournals)
                    ->where('account_id', $inventoryAccount->id)
                    ->update(['account_id' => $hppAccount->id]);
            }
        }
    }
};
