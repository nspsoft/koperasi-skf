<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

$cashAccount = Account::where('code', '1101')->first();
$salesRevenueAccount = Account::where('code', '4102')->first();
$interestRevenueAccount = Account::where('code', '4101')->first();

if (!$cashAccount || !$salesRevenueAccount || !$interestRevenueAccount) {
    echo "Akun Kas atau Pendapatan tidak ditemukan.\n";
    exit;
}

DB::beginTransaction();
try {
    $date = Carbon::create(2025, 12, 31); // Akhir tahun 2025
    
    // 1. Pendapatan Penjualan Koperasi
    $salesAmount = 150000000; // Rp 150.000.000
    $journal1 = JournalEntry::create([
        'transaction_date' => $date,
        'journal_number' => 'REV-' . $date->format('Ymd') . '-001',
        'reference_type' => 'App\Models\Transaction',
        'reference_id' => 999,
        'description' => "Akumulasi Pendapatan Penjualan Toko 2025",
        'total_debit' => $salesAmount,
        'total_credit' => $salesAmount,
        'status' => 'posted',
        'created_by' => 1,
    ]);

    JournalEntryLine::create(['journal_entry_id' => $journal1->id, 'account_id' => $cashAccount->id, 'debit' => $salesAmount, 'credit' => 0, 'description' => $journal1->description]);
    JournalEntryLine::create(['journal_entry_id' => $journal1->id, 'account_id' => $salesRevenueAccount->id, 'debit' => 0, 'credit' => $salesAmount, 'description' => $journal1->description]);

    // 2. Pendapatan Bunga Pinjaman
    $interestAmount = 75000000; // Rp 75.000.000
    $journal2 = JournalEntry::create([
        'transaction_date' => $date,
        'journal_number' => 'REV-' . $date->format('Ymd') . '-002',
        'reference_type' => 'App\Models\LoanPayment',
        'reference_id' => 999,
        'description' => "Akumulasi Pendapatan Bunga Pinjaman 2025",
        'total_debit' => $interestAmount,
        'total_credit' => $interestAmount,
        'status' => 'posted',
        'created_by' => 1,
    ]);

    JournalEntryLine::create(['journal_entry_id' => $journal2->id, 'account_id' => $cashAccount->id, 'debit' => $interestAmount, 'credit' => 0, 'description' => $journal2->description]);
    JournalEntryLine::create(['journal_entry_id' => $journal2->id, 'account_id' => $interestRevenueAccount->id, 'debit' => 0, 'credit' => $interestAmount, 'description' => $journal2->description]);

    DB::commit();
    echo "\n\n>>> BERHASIL! DATA PENDAPATAN 2025 TELAH DITAMBAHKAN <<<\n\n";
} catch (\Exception $e) {
    DB::rollBack();
    echo "Error: " . $e->getMessage();
}
