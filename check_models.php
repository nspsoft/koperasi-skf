<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $purchase = \App\Models\Purchase::first();
    print_r($purchase->getAttributes());
    
    $transaction = \App\Models\Transaction::first();
    print_r($transaction->getAttributes());
} catch (\Exception $e) {
    echo $e->getMessage();
}
