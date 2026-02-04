<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\User;
use App\Services\JournalService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Expense2025Seeder extends Seeder
{
    public function run(): void
    {
        $year = 2025;
        $admin = User::where('role', 'admin')->first() ?? User::first();
        
        $categories = ExpenseCategory::all();
        if ($categories->isEmpty()) {
            $this->command->error('Expense categories not found.');
            return;
        }

        $this->command->info("Generating Calibrated Operational Expenses for 2025 (Budget: 5-6M/month)...");

        for ($month = 1; $month <= 12; $month++) {
            $date = Carbon::create($year, $month, 25);
            
            // Monthly Budget allocation: Total 5-6M
            // 1. Gaji & Tunjangan (Monthly)
            $this->createExpense(
                2, 
                $date->copy()->setDay(28),
                rand(3500000, 4000000), // Reduced salary budget
                "Gaji staff operasional bulan " . $date->format('F Y'),
                $admin->id
            );

            // 2. Listrik & Air (Monthly)
            $this->createExpense(
                3, 
                $date->copy()->setDay(15),
                rand(400000, 600000), 
                "Tagihan Listrik & Air " . $date->format('F Y'),
                $admin->id
            );

            // 3. Telepon & Internet (Monthly)
            $this->createExpense(
                4, 
                $date->copy()->setDay(15),
                rand(200000, 300000),
                "Tagihan Internet " . $date->format('F Y'),
                $admin->id
            );

            // 4. Misc / ATK / Rapat (Occasional)
            $miscAmount = rand(500000, 1000000); // Remaining budget to hit 5-6M total
            $this->createExpense(
                1, // Operasional Kantor
                $date->copy()->setDay(rand(5, 20)),
                $miscAmount,
                "Biaya rutin kantor & ATK " . $date->format('F Y'),
                $admin->id
            );
        }

        $this->command->info("✅ Expense recalibration for 2025 completed!");
    }

    private function createExpense($categoryId, $date, $amount, $description, $userId)
    {
        DB::transaction(function() use ($categoryId, $date, $amount, $description, $userId) {
            $expense = Expense::create([
                'expense_category_id' => $categoryId,
                'expense_date' => $date,
                'amount' => $amount,
                'description' => $description,
                'created_by' => $userId,
            ]);

            JournalService::journalExpense($expense);
        });
    }
}
