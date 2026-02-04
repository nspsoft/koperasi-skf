<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$col = \DB::select('SHOW COLUMNS FROM journal_entries LIKE "total_debit"');
print_r($col);
