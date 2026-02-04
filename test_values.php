<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$rows = \DB::table('transactions')->take(5)->get();
foreach($rows as $row) {
    echo $row->invoice_number . ": " . $row->total_amount . PHP_EOL;
}
