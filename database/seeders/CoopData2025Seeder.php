<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Member;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use App\Services\JournalService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CoopData2025Seeder extends Seeder
{
    public function run(): void
    {
        $targetMinSpending = 10500000; // Aim a bit higher than 10M
        $targetMaxSpending = 11500000;
        $year = 2025;
        
        $admin = User::where('role', 'admin')->first() ?? User::first();
        $products = Product::where('is_active', true)->get();
        $members = Member::has('user')->with('user')->get();
        
        if ($members->isEmpty() || $products->isEmpty()) {
            $this->command->error('Missing members or products.');
            return;
        }

        $this->command->info("Generating Member Sales for 2025 (Target ~11M per member)...");
        
        foreach ($members as $index => $member) {
            $currentSpending = (float) Transaction::where('user_id', $member->user_id)
                ->whereYear('created_at', $year)
                ->sum('total_amount');
            
            $remainingTarget = (float) (rand($targetMinSpending, $targetMaxSpending)) - $currentSpending;
            
            if ($remainingTarget <= 0) {
                //$this->command->line("Member #{$member->member_id} target reached.");
                continue;
            }

            $this->command->line("Member #{$member->member_id} (" . ($index+1) . "/" . count($members) . ") - Needed: " . number_format($remainingTarget));
            
            // Generate about 15-30 transactions per member to reach the target
            while ($remainingTarget > 0) {
                try {
                    $txDate = Carbon::create($year, rand(1, 12), rand(1, 28), rand(8, 20), rand(0, 59));
                    
                    DB::transaction(function() use ($member, $admin, $products, $txDate, &$remainingTarget) {
                        $transaction = new Transaction();
                        $transaction->invoice_number = 'INV/' . $txDate->format('ymd') . '/' . strtoupper(Str::random(6));
                        $transaction->user_id = $member->user_id;
                        $transaction->cashier_id = $admin->id;
                        $transaction->type = 'offline';
                        $transaction->status = 'paid';
                        $transaction->payment_method = collect(['cash', 'transfer', 'qris', 'saldo_sukarela'])->random();
                        $transaction->total_amount = 0;
                        $transaction->paid_amount = 0;
                        $transaction->change_amount = 0;
                        $transaction->notes = 'Generated historical 2025';
                        $transaction->created_at = $txDate;
                        $transaction->updated_at = $txDate;
                        $transaction->save();

                        // Buy 5-12 items
                        $itemsToBuy = $products->random(rand(5, 12));
                        $total = 0;

                        foreach ($itemsToBuy as $product) {
                            $qty = rand(1, 4);
                            $price = round((float) $product->price, 2);
                            $subtotal = round($qty * $price, 2);

                            TransactionItem::create([
                                'transaction_id' => $transaction->id,
                                'product_id' => $product->id,
                                'quantity' => $qty,
                                'price' => $price,
                                'subtotal' => $subtotal,
                            ]);

                            $total += $subtotal;
                        }

                        $total = round($total, 2);
                        $transaction->total_amount = $total;
                        $transaction->paid_amount = $total;
                        $transaction->save();

                        // Clear cached items and load with products for journal
                        $transaction->unsetRelation('items');
                        $transaction->load('items.product');

                        JournalService::journalSale($transaction);
                        
                        $remainingTarget -= $total;
                    });
                } catch (\Exception $e) {
                    $this->command->error("TX Error for member {$member->member_id}: " . $e->getMessage());
                    break; // Move to next member on error
                }
            }
        }
        
        $this->command->info("✅ Seeding completed!");
    }
}
