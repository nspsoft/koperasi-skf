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

class RealDataConsignmentTrialSeeder extends Seeder
{
    public function run()
    {
        // Mock Admin for Created_By
        auth()->loginUsingId(1);

        DB::transaction(function () {
            $this->command->info('Setting up Trial for Real Members: Nata & Ngafif...');

            // FIND TARGET MEMBERS
            $userNgafif = User::where('name', 'Ngafif Usman')->firstOrFail();
            $userNata = User::where('name', 'like', 'Nata Surya%')->firstOrFail();

            $memberNgafif = $userNgafif->member;
            $memberNata = $userNata->member;

            if (!$memberNgafif || !$memberNata) {
                $this->command->error("One of the users is not registered as a Member!");
                return;
            }

            // --- SCENARIO 1: NGAFIF USMAN (Full Auto Settlement to Savings) ---
            $this->command->info(" Creating Scenario for {$userNgafif->name} (Auto-Settled)...");
            
            // 1. Product
            $prodNgafif = Product::create([
                'code' => 'CS-NGAFIF-001',
                'name' => 'Keripik Singkong (Trial Ngafif)',
                'category_id' => 1, 
                'price' => 10000, 
                'stock' => 0,
                'is_consignment' => true,
                'consignor_type' => 'member',
                'consignor_id' => $memberNgafif->id,
                'consignment_price' => 8000, // HPP
                'consignment_profit_percent' => 20
            ]);

            // 2. Inbound 20 pcs
            $inbound1 = ConsignmentInbound::create([
                'transaction_number' => 'INB-NGAFIF-001',
                'consignor_type' => 'member',
                'consignor_id' => $memberNgafif->id,
                'inbound_date' => now(),
                'status' => 'completed',
                'created_by' => 1
            ]);
            
            ConsignmentInboundItem::create([
                'consignment_inbound_id' => $inbound1->id,
                'product_id' => $prodNgafif->id,
                'quantity' => 20,
                'unit_cost' => 8000
            ]);
            $prodNgafif->increment('stock', 20);

            // 3. Sell 10 pcs
            $trx1 = Transaction::create([
                'invoice_number' => 'INV-NGAFIF-001',
                'user_id' => 1,
                'total_amount' => 10 * 10000,
                'payment_method' => 'cash',
                'status' => 'completed'
            ]);

            $trxItem1 = TransactionItem::create([
                'transaction_id' => $trx1->id,
                'product_id' => $prodNgafif->id,
                'quantity' => 10,
                'price' => 10000,
                'subtotal' => 100000
            ]);
            $prodNgafif->decrement('stock', 10);

            // 4. Settlement (Auto to Savings)
            $settlement1 = ConsignmentSettlement::create([
                'transaction_number' => 'STL-NGAFIF-001',
                'consignor_type' => 'member',
                'consignor_id' => $memberNgafif->id,
                'period_start' => now()->startOfMonth(),
                'period_end' => now()->endOfMonth(),
                'total_sales_amount' => 100000,
                'total_payable_amount' => 10 * 8000, // 80,000
                'total_profit_amount' => 100000 - 80000, // 20,000
                'status' => 'paid',
                'paid_at' => now(),
                'paid_by' => 1,
                'notes' => 'Settlement Konsinyasi (Trial)'
            ]);

            $trxItem1->consignment_settlement_id = $settlement1->id;
            $trxItem1->save();

            // 5. Deposit Savings (+ Journaling)
            $saving1 = Saving::create([
                'member_id' => $memberNgafif->id,
                'type' => 'sukarela',
                'transaction_type' => 'deposit',
                'amount' => 80000,
                'transaction_date' => now(),
                'reference_number' => 'SAV-NGAFIF-001',
                'description' => "Settlement Konsinyasi #STL-NGAFIF-001",
                'created_by' => 1,
            ]);

            JournalService::journalSavingDeposit($saving1);
            JournalService::journalConsignmentSettlement($settlement1, 'savings');


            // --- SCENARIO 2: NATA SURYA (Pending for Manual Test) ---
            $this->command->info(" Creating Scenario for {$userNata->name} (Pending Manual Test)...");

            // 1. Product
            $prodNata = Product::create([
                'code' => 'CS-NATA-001',
                'name' => 'Kopi Bubuk (Trial Nata)',
                'category_id' => 1,
                'price' => 25000,
                'stock' => 0,
                'is_consignment' => true,
                'consignor_type' => 'member',
                'consignor_id' => $memberNata->id,
                'consignment_price' => 20000,
                'consignment_profit_percent' => 20
            ]);

            // 2. Inbound 30 pcs
            $inbound2 = ConsignmentInbound::create([
                'transaction_number' => 'INB-NATA-001',
                'consignor_type' => 'member',
                'consignor_id' => $memberNata->id,
                'inbound_date' => now(),
                'status' => 'completed',
                'created_by' => 1
            ]);
            
            ConsignmentInboundItem::create([
                'consignment_inbound_id' => $inbound2->id,
                'product_id' => $prodNata->id,
                'quantity' => 30,
                'unit_cost' => 20000
            ]);
            $prodNata->increment('stock', 30);

            // 3. Sell 5 pcs
            $trx2 = Transaction::create([
                'invoice_number' => 'INV-NATA-001',
                'user_id' => 1,
                'total_amount' => 5 * 25000,
                'payment_method' => 'cash',
                'status' => 'completed'
            ]);

            TransactionItem::create([
                'transaction_id' => $trx2->id,
                'product_id' => $prodNata->id,
                'quantity' => 5,
                'price' => 25000,
                'subtotal' => 125000
            ]);
            $prodNata->decrement('stock', 5);

            // STOP. Left pending for User to settle via Database.
            
            $this->command->info("Real Data Trial Created Successfully!");
        });
    }
}
