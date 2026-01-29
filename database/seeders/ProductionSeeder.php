<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Setting;
use App\Models\AiSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * ProductionSeeder - Seeder untuk environment production
 * 
 * Hanya membuat:
 * - 1 akun Admin
 * - Default settings
 * - TANPA data dummy
 * 
 * Jalankan: php artisan db:seed --class=ProductionSeeder
 */
class ProductionSeeder extends Seeder
{
    /**
     * Seed the application's database for production.
     */
    public function run(): void
    {
        $this->command->warn('🚀 Running Production Seeder...');
        $this->command->newLine();

        // 1. Create Admin User
        $this->command->info('Creating Admin user...');
        
        $admin = User::firstOrCreate(
            ['email' => 'admin@koperasi.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'phone' => null,
                'is_active' => true,
            ]
        );

        $this->command->info("✅ Admin created: {$admin->email}");

        // 2. Seed Default Settings
        $this->command->info('Creating default settings...');
        
        if (class_exists(Setting::class) && method_exists(Setting::class, 'seedDefaults')) {
            Setting::seedDefaults();
            $this->command->info('✅ Default settings created');
        }

        // 3. Seed Default AI Settings
        $this->command->info('Creating default AI settings...');
        
        $aiDefaults = [
            'ai_enabled' => 'false',
            'ai_provider' => 'ollama',
            'ai_url' => 'http://localhost:11434',
            'ai_model' => 'llama3.2',
            'ai_api_key' => '',
            'ai_system_prompt' => 'Anda adalah asisten koperasi yang membantu anggota dengan informasi simpanan dan pinjaman.',
            'wa_bot_enabled' => 'false',
            'wa_provider' => 'fonnte',
            'fonnte_token' => '',
            'twilio_sid' => '',
            'twilio_token' => '',
            'twilio_wa_number' => '',
        ];

        foreach ($aiDefaults as $key => $value) {
            AiSetting::firstOrCreate(['key' => $key], ['value' => $value]);
        }
        $this->command->info('✅ Default AI settings created');

        // 4. Create Chart of Accounts
        $this->command->info('Creating chart of accounts...');
        $this->call(ChartOfAccountsSeeder::class);
        $this->command->info('✅ Chart of accounts created');

        // 5. Create Expense Categories
        $this->command->info('Creating expense categories...');
        $this->call(ExpenseCategorySeeder::class);
        $this->command->info('✅ Expense categories created');

        // Summary
        $this->command->newLine();
        $this->command->info('═══════════════════════════════════════════════════');
        $this->command->info('🎉 PRODUCTION SEEDER COMPLETED!');
        $this->command->info('═══════════════════════════════════════════════════');
        $this->command->newLine();
        $this->command->warn('📋 Login Credentials:');
        $this->command->line("   Email    : admin@koperasi.com");
        $this->command->line("   Password : admin123");
        $this->command->newLine();
        $this->command->warn('⚠️  IMPORTANT: Change the password after first login!');
        $this->command->newLine();
    }
}
