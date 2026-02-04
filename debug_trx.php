<?php
$trxs = \App\Models\Transaction::whereIn('invoice_number', ['INV-NGAFIF-001', 'INV-MANUAL-001', 'INV-AUTO-001'])
    ->with(['cashier', 'items'])
    ->get();

foreach ($trxs as $trx) {
    echo "Invoice: " . $trx->invoice_number . "\n";
    echo "Cashier ID: " . ($trx->cashier_id ?? 'NULL') . "\n";
    echo "Items Count: " . $trx->items->count() . "\n";
    echo "------------------\n";
}
