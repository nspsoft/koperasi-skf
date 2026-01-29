<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Member;
use App\Models\Setting;
use App\Models\Announcement;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        $admin = User::create([
            'name' => 'Admin Koperasi',
            'email' => 'admin@koperasi.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '081234567890',
            'is_active' => true,
        ]);

        // Create Pengurus User
        $pengurus = User::create([
            'name' => 'Pengurus Koperasi',
            'email' => 'pengurus@koperasi.com',
            'password' => Hash::make('password'),
            'role' => 'pengurus',
            'phone' => '081234567891',
            'is_active' => true,
        ]);

        // Create Member User
        $memberUser = User::create([
            'name' => 'John Doe',
            'email' => 'member@koperasi.com',
            'password' => Hash::make('password'),
            'role' => 'member',
            'phone' => '081234567892',
            'is_active' => true,
        ]);

        // Create Member Profile for member user
        Member::create([
            'user_id' => $memberUser->id,
            'member_id' => 'KOP' . date('Y') . '0001',
            'employee_id' => 'EMP001',
            'department' => 'IT Department',
            'position' => 'Software Developer',
            'join_date' => now(),
            'status' => 'active',
            'address' => 'Karawang, Jawa Barat',
            'id_card_number' => '3215010101990001',
            'birth_date' => '1990-01-01',
            'gender' => 'male',
        ]);

        // Create Admin Member Profile
        Member::create([
            'user_id' => $admin->id,
            'member_id' => 'KOP' . date('Y') . '0000',
            'employee_id' => 'ADM001',
            'department' => 'Management',
            'position' => 'Administrator',
            'join_date' => now()->subYear(),
            'status' => 'active',
            'address' => 'Karawang, Jawa Barat',
            'id_card_number' => '3215010101980001',
            'birth_date' => '1980-01-01',
            'gender' => 'male',
        ]);

        // Seed Default Settings
        Setting::seedDefaults();

        // Create Sample Announcement
        Announcement::create([
            'title' => 'Selamat Datang di Koperasi Karyawan PT. SPINDO',
            'content' => 'Website Koperasi Karyawan PT. SPINDO Karawang Factory telah diluncurkan. Silakan login menggunakan akun Anda untuk mengakses fitur simpanan dan pinjaman.',
            'type' => 'important',
            'priority' => 'high',
            'is_published' => true,
            'publish_date' => now(),
            'created_by' => $admin->id,
        ]);

        Announcement::create([
            'title' => 'Info Simpanan Wajib Bulanan',
            'content' => 'Simpanan wajib bulanan sebesar Rp 50.000 akan dipotong otomatis dari gaji setiap bulannya. Pastikan saldo Anda mencukupi.',
            'type' => 'info',
            'priority' => 'medium',
            'is_published' => true,
            'publish_date' => now(),
            'created_by' => $admin->id,
        ]);

        // Seed Accounting Data
        $this->call([
            ChartOfAccountsSeeder::class,
            ExpenseCategorySeeder::class,
            CommerceSeeder::class,
        ]);

        $this->command->info('Database seeded successfully!');
        $this->command->info('Admin: admin@koperasi.com / password');
        $this->command->info('Pengurus: pengurus@koperasi.com / password');
        $this->command->info('Member: member@koperasi.com / password');
    }
}
