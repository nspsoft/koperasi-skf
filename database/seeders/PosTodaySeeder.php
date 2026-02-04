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

class PosTodaySeeder extends Seeder
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
        
        // Strictly for TODAY
        $today = Carbon::create(2026, 2, 3); // Hardcoded to match user's current date perception if needed, or use now()
        // Using explicit date 2026-02-03 to avoid any server time ambiguity
        
        $salesCount = rand(15, 25); // Ensure a good number of transactions

        DB::transaction(function () use ($products, $cashier, $users, $today, $salesCount, &$totalSales) {
            
            for ($i = 0; $i < $salesCount; $i++) {
                // Random time today between 07:00 and 22:00
                // If it's technically "future" in real-time, that's fine for dummy data, 
                // but let's try to keep it somewhat realistic or spread out.
                $transactionDate = $today->copy()->setTime(rand(7, 21), rand(0, 59));
                
                // Create Transaction Header
                $transaction = Transaction::create([
                    'invoice_number' => 'INV-' . $transactionDate->format('Ymd') . '-' . rand(100000, 999999), 
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
                $itemsCount = rand(1, 4);
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
                
                if ($transaction->journalEntry) {
                    $transaction->journalEntry->update(['transaction_date' => $transactionDate]);
                }

                $totalSales++;
            }
        });

        $this->command->info("Successfully created {$totalSales} transactions specifically for TODAY (2026-02-03).");
    }
}
