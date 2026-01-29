<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;
use App\Models\Member;
use App\Models\Saving;
use App\Models\Loan;
use App\Models\LoanPayment;

class ResetForProduction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'koperasi:reset-for-production 
                            {--force : Skip confirmation prompts}
                            {--keep-admin : Keep existing admin accounts}
                            {--fresh : Also run migrate:fresh (WARNING: drops all tables)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset database for production deployment. Removes all dummy data while keeping admin accounts.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->newLine();
        $this->warn('╔═══════════════════════════════════════════════════════════════╗');
        $this->warn('║           KOPERASI - RESET FOR PRODUCTION                     ║');
        $this->warn('╚═══════════════════════════════════════════════════════════════╝');
        $this->newLine();

        // Show current data counts
        $this->info('📊 Current Data:');
        $this->table(
            ['Data', 'Count'],
            [
                ['Users (Admin)', User::where('role', 'admin')->count()],
                ['Users (Non-Admin)', User::where('role', '!=', 'admin')->count()],
                ['Members', Member::count()],
                ['Savings', Saving::count()],
                ['Loans', Loan::count()],
                ['Loan Payments', LoanPayment::count()],
            ]
        );

        // Confirmation
        if (!$this->option('force')) {
            $this->newLine();
            $this->error('⚠️  WARNING: This will DELETE all data except admin accounts!');
            
            if (!$this->confirm('Are you sure you want to proceed?')) {
                $this->info('Operation cancelled.');
                return 0;
            }

            if (!$this->confirm('This action CANNOT be undone. Type "yes" to confirm:')) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        $this->newLine();
        $this->info('🚀 Starting reset process...');
        $this->newLine();

        try {
            DB::beginTransaction();

            // Option: Fresh migration
            if ($this->option('fresh')) {
                $this->warn('Running migrate:fresh...');
                Artisan::call('migrate:fresh', ['--force' => true]);
                $this->info('✅ Migrations refreshed');
                
                // Run production seeder
                $this->warn('Running ProductionSeeder...');
                Artisan::call('db:seed', [
                    '--class' => 'Database\\Seeders\\ProductionSeeder',
                    '--force' => true
                ]);
                $this->info('✅ Production seeder completed');
                
                DB::commit();
                $this->showCompletionMessage();
                return 0;
            }

            // Step 1: Delete Loan Payments
            $this->info('Deleting loan payments...');
            $count = LoanPayment::count();
            LoanPayment::query()->delete();
            $this->info("   ✅ Deleted {$count} loan payments");

            // Step 2: Delete Loans
            $this->info('Deleting loans...');
            $count = Loan::count();
            Loan::query()->delete();
            $this->info("   ✅ Deleted {$count} loans");

            // Step 3: Delete Savings
            $this->info('Deleting savings...');
            $count = Saving::count();
            Saving::query()->delete();
            $this->info("   ✅ Deleted {$count} savings");

            // Step 4: Delete Members
            $this->info('Deleting members...');
            $count = Member::count();
            Member::query()->delete();
            $this->info("   ✅ Deleted {$count} members");

            // Step 5: Delete Non-Admin Users
            $this->info('Deleting non-admin users...');
            $count = User::where('role', '!=', 'admin')->count();
            User::where('role', '!=', 'admin')->delete();
            $this->info("   ✅ Deleted {$count} users");

            // Step 6: Keep admin or recreate
            if (!$this->option('keep-admin')) {
                $adminCount = User::where('role', 'admin')->count();
                if ($adminCount === 0) {
                    $this->info('Creating default admin account...');
                    Artisan::call('db:seed', [
                        '--class' => 'Database\\Seeders\\ProductionSeeder',
                        '--force' => true
                    ]);
                    $this->info('   ✅ Default admin created');
                }
            }

            DB::commit();
            
            $this->showCompletionMessage();
            
            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Error: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Show completion message
     */
    protected function showCompletionMessage(): void
    {
        $this->newLine();
        $this->info('═══════════════════════════════════════════════════════════════');
        $this->info('🎉 RESET COMPLETED SUCCESSFULLY!');
        $this->info('═══════════════════════════════════════════════════════════════');
        $this->newLine();
        
        // Show remaining data
        $this->info('📊 Remaining Data:');
        $this->table(
            ['Data', 'Count'],
            [
                ['Users (Admin)', User::where('role', 'admin')->count()],
                ['Users (Non-Admin)', User::where('role', '!=', 'admin')->count()],
                ['Members', Member::count()],
                ['Savings', Saving::count()],
                ['Loans', Loan::count()],
            ]
        );

        $this->newLine();
        $this->warn('📋 Next Steps:');
        $this->line('   1. Login with admin account');
        $this->line('   2. Change admin password');
        $this->line('   3. Configure settings');
        $this->line('   4. Import real data via Excel');
        $this->newLine();
        
        $this->warn('🔐 Default Admin:');
        $this->line('   Email    : admin@koperasi.com');
        $this->line('   Password : admin123');
        $this->newLine();
    }
}
