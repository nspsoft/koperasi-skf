@extends('layouts.app')

@section('title', 'User Acceptance Test (UAT)')

@section('content')
<!-- Styling for Print Scope -->
<style>
    @media print {
        body * { visibility: hidden; }
        #print-container, #print-container * { visibility: visible; }
        #print-container { position: absolute; left: 0; top: 0; width: 100%; }
        .no-print { display: none !important; }
        .print-break-inside { page-break-inside: auto; }
        .print-no-break { page-break-inside: avoid; break-inside: avoid; }
        .print-header { display: block !important; } /* CRITICAL FIX */
        #print-container { padding-bottom: 0 !important; margin-bottom: 0 !important; }
        
        @page { margin: 1.5cm; size: auto; }
        
        /* Ensure sections don't break in middle of content */
        .sign-block { page-break-inside: avoid; break-inside: avoid; }
        .footer-validation { page-break-inside: avoid; break-inside: avoid; }
        
        /* Clean table for print */
        th, td { border: 1px solid #ddd !important; }
        
        /* Remove border from header table specifically */
        table.header-table, table.header-table td { border: none !important; }
        
        input[type="checkbox"] { -webkit-appearance: none; appearance: none; border: 1px solid #000; width: 15px; height: 15px; display: inline-block; }
        input[type="checkbox"]:checked { background-color: #000; }
    }
    .print-header { display: none; }

    /* FORCE DARK MODE STYLES IF TAILWIND MISSING */
    .dark .dark\:bg-gray-800 { background-color: #1f2937 !important; }
    .dark .dark\:bg-gray-700 { background-color: #374151 !important; }
    .dark .dark\:bg-gray-900 { background-color: #111827 !important; }
    .dark .dark\:text-white { color: #ffffff !important; }
    .dark .dark\:text-gray-400 { color: #9ca3af !important; }
    .dark .dark\:text-gray-300 { color: #d1d5db !important; }
    .dark .dark\:border-gray-700 { border-color: #374151 !important; }
    .dark .dark\:border-gray-600 { border-color: #4b5563 !important; }
    .dark .dark\:hover\:bg-gray-700:hover { background-color: #374151 !important; }
    
    /* Specific overrides for tables in dark mode */
    .dark .uat-table-header { background-color: #374151 !important; color: #d1d5db !important; } 
    .dark .uat-table-row { background-color: #1f2937 !important; color: #e5e7eb !important; }
    .dark .uat-table-row:hover { background-color: #374151 !important; }
</style>

<div class="space-y-6 max-w-7xl mx-auto pb-20" id="print-container" x-data="{
    total: 0,
    checked: 0,
    percentage: 0,
    moduleStats: [
        { id: 'auth', name: 'Otentikasi & Login', total: 0, checked: 0 },
        { id: 'dashboard', name: 'Dashboard Monitoring', total: 0, checked: 0 },
        { id: 'finance', name: 'Keuangan (Simpan Pinjam)', total: 0, checked: 0 },
        { id: 'mart', name: 'Koperasi Mart', total: 0, checked: 0 },
        { id: 'shopping', name: 'Belanja Online', total: 0, checked: 0 },
        { id: 'report', name: 'Laporan & Info', total: 0, checked: 0 },
        { id: 'org', name: 'Kepengurusan', total: 0, checked: 0 },
        { id: 'admin', name: 'Administrasi', total: 0, checked: 0 },
        { id: 'cross', name: 'Cross Functional', total: 0, checked: 0 }
    ],
    init() {
        this.moduleStats.forEach(m => {
            m.total = document.querySelectorAll(`.uat-checkbox[data-module='${m.id}']`).length;
        });
        this.total = document.querySelectorAll('.uat-checkbox').length;
        this.updateCount();
    },
    updateCount() {
        this.checked = document.querySelectorAll('.uat-checkbox:checked').length;
        this.percentage = this.total > 0 ? Math.round((this.checked / this.total) * 100) : 0;
        
        this.moduleStats.forEach(m => {
            m.checked = document.querySelectorAll(`.uat-checkbox[data-module='${m.id}']:checked`).length;
        });
    }
}">
    <!-- Print Header (Exactly matching pdf_template logic) -->
    <div class="print-header mb-6" style="background: white !important; color: black !important;">
        <table class="w-full" style="border-collapse: collapse; width: 100%; border-bottom: 3px solid #000; margin-bottom: 20px; background: white !important;">
            <tr style="background: white !important;">
                <!-- Left Logo -->
                <td style="width: 90px; text-align: center; vertical-align: middle; padding-bottom: 15px; border: none !important; background: white !important;">
                    @if(isset($globalSettings['coop_logo']))
                        <img src="{{ Storage::url($globalSettings['coop_logo']) }}" style="max-height: 85px; width: auto;">
                    @else
                        <img src="/icons/icon-192x192.png" style="max-height: 85px; width: auto;">
                    @endif
                </td>

                <!-- Center Text -->
                <td style="text-align: center; vertical-align: middle; padding: 0 10px 15px 10px; border: none !important; background: white !important;">
                    <h1 style="font-family: 'Times New Roman', Times, serif; font-size: 14pt; margin: 0; padding: 0; line-height: 1.1; text-transform: uppercase; font-weight: bold; color: black !important;">KOPERASI KARYAWAN</h1>
                    <h1 style="font-family: 'Times New Roman', Times, serif; font-size: 14pt; margin: 0; padding: 0; line-height: 1.1; text-transform: uppercase; font-weight: bold; color: black !important;">SPINDO KARAWANG FACTORY</h1>
                    <h2 style="font-family: 'Times New Roman', Times, serif; font-size: 12pt; margin: 2px 0; padding: 0; line-height: 1.1; text-transform: uppercase; font-weight: bold; color: black !important;">PT STEEL PIPE INDUSTRY OF INDONESIA TBK</h2>
                    <p style="font-family: 'Times New Roman', Times, serif; font-size: 8pt; margin: 2px 0 0 0; line-height: 1.2; font-style: italic; color: black !important;">
                        Jl. Mitra Raya Blok F2 Kawasan Industri Mitra Karawang, Ds. Parungmulya Kec. Ciampel Karawang
                    </p>
                </td>

                <!-- Right Logo -->
                <td style="width: 90px; text-align: center; vertical-align: middle; padding-bottom: 15px; border: none !important; background: white !important;">
                    <img src="/images/spindo-logo.png" style="max-height: 85px; width: auto;">
                </td>
            </tr>
        </table>
        
        <!-- Doc Content Meta -->
        <div style="display: flex; justify-content: space-between; align-items: center; font-family: 'Courier New', Courier, monospace; font-size: 9pt; color: black !important; margin-top: -10px; padding-bottom: 10px; background: white !important;">
            <div style="font-weight: bold; color: black !important;">DOKUMEN: USER ACCEPTANCE TEST (UAT)</div>
            <div style="text-align: right; color: black !important;">REF: UAT-2026-V2 | TANGGAL: {{ date('d/m/Y') }}</div>
        </div>
    </div>

    <!-- Screen Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 no-print bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-300 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">UAT Testing Suite</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Versi 2.1.0 • 83 Test Cases • 9 Modul</p>
            </div>
        </div>
        <div class="flex gap-3">
            <button onclick="window.print()" class="btn-secondary flex items-center gap-2 px-4 py-2 rounded-lg border dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                <span>Cetak PDF</span>
            </button>
        </div>
    </div>

    <!-- MAIN FORM : 9 MODULES -->
    <div class="space-y-8">

        <!-- 1. OTENTIKASI -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden print-break-inside">
            <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 border-b border-gray-200 dark:border-gray-600 flex justify-between items-center">
                <h3 class="font-bold text-gray-800 dark:text-white flex items-center gap-2"><span class="bg-gray-800 dark:bg-gray-900 text-white w-6 h-6 rounded flex items-center justify-center text-xs">1</span> Otentikasi & Login</h3>
                <span class="text-xs font-mono bg-gray-200 dark:bg-gray-600 px-2 py-1 rounded text-gray-600 dark:text-gray-300">3 Cases</span>
            </div>
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 dark:text-gray-400 uppercase bg-gray-50 dark:bg-gray-700 border-b dark:border-gray-600"><tr><th class="px-6 py-3 w-24">ID</th><th class="px-6 py-3">Skenario Pengujian</th><th class="px-6 py-3">Ekspektasi Hasil</th><th class="px-6 py-3">Hasil Aktual / Temuan</th><th class="px-6 py-3 text-center w-24">Status</th></tr></thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <tr><td class="px-6 py-3 font-mono text-xs">TC-001</td><td class="px-6 py-3">Login Kredensial Valid</td><td class="px-6 py-3">Redirect Dashboard, Menu sesuai Role</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-gray-200 dark:border-gray-700 focus:border-indigo-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="auth" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500 dark:bg-gray-700"></td></tr>
                    <tr><td class="px-6 py-3 font-mono text-xs">TC-002</td><td class="px-6 py-3">Login Password Salah</td><td class="px-6 py-3">Pesan error muncul, tetap di login page</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-gray-200 dark:border-gray-700 focus:border-indigo-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="auth" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500 dark:bg-gray-700"></td></tr>
                    <tr><td class="px-6 py-3 font-mono text-xs">TC-003</td><td class="px-6 py-3">Lupa Password (Reset)</td><td class="px-6 py-3">Email reset terkirim, link valid</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-gray-200 dark:border-gray-700 focus:border-indigo-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="auth" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500 dark:bg-gray-700"></td></tr>
                </tbody>
            </table>
        </div>

        <!-- 2. DASHBOARD -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden print-break-inside">
            <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 border-b border-gray-200 dark:border-gray-600 flex justify-between items-center">
                <h3 class="font-bold text-gray-800 dark:text-white flex items-center gap-2"><span class="bg-blue-600 text-white w-6 h-6 rounded flex items-center justify-center text-xs">2</span> Dashboard Monitoring</h3>
                <span class="text-xs font-mono bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 px-2 py-1 rounded">2 Cases</span>
            </div>
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 dark:text-gray-400 uppercase bg-gray-50 dark:bg-gray-700 border-b dark:border-gray-600"><tr><th class="px-6 py-3 w-24">ID</th><th class="px-6 py-3">Skenario Pengujian</th><th class="px-6 py-3">Ekspektasi Hasil</th><th class="px-6 py-3">Hasil Aktual / Temuan</th><th class="px-6 py-3 text-center w-24">Status</th></tr></thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <tr><td class="px-4 py-3 font-mono text-xs w-24">TC-004</td><td class="px-4 py-3">Load Dashboard Admin</td><td class="px-4 py-3">Statistik tampil lengkap < 3 detik</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-gray-200 dark:border-gray-700 focus:border-indigo-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center w-24"><input type="checkbox" data-module="dashboard" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:bg-gray-700"></td></tr>
                    <tr><td class="px-4 py-3 font-mono text-xs w-24">TC-005</td><td class="px-4 py-3">Update Data Real-time</td><td class="px-4 py-3">Counter transaksi bertambah tanpa refresh</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-gray-200 dark:border-gray-700 focus:border-indigo-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center w-24"><input type="checkbox" data-module="dashboard" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:bg-gray-700"></td></tr>
                </tbody>
            </table>
        </div>

        <!-- 3. KEUANGAN -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden print-break-inside">
            <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 border-b border-gray-200 dark:border-gray-600 flex justify-between items-center">
                <h3 class="font-bold text-gray-800 dark:text-white flex items-center gap-2"><span class="bg-amber-500 text-white w-6 h-6 rounded flex items-center justify-center text-xs">3</span> Keuangan (Simpan Pinjam)</h3>
                <span class="text-xs font-mono bg-amber-100 dark:bg-amber-900 text-amber-700 dark:text-amber-300 px-2 py-1 rounded">13 Cases</span>
            </div>
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 dark:text-gray-400 uppercase bg-gray-50 dark:bg-gray-700 border-b dark:border-gray-600"><tr><th class="px-6 py-3 w-24">ID</th><th class="px-6 py-3">Skenario Pengujian</th><th class="px-6 py-3">Ekspektasi Hasil</th><th class="px-6 py-3">Hasil Aktual / Temuan</th><th class="px-6 py-3 text-center w-24">Status</th></tr></thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <tr><td class="px-4 py-3 font-mono text-xs w-24">TC-006</td><td class="px-4 py-3">Cek Saldo Simpanan</td><td class="px-4 py-3">Breakdown Pokok/Wajib/Sukarela tampil</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-gray-200 dark:border-gray-700 focus:border-indigo-500 outline-none" placeholder="..."></td><td class="px-6 py-3 w-24 text-center"><input type="checkbox" data-module="finance" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:bg-gray-700"></td></tr>
                    <tr><td class="px-4 py-3 font-mono text-xs">TC-007</td><td class="px-4 py-3">Riwayat Simpanan</td><td class="px-4 py-3">Detail transaksi & filter tanggal jalan</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-gray-200 dark:border-gray-700 focus:border-indigo-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="finance" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:bg-gray-700"></td></tr>
                    <tr><td class="px-4 py-3 font-mono text-xs">TC-008</td><td class="px-4 py-3">Pengajuan Pinjaman</td><td class="px-4 py-3">Form valid, TTD digital tersimpan</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-gray-200 dark:border-gray-700 focus:border-indigo-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="finance" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:bg-gray-700"></td></tr>
                    <tr><td class="px-4 py-3 font-mono text-xs">TC-009</td><td class="px-4 py-3">Approval Pinjaman</td><td class="px-4 py-3">Status Approved, Email notif terkirim</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-gray-200 dark:border-gray-700 focus:border-indigo-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="finance" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:bg-gray-700"></td></tr>
                    <tr><td class="px-4 py-3 font-mono text-xs">TC-010</td><td class="px-4 py-3">Reject Pinjaman</td><td class="px-4 py-3">Status Ditolak + Alasan wajib diisi</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-gray-200 dark:border-gray-700 focus:border-indigo-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="finance" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:bg-gray-700"></td></tr>
                    <tr><td class="px-4 py-3 font-mono text-xs">TC-011</td><td class="px-4 py-3">Simulasi Bunga Flat</td><td class="px-4 py-3">Hitungan angsuran tetap valid</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-gray-200 dark:border-gray-700 focus:border-indigo-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="finance" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:bg-gray-700"></td></tr>
                    <tr><td class="px-4 py-3 font-mono text-xs">TC-012</td><td class="px-4 py-3">Simulasi Bunga Efektif</td><td class="px-4 py-3">Hitungan angsuran menurun valid</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-gray-200 dark:border-gray-700 focus:border-indigo-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="finance" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:bg-gray-700"></td></tr>
                    <tr><td class="px-4 py-3 font-mono text-xs">TC-013</td><td class="px-4 py-3">Bayar Angsuran Manual</td><td class="px-4 py-3">Saldo pinjaman berkurang, status lunas</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-gray-200 dark:border-gray-700 focus:border-indigo-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="finance" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:bg-gray-700"></td></tr>
                    <tr><td class="px-4 py-3 font-mono text-xs">TC-014</td><td class="px-4 py-3">Deteksi Denda (Overdue)</td><td class="px-4 py-3">Pinjaman telat kena penalti otomatis</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-gray-200 dark:border-gray-700 focus:border-indigo-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="finance" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:bg-gray-700"></td></tr>
                    <tr><td class="px-4 py-3 font-mono text-xs">TC-015</td><td class="px-4 py-3">Request Tarik Saldo</td><td class="px-4 py-3">Status pending approval, saldo hold</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-gray-200 dark:border-gray-700 focus:border-indigo-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="finance" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:bg-gray-700"></td></tr>
                    <tr><td class="px-4 py-3 font-mono text-xs">TC-016</td><td class="px-4 py-3">Approval Penarikan</td><td class="px-4 py-3">Saldo potong permanen setelah transfer</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-gray-200 dark:border-gray-700 focus:border-indigo-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="finance" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:bg-gray-700"></td></tr>
                    <tr><td class="px-4 py-3 font-mono text-xs">TC-017</td><td class="px-4 py-3">Kalkulasi SHU</td><td class="px-4 py-3">Distribusi SHU ke akun member akurat</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-gray-200 dark:border-gray-700 focus:border-indigo-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="finance" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:bg-gray-700"></td></tr>
                    <tr><td class="px-4 py-3 font-mono text-xs">TC-018</td><td class="px-4 py-3">Cetak Slip SHU</td><td class="px-4 py-3">PDF download, QR Code SHU valid</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-gray-200 dark:border-gray-700 focus:border-indigo-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="finance" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:bg-gray-700"></td></tr>
                </tbody>
            </table>
        </div>

        <!-- 4. KOPERASI MART -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden print-break-inside">
            <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 border-b border-gray-200 dark:border-gray-600 flex justify-between items-center">
                <h3 class="font-bold text-gray-800 dark:text-white flex items-center gap-2"><span class="bg-green-600 text-white w-6 h-6 rounded flex items-center justify-center text-xs">4</span> Koperasi Mart</h3>
                <span class="text-xs font-mono bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 px-2 py-1 rounded">17 Cases</span>
            </div>
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 dark:text-gray-400 uppercase bg-gray-50 dark:bg-gray-700 border-b dark:border-gray-600"><tr><th class="px-6 py-3 w-24">ID</th><th class="px-6 py-3">Skenario Pengujian</th><th class="px-6 py-3">Ekspektasi Hasil</th><th class="px-6 py-3">Hasil Aktual / Temuan</th><th class="px-6 py-3 text-center w-24">Status</th></tr></thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <tr><td class="px-4 py-3 font-mono text-xs w-24">TC-019</td><td class="px-4 py-3">POS Scan & Add Item</td><td class="px-4 py-3">Scan Barcode/Search, barang masuk cart</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-gray-200 dark:border-gray-700 focus:border-indigo-500 outline-none" placeholder="..."></td><td class="px-6 py-3 w-24 text-center"><input type="checkbox" data-module="mart" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:bg-gray-700"></td></tr>
                    
                    <!-- POS Payment Testing -->
                    <tr class="bg-green-50 dark:bg-green-900/10"><td class="px-4 py-3 font-mono text-xs font-bold text-green-600">TC-POS1</td><td class="px-4 py-3 font-medium text-green-800 dark:text-green-300">Bayar Tunai (Cash)</td><td class="px-4 py-3">Input nominal bayar > total, Kembalian Muncul Benar</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-green-200 focus:border-green-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="mart" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-green-300 text-green-600 focus:ring-green-500"></td></tr>
                    <tr class="bg-green-50 dark:bg-green-900/10"><td class="px-4 py-3 font-mono text-xs font-bold text-green-600">TC-POS2</td><td class="px-4 py-3 font-medium text-green-800 dark:text-green-300">Bayar Kredit (Limit Cukup)</td><td class="px-4 py-3">Transaksi Sukses, Limit Berkurang, Piutang Bertambah</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-green-200 focus:border-green-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="mart" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-green-300 text-green-600 focus:ring-green-500"></td></tr>
                    <tr class="bg-red-50 dark:bg-red-900/10"><td class="px-4 py-3 font-mono text-xs font-bold text-red-600">TC-POS3</td><td class="px-4 py-3 font-medium text-red-800 dark:text-red-300">Bayar Kredit (Limit KURANG)</td><td class="px-4 py-3">Transaksi DITOLAK (Alert Limit Tidak Cukup)</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-red-200 focus:border-red-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="mart" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-red-300 text-red-600 focus:ring-red-500"></td></tr>
                    
                    <tr><td class="px-4 py-3 font-mono text-xs">TC-021</td><td class="px-4 py-3">POS E-Wallet Midtrans</td><td class="px-4 py-3">QRIS tampil, callback sukses</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-gray-200 dark:border-gray-700 focus:border-indigo-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="mart" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:bg-gray-700"></td></tr>
                    
                    <!-- NEW UAT: 58mm Printing & Dynamic Header -->
                    <tr class="bg-blue-50 dark:bg-blue-900/10"><td class="px-4 py-3 font-mono text-xs font-bold text-blue-600">TC-NEW1</td><td class="px-4 py-3 font-medium text-blue-800 dark:text-blue-300">Cetak Struk 58mm (Thermal)</td><td class="px-4 py-3">Layout pas di kertas 58mm, bersih (tanpa header browser)</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-blue-200 focus:border-blue-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="mart" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-blue-300 text-blue-600 focus:ring-blue-500"></td></tr>
                    <tr class="bg-blue-50 dark:bg-blue-900/10"><td class="px-4 py-3 font-mono text-xs font-bold text-blue-600">TC-NEW2</td><td class="px-4 py-3 font-medium text-blue-800 dark:text-blue-300">Konfigurasi Header Struk</td><td class="px-4 py-3">Header struk mengikuti Nama Profil Koperasi di Settings</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-blue-200 focus:border-blue-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="mart" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-blue-300 text-blue-600 focus:ring-blue-500"></td></tr>

                    <tr><td class="px-4 py-3 font-mono text-xs">TC-022</td><td class="px-4 py-3">Laporan Penjualan</td><td class="px-4 py-3">Filter & Export Excel jalan</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-gray-200 dark:border-gray-700 focus:border-indigo-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="mart" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:bg-gray-700"></td></tr>
                    <tr><td class="px-4 py-3 font-mono text-xs">TC-023</td><td class="px-4 py-3">Laporan Umur Hutang</td><td class="px-4 py-3">Aging piutang (30/60 hari) benar</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-gray-200 dark:border-gray-700 focus:border-indigo-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="mart" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:bg-gray-700"></td></tr>
                    <tr><td class="px-4 py-3 font-mono text-xs">TC-024</td><td class="px-4 py-3">Tambah Produk Baru</td><td class="px-4 py-3">Produk tampil di katalog & search</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-gray-200 dark:border-gray-700 focus:border-indigo-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="mart" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:bg-gray-700"></td></tr>
                    <tr><td class="px-4 py-3 font-mono text-xs">TC-025</td><td class="px-4 py-3">Alert Stok Menipis</td><td class="px-4 py-3">List barang < min stock muncul</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-gray-200 dark:border-gray-700 focus:border-indigo-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="mart" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:bg-gray-700"></td></tr>
                    <tr><td class="px-4 py-3 font-mono text-xs">TC-026</td><td class="px-4 py-3">Stock Opname Fisik</td><td class="px-4 py-3">Selisih varian stok terhitung auto</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-gray-200 dark:border-gray-700 focus:border-indigo-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="mart" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:bg-gray-700"></td></tr>
                    <tr><td class="px-4 py-3 font-mono text-xs">TC-027</td><td class="px-4 py-3">Buat Purchase Order (PO)</td><td class="px-4 py-3">Dokumen PO PDF terbentuk</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-gray-200 dark:border-gray-700 focus:border-indigo-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="mart" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:bg-gray-700"></td></tr>
                    <tr><td class="px-4 py-3 font-mono text-xs">TC-028</td><td class="px-4 py-3">Terima Barang (GRN)</td><td class="px-4 py-3">Stok bertambah, hutang dagang +</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-gray-200 dark:border-gray-700 focus:border-indigo-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="mart" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:bg-gray-700"></td></tr>
                    <tr><td class="px-4 py-3 font-mono text-xs">TC-029</td><td class="px-4 py-3">Catat Biaya Toko</td><td class="px-4 py-3">Biaya terekam & bukti foto terupload</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-gray-200 dark:border-gray-700 focus:border-indigo-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="mart" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:bg-gray-700"></td></tr>
                    <tr><td class="px-4 py-3 font-mono text-xs">TC-030</td><td class="px-4 py-3">Konsinyasi (Titip Jual)</td><td class="px-4 py-3">Stok titipan & bagi hasil valid</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-gray-200 dark:border-gray-700 focus:border-indigo-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="mart" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:bg-gray-700"></td></tr>
                    <tr><td class="px-4 py-3 font-mono text-xs">TC-031</td><td class="px-4 py-3">Voucher Promo</td><td class="px-4 py-3">Diskon memotong harga POS</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-gray-200 dark:border-gray-700 focus:border-indigo-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="mart" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:bg-gray-700"></td></tr>
                </tbody>
            </table>
        </div>

        <!-- 5. BELANJA -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden print-break-inside">
            <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 border-b border-gray-200 dark:border-gray-600 flex justify-between items-center">
                <h3 class="font-bold text-gray-800 dark:text-white flex items-center gap-2"><span class="bg-pink-600 text-white w-6 h-6 rounded flex items-center justify-center text-xs">5</span> Belanja Online</h3>
                <span class="text-xs font-mono bg-pink-100 dark:bg-pink-900 text-pink-700 dark:text-pink-300 px-2 py-1 rounded">7 Cases</span>
            </div>
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 dark:text-gray-400 uppercase bg-gray-50 dark:bg-gray-700 border-b dark:border-gray-600"><tr><th class="px-6 py-3 w-24">ID</th><th class="px-6 py-3">Skenario Pengujian</th><th class="px-6 py-3">Ekspektasi Hasil</th><th class="px-6 py-3">Hasil Aktual / Temuan</th><th class="px-6 py-3 text-center w-24">Status</th></tr></thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <tr><td class="px-4 py-3 font-mono text-xs w-24">TC-032</td><td class="px-4 py-3">Katalog & Search</td><td class="px-4 py-3">Produk item ditemukan</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-gray-200 dark:border-gray-700 focus:border-indigo-500 outline-none" placeholder="..."></td><td class="px-6 py-3 w-24 text-center"><input type="checkbox" data-module="shopping" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:bg-gray-700"></td></tr>
                    
                    <!-- Payment Methods Testing -->
                    <tr class="bg-pink-50 dark:bg-pink-900/10"><td class="px-4 py-3 font-mono text-xs font-bold text-pink-600">TC-PAY1</td><td class="px-4 py-3 font-medium text-pink-800 dark:text-pink-300">Checkout: Saldo Simpanan</td><td class="px-4 py-3">Order Sukses, Saldo Simpanan Sukarela terpotong</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-pink-200 focus:border-pink-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="shopping" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-pink-300 text-pink-600 focus:ring-pink-500"></td></tr>
                    <tr class="bg-pink-50 dark:bg-pink-900/10"><td class="px-4 py-3 font-mono text-xs font-bold text-pink-600">TC-PAY2</td><td class="px-4 py-3 font-medium text-pink-800 dark:text-pink-300">Checkout: Kredit (Limit)</td><td class="px-4 py-3">Order Sukses, Limit Berkurang, Masuk Piutang</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-pink-200 focus:border-pink-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="shopping" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-pink-300 text-pink-600 focus:ring-pink-500"></td></tr>
                    <tr class="bg-pink-50 dark:bg-pink-900/10"><td class="px-4 py-3 font-mono text-xs font-bold text-pink-600">TC-PAY3</td><td class="px-4 py-3 font-medium text-pink-800 dark:text-pink-300">Checkout: Transfer/QRIS</td><td class="px-4 py-3">Redirect Payment Gateway / Upload Bukti Sukses</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-pink-200 focus:border-pink-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="shopping" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-pink-300 text-pink-600 focus:ring-pink-500"></td></tr>
                    <tr class="bg-pink-50 dark:bg-pink-900/10"><td class="px-4 py-3 font-mono text-xs font-bold text-pink-600">TC-PAY4</td><td class="px-4 py-3 font-medium text-pink-800 dark:text-pink-300">Checkout: COD / Tunai</td><td class="px-4 py-3">Order terbentuk status Unpaid (Bayar saat ambil)</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-pink-200 focus:border-pink-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="shopping" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-pink-300 text-pink-600 focus:ring-pink-500"></td></tr>

                    <tr><td class="px-4 py-3 font-mono text-xs">TC-033</td><td class="px-4 py-3">Pre-Order Stok Kosong</td><td class="px-4 py-3">Request PO tersimpan</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-gray-200 dark:border-gray-700 focus:border-indigo-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="shopping" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:bg-gray-700"></td></tr>
                    <tr><td class="px-4 py-3 font-mono text-xs">TC-034</td><td class="px-4 py-3">Riwayat Belanja</td><td class="px-4 py-3">History tampil, Struk PDF download</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-gray-200 dark:border-gray-700 focus:border-indigo-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="shopping" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:bg-gray-700"></td></tr>
                </tbody>
            </table>
        </div>

        <!-- 6. LAPORAN & INFO -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden print-break-inside">
            <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 border-b border-gray-200 dark:border-gray-600 flex justify-between items-center">
                <h3 class="font-bold text-gray-800 dark:text-white flex items-center gap-2"><span class="bg-purple-600 text-white w-6 h-6 rounded flex items-center justify-center text-xs">6</span> Laporan & Info</h3>
                <span class="text-xs font-mono bg-purple-100 dark:bg-purple-900 text-purple-700 dark:text-purple-300 px-2 py-1 rounded">15 Cases</span>
            </div>
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 dark:text-gray-400 uppercase bg-gray-50 dark:bg-gray-700 border-b dark:border-gray-600"><tr><th class="px-6 py-3 w-24">ID</th><th class="px-6 py-3">Skenario Pengujian</th><th class="px-6 py-3">Ekspektasi Hasil</th><th class="px-6 py-3">Hasil Aktual / Temuan</th><th class="px-6 py-3 text-center w-24">Status</th></tr></thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <!-- Pusat Laporan: Operasional -->
                    <tr class="bg-purple-50 dark:bg-purple-900/10"><td class="px-4 py-3 font-mono text-xs font-bold text-purple-600">TC-RPT1</td><td class="px-4 py-3 font-medium text-purple-800 dark:text-purple-300">Laporan Anggota</td><td class="px-4 py-3">Demografi & Status Keaktifan Tampil Akurat</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-purple-200 focus:border-purple-500 outline-none" placeholder="..."></td><td class="px-6 py-3 w-24 text-center"><input type="checkbox" data-module="report" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-purple-300 text-purple-600 focus:ring-purple-500"></td></tr>
                    <tr class="bg-purple-50 dark:bg-purple-900/10"><td class="px-4 py-3 font-mono text-xs font-bold text-purple-600">TC-RPT2</td><td class="px-4 py-3 font-medium text-purple-800 dark:text-purple-300">Laporan Simpanan</td><td class="px-4 py-3">Rekap arus kas masuk/keluar per jenis valid</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-purple-200 focus:border-purple-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="report" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-purple-300 text-purple-600 focus:ring-purple-500"></td></tr>
                    <tr class="bg-purple-50 dark:bg-purple-900/10"><td class="px-4 py-3 font-mono text-xs font-bold text-purple-600">TC-RPT3</td><td class="px-4 py-3 font-medium text-purple-800 dark:text-purple-300">Laporan Pinjaman</td><td class="px-4 py-3">Data Penyaluran & Kredit Macet (NPL) muncul</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-purple-200 focus:border-purple-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="report" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-purple-300 text-purple-600 focus:ring-purple-500"></td></tr>
                    <tr class="bg-purple-50 dark:bg-purple-900/10"><td class="px-4 py-3 font-mono text-xs font-bold text-purple-600">TC-RPT4</td><td class="px-4 py-3 font-medium text-purple-800 dark:text-purple-300">Laporan Transaksi Mart</td><td class="px-4 py-3">Statistik penjualan & produk terlaris valid</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-purple-200 focus:border-purple-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="report" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-purple-300 text-purple-600 focus:ring-purple-500"></td></tr>
                    <tr class="bg-purple-50 dark:bg-purple-900/10"><td class="px-4 py-3 font-mono text-xs font-bold text-purple-600">TC-RPT5</td><td class="px-4 py-3 font-medium text-purple-800 dark:text-purple-300">Laporan Kredit & Belanja</td><td class="px-4 py-3">Status tagihan kredit toko member sesuai</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-purple-200 focus:border-purple-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="report" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-purple-300 text-purple-600 focus:ring-purple-500"></td></tr>

                    <!-- Pusat Laporan: Akuntansi -->
                    <tr class="bg-gray-50 dark:bg-gray-800"><td class="px-4 py-3 font-mono text-xs font-bold">TC-RPT6</td><td class="px-4 py-3 font-medium">Buku Besar (General Ledger)</td><td class="px-4 py-3">Mutasi debit/kredit per akun balance</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-gray-200 dark:border-gray-700 focus:border-indigo-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="report" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:bg-gray-700"></td></tr>
                    <tr class="bg-gray-50 dark:bg-gray-800"><td class="px-4 py-3 font-mono text-xs font-bold">TC-RPT7</td><td class="px-4 py-3 font-medium">Neraca Saldo (Trial Balance)</td><td class="px-4 py-3">Saldo akhir debit = kredit (Seimbang)</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-gray-200 dark:border-gray-700 focus:border-indigo-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="report" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:bg-gray-700"></td></tr>
                    <tr class="bg-gray-50 dark:bg-gray-800"><td class="px-4 py-3 font-mono text-xs font-bold">TC-RPT8</td><td class="px-4 py-3 font-medium">Laba Rugi (Income Stmt)</td><td class="px-4 py-3">Pendapatan - Biaya = SHU berjalan</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-gray-200 dark:border-gray-700 focus:border-indigo-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="report" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:bg-gray-700"></td></tr>
                    <tr class="bg-gray-50 dark:bg-gray-800"><td class="px-4 py-3 font-mono text-xs font-bold">TC-035</td><td class="px-4 py-3 font-medium">Neraca (Balance Sheet)</td><td class="px-4 py-3">Aktiva = Pasiva (Kewajiban + Modal)</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-gray-200 dark:border-gray-700 focus:border-indigo-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="report" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:bg-gray-700"></td></tr>
                    
                    <!-- Lainnya -->
                    <tr><td class="px-4 py-3 font-mono text-xs">TC-036</td><td class="px-4 py-3">Broadcast Pengumuman</td><td class="px-4 py-3">Notifikasi masuk dashboard member</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-gray-200 dark:border-gray-700 focus:border-indigo-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="report" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:bg-gray-700"></td></tr>
                    <tr><td class="px-4 py-3 font-mono text-xs">TC-037</td><td class="px-4 py-3">Dokumen AD/ART</td><td class="px-4 py-3">File PDF terunduh sempurna</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-gray-200 dark:border-gray-700 focus:border-indigo-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="report" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:bg-gray-700"></td></tr>
                    <tr><td class="px-4 py-3 font-mono text-xs">TC-038</td><td class="px-4 py-3">Lapor Bug UAT</td><td class="px-4 py-3">Tiket ID terbentuk, admin terima notif</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-gray-200 dark:border-gray-700 focus:border-indigo-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="report" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:bg-gray-700"></td></tr>
                    <tr><td class="px-4 py-3 font-mono text-xs">TC-039</td><td class="px-4 py-3">Voting Online</td><td class="px-4 py-3">Vote 1x per user, hasil update</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-gray-200 dark:border-gray-700 focus:border-indigo-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="report" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:bg-gray-700"></td></tr>
                    <tr><td class="px-4 py-3 font-mono text-xs">TC-040</td><td class="px-4 py-3">Aspirasi Anggota (Pengadaan)</td><td class="px-4 py-3">Input Estimasi Harga Valid, Admin Omset Muncul</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-gray-200 dark:border-gray-700 focus:border-indigo-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="report" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:bg-gray-700"></td></tr>
                </tbody>
            </table>
        </div>

        <!-- 7. KEPENGURUSAN -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden print-break-inside">
            <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 border-b border-gray-200 dark:border-gray-600 flex justify-between items-center">
                <h3 class="font-bold text-gray-800 dark:text-white flex items-center gap-2"><span class="bg-red-600 text-white w-6 h-6 rounded flex items-center justify-center text-xs">7</span> Kepengurusan</h3>
                <span class="text-xs font-mono bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300 px-2 py-1 rounded">9 Cases</span>
            </div>
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 dark:text-gray-400 uppercase bg-gray-50 dark:bg-gray-700 border-b dark:border-gray-600"><tr><th class="px-6 py-3 w-24">ID</th><th class="px-6 py-3">Skenario Pengujian</th><th class="px-6 py-3">Ekspektasi Hasil</th><th class="px-6 py-3">Hasil Aktual / Temuan</th><th class="px-6 py-3 text-center w-24">Status</th></tr></thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <tr><td class="px-4 py-3 font-mono text-xs w-24">TC-041</td><td class="px-4 py-3">Register Anggota</td><td class="px-4 py-3">Data save, Kartu QR generate</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-gray-200 dark:border-gray-700 focus:border-indigo-500 outline-none" placeholder="..."></td><td class="px-6 py-3 w-24 text-center"><input type="checkbox" data-module="org" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:bg-gray-700"></td></tr>
                    <tr><td class="px-4 py-3 font-mono text-xs">TC-042</td><td class="px-4 py-3">Import Excel Anggota</td><td class="px-4 py-3">Data massal (10 user) masuk DB</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-gray-200 dark:border-gray-700 focus:border-indigo-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="org" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:bg-gray-700"></td></tr>
                    <tr><td class="px-4 py-3 font-mono text-xs">TC-043</td><td class="px-4 py-3">Inventaris Aset</td><td class="px-4 py-3">Aset tercatat dengan nilai buku</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-gray-200 dark:border-gray-700 focus:border-indigo-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="org" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:bg-gray-700"></td></tr>
                    <tr><td class="px-4 py-3 font-mono text-xs">TC-044</td><td class="px-4 py-3">Notulen Rapat</td><td class="px-4 py-3">Dokumen rapat tersimpan</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-gray-200 dark:border-gray-700 focus:border-indigo-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="org" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:bg-gray-700"></td></tr>
                    <tr><td class="px-4 py-3 font-mono text-xs">TC-046</td><td class="px-4 py-3">Surat Resmi QR</td><td class="px-4 py-3">No. Surat urut, QR Code valid</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-gray-200 dark:border-gray-700 focus:border-indigo-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="org" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:bg-gray-700"></td></tr>
                    <tr><td class="px-4 py-3 font-mono text-xs">TC-047</td><td class="px-4 py-3">Edit Arsip Surat</td><td class="px-4 py-3">No. Surat tidak berubah (konsisten)</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-gray-200 dark:border-gray-700 focus:border-indigo-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="org" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:bg-gray-700"></td></tr>
                    <tr><td class="px-4 py-3 font-mono text-xs">TC-048</td><td class="px-4 py-3">Jurnal Umum Manual</td><td class="px-4 py-3">Jurnal balance tersimpan</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-gray-200 dark:border-gray-700 focus:border-indigo-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="org" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:bg-gray-700"></td></tr>
                    <tr><td class="px-4 py-3 font-mono text-xs">TC-049</td><td class="px-4 py-3">Rekonsiliasi Bank</td><td class="px-4 py-3">Auto-match mutasi bank sukses</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-gray-200 dark:border-gray-700 focus:border-indigo-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="org" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:bg-gray-700"></td></tr>
                </tbody>
            </table>
        </div>

        <!-- 8. ADMINISTRASI -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden print-break-inside">
            <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 border-b border-gray-200 dark:border-gray-600 flex justify-between items-center">
                <h3 class="font-bold text-gray-800 dark:text-white flex items-center gap-2"><span class="bg-gray-600 text-white w-6 h-6 rounded flex items-center justify-center text-xs">8</span> Administrasi</h3>
                <span class="text-xs font-mono bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 px-2 py-1 rounded">11 Cases</span>
            </div>
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 dark:text-gray-400 uppercase bg-gray-50 dark:bg-gray-700 border-b dark:border-gray-600"><tr><th class="px-6 py-3 w-24">ID</th><th class="px-6 py-3">Skenario Pengujian</th><th class="px-6 py-3">Ekspektasi Hasil</th><th class="px-6 py-3">Hasil Aktual / Temuan</th><th class="px-6 py-3 text-center w-24">Status</th></tr></thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <!-- Dashboard Monitoring Scenarios -->
                    <tr class="bg-gray-100 dark:bg-gray-900/50"><td class="px-4 py-3 font-mono text-xs font-bold text-gray-600">TC-MON1</td><td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-300">Mon: Keuangan Realtime</td><td class="px-4 py-3">Widget Total Aset, SHU, Cashflow sinkron dengan Jurnal</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-gray-300 focus:border-gray-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="admin" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-gray-400 text-gray-700 focus:ring-gray-600"></td></tr>
                    <tr class="bg-gray-100 dark:bg-gray-900/50"><td class="px-4 py-3 font-mono text-xs font-bold text-gray-600">TC-MON2</td><td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-300">Mon: Stok Kritis (Mart)</td><td class="px-4 py-3">Alert/List barang stok < minimum tampil valid</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-gray-300 focus:border-gray-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="admin" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-gray-400 text-gray-700 focus:ring-gray-600"></td></tr>
                    <tr class="bg-gray-100 dark:bg-gray-900/50"><td class="px-4 py-3 font-mono text-xs font-bold text-gray-600">TC-MON3</td><td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-300">Mon: Kredit Jatuh Tempo</td><td class="px-4 py-3">List member telat bayar / NPL muncul (Early Warning)</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-gray-300 focus:border-gray-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="admin" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-gray-400 text-gray-700 focus:ring-gray-600"></td></tr>
                    <tr class="bg-gray-100 dark:bg-gray-900/50"><td class="px-4 py-3 font-mono text-xs font-bold text-gray-600">TC-MON4</td><td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-300">Mon: Grafik Kinerja</td><td class="px-4 py-3">Grafik Tren Penjualan & Anggota interaktif & load data</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-gray-300 focus:border-gray-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="admin" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-gray-400 text-gray-700 focus:ring-gray-600"></td></tr>
                    
                    <tr><td class="px-4 py-3 font-mono text-xs w-24">TC-050</td><td class="px-4 py-3">Tambah Akun COA</td><td class="px-4 py-3">Akun baru aktif di jurnal</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-gray-200 dark:border-gray-700 focus:border-indigo-500 outline-none" placeholder="..."></td><td class="px-6 py-3 w-24 text-center"><input type="checkbox" data-module="admin" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:bg-gray-700"></td></tr>
                    <tr><td class="px-4 py-3 font-mono text-xs">TC-051</td><td class="px-4 py-3">Role & Permissions</td><td class="px-4 py-3">Akses tanpa hak ditolak (403)</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-gray-200 dark:border-gray-700 focus:border-indigo-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="admin" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:bg-gray-700"></td></tr>
                    <tr><td class="px-4 py-3 font-mono text-xs">TC-052</td><td class="px-4 py-3">Setting Identitas</td><td class="px-4 py-3">Logo & Nama Koperasi update</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-gray-200 dark:border-gray-700 focus:border-indigo-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="admin" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:bg-gray-700"></td></tr>
                    <tr><td class="px-4 py-3 font-mono text-xs">TC-053</td><td class="px-4 py-3">Backup Database</td><td class="px-4 py-3">File SQL terdownload</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-gray-200 dark:border-gray-700 focus:border-indigo-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="admin" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:bg-gray-700"></td></tr>
                    <tr><td class="px-4 py-3 font-mono text-xs">TC-054</td><td class="px-4 py-3">Restore Database</td><td class="px-4 py-3">Data kembali ke point backup</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-gray-200 dark:border-gray-700 focus:border-indigo-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="admin" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:bg-gray-700"></td></tr>
                    <tr><td class="px-4 py-3 font-mono text-xs">TC-055</td><td class="px-4 py-3">Audit Log</td><td class="px-4 py-3">Aktivitas user tercatat detail</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-gray-200 dark:border-gray-700 focus:border-indigo-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="admin" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:bg-gray-700"></td></tr>
                    <tr><td class="px-4 py-3 font-mono text-xs">TC-056</td><td class="px-4 py-3">Test Koneksi Midtrans</td><td class="px-4 py-3">Status API Connected</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-gray-200 dark:border-gray-700 focus:border-indigo-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="admin" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:bg-gray-700"></td></tr>
                </tbody>
            </table>
        </div>

        <!-- 9. CROSS-FUNCTIONAL -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden print-break-inside">
            <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 border-b border-gray-200 dark:border-gray-600 flex justify-between items-center">
                <h3 class="font-bold text-gray-800 dark:text-white flex items-center gap-2"><span class="bg-teal-600 text-white w-6 h-6 rounded flex items-center justify-center text-xs">9</span> Cross Functional & Security</h3>
                <span class="text-xs font-mono bg-teal-100 dark:bg-teal-900 text-teal-700 dark:text-teal-300 px-2 py-1 rounded">7 Cases</span>
            </div>
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 dark:text-gray-400 uppercase bg-gray-50 dark:bg-gray-700 border-b dark:border-gray-600"><tr><th class="px-6 py-3 w-24">ID</th><th class="px-6 py-3">Skenario Pengujian</th><th class="px-6 py-3">Ekspektasi Hasil</th><th class="px-6 py-3">Hasil Aktual / Temuan</th><th class="px-6 py-3 text-center w-24">Status</th></tr></thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <tr><td class="px-4 py-3 font-mono text-xs w-24">TC-057</td><td class="px-4 py-3">E2E Anggota Baru</td><td class="px-4 py-3">Flow Daftar -> Simpan -> Pinjam -> Belanja</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-gray-200 dark:border-gray-700 focus:border-indigo-500 outline-none" placeholder="..."></td><td class="px-6 py-3 w-24 text-center"><input type="checkbox" data-module="cross" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:bg-gray-700"></td></tr>
                    
                    <!-- Critical Non-Functional -->
                    <tr class="bg-teal-50 dark:bg-teal-900/10"><td class="px-4 py-3 font-mono text-xs font-bold text-teal-600">TC-SEC1</td><td class="px-4 py-3 font-medium text-teal-800 dark:text-teal-300">Security: Role Access</td><td class="px-4 py-3">Member akses URL /admin wajib DITOLAK (403)</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-teal-200 focus:border-teal-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="cross" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-teal-300 text-teal-600 focus:ring-teal-500"></td></tr>
                    <tr class="bg-teal-50 dark:bg-teal-900/10"><td class="px-4 py-3 font-mono text-xs font-bold text-teal-600">TC-PWA1</td><td class="px-4 py-3 font-medium text-teal-800 dark:text-teal-300">Mobile PWA Install</td><td class="px-4 py-3">Bisa 'Add to Home Screen' & Icon muncul</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-teal-200 focus:border-teal-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="cross" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-teal-300 text-teal-600 focus:ring-teal-500"></td></tr>
                    <tr class="bg-teal-50 dark:bg-teal-900/10"><td class="px-4 py-3 font-mono text-xs font-bold text-teal-600">TC-NOT1</td><td class="px-4 py-3 font-medium text-teal-800 dark:text-teal-300">Notifikasi WA/Email</td><td class="px-4 py-3">Pesan WA/Email masuk saat transaksi terjadi</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-teal-200 focus:border-teal-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="cross" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-teal-300 text-teal-600 focus:ring-teal-500"></td></tr>
                    <tr><td class="px-4 py-3 font-mono text-xs">TC-058</td><td class="px-4 py-3">E2E Rapat Tahunan</td><td class="px-4 py-3">Flow Laporan -> SHU -> Voting</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-gray-200 dark:border-gray-700 focus:border-indigo-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="cross" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:bg-gray-700"></td></tr>
                    <tr><td class="px-4 py-3 font-mono text-xs">TC-059</td><td class="px-4 py-3">Performance Test</td><td class="px-4 py-3">10 User load, server stabil</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-gray-200 dark:border-gray-700 focus:border-indigo-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="cross" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:bg-gray-700"></td></tr>
                    <tr><td class="px-4 py-3 font-mono text-xs">TC-060</td><td class="px-4 py-3">Security Test</td><td class="px-4 py-3">Akses URL paksa diblokir</td><td class="px-4 py-3"><input type="text" class="w-full text-xs bg-transparent border-b border-gray-200 dark:border-gray-700 focus:border-indigo-500 outline-none" placeholder="..."></td><td class="px-6 py-3 text-center"><input type="checkbox" data-module="cross" @change="updateCount()" class="uat-checkbox w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:bg-gray-700"></td></tr>
                </tbody>
            </table>
        </div>

    </div>

    <!-- FINAL CONCLUSION & SIGN-OFF SECTION -->
    <div class="mt-6 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-2xl p-8 shadow-xl sign-block border border-gray-200 dark:border-gray-700">
        <h3 class="text-xl font-bold mb-6 flex items-center gap-2">
            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            UAT CONCLUSION & SIGN-OFF
        </h3>

        <!-- 1. Ringkasan Hasil Per Modul (Moved inside) -->
        <div class="mb-8">
            <p class="text-sm font-bold text-gray-500 dark:text-gray-400 mb-4 uppercase tracking-wider">I. Ringkasan Hasil Per Modul</p>
            <template x-if="total > 0">
                <table class="w-full text-sm text-left border dark:border-gray-700 rounded-lg overflow-hidden">
                    <thead class="text-xs text-gray-700 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 uppercase">
                        <tr>
                            <th class="px-4 py-2">Nama Modul</th>
                            <th class="px-4 py-2 text-center">Total</th>
                            <th class="px-4 py-2 text-center">Pass</th>
                            <th class="px-4 py-2 text-center">Fail</th>
                            <th class="px-4 py-2 text-center">Completion</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 dark:text-gray-300">
                        <template x-for="mod in moduleStats" :key="mod.id">
                            <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-4 py-2 font-medium text-gray-900 dark:text-white" x-text="mod.name"></td>
                                <td class="px-4 py-2 text-center" x-text="mod.total"></td>
                                <td class="px-4 py-2 text-center text-green-600 dark:text-green-400 font-bold" x-text="mod.checked"></td>
                                <td class="px-4 py-2 text-center text-red-600 dark:text-red-400" x-text="mod.total - mod.checked"></td>
                                <td class="px-4 py-2 text-center">
                                    <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-600">
                                        <div class="bg-blue-600 h-2.5 rounded-full" 
                                             :style="`width: ${mod.total > 0 ? (mod.checked/mod.total)*100 : 0}%`"></div>
                                    </div>
                                    <span class="text-[10px] text-gray-500" x-text="mod.total > 0 ? Math.round((mod.checked/mod.total)*100) + '%' : '0%'"></span>
                                </td>
                            </tr>
                        </template>
                        <tr class="bg-gray-100 dark:bg-gray-900 font-bold border-t dark:border-gray-700">
                            <td class="px-4 py-2 text-gray-900 dark:text-white uppercase">Total Keseluruhan</td>
                            <td class="px-4 py-2 text-center" x-text="total"></td>
                            <td class="px-4 py-2 text-center text-green-700" x-text="checked"></td>
                            <td class="px-4 py-2 text-center text-red-700" x-text="total - checked"></td>
                            <td class="px-4 py-2 text-center" x-text="percentage + '%'"></td>
                        </tr>
                    </tbody>
                </table>
            </template>
        </div>

        <p class="text-sm font-bold text-gray-500 dark:text-gray-400 mb-4 uppercase tracking-wider">II. Sertifikasi & Persetujuan</p>

        <!-- Progress Bar -->
        <div class="mb-6">
            <div class="flex justify-between text-sm mb-2 text-gray-500">
                <span>Total Progress Pengujian</span>
                <span><span x-text="percentage"></span>% Selesai</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden">
                <div class="bg-gradient-to-r from-green-500 to-blue-600 h-4 rounded-full transition-all duration-500" :style="`width: ${percentage}%`"></div>
            </div>
        </div>

        <!-- Stats Section (Forced 1 row 4 columns using Table for Print Robustness) -->
        <table class="w-full table-fixed border-separate border-spacing-2 -ml-2 mb-4">
            <tr>
                <td class="w-1/4">
                    <div class="bg-gray-50 dark:bg-gray-900 p-2 rounded-lg border border-gray-100 dark:border-gray-800 text-center">
                        <p class="text-gray-500 text-[9px] uppercase font-bold tracking-tight mb-1">Total Kasus</p>
                        <p class="text-xl font-bold" x-text="total">60</p>
                    </div>
                </td>
                <td class="w-1/4">
                    <div class="bg-gray-50 dark:bg-gray-900 p-2 rounded-lg border border-gray-100 dark:border-gray-800 text-center">
                        <p class="text-green-600 text-[9px] uppercase font-bold tracking-tight mb-1">Lulus (OK)</p>
                        <p class="text-xl font-bold text-green-600" x-text="checked">0</p>
                    </div>
                </td>
                <td class="w-1/4">
                    <div class="bg-gray-50 dark:bg-gray-900 p-2 rounded-lg border border-gray-100 dark:border-gray-800 text-center">
                        <p class="text-red-500 text-[9px] uppercase font-bold tracking-tight mb-1">Gagal/Pending</p>
                        <p class="text-xl font-bold text-red-500" x-text="total - checked">60</p>
                    </div>
                </td>
                <td class="w-1/4">
                    <div class="bg-gray-50 dark:bg-gray-900 p-2 rounded-lg border border-gray-100 dark:border-gray-800 text-center">
                        <p class="text-gray-500 text-[9px] uppercase font-bold tracking-tight mb-1">Status Akhir</p>
                        <p class="text-[11px] font-bold leading-tight pt-1" x-text="percentage == 100 ? 'READY' : 'IN PROGRESS'" :class="percentage == 100 ? 'text-green-600' : 'text-yellow-600'"></p>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Notes Area (Moved up and merged closer) -->
        <div class="mb-6 print-no-break">
            <label class="block text-[11px] text-gray-500 mb-1 font-bold italic">Catatan Kesimpulan Akhir (Tester Notes):</label>
            <textarea class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white p-3 h-20 text-sm focus:ring-green-500 focus:border-green-500 transition-all font-sans" placeholder="Tuliskan kesimpulan di sini..."></textarea>
        </div>

        <!-- Balanced & Proportional Sign-Off Section -->
        <div class="mt-6 pt-6 border-t border-gray-200">
            
            <!-- SECTION 1: TIM PENGUJI -->
            <table class="w-full mb-8">
                <thead>
                    <tr>
                        <th colspan="2" class="text-center text-xs font-bold text-gray-500 uppercase tracking-widest pb-6 underline">
                            Tim Penguji (Tester)
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="w-1/2 text-center pb-8">
                            <div class="h-20 flex flex-col justify-end">
                                <span class="text-[9px] text-gray-400 italic mb-1">Sign here</span>
                                <div class="border-b border-dashed border-gray-300 w-3/4 mx-auto mb-2"></div>
                            </div>
                            <p class="text-xs font-bold text-gray-900 border-b border-gray-800 inline-block w-3/4">Tester 1</p>
                        </td>
                        <td class="w-1/2 text-center pb-8">
                            <div class="h-20 flex flex-col justify-end">
                                <span class="text-[9px] text-gray-400 italic mb-1">Sign here</span>
                                <div class="border-b border-dashed border-gray-300 w-3/4 mx-auto mb-2"></div>
                            </div>
                            <p class="text-xs font-bold text-gray-900 border-b border-gray-800 inline-block w-3/4">Tester 2</p>
                        </td>
                    </tr>
                    <tr>
                        <td class="w-1/2 text-center pb-6">
                            <div class="h-20 flex flex-col justify-end">
                                <span class="text-[9px] text-gray-400 italic mb-1">Sign here</span>
                                <div class="border-b border-dashed border-gray-300 w-3/4 mx-auto mb-2"></div>
                            </div>
                            <p class="text-xs font-bold text-gray-900 border-b border-gray-800 inline-block w-3/4">Tester 3</p>
                        </td>
                        <td class="w-1/2 text-center pb-6">
                            <div class="h-20 flex flex-col justify-end">
                                <span class="text-[9px] text-gray-400 italic mb-1">Sign here</span>
                                <div class="border-b border-dashed border-gray-300 w-3/4 mx-auto mb-2"></div>
                            </div>
                            <p class="text-xs font-bold text-gray-900 border-b border-gray-800 inline-block w-3/4">Tester 4</p>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- SECTION 2: DEVELOPER & PENGAWAS -->
            <table class="w-full mb-8 pt-6 border-t border-gray-100">
                <tbody>
                    <tr>
                        <td class="w-1/2 text-center pb-8">
                            <p class="text-[10px] font-bold text-gray-500 uppercase mb-4">Developer</p>
                            <div class="h-16 flex flex-col justify-end">
                                <span class="text-[9px] text-gray-400 italic mb-1">Sign here</span>
                                <div class="border-b border-dashed border-gray-300 w-3/4 mx-auto mb-2"></div>
                            </div>
                            <p class="text-xs font-bold text-gray-900 border-b border-gray-800 inline-block w-3/4">System Developer</p>
                        </td>
                        <td class="w-1/2 text-center pb-8">
                            <p class="text-[10px] font-bold text-gray-500 uppercase mb-4">Pengawas 1</p>
                            <div class="h-16 flex flex-col justify-end">
                                <span class="text-[9px] text-gray-400 italic mb-1">Sign here</span>
                                <div class="border-b border-dashed border-gray-300 w-3/4 mx-auto mb-2"></div>
                            </div>
                            <p class="text-xs font-bold text-gray-900 border-b border-gray-800 inline-block w-3/4">Nama Pengawas</p>
                        </td>
                    </tr>
                    <tr>
                        <td class="w-1/2 text-center pb-6">
                            <p class="text-[10px] font-bold text-gray-500 uppercase mb-4">Pengawas 2</p>
                            <div class="h-16 flex flex-col justify-end">
                                <span class="text-[9px] text-gray-400 italic mb-1">Sign here</span>
                                <div class="border-b border-dashed border-gray-300 w-3/4 mx-auto mb-2"></div>
                            </div>
                            <p class="text-xs font-bold text-gray-900 border-b border-gray-800 inline-block w-3/4">Nama Pengawas</p>
                        </td>
                        <td class="w-1/2 text-center pb-6">
                            <p class="text-[10px] font-bold text-gray-500 uppercase mb-4">Pengawas 3</p>
                            <div class="h-16 flex flex-col justify-end">
                                <span class="text-[9px] text-gray-400 italic mb-1">Sign here</span>
                                <div class="border-b border-dashed border-gray-300 w-3/4 mx-auto mb-2"></div>
                            </div>
                            <p class="text-xs font-bold text-gray-900 border-b border-gray-800 inline-block w-3/4">Nama Pengawas</p>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- SECTION 3: PEMBINA & QR FOOTER (Balanced together) -->
            <div class="print-no-break">
                <table class="w-full pt-6 border-t border-gray-100 mb-6">
                    <tbody>
                        <tr>
                            <td class="text-center">
                                <p class="text-[10px] font-bold text-gray-500 uppercase mb-4">Menyetujui, Pembina</p>
                                <div class="h-20 flex flex-col justify-end">
                                    <span class="text-[9px] text-gray-400 italic mb-1">Sign here</span>
                                    <div class="border-b border-dashed border-gray-300 w-1/3 mx-auto mb-2"></div>
                                </div>
                                <p class="text-xs font-bold text-gray-900 border-b border-gray-800 inline-block w-1/3 font-serif uppercase tracking-tighter">Ketua Pembina / Manager</p>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- DIGITAL VALIDATION FOOTER (Balanced) -->
                <div class="mt-4 pt-6 border-t-2 border-gray-100 flex items-center gap-6 footer-validation">
                    <div class="flex-shrink-0 bg-white p-2 border border-gray-200 rounded-lg">
                        @php
                            $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=" . urlencode(url()->current());
                        @endphp
                        <img src="{{ $qrUrl }}" alt="QR Validation" class="w-16 h-16 grayscale">
                    </div>
                    <div class="text-xs text-gray-600 font-serif leading-relaxed">
                        <p class="font-bold text-gray-900 mb-1 uppercase">Sertifikasi Sah Digital Koperasi Spindo</p>
                        <p>Dokumen ini diterbitkan secara otomatis oleh sistem. Scan QR untuk verifikasi detail UAT.</p>
                        <div class="mt-2 flex gap-4 font-mono text-[9px] text-gray-400">
                            <span>REF: {{ Str::upper(Str::random(12)) }}</span>
                            <span>CETAK: {{ date('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
