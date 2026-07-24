<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Member;
use App\Models\MemberAspiration;

// Clear old aspirations
MemberAspiration::truncate();

$members = Member::inRandomOrder()->limit(15)->get();

if($members->isEmpty()) {
    echo "Tidak ada member, buat member dummy dulu.";
    exit;
}

// 1. Tambah Data Request Barang (item_request)
$items = [
    ['category' => 'Elektronik', 'item_name' => 'Kipas Angin Dinding', 'frequency' => 'Harian', 'qty' => 1, 'estimated_price' => 250000],
    ['category' => 'Konsumsi', 'item_name' => 'Kopi Kemasan Renceng', 'frequency' => 'Harian', 'qty' => 10, 'estimated_price' => 15000],
    ['category' => 'Sembako', 'item_name' => 'Beras 5kg', 'frequency' => 'Mingguan', 'qty' => 2, 'estimated_price' => 75000],
    ['category' => 'Elektronik', 'item_name' => 'Kipas Angin Dinding', 'frequency' => 'Harian', 'qty' => 1, 'estimated_price' => 250000],
    ['category' => 'Alat Tulis', 'item_name' => 'Buku Catatan Kecil', 'frequency' => 'Bulanan', 'qty' => 3, 'estimated_price' => 5000],
    ['category' => 'Konsumsi', 'item_name' => 'Mie Instan (Karton)', 'frequency' => 'Mingguan', 'qty' => 1, 'estimated_price' => 110000],
    ['category' => 'Sembako', 'item_name' => 'Minyak Goreng 2L', 'frequency' => 'Mingguan', 'qty' => 2, 'estimated_price' => 35000],
    ['category' => 'Konsumsi', 'item_name' => 'Kopi Kemasan Renceng', 'frequency' => 'Harian', 'qty' => 5, 'estimated_price' => 15000],
    ['category' => 'Alat Mandi', 'item_name' => 'Sabun Cair Botol', 'frequency' => 'Bulanan', 'qty' => 2, 'estimated_price' => 25000],
];

foreach ($items as $idx => $item) {
    if (isset($members[$idx])) {
        MemberAspiration::create([
            'member_id' => $members[$idx]->id,
            'type' => 'item_request',
            'data' => $item
        ]);
    }
}

// 2. Tambah Data Evaluasi Sistem (system_eval)
$evals = [
    ['system_choice' => 'digital', 'payment_choice' => 'digital', 'reason' => 'Lebih cepat pakai aplikasi dan bayar pakai saldo/qris.'],
    ['system_choice' => 'digital', 'payment_choice' => 'digital', 'reason' => 'Sangat praktis tidak perlu bawa uang tunai.'],
    ['system_choice' => 'digital', 'payment_choice' => 'cash', 'reason' => 'Pesannya enak lewat HP, tapi saya lebih suka bayar tunai pas ambil barang.'],
    ['system_choice' => 'manual', 'payment_choice' => 'cash', 'reason' => 'Lebih mantap pilih barang langsung dan bayar tunai.'],
    ['system_choice' => 'digital', 'payment_choice' => 'digital', 'reason' => 'Transparan, saya bisa pantau saldo simpanan langsung dari HP.'],
    ['system_choice' => 'digital', 'payment_choice' => 'digital', 'reason' => 'Gampang lihat riwayat belanja koperasi.']
];

foreach ($evals as $idx => $eval) {
    // Offset member index so it assigns to different members if possible
    $mIdx = ($idx + count($items)) % $members->count();
    MemberAspiration::create([
        'member_id' => $members[$mIdx]->id,
        'type' => 'system_eval',
        'data' => $eval
    ]);
}

echo "\n\n>>> BERHASIL! DATA DUMMY ASPIRASI ANGGOTA SUDAH DITAMBAHKAN! <<<\n\n";
