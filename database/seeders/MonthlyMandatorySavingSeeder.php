<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\Saving;
use App\Models\User;
use App\Services\JournalService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MonthlyMandatorySavingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $targetDate = '2026-02-01';
        $amount = 100000; // Standard amount based on CompulsorySavingSeeder

        $members = Member::where('status', 'active')->get();
        $admin = User::first(); 

        if ($members->isEmpty()) {
            $this->command->warn("No active members found.");
            return;
        }

        $count = 0;
        $this->command->info("Generating Mandatory Savings for " . $members->count() . " members on {$targetDate}...");

        DB::transaction(function () use ($members, $admin, $targetDate, $amount, &$count) {
            foreach ($members as $member) {
                // Check if already exists to prevent duplicates
                $exists = Saving::where('member_id', $member->id)
                    ->where('type', 'wajib')
                    ->where('transaction_date', $targetDate)
                    ->exists();

                if (!$exists) {
                    $saving = Saving::create([
                        'member_id' => $member->id,
                        'type' => 'wajib',
                        'transaction_type' => 'deposit',
                        'amount' => $amount,
                        'transaction_date' => $targetDate,
                        'description' => 'Simpanan Wajib Februari 2026',
                        'reference_number' => 'SW-FEB26-' . $member->member_id,
                        'created_by' => $admin->id,
                        // 'payment_method' => 'cash', // Column does not exist
                        // 'status' => 'approved' // Column does not exist
                    ]);

                    // Update Member Totals (Handled by accessors, no manual update needed)
                    // $member->increment('total_simpanan_wajib', $amount);
                    // $member->increment('total_simpanan', $amount);

                    // Create Journal Entry
                    // Method Might be different depending on Service, but usually:
                    // Debit Cash, Credit Simpanan Wajib Member
                    if (method_exists(JournalService::class, 'journalSavingDeposit')) {
                        JournalService::journalSavingDeposit($saving);
                    } else {
                         // Manual Journal Fallback
                        $journal = \App\Models\JournalEntry::create([
                            'journal_number' => 'JE-SW-' . $saving->reference_number,
                            'transaction_date' => $targetDate,
                            'description' => 'Setoran Simpanan Wajib ' . $member->name,
                            'reference_type' => Saving::class,
                            'reference_id' => $saving->id,
                            'total_debit' => $amount,
                            'total_credit' => $amount,
                            'status' => 'posted',
                            'created_by' => $admin->id,
                            'posted_by' => $admin->id,
                            'posted_at' => now(),
                        ]);

                        $accCash = \App\Models\Account::where('code', '1101')->first(); // Kas
                         // Simpanan Wajib Account (Liability) - Usually 2-xxxx
                        $accSimpananWajib = \App\Models\Account::where('name', 'like', '%Simpanan Wajib%')->first();

                        if ($accCash && $accSimpananWajib) {
                             // Debit Cash
                            \App\Models\JournalEntryLine::create([
                                'journal_entry_id' => $journal->id,
                                'account_id' => $accCash->id,
                                'debit' => $amount,
                                'credit' => 0,
                                'description' => 'Kas Masuk'
                            ]);
                            // Credit Simpanan Wajib
                            \App\Models\JournalEntryLine::create([
                                'journal_entry_id' => $journal->id,
                                'account_id' => $accSimpananWajib->id,
                                'debit' => 0,
                                'credit' => $amount,
                                'description' => 'Simpanan Wajib Anggota'
                            ]);
                        }
                    }

                    $count++;
                }
            }
        });

        $this->command->info("Successfully created {$count} Mandatory Saving records.");
    }
}
