<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Member;
use App\Models\Saving;
use App\Models\User;
use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

$admin = User::first();
$members = Member::all();

if ($members->isEmpty()) {
    echo "Tidak ada data anggota.\n";
    exit;
}

// Akun-akun jurnal keuangan (Opsional)
$cashAccount = Account::where('code', '1100')->orWhere('code', '1110')->first();
$savingWajibAccount = Account::where('code', '3120')->first();
$savingSukarelaAccount = Account::where('code', '2110')->first();

DB::beginTransaction();
try {
    for ($month = 1; $month <= 12; $month++) {
        $date = Carbon::create(2025, $month, rand(5, 25));
        
        foreach ($members as $member) {
            // 1. Simpanan Wajib Bulanan
            $refWajib = 'SAV' . $date->format('ymd') . rand(1000, 9999);
            $savingWajib = Saving::create([
                'member_id' => $member->id,
                'type' => 'wajib',
                'transaction_type' => 'deposit',
                'amount' => 50000, // Rp 50.000 per bulan
                'transaction_date' => $date->copy(),
                'reference_number' => $refWajib,
                'description' => "Simpanan Wajib bulan " . $date->format('F Y'),
                'created_by' => $admin->id ?? 1,
            ]);

            if ($cashAccount && $savingWajibAccount) {
                $journal = JournalEntry::create([
                    'date' => $savingWajib->transaction_date,
                    'reference_number' => 'JRN-SAV-' . time() . '-' . rand(1000,9999),
                    'reference_type' => Saving::class,
                    'reference_id' => $savingWajib->id,
                    'description' => $savingWajib->description,
                    'status' => 'posted',
                    'created_by' => $admin->id ?? 1,
                ]);

                JournalEntryLine::create(['journal_entry_id' => $journal->id, 'account_id' => $cashAccount->id, 'debit' => $savingWajib->amount, 'credit' => 0, 'description' => $savingWajib->description]);
                JournalEntryLine::create(['journal_entry_id' => $journal->id, 'account_id' => $savingWajibAccount->id, 'debit' => 0, 'credit' => $savingWajib->amount, 'description' => $savingWajib->description]);
            }

            // 2. Simpanan Sukarela (Secara Acak)
            if (rand(1, 10) <= 5) { // Peluang 50% untuk setor sukarela
                $refSukarela = 'SAV' . $date->format('ymd') . rand(1000, 9999);
                $amount = rand(5, 50) * 10000; // Rp 50.000 - Rp 500.000
                $savingSukarela = Saving::create([
                    'member_id' => $member->id,
                    'type' => 'sukarela',
                    'transaction_type' => 'deposit',
                    'amount' => $amount,
                    'transaction_date' => $date->copy()->addDays(rand(1, 3)),
                    'reference_number' => $refSukarela,
                    'description' => "Setoran Simpanan Sukarela",
                    'created_by' => $admin->id ?? 1,
                ]);

                if ($cashAccount && $savingSukarelaAccount) {
                    $journal = JournalEntry::create([
                        'date' => $savingSukarela->transaction_date,
                        'reference_number' => 'JRN-SAV-' . time() . '-' . rand(1000,9999),
                        'reference_type' => Saving::class,
                        'reference_id' => $savingSukarela->id,
                        'description' => $savingSukarela->description,
                        'status' => 'posted',
                        'created_by' => $admin->id ?? 1,
                    ]);

                    JournalEntryLine::create(['journal_entry_id' => $journal->id, 'account_id' => $cashAccount->id, 'debit' => $savingSukarela->amount, 'credit' => 0, 'description' => $savingSukarela->description]);
                    JournalEntryLine::create(['journal_entry_id' => $journal->id, 'account_id' => $savingSukarelaAccount->id, 'debit' => 0, 'credit' => $savingSukarela->amount, 'description' => $savingSukarela->description]);
                }
            }
        }
    }
    DB::commit();
    echo "\n\n>>> BERHASIL! DATA SIMPANAN TAHUN 2025 TELAH DITAMBAHKAN <<<\n\n";
} catch (\Exception $e) {
    DB::rollBack();
    echo "Error: " . $e->getMessage();
}
