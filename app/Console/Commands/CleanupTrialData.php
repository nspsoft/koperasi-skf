<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Member;
use App\Models\Product;
use App\Models\ConsignmentInbound;
use App\Models\ConsignmentSettlement;
use App\Models\Saving;
use App\Models\JournalEntry;
use Illuminate\Support\Facades\DB;

class CleanupTrialData extends Command
{
    protected $signature = 'cleanup:trial';
    protected $description = 'Remove Budi and Siti trial data';

    public function handle()
    {
        $emails = ['budiauto@example.com', 'sitimanual@example.com'];
        
        DB::transaction(function() use ($emails) {
            $users = User::whereIn('email', $emails)->get();
            
            foreach ($users as $user) {
                $this->info("Processing {$user->name}...");
                $member = $user->member;
                
                if ($member) {
                    // 1. Delete Savings & Related Journals
                    $savings = Saving::where('member_id', $member->id)->get();
                    foreach ($savings as $saving) {
                         // Delete Journal linked to Saving
                         JournalEntry::where('reference_type', Saving::class)
                                     ->where('reference_id', $saving->id)
                                     ->delete(); // Cascades lines usually
                         $saving->delete();
                    }

                    // 2. Delete Consignment Settlements & Related Journals
                    // Note: Settlements are polymorphic but trial used 'member'
                    $settlements = ConsignmentSettlement::where('consignor_type', 'member')
                        ->where('consignor_id', $member->id)
                        ->get();
                    
                    foreach ($settlements as $st) {
                        JournalEntry::where('reference_type', ConsignmentSettlement::class)
                             ->where('reference_id', $st->id)
                             ->delete();
                        $st->delete();
                    }

                    // 3. Delete Products & Trace
                    $products = Product::where('consignor_type', 'member')
                        ->where('consignor_id', $member->id)
                        ->get();

                    foreach ($products as $p) {
                        // Inbound Items
                        \App\Models\ConsignmentInboundItem::where('product_id', $p->id)->delete();
                        // Transaction Items
                        \App\Models\TransactionItem::where('product_id', $p->id)->delete();
                        $p->delete();
                    }

                    // 4. Inbound Headers
                    ConsignmentInbound::where('consignor_type', 'member')
                        ->where('consignor_id', $member->id)
                        ->delete();
                    
                    $member->delete();
                }
                
                $user->delete();
            }
        });

        $this->info('Cleanup completed successfully.');
    }
}
