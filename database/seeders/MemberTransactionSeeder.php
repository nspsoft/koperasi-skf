<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use App\Services\JournalService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MemberTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $members = Member::with('user')->where('status', 'active')->get();
        $products = Product::where('is_active', true)->where('stock', '>', 10)->get();
        $cashier = User::where('role', 'admin')->first() ?? User::first(); 

        if ($members->isEmpty() || $products->isEmpty()) {
            $this->command->warn("No active members or products found. Skipping Member Transaction Seed.");
            return;
        }

        $totalSales = 0;
        $this->command->info("Generating transactions for {$members->count()} members...");

        DB::transaction(function () use ($members, $products, $cashier, &$totalSales) {
            
            foreach ($members as $member) {
                if (!$member->user) continue;

                // 1. Transactions for 2025 (SHU Period)
                // 3 to 8 transactions per member
                $count2025 = rand(3, 8);
                
                for ($i = 0; $i < $count2025; $i++) {
                    $date = Carbon::create(2025, rand(1, 12), rand(1, 28), rand(8, 20), rand(0, 59));
                    $this->createTransaction($member->user, $cashier, $products, $date, $totalSales);
                }

                // 2. Transactions for 2026 (Current Year)
                // 1 to 3 transactions per member
                $count2026 = rand(1, 3);
                
                for ($i = 0; $i < $count2026; $i++) {
                    $date = Carbon::create(2026, rand(1, 2), rand(1, 3), rand(8, 20), rand(0, 59)); // Jan-Feb 2026
                    
                    // Don't create future dates beyond today
                    if ($date->gt(Carbon::now())) {
                        $date = Carbon::now()->subHours(rand(1, 24));
                    }
                    
                    $this->createTransaction($member->user, $cashier, $products, $date, $totalSales);
                }
            }
        });

        $this->command->info("Successfully created {$totalSales} member transactions.");
    }

    private function createTransaction($user, $cashier, $products, $date, &$counter)
    {
        // Create Transaction Header
        $transaction = Transaction::create([
            'invoice_number' => 'INV-MEM-' . $date->format('Ymd') . '-' . rand(1000, 9999) . '-' . $user->id,
            'user_id' => $user->id,
            'cashier_id' => $cashier->id,
            'type' => 'offline',
            'status' => 'completed',
            'payment_method' => ['cash', 'transfer', 'saldo'][rand(0, 2)],
            'total_amount' => 0, 
            'paid_amount' => 0,
            'change_amount' => 0,
            'created_at' => $date,
            'updated_at' => $date,
        ]);

        $totalAmount = 0;
        
        // Add Items
        $itemsCount = rand(1, 5);
        $selectedProducts = $products->random(min($itemsCount, $products->count()));
        
        foreach ($selectedProducts as $product) {
            $qty = rand(1, 3);
            
            // Allow stock to go negative for dummy data if needed, or check
            // For now, let's just decrement, assuming we have enough or don't strictly care about exact inventory accuracy for this bulk seed
            
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

            $product->decrement('stock', $qty);
            $totalAmount += $subtotal;
        }
        
        // Update Transaction Totals
        $paidAmount = $totalAmount;
        $transaction->update([
            'total_amount' => $totalAmount,
            'paid_amount' => $paidAmount,
            'change_amount' => 0
        ]);
        
        // CREATE JOURNAL
        // Fake the request context logic if needed, but JournalService usually just needs the model
        $transaction->created_at = $date;
        JournalService::journalSale($transaction);
        
        if ($transaction->journalEntry) {
            $transaction->journalEntry->update(['transaction_date' => $date]);
        }

        $counter++;
    }
}
