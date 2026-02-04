<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    \DB::table('transactions')->insert([
        'invoice_number' => 'RAW/' . time(),
        'user_id' => 1,
        'cashier_id' => 1,
        'type' => 'sale',
        'status' => 'completed',
        'payment_method' => 'cash',
        'total_amount' => 1234.56,
        'paid_amount' => 1234.56,
        'change_amount' => 0,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    echo "Raw Inserted" . PHP_EOL;
} catch (\Exception $e) {
    echo "RAW ERROR: " . $e->getMessage() . PHP_EOL;
}
