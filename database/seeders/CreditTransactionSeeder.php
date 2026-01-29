<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;

class CreditTransactionSeeder extends Seeder
{
    /**
     * Generate credit transactions for simulation
     */
    public function run()
    {
        $this->command->info('🛒 Generating Credit Transactions...');

        // Get members (users with member profile)
        $members = User::whereHas('member')->with('member')->limit(20)->get();

        if ($members->isEmpty()) {
            $this->command->warn('No members found! Please run MembersSeeder first.');
            return;
        }

        // Get products
        $products = Product::where('is_active', true)->where('stock', '>', 5)->limit(30)->get();

        if ($products->isEmpty()) {
            $this->command->warn('No products found! Please add products first.');
            return;
        }

        $generated = 0;

        // Generate POS Credit Transactions (Koperasi Mart)
        foreach ($members as $member) {
            // 50% chance each member has a credit transaction
            if (rand(1, 100) <= 50) {
                $transaction = $this->createCreditTransaction($member, $products, 'offline');
                if ($transaction) {
                    $generated++;
                    $this->command->line("  ✓ POS Credit: {$transaction->invoice_number} - {$member->name} - Rp " . number_format($transaction->total_amount, 0, ',', '.'));
                }
            }
        }

        // Generate Online Shop Credit Transactions
        foreach ($members->take(10) as $member) {
            // 30% chance for online credit
            if (rand(1, 100) <= 30) {
                $transaction = $this->createCreditTransaction($member, $products, 'online');
                if ($transaction) {
                    $generated++;
                    $this->command->line("  ✓ Online Credit: {$transaction->invoice_number} - {$member->name} - Rp " . number_format($transaction->total_amount, 0, ',', '.'));
                }
            }
        }

        $this->command->info("✅ Generated {$generated} credit transactions!");
        
        // Summary
        $totalCredit = Transaction::where('status', 'credit')->sum('total_amount');
        $this->command->info("📊 Total outstanding credit: Rp " . number_format($totalCredit, 0, ',', '.'));
    }

    /**
     * Create a single credit transaction
     */
    private function createCreditTransaction($member, $products, $type = 'pos')
    {
        // Random date in last 30 days
        $transactionDate = Carbon::now()->subDays(rand(1, 30));

        // Generate invoice number
        $prefix = $type === 'offline' ? 'POS' : 'ONL';
        $invoiceNumber = $prefix . $transactionDate->format('Ymd') . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);

        // Check if invoice already exists
        if (Transaction::where('invoice_number', $invoiceNumber)->exists()) {
            $invoiceNumber .= rand(10, 99);
        }

        // Create transaction
        $transaction = Transaction::create([
            'invoice_number' => $invoiceNumber,
            'user_id' => $member->id,
            'cashier_id' => 1, // Admin
            'type' => $type, // 'offline' or 'online'
            'status' => 'credit',
            'payment_method' => 'kredit',
            'total_amount' => 0,
            'paid_amount' => 0,
            'change_amount' => 0,
            'notes' => $type === 'offline' ? 'Belanja kredit di Koperasi Mart' : 'Belanja online - kredit',
            'created_at' => $transactionDate,
            'updated_at' => $transactionDate,
        ]);

        // Add random items (1-5 products)
        $itemCount = rand(1, 5);
        $selectedProducts = $products->random(min($itemCount, $products->count()));
        $totalAmount = 0;

        foreach ($selectedProducts as $product) {
            $qty = rand(1, 3);
            $subtotal = $product->price * $qty;
            $totalAmount += $subtotal;

            TransactionItem::create([
                'transaction_id' => $transaction->id,
                'product_id' => $product->id,
                'quantity' => $qty,
                'price' => $product->price,
                'subtotal' => $subtotal,
            ]);
        }

        // Update total amount
        $transaction->update(['total_amount' => $totalAmount]);

        return $transaction;
    }
}
