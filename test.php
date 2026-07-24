<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$m = \App\Models\Member::where('member_id', 'MBR0002')->first();

if($m) {
    echo "Hendra user_id: " . $m->user_id . "\n";
    echo "Transactions found in 2025: " . \App\Models\Transaction::where('user_id', $m->user_id)->whereYear('created_at', 2025)->count() . "\n";
    echo "Transactions found in 2025 (completed): " . \App\Models\Transaction::where('user_id', $m->user_id)->where('status', 'completed')->whereYear('created_at', 2025)->count() . "\n";
    echo "Total amount: " . \App\Models\Transaction::where('user_id', $m->user_id)->where('status', 'completed')->whereYear('created_at', 2025)->sum('total_amount') . "\n";
} else {
    echo "MBR0002 not found\n";
}
