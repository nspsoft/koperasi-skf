<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Member;
use App\Models\User;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\ConsignmentInbound;
use App\Models\ConsignmentInboundItem;
use App\Models\ConsignmentSettlement;
use App\Services\JournalService;
use App\Models\Saving;
use Illuminate\Support\Facades\DB;

class ConsignmentTrialSeeder extends Seeder
{
    public function run()
    {
        // Mock Auth for AuditLogs and Observers
        auth()->loginUsingId(1);

        DB::transaction(function () {
            $this->command->info('Starting Consignment Trial Data Generation...');

            // 1. Setup Users/Members
            $userAuto = User::firstOrCreate([
                'email' => 'budiauto@example.com'
            ], [
                'name' => 'Budi (Auto Trial)',
                'password' => bcrypt('password'),
                'role' => 'member',
                'phone' => '08123456789'
            ]);
            
            $memberAuto = Member::firstOrCreate([
                'user_id' => $userAuto->id
            ], [
                'member_id' => 'TRL-001',
                'status' => 'active',
                'join_date' => now(),
                'birth_date' => '1990-01-01',
            ]);

            $userManual = User::firstOrCreate([
                'email' => 'sitimanual@example.com'
            ], [
                'name' => 'Siti (Manual Trial)',
                'password' => bcrypt('password'),
                'role' => 'member',
                'phone' => '08987654321'
            ]);

            $memberManual = Member::firstOrCreate([
                'user_id' => $userManual->id
            ], [
                'member_id' => 'TRL-002',
                'status' => 'active',
                'join_date' => now(),
                'birth_date' => '1995-05-05',
            ]);

            // --- SCENARIO A: FULL AUTO (Completed Cycle) ---
            $this->command->info('Creating Scenario A: Full Automation...');
            
            // Product
            $prodAuto = Product::create([
                'code' => 'AUTO-001',
                'name' => 'Keripik Pisang (Auto)',
                'category_id' => 1, 
                'price' => 15000, 
                'stock' => 0,
                'is_consignment' => true,
                'consignor_type' => 'member',
                'consignor_id' => $memberAuto->id,
                'consignment_price' => 12000, 
                'consignment_profit_percent' => 20
            ]);

            // Inbound (Receive 10)
            $inbound = ConsignmentInbound::create([
                'transaction_number' => 'INB-AUTO-001',
                'consignor_type' => 'member',
                'consignor_id' => $memberAuto->id,
                'inbound_date' => now(),
                'status' => 'completed',
                'created_by' => 1 // ADDED
            ]);
            
            ConsignmentInboundItem::create([
                'consignment_inbound_id' => $inbound->id,
                'product_id' => $prodAuto->id,
                'quantity' => 10,
                'unit_cost' => 12000
            ]);
            $prodAuto->increment('stock', 10);

            // Sales (Sell 5)
            $trx = Transaction::create([
                'invoice_number' => 'INV-AUTO-001',
                'user_id' => 1, 
                'total_amount' => 5 * 15000,
                'payment_method' => 'cash',
                'status' => 'completed'
            ]);

            $trxItem = TransactionItem::create([
                'transaction_id' => $trx->id,
                'product_id' => $prodAuto->id,
                'quantity' => 5,
                'price' => 15000,
                'subtotal' => 75000
            ]);
            $prodAuto->decrement('stock', 5);

            // Settlement (Programmatic)
            $settlement = ConsignmentSettlement::create([
                'transaction_number' => 'STL-AUTO-001',
                'consignor_type' => 'member',
                'consignor_id' => $memberAuto->id,
                'period_start' => now()->startOfMonth(),
                'period_end' => now()->endOfMonth(),
                'total_sales_amount' => 75000,
                'total_payable_amount' => 5 * 12000, // 60,000
                'total_profit_amount' => 75000 - 60000, // 15,000
                'status' => 'paid',
                'paid_at' => now(),
                'paid_by' => 1,
                'notes' => 'Metode Pembayaran: Savings (Trial)'
            ]);

            // Link Item
            $trxItem->consignment_settlement_id = $settlement->id;
            $trxItem->save();

            // Auto-deposit Savings
            $saving = Saving::create([
                'member_id' => $memberAuto->id,
                'type' => 'sukarela',
                'transaction_type' => 'deposit',
                'amount' => 60000,
                'transaction_date' => now(),
                'reference_number' => 'SAV-AUTO-001',
                'description' => "Bagi Hasil Konsinyasi (Settlement #STL-AUTO-001)",
                'created_by' => 1,
            ]);

            // Journal
            JournalService::journalSavingDeposit($saving);
            JournalService::journalConsignmentSettlement($settlement, 'savings');


            // --- SCENARIO B: MANUAL TRIAL (Pending) ---
            $this->command->info('Creating Scenario B: Pending for User Trial...');

            // Product
            $prodManual = Product::create([
                'code' => 'MANUAL-001',
                'name' => 'Basreng Pedas (User Test)',
                'category_id' => 1,
                'price' => 20000,
                'stock' => 0,
                'is_consignment' => true,
                'consignor_type' => 'member',
                'consignor_id' => $memberManual->id,
                'consignment_price' => 16000, // HPP
                'consignment_profit_percent' => 20
            ]);

            // Inbound (Receive 20)
            $inbound2 = ConsignmentInbound::create([
                'transaction_number' => 'INB-MANUAL-001',
                'consignor_type' => 'member',
                'consignor_id' => $memberManual->id,
                'inbound_date' => now(),
                'status' => 'completed',
                'created_by' => 1 // ADDED
            ]);
            
            ConsignmentInboundItem::create([
                'consignment_inbound_id' => $inbound2->id,
                'product_id' => $prodManual->id,
                'quantity' => 20,
                'unit_cost' => 16000
            ]);
            $prodManual->increment('stock', 20);

            // Sales (Sell 8)
            $trx2 = Transaction::create([
                'invoice_number' => 'INV-MANUAL-001',
                'user_id' => 1,
                'total_amount' => 8 * 20000, // 160,000
                'payment_method' => 'cash',
                'status' => 'completed'
            ]);

            TransactionItem::create([
                'transaction_id' => $trx2->id,
                'product_id' => $prodManual->id,
                'quantity' => 8,
                'price' => 20000,
                'subtotal' => 160000
            ]);
            $prodManual->decrement('stock', 8);

            // No settlement created. User must do it via UI.
            
            $this->command->info("Trial Data Created Successfully!");
        });
    }
}
