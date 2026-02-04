<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Transaction;
use App\Models\Purchase;
use App\Models\Member;

$totalSales = Transaction::whereYear('created_at', 2025)->sum('total_amount');
$totalPurchases = Purchase::whereYear('purchase_date', 2025)->sum('total_amount');
$memberCount = Member::count();
$avgSales = $memberCount > 0 ? $totalSales / $memberCount : 0;

echo "--- 2025 DATA SUMMARY ---" . PHP_EOL;
echo "Total Members: " . $memberCount . PHP_EOL;
echo "Total Sales: Rp " . number_format($totalSales, 0, ',', '.') . PHP_EOL;
echo "Total Purchases: Rp " . number_format($totalPurchases, 0, ',', '.') . PHP_EOL;
echo "Average Sales per Member: Rp " . number_format($avgSales, 0, ',', '.') . PHP_EOL;
echo "-------------------------" . PHP_EOL;
