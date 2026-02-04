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

class PosTransactionMockDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::where('is_active', true)->where('stock', '>', 0)->get();
        
        // Ensure we have an admin/cashier user
        $cashier = User::first(); 
        
        // Members or General Customers (User ID null for general)
        // Let's mix it up
        $users = User::take(10)->get();

        if ($products->isEmpty()) {
            $this->command->warn('No active products with stock found. Cannot seed sales.');
            return;
        }

        $totalSales = 0;

        // Loop through 2025
        DB::transaction(function () use ($products, $cashier, $users, &$totalSales) {
            
            for ($month = 1; $month <= 12; $month++) {
                // Sales volume varies per month (10-30 transactions)
                $salesCount = rand(10, 30);
                
                for ($i = 0; $i < $salesCount; $i++) {
                    $day = rand(1, 28);
                    $date = Carbon::create(2025, $month, $day, rand(8, 20), rand(0, 59));
                    
                    // Create Transaction Header
                    $transaction = Transaction::create([
                        'invoice_number' => 'INV-' . $date->format('Ymd') . '-' . rand(1000, 9999),
                        'user_id' => rand(0, 1) ? $users->random()->id : null, // 50% Member, 50% General
                        'cashier_id' => $cashier->id,
                        'type' => 'offline',
                        'status' => 'completed',
                        'payment_method' => ['cash', 'qris', 'transfer'][rand(0, 2)],
                        'total_amount' => 0, // Update later
                        'paid_amount' => 0,
                        'change_amount' => 0,
                        'created_at' => $date,
                        'updated_at' => $date,
                    ]);

                    $totalAmount = 0;
                    
                    // Add Random Items (1-5 items)
                    $itemsCount = rand(1, 5);
                    $selectedProducts = $products->random(min($itemsCount, $products->count()));
                    
                    foreach ($selectedProducts as $product) {
                        $qty = rand(1, 5);
                        
                        // Check stock (simplified for seeder, allow going negative if forced, but try not to)
                        // Repull product to get fresh stock
                        $product->refresh();
                        
                        // Price
                        $price = $product->price;
                        $subtotal = $qty * $price;
                        
                        TransactionItem::create([
                            'transaction_id' => $transaction->id,
                            'product_id' => $product->id,
                            'quantity' => $qty,
                            'price' => $price,
                            'subtotal' => $subtotal,
                            'created_at' => $date,
                            'updated_at' => $date,
                        ]);

                        // 1. UPDATE STOCK
                        $product->decrement('stock', $qty);
                        
                        $totalAmount += $subtotal;
                    }
                    
                    // Update Transaction Totals
                    $paidAmount = $totalAmount; // Exact payment simplification
                    if ($transaction->payment_method === 'cash') {
                        // Round up to nearest 5000 or 10000 for realistic cash payment
                        $paidAmount = ceil($totalAmount / 5000) * 5000;
                        if ($paidAmount < $totalAmount) $paidAmount = $totalAmount;
                    }
                    $change = $paidAmount - $totalAmount;

                    $transaction->update([
                        'total_amount' => $totalAmount,
                        'paid_amount' => $paidAmount,
                        'change_amount' => $change
                    ]);
                    
                    // 2. CREATE JOURNAL
                    // Need to manually set created_at on transaction for the journal date
                    $transaction->created_at = $date; 
                    JournalService::journalSale($transaction);
                    
                    // Update journal date to match transaction date (Service defaults to today)
                    if ($transaction->journalEntry) {
                        $transaction->journalEntry->update(['transaction_date' => $date]);
                    }

                    $totalSales++;
                }
            }
        });

        $this->command->info("Successfully created {$totalSales} POS transactions for 2025 (Stock & Finance Updated).");
    }
}
