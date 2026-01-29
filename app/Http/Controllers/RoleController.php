<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display role management page
     */
    public function index(Request $request)
    {
        $query = User::with('member');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
        
        // Role definitions with permissions
        $roles = [
            'admin' => [
                'label' => 'Administrator',
                'description' => 'Akses penuh ke semua fitur sistem, termasuk pengaturan dan manajemen user',
                'permissions' => [
                    'Kelola Anggota (CRUD)',
                    'Kelola Simpanan (CRUD)',
                    'Kelola Pinjaman (CRUD)',
                    'Approve/Reject Pinjaman',
                    'Kelola Pembayaran Angsuran',
                    'Lihat Semua Laporan',
                    'Export Data (Excel/PDF)',
                    'Import Data (Excel)',
                    'Kelola POS Mart & Produk',
                    'Kelola Inventory & Stok',
                    'Kelola Supplier & Purchasing',
                    'Hitung & Distribusi SHU',
                    'Kelola Pengumuman',
                    'Kelola Pengaturan Sistem',
                    'Kelola Role & User',
                    'Akses Audit Log',
                    'Backup & Restore Data',
                ]
            ],
            'pengurus' => [
                'label' => 'Pengurus',
                'description' => 'Mengelola operasional koperasi, verifikasi keuangan, dan memantau laporan',
                'permissions' => [
                    'Lihat Data Simpanan Anggota',
                    'Lihat Data Pinjaman Anggota',
                    'Approve/Reject Pinjaman',
                    'Lihat Pembayaran Angsuran',
                    'Lihat Laporan Keuangan & Akuntansi',
                    'Export Laporan (Excel/PDF)',
                    'Kelola POS Kasir Mart',
                    'Kelola Produk & Stok Mart',
                    'Kelola Supplier & Purchasing',
                ]
            ],
            'manager_toko' => [
                'label' => 'Manager Toko',
                'description' => 'Mengelola operasional toko/mart dan transaksi harian',
                'permissions' => [
                    'Akses POS Kasir',
                    'Kelola Transaksi Penjualan',
                    'Kelola Produk & Kategori',
                    'Kelola Stok & Inventory',
                    'Lihat Laporan Penjualan',
                    'Kelola Supplier',
                    'Input Purchase Order',
                    'Lihat Data Anggota (Read Only)',
                    'Proses Pembayaran Kredit',
                ]
            ],
            'member' => [
                'label' => 'Anggota',
                'description' => 'Akses ke dashboard pribadi dan data diri sendiri',
                'permissions' => [
                    'Lihat Dashboard Pribadi',
                    'Lihat Simpanan Pribadi',
                    'Lihat Riwayat Pinjaman',
                    'Ajukan Pinjaman Baru',
                    'Lihat Kartu Anggota Digital',
                    'Edit Profil Sendiri',
                    'Belanja di Mart (Tunai/Kredit)',
                    'Lihat Riwayat Kredit Mart',
                    'Ajukan Penarikan Simpanan',
                    'Lihat Pembagian SHU',
                ]
            ]
        ];
        
        return view('roles.index', compact('users', 'roles'));
    }

    /**
     * Update user role
     */
    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:admin,pengurus,manager_toko,member'
        ]);

        $user->update([
            'role' => $request->role
        ]);

        \App\Models\AuditLog::log(
            'update', 
            "Mengubah role user {$user->name} menjadi " . ucfirst($request->role)
        );

        return redirect()->back()->with('success', 'Role user berhasil diubah menjadi ' . ucfirst($request->role));
    }

    /**
     * Get role permissions (for AJAX)
     */
    public function getPermissions($role)
    {
        $permissions = match($role) {
            'admin' => [
                'Kelola Anggota (CRUD)', 'Kelola Simpanan (CRUD)', 'Kelola Pinjaman (CRUD)',
                'Approve/Reject Pinjaman', 'Kelola Pembayaran Angsuran', 'Lihat Semua Laporan',
                'Export Data (Excel/PDF)', 'Import Data (Excel)', 'Kelola POS Mart & Produk',
                'Kelola Inventory & Stok', 'Kelola Supplier & Purchasing', 'Hitung & Distribusi SHU',
                'Kelola Pengumuman', 'Kelola Pengaturan Sistem', 'Kelola Role & User',
                'Akses Audit Log', 'Backup & Restore Data'
            ],
            'pengurus' => [
                'Lihat Data Simpanan Anggota', 'Lihat Data Pinjaman Anggota',
                'Approve/Reject Pinjaman', 'Lihat Pembayaran Angsuran', 'Lihat Laporan Keuangan & Akuntansi',
                'Export Laporan (Excel/PDF)', 'Kelola POS Kasir Mart', 'Kelola Produk & Stok Mart',
                'Kelola Supplier & Purchasing'
            ],
            'manager_toko' => [
                'Akses POS Kasir', 'Kelola Transaksi Penjualan', 'Kelola Produk & Kategori',
                'Kelola Stok & Inventory', 'Lihat Laporan Penjualan', 'Kelola Supplier',
                'Input Purchase Order', 'Lihat Data Anggota (Read Only)', 'Proses Pembayaran Kredit'
            ],
            'member' => [
                'Lihat Dashboard Pribadi', 'Lihat Simpanan Pribadi', 'Lihat Riwayat Pinjaman',
                'Ajukan Pinjaman Baru', 'Lihat Kartu Anggota Digital', 'Edit Profil Sendiri',
                'Belanja di Mart (Tunai/Kredit)', 'Lihat Riwayat Kredit Mart',
                'Ajukan Penarikan Simpanan', 'Lihat Pembagian SHU'
            ],
            default => []
        };

        return response()->json(['permissions' => $permissions]);
    }
}
