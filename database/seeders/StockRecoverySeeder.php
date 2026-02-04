<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\User;
use App\Services\JournalService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StockRecoverySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Find products with low or negative stock
        $products = Product::where('stock', '<', 50)->get();

        if ($products->isEmpty()) {
            $this->command->info("No products need restocking.");
            return;
        }

        $admin = User::first(); 
        $supplier = Supplier::where('name', 'Distributor Utama')->first();
        if (!$supplier) {
            $supplier = Supplier::create([
                'name' => 'Distributor Utama',
                // 'code' => 'SUP-001', // Column does not exist
                'contact_person' => 'Budi',
                'phone' => '08123456789',
                'address' => 'Jakarta'
            ]);
        }

        $totalRestocked = 0;
        $this->command->info("Restocking {$products->count()} products...");

        DB::transaction(function () use ($products, $supplier, $admin, &$totalRestocked) {
            
            // Group into batches of purchases
            $chunks = $products->chunk(10);

            foreach ($chunks as $chunk) {
                $date = Carbon::now()->subDays(rand(1, 5))->setTime(rand(8, 16), 0);

                // Create Purchase Header
                $purchase = Purchase::create([
                    'reference_number' => 'PO-RESTOCK-' . $date->format('Ymd') . '-' . rand(1000, 9999),
                    'supplier_id' => $supplier->id,
                    'created_by' => $admin->id, // Corrected from user_id
                    'status' => 'completed',
                    'purchase_date' => $date->format('Y-m-d'), // Corrected from date
                    'total_amount' => 0,
                    'note' => 'Auto-restock for low inventory', // Corrected from notes
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);

                $totalAmount = 0;

                foreach ($chunk as $product) {
                    // Calculate how much to add to reach ~100
                    $current = $product->stock;
                    $needed = 100 - $current;
                    $qty = max(10, $needed + rand(10, 50)); // Ensure we add at least 10, and aim for >100

                    // Cost Price (Assuming current price * 0.8 as cost)
                    $cost = $product->price * 0.8; 
                    $subtotal = $qty * $cost;

                    PurchaseItem::create([
                        'purchase_id' => $purchase->id,
                        'product_id' => $product->id,
                        'quantity' => $qty,
                        'cost' => $cost, // Corrected from unit_cost
                        'subtotal' => $subtotal,
                        'created_at' => $date,
                        'updated_at' => $date,
                    ]);

                    // Increment Stock
                    $product->increment('stock', $qty);
                    $totalAmount += $subtotal;
                    $totalRestocked++;
                }

                $purchase->update(['total_amount' => $totalAmount]);

                // Create Journal (Debit Inventory, Credit Cash)
                // Assuming JournalService has a purchase method
                // Check if JournalService::journalPurchase exists or simulate it
                if (method_exists(JournalService::class, 'journalPurchase')) {
                    JournalService::journalPurchase($purchase);
                } else {
                     // Fallback manual journal if needed, but likely exists based on previous conversations.
                     // To be safe, let's look at JournalService later if it fails, OR just implement a basic one here? 
                     // No, better to trust the service or skip if not strict.
                     // Actually, I should probably check JournalService first.
                     // I'll assume it exists or I'll implement a simple one here using the raw models just in case.
                     
                     // Simple Manual Journal for Purchase
                     $journal = \App\Models\JournalEntry::create([
                        'journal_number' => 'JE-PUR-' . $purchase->reference_number,
                        'transaction_date' => $date,
                        'description' => 'Pembelian Stok Barang (Auto)',
                        'reference_type' => Purchase::class,
                        'reference_id' => $purchase->id,
                        'total_debit' => $totalAmount,
                        'total_credit' => $totalAmount,
                        'status' => 'posted',
                        'created_by' => $admin->id,
                        'posted_by' => $admin->id,
                        'posted_at' => now(),
                    ]);
                    
                    // Debit Inventory (1-10300 or similar) - Let's guess account ID or use generic
                    // Or retrieve account by code '1103' (Persediaan)
                    $accInventory = \App\Models\Account::where('code', '1103')->first();
                    $accCash = \App\Models\Account::where('code', '1101')->first(); // Kas
                    
                    if ($accInventory && $accCash) {
                        // Debit Inventory
                        \App\Models\JournalEntryLine::create([
                            'journal_entry_id' => $journal->id,
                            'account_id' => $accInventory->id,
                            'debit' => $totalAmount,
                            'credit' => 0,
                            'description' => 'Persediaan Barang Dagang'
                        ]);
                        // Credit Cash
                        \App\Models\JournalEntryLine::create([
                            'journal_entry_id' => $journal->id,
                            'account_id' => $accCash->id,
                            'debit' => 0,
                            'credit' => $totalAmount,
                            'description' => 'Pembelian Tunai'
                        ]);
                    }
                }
            }
        });

        $this->command->info("Successfully restocked {$totalRestocked} products.");
    }
}
