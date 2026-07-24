<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = \App\Models\User::whereNotNull('phone')->where('phone', '!=', '')->count();
$members = \App\Models\Member::whereNotNull('phone')->where('phone', '!=', '')->count();

echo "Users with phone: $users\n";
echo "Members with phone: $members\n";
