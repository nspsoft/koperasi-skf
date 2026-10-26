@extends('layouts.app')

@section('title', 'Rekap Potong Gaji Payroll')

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('reports.index') }}" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    <svg class="w-6 h-6 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="page-title">Rekap Potong Gaji Koperasi (Payroll)</h1>
                    <p class="page-subtitle">
                        Bulan Payroll: <span class="font-bold text-primary">{{ $monthLabel }}</span> | 
                        Cut-Off: <span class="font-medium">{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</span>
                    </p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center gap-2.5">
                <button type="button" 
                        onclick="document.getElementById('modalImportAmigo').classList.remove('hidden')" 
                        class="btn-secondary flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                    </svg>
                    <span>Import Tagihan AMIGO</span>
                    @if($amigoTotalRecords > 0)
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-emerald-100 text-emerald-800 font-bold">
                        {{ $amigoTotalRecords }}
                    </span>
                    @endif
                </button>

                <a href="{{ route('reports.payroll-deductions.export-excel', request()->query()) }}" 
                   class="btn-primary flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>Export Excel Resmi</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Filter Tools: Period & Custom Cut-Off -->
    <div class="glass-card-solid p-6 mb-6">
        <form method="GET" action="{{ route('reports.payroll-deductions.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
            <div>
                <label class="form-label">Bulan Payroll</label>
                <input type="month" name="period" value="{{ $period }}" class="form-input font-medium" onchange="this.form.submit()">
            </div>
            <div>
                <label class="form-label">Mulai Cut-Off Transaksi</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="form-input">
            </div>
            <div>
                <label class="form-label">Selesai Cut-Off Transaksi</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="form-input">
            </div>
            <div>
                <label class="form-label">Tanggal Cetak Dokumen</label>
                <input type="date" name="doc_date" value="{{ $docDate }}" class="form-input">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn-primary flex-1">
                    Terapkan Cut-Off
                </button>
                <a href="{{ route('reports.payroll-deductions.index') }}" class="btn-secondary px-3" title="Reset">
                    ↺
                </a>
            </div>
        </form>
    </div>

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <div class="glass-card p-4 border-l-4 border-blue-500">
            <p class="text-xs text-gray-500 mb-1">Simpanan Wajib</p>
            <p class="text-lg font-bold text-gray-900 dark:text-white">Rp {{ number_format($grandTotals['simpanan_wajib'], 0, ',', '.') }}</p>
            <p class="text-[11px] text-gray-400 mt-1">{{ $grandTotals['count'] }} Anggota Aktif</p>
        </div>
        <div class="glass-card p-4 border-l-4 border-indigo-500">
            <p class="text-xs text-gray-500 mb-1">Pinjaman Koperasi</p>
            <p class="text-lg font-bold text-gray-900 dark:text-white">Rp {{ number_format($grandTotals['pinjaman_angsuran'], 0, ',', '.') }}</p>
            <p class="text-[11px] text-gray-400 mt-1">Angsuran Pinjaman</p>
        </div>
        <div class="glass-card p-4 border-l-4 border-emerald-500">
            <p class="text-xs text-gray-500 mb-1">Toko Koperasi</p>
            <p class="text-lg font-bold text-gray-900 dark:text-white">Rp {{ number_format($grandTotals['toko_koperasi'], 0, ',', '.') }}</p>
            <p class="text-[11px] text-gray-400 mt-1">Belanja Toko/POS</p>
        </div>
        <div class="glass-card p-4 border-l-4 border-amber-500">
            <p class="text-xs text-gray-500 mb-1">Unit Toko AMIGO</p>
            <p class="text-lg font-bold text-gray-900 dark:text-white">Rp {{ number_format($grandTotals['toko_amigo'], 0, ',', '.') }}</p>
            <p class="text-[11px] text-gray-400 mt-1">{{ $amigoTotalRecords }} Baris Terimpor</p>
        </div>
        <div class="glass-card p-4 border-l-4 border-purple-600 bg-purple-50/20 dark:bg-purple-950/20">
            <p class="text-xs text-purple-700 dark:text-purple-300 font-semibold mb-1">TOTAL POTONG GAJI</p>
            <p class="text-xl font-black text-purple-700 dark:text-purple-300">Rp {{ number_format($grandTotals['jumlah'], 0, ',', '.') }}</p>
            <p class="text-[11px] text-gray-400 mt-1">Total Siap Setor Payroll</p>
        </div>
    </div>

    <!-- Main Table Container with Tab Switching -->
    <div class="glass-card-solid p-6 mb-6">
        <!-- Tab Selector: Pabrik vs Manajemen vs Semua -->
        <div class="flex flex-wrap items-center justify-between gap-4 mb-4 border-b border-gray-100 dark:border-gray-800 pb-3">
            <div class="flex items-center gap-2">
                <button type="button" 
                        id="btnTabFactory" 
                        onclick="switchDeductionTab('factory')" 
                        class="px-4 py-2 rounded-lg text-xs font-semibold flex items-center gap-2 transition-all bg-primary text-white shadow-sm">
                    <span>🏭 Karyawan Pabrik / Staff</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-white/20 text-white font-bold">
                        {{ count($factoryList) }}
                    </span>
                </button>

                <button type="button" 
                        id="btnTabManagement" 
                        onclick="switchDeductionTab('management')" 
                        class="px-4 py-2 rounded-lg text-xs font-semibold flex items-center gap-2 transition-all text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800">
                    <span>💼 Karyawan Manajemen</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold">
                        {{ count($managementList) }}
                    </span>
                </button>
            </div>

            <div class="text-xs text-gray-500">
                Format resmi SPK Spindo Karawang Factory
            </div>
        </div>

        <!-- Panel 1: Karyawan Pabrik / Staff Umum -->
        <div id="panelFactory" class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 bg-gray-50/70 dark:bg-gray-800/50">
                        <th class="py-2.5 px-2 text-center">No</th>
                        <th class="py-2.5 px-2 text-center">No. Anggota</th>
                        <th class="py-2.5 px-2 text-center">No. Absen</th>
                        <th class="py-2.5 px-3 text-left">Nama</th>
                        <th class="py-2.5 px-2 text-left">Jabatan</th>
                        <th class="py-2.5 px-2 text-right">Wajib</th>
                        <th class="py-2.5 px-2 text-right">Pinjaman</th>
                        <th class="py-2.5 px-2 text-center">Ke</th>
                        <th class="py-2.5 px-2 text-right">Toko Koperasi</th>
                        <th class="py-2.5 px-2 text-right">Toko AMIGO</th>
                        <th class="py-2.5 px-3 text-right font-bold text-gray-900 dark:text-white">Jumlah</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($factoryList as $index => $row)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/40 transition-colors">
                        <td class="py-2 px-2 text-center text-gray-400">{{ $index + 1 }}</td>
                        <td class="py-2 px-2 text-center font-mono">{{ $row['member_id'] }}</td>
                        <td class="py-2 px-2 text-center font-mono font-semibold">{{ $row['employee_id'] }}</td>
                        <td class="py-2 px-3 font-medium text-gray-900 dark:text-white">{{ $row['name'] }}</td>
                        <td class="py-2 px-2 text-gray-500">{{ $row['position'] }}</td>
                        <td class="py-2 px-2 text-right tabular-nums">{{ number_format($row['simpanan_wajib'], 0, ',', '.') }}</td>
                        <td class="py-2 px-2 text-right tabular-nums">{{ $row['pinjaman_angsuran'] > 0 ? number_format($row['pinjaman_angsuran'], 0, ',', '.') : '-' }}</td>
                        <td class="py-2 px-2 text-center">{{ $row['pinjaman_ke'] ?: '-' }}</td>
                        <td class="py-2 px-2 text-right tabular-nums">{{ $row['toko_koperasi'] > 0 ? number_format($row['toko_koperasi'], 0, ',', '.') : '-' }}</td>
                        <td class="py-2 px-2 text-right tabular-nums {{ $row['toko_amigo'] > 0 ? 'text-amber-700 dark:text-amber-400 font-semibold' : '' }}">
                            {{ $row['toko_amigo'] > 0 ? number_format($row['toko_amigo'], 0, ',', '.') : '-' }}
                        </td>
                        <td class="py-2 px-3 text-right font-bold text-gray-900 dark:text-white tabular-nums">
                            Rp {{ number_format($row['jumlah'], 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="text-center py-6 text-gray-400">Tidak ada data anggota pada kelompok ini.</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-gray-200 dark:border-gray-700 font-bold bg-gray-50/50 dark:bg-gray-800/50 text-gray-900 dark:text-white">
                        <td colspan="5" class="py-3 px-3 text-center uppercase tracking-wider">JUMLAH KARYAWAN PABRIK</td>
                        <td class="py-3 px-2 text-right tabular-nums">Rp {{ number_format($factoryTotals['simpanan_wajib'], 0, ',', '.') }}</td>
                        <td class="py-3 px-2 text-right tabular-nums">Rp {{ number_format($factoryTotals['pinjaman_angsuran'], 0, ',', '.') }}</td>
                        <td class="py-3 px-2 text-center">-</td>
                        <td class="py-3 px-2 text-right tabular-nums">Rp {{ number_format($factoryTotals['toko_koperasi'], 0, ',', '.') }}</td>
                        <td class="py-3 px-2 text-right tabular-nums">Rp {{ number_format($factoryTotals['toko_amigo'], 0, ',', '.') }}</td>
                        <td class="py-3 px-3 text-right tabular-nums font-black text-primary">Rp {{ number_format($factoryTotals['jumlah'], 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>

            <!-- Tanda Tangan Preview Pabrik -->
            <div class="mt-6 p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/30 text-xs text-center">
                <p class="font-bold text-gray-700 dark:text-gray-300 mb-4">Penandatangan Dokumen Resmi (Karyawan Pabrik):</p>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <div class="h-14"></div>
                        <p class="font-bold underline">Citrawati</p>
                        <p class="text-gray-500">Bendahara</p>
                    </div>
                    <div>
                        <div class="h-14"></div>
                        <p class="font-bold underline">Ngafif Usman</p>
                        <p class="text-gray-500">Ketua</p>
                    </div>
                    <div>
                        <div class="h-14"></div>
                        <p class="font-bold underline">Zia Ulhaq Al-Muqtaffi</p>
                        <p class="text-gray-500">RGA&HSE Dept Head SKF</p>
                    </div>
                    <div>
                        <div class="h-14"></div>
                        <p class="font-bold underline">Budi Dwi S</p>
                        <p class="text-gray-500">HRD Div. Head</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel 2: Karyawan Manajemen -->
        <div id="panelManagement" class="overflow-x-auto hidden">
            <table class="w-full text-xs">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 bg-gray-50/70 dark:bg-gray-800/50">
                        <th class="py-2.5 px-2 text-center">No</th>
                        <th class="py-2.5 px-2 text-center">No. Anggota</th>
                        <th class="py-2.5 px-2 text-center">No. Absen</th>
                        <th class="py-2.5 px-3 text-left">Nama</th>
                        <th class="py-2.5 px-2 text-left">Jabatan</th>
                        <th class="py-2.5 px-2 text-right">Wajib</th>
                        <th class="py-2.5 px-2 text-right">Pinjaman</th>
                        <th class="py-2.5 px-2 text-center">Ke</th>
                        <th class="py-2.5 px-2 text-right">Toko Koperasi</th>
                        <th class="py-2.5 px-2 text-right">Toko AMIGO</th>
                        <th class="py-2.5 px-3 text-right font-bold text-gray-900 dark:text-white">Jumlah</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($managementList as $index => $row)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/40 transition-colors">
                        <td class="py-2 px-2 text-center text-gray-400">{{ $index + 1 }}</td>
                        <td class="py-2 px-2 text-center font-mono">{{ $row['member_id'] }}</td>
                        <td class="py-2 px-2 text-center font-mono font-semibold">{{ $row['employee_id'] }}</td>
                        <td class="py-2 px-3 font-medium text-gray-900 dark:text-white">{{ $row['name'] }}</td>
                        <td class="py-2 px-2 font-semibold text-primary">{{ $row['position'] }}</td>
                        <td class="py-2 px-2 text-right tabular-nums">{{ number_format($row['simpanan_wajib'], 0, ',', '.') }}</td>
                        <td class="py-2 px-2 text-right tabular-nums">{{ $row['pinjaman_angsuran'] > 0 ? number_format($row['pinjaman_angsuran'], 0, ',', '.') : '-' }}</td>
                        <td class="py-2 px-2 text-center">{{ $row['pinjaman_ke'] ?: '-' }}</td>
                        <td class="py-2 px-2 text-right tabular-nums">{{ $row['toko_koperasi'] > 0 ? number_format($row['toko_koperasi'], 0, ',', '.') : '-' }}</td>
                        <td class="py-2 px-2 text-right tabular-nums {{ $row['toko_amigo'] > 0 ? 'text-amber-700 dark:text-amber-400 font-semibold' : '' }}">
                            {{ $row['toko_amigo'] > 0 ? number_format($row['toko_amigo'], 0, ',', '.') : '-' }}
                        </td>
                        <td class="py-2 px-3 text-right font-bold text-gray-900 dark:text-white tabular-nums">
                            Rp {{ number_format($row['jumlah'], 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="text-center py-6 text-gray-400">Tidak ada data anggota pada kelompok ini.</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-gray-200 dark:border-gray-700 font-bold bg-gray-50/50 dark:bg-gray-800/50 text-gray-900 dark:text-white">
                        <td colspan="5" class="py-3 px-3 text-center uppercase tracking-wider">JUMLAH KARYAWAN MANAJEMEN</td>
                        <td class="py-3 px-2 text-right tabular-nums">Rp {{ number_format($managementTotals['simpanan_wajib'], 0, ',', '.') }}</td>
                        <td class="py-3 px-2 text-right tabular-nums">Rp {{ number_format($managementTotals['pinjaman_angsuran'], 0, ',', '.') }}</td>
                        <td class="py-3 px-2 text-center">-</td>
                        <td class="py-3 px-2 text-right tabular-nums">Rp {{ number_format($managementTotals['toko_koperasi'], 0, ',', '.') }}</td>
                        <td class="py-3 px-2 text-right tabular-nums">Rp {{ number_format($managementTotals['toko_amigo'], 0, ',', '.') }}</td>
                        <td class="py-3 px-3 text-right tabular-nums font-black text-primary">Rp {{ number_format($managementTotals['jumlah'], 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>

            <!-- Tanda Tangan Preview Manajemen -->
            <div class="mt-6 p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/30 text-xs text-center">
                <p class="font-bold text-gray-700 dark:text-gray-300 mb-4">Penandatangan Dokumen Resmi (Karyawan Manajemen):</p>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <div class="h-14"></div>
                        <p class="font-bold underline">Citrawati</p>
                        <p class="text-gray-500">Bendahara</p>
                    </div>
                    <div>
                        <div class="h-14"></div>
                        <p class="font-bold underline">Ngafif Usman</p>
                        <p class="text-gray-500">Ketua</p>
                    </div>
                    <div>
                        <div class="h-14"></div>
                        <p class="font-bold underline">Hariyanti K</p>
                        <p class="text-gray-500">HRM SPINDO, Tbk</p>
                    </div>
                    <div>
                        <div class="h-14"></div>
                        <p class="font-bold underline">Budi Dwi S</p>
                        <p class="text-gray-500">HRD Div. Head</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Dialog: Import Excel Toko AMIGO -->
    <div id="modalImportAmigo" class="fixed inset-0 z-50 overflow-y-auto hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-gray-100 dark:border-gray-700">
            <div class="flex justify-between items-center mb-4 pb-3 border-b border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-2">
                    <span class="p-2 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                    </span>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Import Tagihan Toko AMIGO</h3>
                </div>
                <button type="button" onclick="document.getElementById('modalImportAmigo').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                    ✕
                </button>
            </div>

            <form action="{{ route('reports.payroll-deductions.import-amigo') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="period" value="{{ $period }}">

                <div class="mb-4">
                    <label class="form-label text-xs">Periode Tagihan</label>
                    <input type="text" value="{{ $monthLabel }} ({{ $period }})" class="form-input bg-gray-50 dark:bg-gray-900 font-bold" readonly>
                </div>

                <div class="mb-5">
                    <label class="form-label text-xs">Pilih File Excel (.xlsx, .xls, .csv)</label>
                    <input type="file" name="file" accept=".xlsx,.xls,.csv" required class="form-input text-xs">
                    <p class="text-[11px] text-gray-400 mt-2">
                        Format kolom yang didukung: <span class="font-mono font-semibold">Periode | Employee No | Employee Name | Total</span>
                    </p>
                </div>

                <div class="p-3 rounded-lg bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/50 mb-5 text-xs text-amber-800 dark:text-amber-300">
                    💡 Sistem otomatis mencocokkan tagihan berdasarkan <strong>Employee No (No. Absen)</strong> karyawan (misal: <code>KA008</code> atau <code>KA 008</code>). Jika data untuk periode ini sudah pernah diimpor, data lama akan ditimpa dengan data terbaru.
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-gray-100 dark:border-gray-700">
                    <button type="button" onclick="document.getElementById('modalImportAmigo').classList.add('hidden')" class="btn-secondary text-xs">
                        Batal
                    </button>
                    <button type="submit" class="btn-primary text-xs flex items-center gap-2">
                        <span>Upload & Proses Data</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
<script>
window.switchDeductionTab = function(tab) {
    const btnFactory = document.getElementById('btnTabFactory');
    const btnManagement = document.getElementById('btnTabManagement');
    const panelFactory = document.getElementById('panelFactory');
    const panelManagement = document.getElementById('panelManagement');

    if (tab === 'factory') {
        panelFactory.classList.remove('hidden');
        panelManagement.classList.add('hidden');

        btnFactory.className = "px-4 py-2 rounded-lg text-xs font-semibold flex items-center gap-2 transition-all bg-primary text-white shadow-sm";
        btnManagement.className = "px-4 py-2 rounded-lg text-xs font-semibold flex items-center gap-2 transition-all text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800";
    } else {
        panelFactory.classList.add('hidden');
        panelManagement.classList.remove('hidden');

        btnManagement.className = "px-4 py-2 rounded-lg text-xs font-semibold flex items-center gap-2 transition-all bg-primary text-white shadow-sm";
        btnFactory.className = "px-4 py-2 rounded-lg text-xs font-semibold flex items-center gap-2 transition-all text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800";
    }
};
</script>
@endpush
