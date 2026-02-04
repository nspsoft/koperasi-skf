<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PurchaseMockDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = Supplier::all();
        $products = Product::all();
        $admin = User::first(); 

        if ($suppliers->isEmpty() || $products->isEmpty()) {
            $this->command->warn('Suppliers or Products data is empty. Cannot seed purchases.');
            return;
        }

        $totalPurchases = 0;

        DB::transaction(function () use ($suppliers, $products, $admin, &$totalPurchases) {
            
            // Loop through each month of 2025
            for ($month = 1; $month <= 12; $month++) {
                
                // Random number of purchases per month (e.g., 2-5 purchases)
                $purchasesPerMonth = rand(2, 5);
                
                for ($i = 0; $i < $purchasesPerMonth; $i++) {
                    $day = rand(1, 28);
                    $date = Carbon::create(2025, $month, $day);
                    
                    $supplier = $suppliers->random();
                    
                    // Create Purchase Header
                    $purchase = Purchase::create([
                        'supplier_id' => $supplier->id,
                        'reference_number' => 'PO-2025' . str_pad($month, 2, '0', STR_PAD_LEFT) . str_pad($day, 2, '0', STR_PAD_LEFT) . rand(100, 999),
                        'purchase_date' => $date,
                        'total_amount' => 0, // Will update after items
                        'status' => 'completed', // Assume 2025 data is mostly completed history
                        'note' => 'Restock bulanan',
                        'created_by' => $admin->id,
                        'completed_at' => $date->copy()->addDays(rand(1, 3)),
                    ]);

                    $totalAmount = 0;
                    
                    // Add Items (3-10 varied products)
                    $selectedProducts = $products->random(min(rand(3, 10), $products->count()));
                    
                    foreach ($selectedProducts as $product) {
                        $qty = rand(10, 100);
                        // Use product cost or randomize slightly
                        $cost = $product->cost > 0 ? $product->cost : rand(1000, 50000); 
                        $subtotal = $qty * $cost;
                        
                        PurchaseItem::create([
                            'purchase_id' => $purchase->id,
                            'product_id' => $product->id,
                            'quantity' => $qty,
                            'cost' => $cost,
                            'subtotal' => $subtotal,
                        ]);
                        
                        $totalAmount += $subtotal;
                    }
                    
                    // Update header total
                    $purchase->update(['total_amount' => $totalAmount]);
                    $totalPurchases++;
                }
            }
        });

        $this->command->info("Successfully created {$totalPurchases} dummy purchases for 2025.");
    }
}
