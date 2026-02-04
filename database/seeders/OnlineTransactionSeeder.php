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

class OnlineTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::where('is_active', true)->where('stock', '>', 0)->get();
        // Get users who are likely members (assumes users with ID > 1 are members or can be used as such)
        // Adjust logic if you have specific member users
        $users = User::where('id', '>', 1)->take(20)->get(); 

        if ($products->isEmpty() || $users->isEmpty()) {
            $this->command->warn("No products or users found. Skipping Online Seed.");
            return;
        }

        $totalSales = 0;
        
        // 1. Historical Data (2025) - Occasional online orders
        // ~ 1-2 orders per week
        $startDate = Carbon::create(2025, 1, 1);
        $endDate = Carbon::create(2025, 12, 31);
        
        $dates = [];
        $curr = $startDate->copy();
        while ($curr->lte($endDate)) {
            if (rand(1, 100) <= 20) { // 20% chance per day
                $dates[] = $curr->copy();
            }
            $curr->addDay();
        }

        // 2. Recent Data (Last 14 days) - More active
        $recentStart = Carbon::now()->subDays(14);
        $recentEnd = Carbon::now();
        $curr = $recentStart->copy();
        while ($curr->lte($recentEnd)) {
             // 1-3 orders per day
            $dailyCount = rand(1, 3);
            for($i=0; $i<$dailyCount; $i++) {
                $dates[] = $curr->copy();
            }
            $curr->addDay();
        }

        $this->command->info("Generating " . count($dates) . " Online transactions...");

        DB::transaction(function () use ($products, $users, $dates, &$totalSales) {
            
            foreach ($dates as $date) {
                // Random time (Online = anytime, maybe evening)
                $transactionDate = $date->copy()->setTime(rand(9, 23), rand(0, 59));
                
                $user = $users->random();
                $isRecent = $transactionDate->year == 2026;
                
                // Determine Status
                if (!$isRecent) {
                    $status = 'completed'; // Old ones are done
                } else {
                    // Recent ones vary
                    // 40% Completed, 30% Ready, 20% Processing, 10% Pending
                    $rand = rand(1, 100);
                    if ($rand <= 40) $status = 'completed';
                    elseif ($rand <= 70) $status = 'ready';
                    elseif ($rand <= 90) $status = 'processing';
                    else $status = 'pending';
                }

                if ($status == 'completed') {
                    $payMethod = ['transfer', 'qris', 'saldo_sukarela'][rand(0, 2)];
                } else {
                    // Start of flow
                    $payMethod = ['transfer', 'qris', 'va', 'cash_pickup'][rand(0, 3)];
                }

                // Delivery Method
                $delMethod = rand(0, 1) ? 'pickup' : 'delivery';
                $notes = ($delMethod == 'delivery') ? "[ANTAR: Alamat Member]" : "[AMBIL SENDIRI]";
                if ($payMethod == 'cash_pickup') $notes .= " - Bayar ditempat";

                // Create Transaction Header
                $transaction = Transaction::create([
                    'invoice_number' => 'INV-OL-' . $transactionDate->format('Ymd') . '-' . rand(10000, 99999), 
                    'user_id' => $user->id,
                    'cashier_id' => null, // Online usually no cashier initially
                    'type' => 'online',
                    'status' => $status,
                    'payment_method' => $payMethod,
                    'total_amount' => 0, 
                    'paid_amount' => 0,
                    'change_amount' => 0,
                    'notes' => $notes,
                    'created_at' => $transactionDate,
                    'updated_at' => $transactionDate,
                ]);

                // Determine cashier if processed
                if ($status !== 'pending') {
                    $transaction->update(['cashier_id' => 1]); // Set admin as processor
                }

                $totalAmount = 0;
                
                // Add Items
                $itemsCount = rand(1, 3);
                $selectedProducts = $products->random(min($itemsCount, $products->count()));
                
                foreach ($selectedProducts as $product) {
                    $qty = rand(1, 2); // Online orders usually smaller?
                    $product->refresh();
                    
                    if ($product->stock < $qty) continue; // Skip if no stock

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

                    // UPDATE STOCK (Always decrement for online orders unless cancelled)
                    $product->decrement('stock', $qty);
                    $totalAmount += $subtotal;
                }
                
                if ($totalAmount == 0) {
                    $transaction->delete(); // Cleanup empty
                    continue; 
                }

                // Update Transaction Totals
                $paidAmount = 0;
                if ($status == 'completed' || $status == 'ready' || $status == 'processing') {
                    $paidAmount = $totalAmount; // Assume paid if processing
                }
                
                // If payment is pending/cash_pickup, paid_amount might be 0
                if ($status == 'pending' || $payMethod == 'cash_pickup') {
                    $paidAmount = 0;
                }

                $transaction->update([
                    'total_amount' => $totalAmount,
                    'paid_amount' => $paidAmount,
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

        $this->command->info("Successfully created {$totalSales} ONLINE transactions (2025 & Recent).");
    }
}
