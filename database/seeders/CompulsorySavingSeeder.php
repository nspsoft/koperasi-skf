<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\Saving;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CompulsorySavingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all members who are active (if there is a status) or just all members
        // Based on Member.php, there is a status field. Let's assume active members.
        $members = Member::where('status', 'active')->get();
        if ($members->isEmpty()) {
            $members = Member::all();
        }

        $adminUser = User::where('role', 'admin')->first();
        $adminId = $adminUser ? $adminUser->id : null;

        $count = 0;
        
        DB::beginTransaction();
        try {
            foreach ($members as $member) {
                Saving::create([
                    'member_id' => $member->id,
                    'type' => 'wajib',
                    'transaction_type' => 'deposit',
                    'amount' => 100000,
                    'transaction_date' => '2026-01-01',
                    'description' => 'Simpanan Wajib Januari 2026',
                    'reference_number' => Saving::generateReferenceNumber(),
                    'created_by' => $adminId,
                ]);
                $count++;
            }
            DB::commit();
            $this->command->info("Successfully added {$count} compulsory saving records for January 2026.");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("Error seeding compulsory savings: " . $e->getMessage());
            Log::error("CompulsorySavingSeeder failed: " . $e->getMessage());
        }
    }
}
