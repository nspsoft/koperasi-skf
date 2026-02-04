<?php

namespace Database\Seeders;

use App\Models\Loan;
use App\Models\LoanPayment;
use App\Models\Member;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LoanMockDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some members to attach loans to
        $members = Member::where('status', 'active')->take(20)->get();
        if ($members->count() < 5) {
            $this->command->warn('Not enough active members found. Please run MemberSeeder first.');
            return;
        }

        $admin = User::first(); // Assuming first user is admin/approver

        DB::transaction(function () use ($members, $admin) {
            
            // 1. Pending Loans (Menunggu Persetujuan)
            // ---------------------------------------
            foreach ($members->random(3) as $member) {
                $amount = rand(1, 10) * 1000000; // 1-10 Juta
                $duration = [6, 12, 24][rand(0, 2)];
                $rate = 1.5; // Monthly interest
                
                $loan = new Loan();
                $loan->member_id = $member->id;
                $loan->loan_number = Loan::generateLoanNumber();
                $loan->loan_type = ['regular', 'emergency', 'education'][rand(0, 2)];
                $loan->amount = $amount;
                $loan->interest_rate = $rate;
                $loan->duration_months = $duration;
                $loan->calculateLoanDetails(); // Calculate monthly, total, etc.
                
                $loan->status = 'pending';
                $loan->application_date = Carbon::now()->subDays(rand(1, 14));
                $loan->purpose = 'Keperluan mendesak / renovasi rumah / pendidikan anak';
                $loan->created_by = $admin->id;
                $loan->save();
            }

            // 2. Approved Loans (Disetujui, Menunggu TTD/Cair)
            // ------------------------------------------------
            foreach ($members->random(2) as $member) {
                $amount = rand(5, 20) * 1000000;
                $duration = 12;
                $rate = 1.2;

                $loan = new Loan();
                $loan->member_id = $member->id;
                $loan->loan_number = Loan::generateLoanNumber();
                $loan->loan_type = 'regular';
                $loan->amount = $amount;
                $loan->interest_rate = $rate;
                $loan->duration_months = $duration;
                $loan->calculateLoanDetails();

                $loan->status = 'approved';
                $loan->application_date = Carbon::now()->subDays(5);
                $loan->approval_date = Carbon::now()->subDays(1);
                $loan->approved_by = $admin->id;
                $loan->purpose = 'Modal usaha kecil';
                $loan->created_by = $admin->id;
                $loan->save();
            }

            // 3. Rejected Loans (Ditolak)
            // ---------------------------
            foreach ($members->random(2) as $member) {
                $amount = 50000000; // Large amount
                $loan = new Loan();
                $loan->member_id = $member->id;
                $loan->loan_number = Loan::generateLoanNumber();
                $loan->loan_type = 'special';
                $loan->amount = $amount;
                $loan->interest_rate = 1.5;
                $loan->duration_months = 24;
                $loan->calculateLoanDetails();

                $loan->status = 'rejected';
                $loan->application_date = Carbon::now()->subMonth();
                $loan->purpose = 'Beli mobil baru';
                $loan->notes = 'Plafon pinjaman melebihi batas maksimal gaji.';
                $loan->created_by = $admin->id;
                $loan->save();
            }

            // 4. Active Loans (Sedang Berjalan - Lancar)
            // ------------------------------------------
            foreach ($members->random(5) as $member) {
                $amount = 12000000;
                $duration = 12;
                $rate = 1.0;
                
                // Started 4 months ago
                $startDate = Carbon::now()->subMonths(4);
                
                $loan = new Loan();
                $loan->member_id = $member->id;
                $loan->loan_number = Loan::generateLoanNumber();
                $loan->loan_type = 'regular';
                $loan->amount = $amount;
                $loan->interest_rate = $rate;
                $loan->duration_months = $duration;
                $loan->calculateLoanDetails();

                $loan->status = 'active';
                $loan->application_date = $startDate->copy()->subDays(7);
                $loan->approval_date = $startDate->copy()->subDays(2);
                $loan->disbursement_date = $startDate;
                $loan->due_date = $startDate->copy()->addMonth(); // First due date
                $loan->approved_by = $admin->id;
                $loan->purpose = 'Renovasi rumah';
                $loan->created_by = $admin->id;
                $loan->signature = 'signed.png';
                $loan->signed_at = $startDate;
                $loan->save();

                // Create Installments
                $this->createInstallments($loan, 4); // Pay first 4 months
            }

            // 5. Active Loans (Sedang Berjalan - Macet/Telat)
            // -----------------------------------------------
            foreach ($members->random(2) as $member) {
                $amount = 5000000;
                $duration = 6;
                $rate = 2.0;
                
                // Started 3 months ago
                $startDate = Carbon::now()->subMonths(3);

                $loan = new Loan();
                $loan->member_id = $member->id;
                $loan->loan_number = Loan::generateLoanNumber();
                $loan->loan_type = 'emergency';
                $loan->amount = $amount;
                $loan->interest_rate = $rate;
                $loan->duration_months = $duration;
                $loan->calculateLoanDetails();

                $loan->status = 'active'; // Still active in system but creates overdue payments
                $loan->application_date = $startDate->copy()->subDays(3);
                $loan->disbursement_date = $startDate;
                $loan->approved_by = $admin->id;
                $loan->purpose = 'Biaya rumah sakit';
                $loan->created_by = $admin->id;
                $loan->save();

                // Create Installments (Only paid 1, skipped 2)
                $this->createInstallments($loan, 1); 
            }

            // 6. Completed Loans (Lunas)
            // --------------------------
            foreach ($members->random(3) as $member) {
                $amount = 3000000;
                $duration = 3; 
                
                // Started 5 months ago, finished 2 months ago
                $startDate = Carbon::now()->subMonths(5);

                $loan = new Loan();
                $loan->member_id = $member->id;
                $loan->loan_number = Loan::generateLoanNumber();
                $loan->loan_type = 'emergency';
                $loan->amount = $amount;
                $loan->interest_rate = 1.5;
                $loan->duration_months = $duration;
                $loan->calculateLoanDetails();

                $loan->status = 'completed';
                $loan->application_date = $startDate->copy()->subDays(2);
                $loan->disbursement_date = $startDate;
                $loan->approved_by = $admin->id;
                $loan->remaining_amount = 0;
                $loan->purpose = 'Service motor';
                $loan->created_by = $admin->id;
                $loan->save();

                // Create ALL Installments as PAID
                $this->createInstallments($loan, 3);
            }
        });
    }

    /**
     * Helper to create payment schedule and mark some as paid
     */
    private function createInstallments(Loan $loan, int $paidMonths)
    {
        $monthlyPrincipal = $loan->amount / $loan->duration_months;
        $monthlyInterest = $loan->amount * ($loan->interest_rate / 100);
        $startDate = Carbon::parse($loan->disbursement_date);

        for ($i = 1; $i <= $loan->duration_months; $i++) {
            $dueDate = $startDate->copy()->addMonths($i);
            
            $status = 'pending';
            $paymentDate = null;
            $notes = null;

            if ($i <= $paidMonths) {
                $status = 'paid';
                $paymentDate = $dueDate->copy()->subDays(rand(0, 5)); // Paid on time or early
                $loan->remaining_amount -= $loan->monthly_installment;
            } elseif ($dueDate < Carbon::now()) {
                $status = 'overdue'; // Past due date and not paid
            }

            LoanPayment::create([
                'loan_id' => $loan->id,
                'payment_number' => LoanPayment::generatePaymentNumber(),
                'installment_number' => $i,
                'amount' => $loan->monthly_installment,
                'principal_amount' => $monthlyPrincipal,
                'interest_amount' => $monthlyInterest,
                'due_date' => $dueDate,
                'payment_date' => $paymentDate,
                'status' => $status,
                'payment_method' => $status === 'paid' ? 'salary_deduction' : null,
                'received_by' => $status === 'paid' ? $loan->approved_by : null,
            ]);
        }
        
        $loan->save();
    }
}
