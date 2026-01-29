<?php

namespace Database\Seeders;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Loan;
use App\Models\LoanPayment;
use App\Models\Member;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Department;
use App\Models\Position;
use App\Models\Saving;
use App\Models\Supplier;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use App\Models\Voucher;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // $this->command->warn('⚠️  This will delete all existing Members, Savings, and Loans data!');
        // $this->command->warn('    Admin and Pengurus accounts will NOT be deleted.');

        // Confirmation skipped for automation
        // if (!$this->command->confirm('Do you want to continue?', true)) {
        //     $this->command->info('Seeding cancelled.');
        //     return;
        // }

        // Disable Foreign Key Checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Delete existing dummy data (keep admin/pengurus)
        DB::table('journal_entry_lines')->truncate();
        DB::table('journal_entries')->truncate();
        DB::table('loan_payments')->truncate();
        DB::table('loans')->truncate();
        DB::table('savings')->truncate();
        DB::table('transaction_items')->truncate();
        DB::table('transactions')->truncate();
        DB::table('expenses')->truncate();
        DB::table('purchase_items')->truncate();
        DB::table('purchases')->truncate();
        DB::table('vouchers')->truncate();

        // Delete member users only
        $memberUserIds = DB::table('users')->where('role', 'member')->pluck('id');
        DB::table('members')->whereIn('user_id', $memberUserIds)->delete();
        DB::table('users')->where('role', 'member')->delete();

        // Enable Foreign Key Checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Create Departments if they don't exist
        $deptNames = ['Produksi', 'Quality Control', 'Warehouse', 'Maintenance', 'HR & GA', 'Finance', 'IT', 'Purchasing'];
        foreach ($deptNames as $name) {
            Department::firstOrCreate(['name' => $name], [
                'code' => strtoupper(substr($name, 0, 3)) . rand(10, 99),
                'is_active' => true,
            ]);
        }

        // Create Positions if they don't exist
        $posNames = ['Manager', 'Asisten Manager', 'Supervisor', 'Staff', 'Operator', 'Admin', 'Driver', 'Security'];
        foreach ($posNames as $name) {
            Position::firstOrCreate(['name' => $name], [
                'code' => strtoupper(substr($name, 0, 3)) . rand(10, 99),
                'is_active' => true,
            ]);
        }

        // Create Suppliers if they don't exist
        $supplierNames = ['PT. Indofood Sukses Makmur', 'PT. Mayora Indah', 'PT. Wings Surya', 'PT. Unilever Indonesia', 'Agen Sembako Makmur'];
        foreach ($supplierNames as $name) {
            Supplier::firstOrCreate(['name' => $name], [
                'contact_person' => 'Bpk. '.Str::random(5),
                'phone' => '08'.rand(11111111, 99999999),
                'address' => 'Kawasan Industri, Karawang',
            ]);
        }

        $this->command->info('🌱 Generating dummy data...');

        // Create 20 Members
        $members = [];
        for ($i = 1; $i <= 20; $i++) {
            $user = User::create([
                'name' => $this->generateName(),
                'email' => 'member'.$i.'@koperasi.test',
                'password' => Hash::make('password'),
                'role' => 'member',
            ]);

            $member = Member::create([
                'user_id' => $user->id,
                'member_id' => 'MBR'.str_pad($i, 4, '0', STR_PAD_LEFT),
                'employee_id' => 'EMP'.str_pad($i, 4, '0', STR_PAD_LEFT),
                'department' => $deptNames[array_rand($deptNames)],
                'position' => $posNames[array_rand($posNames)],
                'gender' => $i % 3 == 0 ? 'female' : 'male',
                'join_date' => Carbon::now()->subMonths(rand(1, 24)),
                'birth_date' => Carbon::now()->subYears(rand(25, 50)),
                'id_card_number' => '3215'.rand(100000000000, 999999999999),
                'address' => 'Jl. Contoh No. '.rand(1, 100).', Karawang',
                'status' => 'active',
            ]);

            $members[] = $member;
        }

        $this->command->info('✅ Created 20 members');

        // Create Savings (150 transactions)
        $types = ['pokok', 'wajib', 'sukarela'];
        // Static calls used below

        for ($i = 0; $i < 150; $i++) {
            $member = $members[array_rand($members)];
            $type = $types[array_rand($types)];
            $transType = $i % 7 == 0 ? 'withdrawal' : 'deposit';

            $amount = match ($type) {
                'pokok' => 100000,
                'wajib' => rand(50, 150) * 1000,
                'sukarela' => rand(100, 500) * 1000,
            };

            $saving = Saving::create([
                'member_id' => $member->id,
                'type' => $type,
                'transaction_type' => $transType,
                'amount' => $amount,
                'transaction_date' => Carbon::now()->subDays(rand(1, 180)),
                'reference_number' => 'SAV'.time().rand(1000, 9999),
                'description' => ($transType === 'deposit' ? 'Setoran ' : 'Penarikan ').ucfirst($type),
            ]);

            // Create Journal
            if ($transType === 'deposit') {
                \App\Services\JournalService::journalSavingDeposit($saving);
            } else {
                \App\Services\JournalService::journalSavingWithdrawal($saving);
            }
        }

        $this->command->info('✅ Created 150 savings transactions with Journals');

        // Create Loans (30)
        $loanTypes = ['regular', 'emergency', 'education'];
        $statuses = ['pending', 'approved', 'active', 'completed'];

        for ($i = 1; $i <= 30; $i++) {
            try {
                $member = $members[array_rand($members)];
                $loanType = $loanTypes[array_rand($loanTypes)];
                $status = $statuses[array_rand($statuses)];

                $amount = rand(5, 50) * 1000000;
                $duration = rand(6, 36);
                $rate = $loanType === 'emergency' ? 2.0 : 1.5; // Monthly rate %

                $installment = ($amount * (1 + ($rate / 100 * $duration / 12))) / $duration;
                $monthlyPrincipal = $amount / $duration;
                $monthlyInterest = $installment - $monthlyPrincipal;
                $totalAmount = $installment * $duration;

                $appDate = Carbon::now()->subDays(rand(10, 200));

                $loan = Loan::create([
                    'member_id' => $member->id,
                    'loan_number' => 'LN'.date('Y').str_pad($i, 4, '0', STR_PAD_LEFT),
                    'loan_type' => $loanType,
                    'amount' => $amount,
                    'interest_rate' => $rate,
                    'duration_months' => $duration,
                    'monthly_installment' => $installment,
                    'total_amount' => $totalAmount,
                    'remaining_amount' => $totalAmount, // Initial remaining
                    'status' => $status,
                    'purpose' => 'Keperluan '.ucfirst($loanType),
                    'application_date' => $appDate,
                    'approval_date' => $status !== 'pending' ? $appDate->copy()->addDays(rand(1, 7)) : null,
                    'approved_by' => null,
                    'disbursement_date' => in_array($status, ['active', 'completed']) ? $appDate->copy()->addDays(rand(7, 14)) : null,
                ]);

                if (in_array($status, ['active', 'completed'])) {
                    // Journal Disbursement
                    \App\Services\JournalService::journalLoanDisbursement($loan);

                    $payCount = $status === 'completed' ? $duration : rand(1, min($duration, 12));

                    for ($p = 1; $p <= $payCount; $p++) {
                        $paymentDate = $loan->disbursement_date->copy()->addMonths($p);
                        $payment = LoanPayment::create([
                            'loan_id' => $loan->id,
                            'payment_number' => 'PAY'.date('Ymd').rand(1000, 9999).$p,
                            'installment_number' => $p,
                            'amount' => $installment,
                            'principal_amount' => $monthlyPrincipal,
                            'interest_amount' => $monthlyInterest,
                            'payment_date' => $paymentDate,
                            'due_date' => $paymentDate, // Set due_date same as payment date for dummy
                            'status' => 'paid',
                            'payment_method' => 'cash',
                        ]);

                        // Journal Payment
                        \App\Services\JournalService::journalLoanPayment($payment, $monthlyPrincipal, $monthlyInterest);
                    }

                    $paid = $payCount * $installment;
                    $loan->update([
                        'remaining_amount' => $totalAmount - $paid,
                    ]);
                }
            } catch (\Exception $e) {
                $this->command->error('Error loop '.$i.': '.$e->getMessage());
            }
        }

        $this->command->info('✅ Created 30 loans with payments and Journals');

        // Create POS Transactions (50)
        $products = Product::where('is_active', true)->get();
        if ($products->count() > 0) {
            for ($i = 0; $i < 50; $i++) {
                try {
                    $member = rand(0, 1) ? $members[array_rand($members)] : null;
                    $paymentMethod = $member ? ['cash', 'transfer', 'kredit', 'saldo'][rand(0, 3)] : 'cash';

                    $transaction = Transaction::create([
                        'invoice_number' => 'TRX-'.date('Ymd').'-'.strtoupper(Str::random(4)),
                        'user_id' => $member ? $member->user_id : null,
                        'type' => 'offline',
                        'status' => $paymentMethod === 'kredit' ? 'credit' : 'completed',
                        'cashier_id' => 1, // Admin
                        'payment_method' => $paymentMethod,
                        'total_amount' => 0,
                        'paid_amount' => 0,
                        'change_amount' => 0,
                    ]);

                    $totalAmount = 0;
                    $itemCount = rand(1, 4);
                    for ($j = 0; $j < $itemCount; $j++) {
                        $product = $products->random();
                        $qty = rand(1, 3);
                        $subtotal = $product->price * $qty;

                        TransactionItem::create([
                            'transaction_id' => $transaction->id,
                            'product_id' => $product->id,
                            'quantity' => $qty,
                            'price' => $product->price,
                            'subtotal' => $subtotal,
                        ]);
                        $totalAmount += $subtotal;
                    }

                    $paidAmount = in_array($paymentMethod, ['kredit']) ? 0 : $totalAmount + (rand(0, 2) * 5000);
                    $transaction->update([
                        'total_amount' => $totalAmount,
                        'paid_amount' => $paidAmount,
                        'change_amount' => $paidAmount > 0 ? $paidAmount - $totalAmount : 0,
                    ]);

                    // Journal POS
                    \App\Services\JournalService::journalSale($transaction);
                } catch (\Exception $e) {
                    $this->command->error('Error TRX loop '.$i.': '.$e->getMessage());
                }
            }
            $this->command->info('✅ Created 50 POS Transactions with Journals');
        }

        // Create Online Shopping Transactions (30)
        if ($products->count() > 0) {
            $onlineStatuses = ['pending', 'processing', 'shipped', 'completed', 'cancelled'];
            for ($i = 0; $i < 30; $i++) {
                try {
                    $member = $members[array_rand($members)];
                    $status = $onlineStatuses[array_rand($onlineStatuses)];
                    $paymentMethod = ['transfer', 'kredit', 'saldo'][rand(0, 2)];
                    $orderDate = Carbon::now()->subDays(rand(1, 60));

                    $transaction = Transaction::create([
                        'invoice_number' => 'ONL-' . $orderDate->format('Ymd') . '-' . strtoupper(Str::random(4)),
                        'user_id' => $member->user_id,
                        'type' => 'online',
                        'status' => $status,
                        'cashier_id' => null,
                        'payment_method' => $paymentMethod,
                        'total_amount' => 0,
                        'paid_amount' => 0,
                        'change_amount' => 0,
                        'notes' => 'Pesanan online dummy #' . ($i + 1),
                        'created_at' => $orderDate,
                        'updated_at' => $orderDate->copy()->addHours(rand(1, 48)),
                    ]);

                    $totalAmount = 0;
                    $itemCount = rand(1, 5);
                    for ($j = 0; $j < $itemCount; $j++) {
                        $product = $products->random();
                        $qty = rand(1, 3);
                        $subtotal = $product->price * $qty;

                        TransactionItem::create([
                            'transaction_id' => $transaction->id,
                            'product_id' => $product->id,
                            'quantity' => $qty,
                            'price' => $product->price,
                            'subtotal' => $subtotal,
                        ]);
                        $totalAmount += $subtotal;
                    }

                    $paidAmount = in_array($status, ['completed', 'shipped', 'processing']) ? $totalAmount : 0;
                    $transaction->update([
                        'total_amount' => $totalAmount,
                        'paid_amount' => $paidAmount,
                    ]);

                    // Only journal completed transactions
                    if ($status === 'completed') {
                        \App\Services\JournalService::journalSale($transaction);
                    }
                } catch (\Exception $e) {
                    $this->command->error('Error Online TRX loop ' . $i . ': ' . $e->getMessage());
                }
            }
            $this->command->info('✅ Created 30 Online Shopping Transactions');
        }

        // Create Expenses (20)
        $expenseCategories = ExpenseCategory::all();
        if ($expenseCategories->count() > 0) {
            for ($i = 0; $i < 20; $i++) {
                try {
                    $expense = Expense::create([
                        'expense_category_id' => $expenseCategories->random()->id,
                        'expense_date' => Carbon::now()->subDays(rand(1, 150)),
                        'amount' => rand(50, 1000) * 1000,
                        'description' => 'Biaya operasional kantor dummy #'.($i + 1),
                        'created_by' => 1,
                    ]);

                    // Journal Expense
                    \App\Services\JournalService::journalExpense($expense);
                } catch (\Exception $e) {
                    $this->command->error('Error Expense loop '.$i.': '.$e->getMessage());
                }
            }
            $this->command->info('✅ Created 20 Expenses with Journals');
        }

        // Create Stock Purchases (10)
        $suppliers = Supplier::all();
        if ($suppliers->count() > 0 && $products->count() > 0) {
            for ($i = 0; $i < 10; $i++) {
                try {
                    $purchase = Purchase::create([
                        'supplier_id' => $suppliers->random()->id,
                        'reference_number' => 'PUR-'.date('Ymd').'-'.strtoupper(Str::random(4)),
                        'purchase_date' => Carbon::now()->subDays(rand(1, 150)),
                        'total_amount' => 0,
                        'status' => 'completed',
                        'completed_at' => Carbon::now()->subDays(rand(1, 150)),
                        'created_by' => 1,
                    ]);

                    $totalAmount = 0;
                    $itemCount = rand(3, 8);
                    for ($j = 0; $j < $itemCount; $j++) {
                        $product = $products->random();
                        $qty = rand(10, 50);
                        $cost = $product->cost ?: ($product->price * 0.8);
                        $subtotal = $cost * $qty;

                        PurchaseItem::create([
                            'purchase_id' => $purchase->id,
                            'product_id' => $product->id,
                            'quantity' => $qty,
                            'cost' => $cost,
                            'subtotal' => $subtotal,
                        ]);
                        $totalAmount += $subtotal;
                    }

                    $purchase->update(['total_amount' => $totalAmount]);

                    // Journal Purchase
                    \App\Services\JournalService::journalPurchase($purchase);
                } catch (\Exception $e) {
                    $this->command->error('Error Purchase loop '.$i.': '.$e->getMessage());
                }
            }
            $this->command->info('✅ Created 10 Purchases with Journals');
        }

        // Create Vouchers (10)
        $vouchers = [
            ['code' => 'WELCOME10', 'type' => 'percentage', 'value' => 10, 'min_purchase' => 50000, 'usage_limit' => 100, 'days_valid' => 90],
            ['code' => 'DISKON20K', 'type' => 'fixed', 'value' => 20000, 'min_purchase' => 100000, 'usage_limit' => 50, 'days_valid' => 60],
            ['code' => 'MEMBER15', 'type' => 'percentage', 'value' => 15, 'min_purchase' => 75000, 'usage_limit' => null, 'days_valid' => 120],
            ['code' => 'HEMAT25K', 'type' => 'fixed', 'value' => 25000, 'min_purchase' => 150000, 'usage_limit' => 30, 'days_valid' => 45],
            ['code' => 'PROMO5', 'type' => 'percentage', 'value' => 5, 'min_purchase' => 25000, 'usage_limit' => 200, 'days_valid' => 30],
            ['code' => 'SPESIAL50K', 'type' => 'fixed', 'value' => 50000, 'min_purchase' => 300000, 'usage_limit' => 20, 'days_valid' => 60],
            ['code' => 'FLASH30', 'type' => 'percentage', 'value' => 30, 'min_purchase' => 200000, 'usage_limit' => 10, 'days_valid' => 7],
            ['code' => 'BELANJA10K', 'type' => 'fixed', 'value' => 10000, 'min_purchase' => 50000, 'usage_limit' => 100, 'days_valid' => 90],
            ['code' => 'AKHIRTAHUN', 'type' => 'percentage', 'value' => 25, 'min_purchase' => 100000, 'usage_limit' => 50, 'days_valid' => 14],
            ['code' => 'GRATISONGKIR', 'type' => 'fixed', 'value' => 15000, 'min_purchase' => 75000, 'usage_limit' => null, 'days_valid' => 60],
        ];

        foreach ($vouchers as $v) {
            try {
                Voucher::create([
                    'code' => $v['code'],
                    'type' => $v['type'],
                    'value' => $v['value'],
                    'min_purchase' => $v['min_purchase'],
                    'usage_limit' => $v['usage_limit'],
                    'used_count' => rand(0, min(10, $v['usage_limit'] ?? 10)),
                    'start_date' => Carbon::now()->subDays(rand(0, 30)),
                    'end_date' => Carbon::now()->addDays($v['days_valid']),
                    'is_active' => true,
                ]);
            } catch (\Exception $e) {
                $this->command->error('Error Voucher ' . $v['code'] . ': ' . $e->getMessage());
            }
        }
        $this->command->info('✅ Created 10 Vouchers');

        $this->command->newLine();
        $this->command->info('🎉 Data dummy berhasil dibuat!');
        $this->command->newLine();
        $this->command->info('📊 Ringkasan:');
        $this->command->info('   - Anggota: 20 orang');
        $this->command->info('   - Simpanan: 150 transaksi');
        $this->command->info('   - Pinjaman: 30 dengan angsuran');
        $this->command->info('   - Voucher: 10 promo');
        $this->command->newLine();
        $this->command->info('🔐 Login:');
        $this->command->info('   Email: member1@koperasi.test s/d member20@koperasi.test');
        $this->command->info('   Password: password');
    }

    private function generateName(): string
    {
        $first = ['Budi', 'Andi', 'Siti', 'Dewi', 'Agus', 'Rini', 'Joko', 'Sri', 'Ahmad', 'Ratna', 'Hadi', 'Wati', 'Bambang', 'Lestari', 'Yunus', 'Maya', 'Dedi', 'Fitri', 'Hendra', 'Laila'];
        $last = ['Santoso', 'Wijaya', 'Kusuma', 'Pratama', 'Putra', 'Sari', 'Hartono', 'Suryadi', 'Wibowo', 'Nugroho', 'Rahayu', 'Permana', 'Setiawan', 'Hidayat', 'Rahman', 'Kurniawan'];

        return $first[array_rand($first)].' '.$last[array_rand($last)];
    }
}
