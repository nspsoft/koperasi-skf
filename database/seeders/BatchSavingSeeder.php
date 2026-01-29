<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\Saving;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BatchSavingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all members who are NOT admins
        $members = Member::whereHas('user', function ($query) {
            $query->where('role', '!=', 'admin');
        })->get();

        $adminUser = User::where('role', 'admin')->first();
        $adminId = $adminUser ? $adminUser->id : null;

        $count = 0;
        
        DB::beginTransaction();
        try {
            foreach ($members as $member) {
                Saving::create([
                    'member_id' => $member->id,
                    'type' => 'pokok',
                    'transaction_type' => 'deposit',
                    'amount' => 150000,
                    'transaction_date' => '2026-01-02',
                    'description' => 'Simpanan bulan Januari 2026',
                    'reference_number' => Saving::generateReferenceNumber(),
                    'created_by' => $adminId,
                ]);
                $count++;
            }
            DB::commit();
            $this->command->info("Successfully added {$count} simpanan records.");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("Error seeding savings: " . $e->getMessage());
            Log::error("BatchSavingSeeder failed: " . $e->getMessage());
        }
    }
}
