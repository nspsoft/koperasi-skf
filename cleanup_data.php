<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Transaction;
use App\Models\User;

echo "Cleaning up data...\n";

// 1. Delete transactions with 0 items
$deleted = Transaction::doesntHave('items')->delete();
echo "Deleted $deleted transactions with 0 items.\n";

// 2. Fix missing cashier
$admin = User::first();
if ($admin) {
    $updated = Transaction::whereNull('cashier_id')->update(['cashier_id' => $admin->id]);
    echo "Updated $updated transactions with missing cashier (Assigned to {$admin->name}).\n";
}

echo "Done.";
