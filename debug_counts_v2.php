<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Member;
use App\Models\User;

echo "Total Users: " . User::count() . "\n";
echo "Total Members (Active): " . Member::where('status', 'active')->count() . "\n";
echo "Total Members (Pending): " . Member::where('status', 'pending')->count() . "\n";
echo "Total Members (All): " . Member::count() . "\n";
echo "Users with Role 'admin': " . User::where('role', 'admin')->count() . "\n";
echo "Users with Role 'pengurus': " . User::where('role', 'pengurus')->count() . "\n";
