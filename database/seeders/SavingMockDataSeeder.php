<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\Saving;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SavingMockDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $members = Member::where('status', 'active')->get();
        if ($members->count() === 0) {
            $this->command->warn('No active members found. Skipping savings generation.');
            return;
        }

        $admin = User::first();
        $totalCreated = 0;

        DB::transaction(function () use ($members, $admin, &$totalCreated) {
            
            // 1. Simpanan Wajib (Monthly Compulsory Savings) for 2025
            // -------------------------------------------------------
            // Generate for Jan - Dec 2025 (or up to current month if in 2025)
            $startOfYear = Carbon::create(2025, 1, 1);
            $endOfYear = Carbon::create(2025, 12, 31);
            
            // Assuming current date is after 2025 or late 2025, we generate full year.
            // If strictly historical, we can cap at today.
            // But user asked "data simpanan untuk tahun 2025", implies full year context usually.
            
            foreach ($members as $member) {
                // Determine monthly amount based on member attributes if needed, default 100k
                $wajibAmount = 100000; 

                for ($month = 1; $month <= 12; $month++) {
                    $date = Carbon::create(2025, $month, 10); // Paid around 10th of each month
                    
                    // Add slight randomization to date (salary deduction usually fixed, but transfer varies)
                    $transactionDate = $date->copy()->addDays(rand(-2, 5));
                    
                    // Skip if existing to avoid dupes (simple check)
                    $exists = Saving::where('member_id', $member->id)
                        ->where('type', 'wajib')
                        ->whereMonth('transaction_date', $month)
                        ->whereYear('transaction_date', 2025)
                        ->exists();

                    if (!$exists) {
                        Saving::create([
                            'member_id' => $member->id,
                            'type' => 'wajib',
                            'transaction_type' => 'deposit',
                            'amount' => $wajibAmount,
                            'transaction_date' => $transactionDate,
                            'reference_number' => $this->generateReference($transactionDate),
                            'description' => 'Simpanan Wajib Bulan ' . $transactionDate->isoFormat('MMMM YYYY'),
                            'created_by' => $admin->id,
                        ]);
                        $totalCreated++;
                    }
                }
            }

            // 2. Simpanan Sukarela (Voluntary Savings) - Random
            // -------------------------------------------------
            foreach ($members->random(min(5, $members->count())) as $member) {
                // Random 3-8 deposits per year
                $numDeposits = rand(3, 8);
                
                for ($i = 0; $i < $numDeposits; $i++) {
                    $date = Carbon::create(2025, rand(1, 12), rand(1, 28));
                    $amount = rand(5, 50) * 10000; // 50k - 500k
                    
                    Saving::create([
                        'member_id' => $member->id,
                        'type' => 'sukarela',
                        'transaction_type' => 'deposit',
                        'amount' => $amount,
                        'transaction_date' => $date,
                        'reference_number' => $this->generateReference($date),
                        'description' => 'Setoran Simpanan Sukarela',
                        'created_by' => $admin->id,
                    ]);
                    $totalCreated++;
                }
            }

            // 3. Simpanan Hari Raya (THR) - Seasonal
            // --------------------------------------
            // specific deposit around Ramadhan (approx March/April 2025)
            foreach ($members->random(min(3, $members->count())) as $member) {
                 $date = Carbon::create(2025, 3, 20);
                 Saving::create([
                    'member_id' => $member->id,
                    'type' => 'sukarela', // Or specific type if exists
                    'transaction_type' => 'deposit',
                    'amount' => 1000000,
                    'transaction_date' => $date,
                    'reference_number' => $this->generateReference($date),
                    'description' => 'Tabungan THR',
                    'created_by' => $admin->id,
                ]);
                $totalCreated++;
            }
        });
        
        $this->command->info("Successfully generated {$totalCreated} savings transactions for 2025.");
    }

    private function generateReference(Carbon $date)
    {
        // Custom generator to respect the transaction date in the prefix
        static $counter = 1;
        $prefix = 'SMP' . $date->format('Ymd');
        return $prefix . str_pad($counter++, 4, '0', STR_PAD_LEFT) . rand(10,99);
    }
}
