<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use App\Services\JournalService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PosRecentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::where('is_active', true)->where('stock', '>', 0)->get();
        $cashier = User::first(); 
        $users = User::take(10)->get();

        if ($products->isEmpty()) {
            return;
        }

        $totalSales = 0;
        
        // Date Range: Last 7 days to Today
        $endDate = Carbon::now();
        $startDate = Carbon::now()->subDays(7);

        DB::transaction(function () use ($products, $cashier, $users, $startDate, $endDate, &$totalSales) {
            
            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                
                // 5-15 transactions per day for the last week
                $salesCount = rand(5, 15);
                
                for ($i = 0; $i < $salesCount; $i++) {
                    // Random time within working hours (08:00 - 21:00)
                    $transactionDate = $date->copy()->setTime(rand(8, 20), rand(0, 59));
                    
                    // Create Transaction Header
                    $transaction = Transaction::create([
                        'invoice_number' => 'INV-' . $transactionDate->format('Ymd') . '-' . rand(10000, 99999),
                        'user_id' => rand(0, 1) ? $users->random()->id : null,
                        'cashier_id' => $cashier->id,
                        'type' => 'offline',
                        'status' => 'completed',
                        'payment_method' => ['cash', 'qris', 'transfer'][rand(0, 2)],
                        'total_amount' => 0, 
                        'paid_amount' => 0,
                        'change_amount' => 0,
                        'created_at' => $transactionDate,
                        'updated_at' => $transactionDate,
                    ]);

                    $totalAmount = 0;
                    
                    // Add Items
                    $itemsCount = rand(1, 5);
                    $selectedProducts = $products->random(min($itemsCount, $products->count()));
                    
                    foreach ($selectedProducts as $product) {
                        $qty = rand(1, 3);
                        $product->refresh();
                        
                        $price = $product->price;
                        $subtotal = $qty * $price;
                        
                        TransactionItem::create([
                            'transaction_id' => $transaction->id,
                            'product_id' => $product->id,
                            'quantity' => $qty,
                            'price' => $price,
                            'subtotal' => $subtotal,
                            'created_at' => $transactionDate,
                            'updated_at' => $transactionDate,
                        ]);

                        // UPDATE STOCK
                        $product->decrement('stock', $qty);
                        $totalAmount += $subtotal;
                    }
                    
                    // Update Transaction Totals
                    $paidAmount = $totalAmount;
                    if ($transaction->payment_method === 'cash') {
                        $paidAmount = ceil($totalAmount / 5000) * 5000;
                        if ($paidAmount < $totalAmount) $paidAmount = $totalAmount;
                    }
                    $change = $paidAmount - $totalAmount;

                    $transaction->update([
                        'total_amount' => $totalAmount,
                        'paid_amount' => $paidAmount,
                        'change_amount' => $change
                    ]);
                    
                    // CREATE JOURNAL
                    $transaction->created_at = $transactionDate; 
                    JournalService::journalSale($transaction);
                    
                    // Fix Journal Date
                    if ($transaction->journalEntry) {
                        $transaction->journalEntry->update(['transaction_date' => $transactionDate]);
                    }

                    $totalSales++;
                }
            }
        });

        $this->command->info("Successfully created {$totalSales} recent POS transactions.");
    }
}
