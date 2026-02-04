<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$setting = \App\Models\ShuSetting::where('period_year', 2025)->first();
if ($setting) {
    print_r($setting->toArray());
} else {
    echo "No setting for 2025";
}
