<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Member;
use App\Models\Saving;
use App\Models\Loan;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class QuickDummySeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌱 Generating quick dummy data...');

        // Create 10 members quickly
        $members = [];
        $names = ['Budi Santoso', 'Siti Rahayu', 'Ahmad Wijaya', 'Dewi Lestari', 'Joko Susanto', 
                  'Ratna Sari', 'Agus Pratama', 'Maya Kusuma', 'Hendra Putra', 'Laila Fitri'];
        
        $depts = ['Production', 'QC', 'Warehouse', 'Finance', 'IT'];

        foreach ($names as $i => $name) {
            $num = $i + 100; // Start from 100 to avoid conflict
            
            $user = User::create([
                'name' => $name,
                'email' => "demo{$num}@koperasi.test",
                'password' => Hash::make('password'),
                'role' => 'member',
            ]);

            $member = Member::create([
                'user_id' => $user->id,
                'member_id' => "MBR{$num}",
                'employee_id' => "EMP{$num}",
                'department' => $depts[array_rand($depts)],
                'position' => 'Staff',
                'gender' => $i % 2 == 0 ? 'male' : 'female',
                'join_date' => Carbon::now()->subMonths(rand(1, 12)),
                'birth_date' => Carbon::now()->subYears(rand(25, 45)),
                'status' => 'active',
            ]);

            $members[] = $member;

            // Add some savings for each member
            for ($s = 0; $s < rand(3, 8); $s++) {
                Saving::create([
                    'member_id' => $member->id,
                    'type' => ['pokok', 'wajib', 'sukarela'][array_rand(['pokok', 'wajib', 'sukarela'])],
                    'transaction_type' => $s % 5 == 0 ? 'withdrawal' : 'deposit',
                    'amount' => rand(50, 500) * 1000,
                    'transaction_date' => Carbon::now()->subDays(rand(1, 90)),
                    'reference_number' => 'SAV' . time() . rand(100, 999),
                    'description' => 'Transaksi simpanan',
                ]);
            }

            // Add 1-2 loans for some members
            if ($i % 2 == 0) {
                $amount = rand(5, 30) * 1000000;
                $duration = rand(12, 24);
                
                Loan::create([
                    'member_id' => $member->id,
                    'loan_number' => 'LN2024' . str_pad($i, 3, '0', STR_PAD_LEFT),
                    'loan_type' => ['regular', 'emergency', 'education'][array_rand(['regular', 'emergency', 'education'])],
                    'amount' => $amount,
                    'interest_rate' => 1.5,
                    'duration_months' => $duration,
                    'monthly_installment' => ($amount * 1.15) / $duration,
                    'status' => ['approved', 'active'][array_rand(['approved', 'active'])],
                    'purpose' => 'Keperluan pribadi',
                    'application_date' => Carbon::now()->subDays(rand(10, 60)),
                    'approved_at' => Carbon::now()->subDays(rand(5, 50)),
                    'approved_by' => 1,
                ]);
            }
        }

        $this->command->info('✅ Created 10 members with transactions');
        $this->command->newLine();
        $this->command->info('🎉 Quick dummy data created!');
        $this->command->info('📊 Summary: 10 members, ~50 savings, ~5 loans');
        $this->command->newLine();
        $this->command->info('🔐 Login: demo100@koperasi.test s/d demo109@koperasi.test');
        $this->command->info('   Password: password');
    }
}
