<?php
// Fix Saving Transaction Dates: 2025 -> 2026

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== UPDATE TANGGAL TRANSAKSI SIMPANAN ===\n\n";

// Count before
$before = DB::table('savings')->whereYear('transaction_date', 2025)->count();
echo "Transaksi dengan tahun 2025: {$before}\n";

if ($before > 0) {
    // Update: add 1 year to all 2025 dates
    $updated = DB::table('savings')
        ->whereYear('transaction_date', 2025)
        ->update([
            'transaction_date' => DB::raw('DATE_ADD(transaction_date, INTERVAL 1 YEAR)')
        ]);
    
    echo "Berhasil update: {$updated} transaksi\n";
    
    // Verify
    $after = DB::table('savings')->whereYear('transaction_date', 2025)->count();
    echo "Transaksi dengan tahun 2025 setelah update: {$after}\n";
    
    $year2026 = DB::table('savings')->whereYear('transaction_date', 2026)->count();
    echo "Transaksi dengan tahun 2026: {$year2026}\n";
} else {
    echo "Tidak ada transaksi yang perlu di-update.\n";
}

echo "\n=== SELESAI ===\n";
