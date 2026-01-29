<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Models\Member;
use App\Models\User;

echo "Total Users: " . User::count() . "\n";
echo "Total Members (Active): " . Member::where('status', 'active')->count() . "\n";
echo "Total Members (All Status): " . Member::count() . "\n";
echo "Members with Pending Status: " . Member::where('status', 'pending')->count() . "\n";
echo "Users with Role 'member': " . User::where('role', 'member')->count() . "\n";
echo "Users with Role 'admin': " . User::where('role', 'admin')->count() . "\n";
echo "Users with Role 'pengurus': " . User::where('role', 'pengurus')->count() . "\n";
echo "Users WITHOUT Member Profile: " . User::doesntHave('member')->count() . "\n";

$usersWithoutMember = User::doesntHave('member')->get(['id', 'name', 'role']);
if ($usersWithoutMember->count() > 0) {
    echo "\nUsers without Member Profile:\n";
    foreach ($usersWithoutMember as $u) {
        echo "- [{$u->role}] {$u->name}\n";
    }
}
