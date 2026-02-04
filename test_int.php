<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Transaction;
use App\Models\Member;
use App\Models\User;

try {
    $member = Member::first();
    $admin = User::where('role', 'admin')->first();
    
    $tx = Transaction::create([
        'invoice_number' => 'INT/' . time(),
        'user_id' => $member->user_id,
        'cashier_id' => $admin->id,
        'type' => 'sale',
        'status' => 'completed',
        'payment_method' => 'cash',
        'total_amount' => 1000,
        'paid_amount' => 1000,
        'change_amount' => 0,
    ]);
    echo "Created INT TX " . $tx->id . PHP_EOL;
} catch (\Exception $e) {
    echo "INT ERROR: " . $e->getMessage() . PHP_EOL;
}
