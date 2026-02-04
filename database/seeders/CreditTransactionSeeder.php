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

class CreditTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get active members who have a user account
        $members = Member::with('user')->where('status', 'active')->has('user')->get();
        $products = Product::where('is_active', true)->where('stock', '>', 5)->get();
        $cashier = User::where('role', 'admin')->first() ?? User::first(); 

        if ($members->isEmpty() || $products->isEmpty()) {
            $this->command->warn("No active members or products found. Skipping Credit Seed.");
            return;
        }

        $totalCredits = 0;
        // Generate around 30-50 credit transactions
        $targetCount = rand(30, 50);

        $this->command->info("Generating ~{$targetCount} credit transactions...");

        DB::transaction(function () use ($members, $products, $cashier, $targetCount, &$totalCredits) {
            
            for ($i = 0; $i < $targetCount; $i++) {
                $member = $members->random();
                
                // Date: Spread over last 3 months to simulate aging debt
                $date = Carbon::now()->subDays(rand(1, 90))->setTime(rand(8, 20), rand(0, 59));
                
                // Create Transaction Header for CREDIT
                $transaction = Transaction::create([
                    'invoice_number' => 'INV-CR-' . $date->format('Ymd') . '-' . rand(1000, 9999),
                    'user_id' => $member->user->id,
                    'cashier_id' => $cashier->id,
                    'type' => 'offline', // Typically POS credit
                    'status' => 'credit', // 'credit' = Unpaid
                    'payment_method' => 'kredit',
                    'total_amount' => 0, 
                    'paid_amount' => 0, // Unpaid
                    'change_amount' => 0,
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);

                $totalAmount = 0;
                
                // Add Items (1-3 items typically for daily needs)
                $itemsCount = rand(1, 3);
                $selectedProducts = $products->random(min($itemsCount, $products->count()));
                
                foreach ($selectedProducts as $product) {
                    $qty = rand(1, 4);
                    
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

                    // Decrement stock
                    $product->decrement('stock', $qty);
                    $totalAmount += $subtotal;
                }
                
                // Update Total Amount (Paid Amount stays 0)
                $transaction->update([
                    'total_amount' => $totalAmount,
                    'paid_amount' => 0,
                    'change_amount' => 0
                ]);
                
                // CREATE JOURNAL (Piutang Dagang / AR)
                $transaction->created_at = $date;
                JournalService::journalSale($transaction);
                
                if ($transaction->journalEntry) {
                    $transaction->journalEntry->update(['transaction_date' => $date]);
                }

                $totalCredits++;
            }
        });

        $this->command->info("Successfully created {$totalCredits} UNPAID credit transactions.");
    }
}
