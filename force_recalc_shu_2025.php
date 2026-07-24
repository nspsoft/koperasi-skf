<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$year = 2025;
$setting = \App\Models\ShuSetting::where('period_year', $year)->first();

if (!$setting) {
    echo "Konfigurasi SHU 2025 belum ada. Silakan buat di halaman Kalkulator SHU.\n";
    exit;
}

// 1. Hapus hasil perhitungan yang lama
\App\Models\ShuDistribution::where('period_year', $year)->delete();

// 2. Ambil semua anggota aktif
$members = \App\Models\Member::where('status', 'active')->get();

$grandTotalSavings = 0;
$grandTotalTransactions = 0;
$memberData = [];

// 3. Kalkulasi ulang berdasarkan transaksi 2025 terbaru
foreach ($members as $member) {
    // Simpanan
    $memberSavings = \App\Models\Saving::where('member_id', $member->id)
        ->whereYear('transaction_date', '<=', $year)
        ->selectRaw("SUM(CASE WHEN transaction_type = 'deposit' THEN amount ELSE -amount END) as balance")
        ->value('balance') ?? 0;

    // Transaksi (termasuk yang kita seed)
    $memberTransactions = \App\Models\Transaction::whereHas('user', function($q) use ($member) {
            $q->where('id', $member->user_id);
        })
        ->where('status', 'completed')
        ->whereYear('created_at', $year)
        ->sum('total_amount');

    $memberData[$member->id] = [
        'savings' => max(0, $memberSavings),
        'transactions' => $memberTransactions,
    ];

    $grandTotalSavings += max(0, $memberSavings);
    $grandTotalTransactions += $memberTransactions;
}

$poolJasaModal = $setting->pool_jasa_modal;
$poolJasaUsaha = $setting->pool_jasa_usaha;

// 4. Buat ulang data distribusi baru
foreach ($members as $member) {
    $data = $memberData[$member->id];

    $shuJasaModal = $grandTotalSavings > 0 
        ? ($data['savings'] / $grandTotalSavings) * $poolJasaModal 
        : 0;

    $shuJasaUsaha = $grandTotalTransactions > 0 
        ? ($data['transactions'] / $grandTotalTransactions) * $poolJasaUsaha 
        : 0;

    \App\Models\ShuDistribution::create([
        'period_year' => $year,
        'member_id' => $member->id,
        'total_savings' => $data['savings'],
        'total_transactions' => $data['transactions'],
        'shu_savings' => $shuJasaModal,
        'shu_transactions' => $shuJasaUsaha,
        'total_shu' => $shuJasaModal + $shuJasaUsaha,
    ]);
}

echo ">>> BERHASIL! DISTRIBUSI SHU 2025 TELAH DIHITUNG ULANG <<<\n";
