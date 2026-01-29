<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Define all permissions grouped by category
        $permissions = [
            'Anggota' => [
                ['name' => 'manage_members', 'label' => 'Kelola Anggota (CRUD)'],
                ['name' => 'view_members', 'label' => 'Lihat Data Anggota'],
            ],
            'Simpanan' => [
                ['name' => 'manage_savings', 'label' => 'Kelola Simpanan (CRUD)'],
                ['name' => 'view_savings', 'label' => 'Lihat Data Simpanan'],
            ],
            'Pinjaman' => [
                ['name' => 'manage_loans', 'label' => 'Kelola Pinjaman (CRUD)'],
                ['name' => 'approve_loans', 'label' => 'Approve/Reject Pinjaman'],
                ['name' => 'view_loans', 'label' => 'Lihat Data Pinjaman'],
                ['name' => 'manage_loan_payments', 'label' => 'Kelola Pembayaran Angsuran'],
            ],
            'Koperasi Mart' => [
                ['name' => 'access_pos', 'label' => 'Akses POS Kasir'],
                ['name' => 'manage_products', 'label' => 'Kelola Produk & Kategori'],
                ['name' => 'manage_stock', 'label' => 'Kelola Stok & Inventory'],
                ['name' => 'manage_suppliers', 'label' => 'Kelola Supplier & Purchasing'],
                ['name' => 'process_credit', 'label' => 'Proses Pembayaran Kredit'],
            ],
            'Laporan' => [
                ['name' => 'view_reports', 'label' => 'Lihat Semua Laporan'],
                ['name' => 'view_financial_health', 'label' => 'Lihat Kesehatan Keuangan'],
                ['name' => 'export_reports', 'label' => 'Export Laporan (Excel/PDF)'],
                ['name' => 'view_accounting', 'label' => 'Lihat Laporan Akuntansi'],
            ],
            'SHU' => [
                ['name' => 'manage_shu', 'label' => 'Hitung & Distribusi SHU'],
                ['name' => 'view_shu', 'label' => 'Lihat Pembagian SHU'],
            ],
            'Organisasi' => [
                ['name' => 'manage_announcements', 'label' => 'Kelola Pengumuman'],
                ['name' => 'manage_documents', 'label' => 'Kelola Dokumen & Surat'],
                ['name' => 'manage_meetings', 'label' => 'Kelola Rapat & RAT'],
                ['name' => 'manage_polls', 'label' => 'Kelola E-Polling'],
            ],
            'Sistem' => [
                ['name' => 'manage_settings', 'label' => 'Kelola Pengaturan Sistem'],
                ['name' => 'manage_roles', 'label' => 'Kelola Role & User'],
                ['name' => 'view_audit_log', 'label' => 'Akses Audit Log'],
                ['name' => 'backup_restore', 'label' => 'Backup & Restore Data'],
                ['name' => 'import_data', 'label' => 'Import Data (Excel)'],
            ],
        ];

        // Create permissions
        $permissionMap = [];
        foreach ($permissions as $group => $items) {
            foreach ($items as $item) {
                $perm = Permission::updateOrCreate(
                    ['name' => $item['name']],
                    ['label' => $item['label'], 'group' => $group]
                );
                $permissionMap[$item['name']] = $perm->id;
            }
        }

        // Create default roles with permissions
        $roles = [
            [
                'name' => 'admin',
                'label' => 'Administrator',
                'description' => 'Akses penuh ke semua fitur sistem, termasuk pengaturan dan manajemen user',
                'color' => '#ef4444',
                'is_system' => true,
                'permissions' => array_keys($permissionMap), // All permissions
            ],
            [
                'name' => 'pengurus',
                'label' => 'Pengurus',
                'description' => 'Mengelola operasional koperasi, verifikasi keuangan, dan memantau laporan',
                'color' => '#f59e0b',
                'is_system' => true,
                'permissions' => [
                    'view_members', 'view_savings', 'view_loans', 'approve_loans', 
                    'manage_loan_payments', 'view_reports', 'view_financial_health', 
                    'export_reports', 'view_accounting', 'access_pos', 'manage_products',
                    'manage_stock', 'manage_suppliers', 'view_shu', 'manage_announcements',
                ],
            ],
            [
                'name' => 'manager_toko',
                'label' => 'Manager Toko',
                'description' => 'Mengelola operasional toko/mart dan transaksi harian',
                'color' => '#10b981',
                'is_system' => true,
                'permissions' => [
                    'view_members', 'access_pos', 'manage_products', 'manage_stock',
                    'manage_suppliers', 'process_credit', 'view_reports',
                ],
            ],
            [
                'name' => 'member',
                'label' => 'Anggota',
                'description' => 'Akses ke dashboard pribadi dan data diri sendiri',
                'color' => '#6366f1',
                'is_system' => true,
                'permissions' => ['view_shu'],
            ],
        ];

        foreach ($roles as $roleData) {
            $permissionNames = $roleData['permissions'];
            unset($roleData['permissions']);
            
            $role = Role::updateOrCreate(
                ['name' => $roleData['name']],
                $roleData
            );

            // Sync permissions
            $permIds = [];
            foreach ($permissionNames as $permName) {
                if (isset($permissionMap[$permName])) {
                    $permIds[] = $permissionMap[$permName];
                }
            }
            $role->permissions()->sync($permIds);
        }

        // Migrate existing users to role_id
        $roleMapping = Role::pluck('id', 'name')->toArray();
        
        User::whereNull('role_id')->each(function ($user) use ($roleMapping) {
            if (isset($roleMapping[$user->role])) {
                $user->update(['role_id' => $roleMapping[$user->role]]);
            }
        });

        $this->command->info('✅ Permissions and roles seeded successfully!');
    }
}
