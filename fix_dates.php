<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

DB::statement("UPDATE transactions SET created_at = STR_TO_DATE(SUBSTRING(invoice_number, 5, 8), '%Y%m%d'), updated_at = STR_TO_DATE(SUBSTRING(invoice_number, 5, 8), '%Y%m%d') WHERE invoice_number LIKE 'INV-2025%'");

echo "Done\n";
