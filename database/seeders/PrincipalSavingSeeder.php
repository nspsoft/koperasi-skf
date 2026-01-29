<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\Saving;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PrincipalSavingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Seeds Simpanan Pokok (Principal Savings) for all active members.
     */
    public function run(): void
    {
        $members = Member::where('status', 'active')->get();
        if ($members->isEmpty()) {
            $members = Member::all();
        }

        $adminUser = User::where('role', 'admin')->first();
        $adminId = $adminUser ? $adminUser->id : null;

        // Get the standard principal saving amount from settings
        $principalAmount = \App\Models\Setting::where('key', 'saving_principal')->value('value') ?? 100000;

        $count = 0;
        
        DB::beginTransaction();
        try {
            foreach ($members as $member) {
                Saving::create([
                    'member_id' => $member->id,
                    'type' => 'pokok',
                    'transaction_type' => 'deposit',
                    'amount' => $principalAmount,
                    'transaction_date' => '2025-12-30',
                    'description' => 'Simpanan Pokok',
                    'reference_number' => Saving::generateReferenceNumber(),
                    'created_by' => $adminId,
                ]);
                $count++;
            }
            DB::commit();
            $this->command->info("Successfully added {$count} Principal Saving (Simpanan Pokok) records dated 30/12/2025.");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("Error seeding principal savings: " . $e->getMessage());
            Log::error("PrincipalSavingSeeder failed: " . $e->getMessage());
        }
    }
}
