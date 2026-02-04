<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ShuSetting;
use App\Models\JournalEntryLine;
use App\Models\Account;

// 1. Calculate Revenue (Class 4)
$revenue = JournalEntryLine::whereHas('account', function($q) {
    $q->where('code', 'like', '4%');
})->whereHas('journalEntry', function($q) {
    $q->whereYear('transaction_date', 2025);
})->sum('credit') - JournalEntryLine::whereHas('account', function($q) {
    $q->where('code', 'like', '4%');
})->whereHas('journalEntry', function($q) {
    $q->whereYear('transaction_date', 2025);
})->sum('debit');

// 2. Calculate Expenses (Class 5)
$expenses = JournalEntryLine::whereHas('account', function($q) {
    $q->where('code', 'like', '5%');
})->whereHas('journalEntry', function($q) {
    $q->whereYear('transaction_date', 2025);
})->sum('debit') - JournalEntryLine::whereHas('account', function($q) {
    $q->where('code', 'like', '5%');
})->whereHas('journalEntry', function($q) {
    $q->whereYear('transaction_date', 2025);
})->sum('credit');

$netProfit = $revenue - $expenses;

echo "Revenue: " . number_format($revenue) . "\n";
echo "Expenses: " . number_format($expenses) . "\n";
echo "Net Profit: " . number_format($netProfit) . "\n";

// 3. Update SHU Setting
$setting = ShuSetting::updateOrCreate(
    ['period_year' => 2025],
    ['total_shu_pool' => $netProfit]
);

// Trigger recalculation
// We can instantiate the controller or just manually trigger the pool calculation method if it exists on the model
if (method_exists($setting, 'calculatePools')) {
    $setting->calculatePools();
    $setting->save();
}

echo "SHU Setting updated to: " . number_format($setting->total_shu_pool) . "\n";
