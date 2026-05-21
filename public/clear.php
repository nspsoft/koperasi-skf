<?php
// Script untuk membersihkan cache jika route tidak bisa diakses
require __DIR__.'/../kopkarskf/vendor/autoload.php';
$app = require_once __DIR__.'/../kopkarskf/bootstrap/app.php';

use Illuminate\Support\Facades\Artisan;

// Jalankan command
Artisan::call('optimize:clear');

echo "<pre>" . Artisan::output() . "</pre>";
echo "Cache cleared successfully!";
