<?php
// Load Laravel
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Check recent transactions
$recent = \App\Models\Transaction::where('created_at', '>=', '2025-01-01')
    ->where('invoice_number', 'like', 'INV-2025%')
    ->latest()
    ->take(5)
    ->with(['cashier', 'items'])
    ->get();

echo "Checking Recent Seeder Transactions:\n";
foreach ($recent as $trx) {
    echo "Invoice: " . $trx->invoice_number . "\n";
    echo "Cashier: " . ($trx->cashier ? $trx->cashier->name : 'NULL') . "\n";
    echo "Items: " . $trx->items->count() . "\n";
    echo "------------------\n";
}

// Check the 'bad' ones
echo "\nChecking Reported 'Bad' Transactions:\n";
$bad = \App\Models\Transaction::whereIn('invoice_number', ['INV-NGAFIF-001', 'INV-MANUAL-001', 'INV-AUTO-001'])
    ->with(['cashier', 'items'])
    ->get();

foreach ($bad as $trx) {
    echo "Invoice: " . $trx->invoice_number . "\n";
    echo "Cashier ID: " . ($trx->cashier_id ?? 'NULL') . "\n";
    echo "Items: " . $trx->items->count() . "\n";
    echo "------------------\n";
}
