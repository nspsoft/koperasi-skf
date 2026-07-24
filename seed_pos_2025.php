<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Member;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

$members = Member::with('user')->get();

if ($members->isEmpty()) {
    echo "Tidak ada data anggota.\n";
    exit;
}

DB::beginTransaction();
try {
    foreach ($members as $member) {
        if (!$member->user) continue;

        // Bikin 5 - 15 transaksi per anggota selama 2025
        $trxCount = rand(5, 15);
        for ($i = 0; $i < $trxCount; $i++) {
            $date = Carbon::create(2025, rand(1, 12), rand(1, 28));
            $amount = rand(50, 500) * 10000; // Rp 500.000 - Rp 5.000.000 per transaksi

            Transaction::create([
                'invoice_number' => 'INV-' . $date->format('Ymd') . '-' . rand(1000, 9999),
                'user_id' => $member->user->id,
                'cashier_id' => 1,
                'type' => 'offline',
                'status' => 'completed',
                'payment_method' => 'cash',
                'total_amount' => $amount,
                'paid_amount' => $amount,
                'change_amount' => 0,
                'created_at' => $date,
                'updated_at' => $date,
            ]);
        }
    }
    
    DB::commit();
    echo "\n\n>>> BERHASIL! TRANSAKSI POS 2025 DITAMBAHKAN <<<\n\n";
} catch (\Exception $e) {
    DB::rollBack();
    echo "Error: " . $e->getMessage();
}
