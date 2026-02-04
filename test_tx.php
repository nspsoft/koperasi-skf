<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Transaction;
use App\Models\Member;
use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Str;

try {
    $member = Member::first();
    $admin = User::where('role', 'admin')->first();
    $product = Product::first();
    
    $tx = Transaction::create([
        'invoice_number' => 'TEST/' . time(),
        'user_id' => $member->user_id,
        'cashier_id' => $admin->id,
        'type' => 'sale',
        'status' => 'completed',
        'payment_method' => 'cash',
        'total_amount' => 1234.56,
        'paid_amount' => 1234.56,
        'change_amount' => 0,
    ]);
    
    echo "Created TX " . $tx->id . " with amount " . $tx->total_amount . PHP_EOL;
    
    $tx->update(['total_amount' => 9999.9999]); // Should trigger truncation if strict
    echo "Updated TX" . PHP_EOL;
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . PHP_EOL;
    echo $e->getTraceAsString();
}
