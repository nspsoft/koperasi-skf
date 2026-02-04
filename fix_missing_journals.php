<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Transaction;
use App\Models\JournalEntry;
use App\Services\JournalService;

echo "Checking for missing journals...\n";

$existingIds = JournalEntry::where('reference_type', Transaction::class)->pluck('reference_id');
$missing = Transaction::whereNotIn('id', $existingIds)->get();

if ($missing->isEmpty()) {
    echo "All transactions have journals.\n";
    exit;
}

echo "Found " . $missing->count() . " transactions without journals:\n";

foreach ($missing as $trx) {
    echo "- " . $trx->invoice_number . " (" . $trx->created_at . ")\n";
    
    // Fix it
    echo "  Generating journal... ";
    JournalService::journalSale($trx);
    
    // Fix date
    if ($trx->journalEntry) {
        $trx->journalEntry->update(['transaction_date' => $trx->created_at]);
        echo "Done.\n";
    } else {
        echo "Failed.\n";
    }
}

echo "All fixed.\n";
