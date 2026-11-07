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
                        <td class="py-2 px-2 text-right tabular-nums">
                            @if($row['toko_koperasi'] > 0)
                                <button type="button" 
                                        onclick="openMemberCreditModal({{ $row['id'] }}, '{{ addslashes($row['name']) }}', '{{ $row['member_id'] }}', '{{ $row['employee_id'] }}')" 
                                        class="inline-flex items-center gap-1 font-semibold text-primary hover:text-primary-700 hover:underline cursor-pointer group"
                                        title="Klik untuk melihat rincian belanja & pelunasan">
                                    <span>{{ number_format($row['toko_koperasi'], 0, ',', '.') }}</span>
                                    <span class="text-[10px] text-primary/70 group-hover:text-primary">🔍</span>
                                </button>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
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
                        <td class="py-2 px-2 text-right tabular-nums">
                            @if($row['toko_koperasi'] > 0)
                                <button type="button" 
                                        onclick="openMemberCreditModal({{ $row['id'] }}, '{{ addslashes($row['name']) }}', '{{ $row['member_id'] }}', '{{ $row['employee_id'] }}')" 
                                        class="inline-flex items-center gap-1 font-semibold text-primary hover:text-primary-700 hover:underline cursor-pointer group"
                                        title="Klik untuk melihat rincian belanja & pelunasan">
                                    <span>{{ number_format($row['toko_koperasi'], 0, ',', '.') }}</span>
                                    <span class="text-[10px] text-primary/70 group-hover:text-primary">🔍</span>
                                </button>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
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
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="form-label text-xs !mb-0">Pilih File Excel (.xlsx, .xls, .csv)</label>
                        <a href="{{ route('reports.payroll-deductions.download-amigo-template') }}" 
                           class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 dark:hover:text-emerald-300 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            <span>Unduh Template Excel</span>
                        </a>
                    </div>
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

    <!-- Modal Detail Nota Belanja Toko Koperasi per Member -->
    <div id="modalMemberCreditDetails" class="fixed inset-0 z-50 overflow-y-auto hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-2xl w-full shadow-2xl border border-gray-100 dark:border-gray-700 overflow-hidden transform transition-all">
            <!-- Header -->
            <div class="p-5 sm:p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-start bg-gray-50/50 dark:bg-gray-900/50">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-primary-100 dark:bg-primary-900/30 text-primary-600 flex items-center justify-center text-xl flex-shrink-0">
                        🛍️
                    </div>
                    <div>
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white">Rincian Belanja Toko Koperasi</h3>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Anggota: <span id="mcdMemberName" class="font-bold text-gray-900 dark:text-white">-</span> 
                            (<span id="mcdMemberId">-</span> | Absen: <span id="mcdEmployeeId">-</span>)
                        </p>
                    </div>
                </div>
                <button type="button" onclick="closeMemberCreditModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 p-1">
                    ✕
                </button>
            </div>

            <!-- Body -->
            <div class="p-5 sm:p-6 space-y-4 max-h-[65vh] overflow-y-auto">
                <!-- Cut-Off Info & Total -->
                <div class="p-3.5 rounded-xl bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 flex flex-wrap items-center justify-between gap-3 text-xs">
                    <div>
                        <span class="text-gray-500 dark:text-gray-400 block">Periode Cut-Off Transaksi:</span>
                        <span class="font-semibold text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</span>
                    </div>
                    <div class="text-right">
                        <span class="text-gray-500 dark:text-gray-400 block">Total Potongan Cut-Off Ini:</span>
                        <span id="mcdTotalCutoff" class="text-sm font-black font-mono text-primary">Rp 0</span>
                    </div>
                </div>

                <!-- Loading State -->
                <div id="mcdLoading" class="py-10 text-center text-gray-400">
                    <svg class="animate-spin h-7 w-7 mx-auto text-primary mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="text-xs">Memuat rincian transaksi belanja...</p>
                </div>

                <!-- Transactions Content Container -->
                <div id="mcdContent" class="hidden">
                    <div class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden max-h-56 overflow-y-auto">
                        <table class="w-full text-xs">
                            <thead class="bg-gray-50 dark:bg-gray-900 text-gray-600 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700 sticky top-0">
                                <tr>
                                    <th class="py-2 px-3 text-center">No</th>
                                    <th class="py-2 px-3 text-left">Invoice</th>
                                    <th class="py-2 px-3 text-left">Tanggal</th>
                                    <th class="py-2 px-3 text-left">Barang</th>
                                    <th class="py-2 px-3 text-right">Nominal</th>
                                    <th class="py-2 px-3 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody id="mcdTableBody" class="divide-y divide-gray-100 dark:divide-gray-800">
                                <!-- Populated via JS -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Empty State -->
                <div id="mcdEmpty" class="hidden py-8 text-center text-gray-400 text-xs">
                    <span class="text-3xl block mb-1">🛒</span>
                    Tidak ditemukan data nota belanja pada rentang cut-off ini.
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="p-4 sm:p-5 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 flex flex-wrap items-center justify-between gap-3">
                <a id="mcdLinkCreditReport" href="#" target="_blank" class="text-xs font-semibold text-primary hover:underline flex items-center gap-1">
                    <span>🔍 Buka di Mart Credit Report</span>
                    <span class="text-[10px]">↗</span>
                </a>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="closeMemberCreditModal()" class="btn-secondary py-2 px-4 text-xs font-semibold">
                        Tutup
                    </button>
                    <button type="button" id="mcdBtnPayPayroll" onclick="payMemberCreditsFromModal()" class="btn-primary py-2 px-4 text-xs font-bold flex items-center gap-1.5 shadow-sm">
                        <span>⚡ Proses Potong Gaji (Payroll)</span>
                    </button>
                </div>
            </div>
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

let currentMcdData = null;

window.openMemberCreditModal = function(memberId, memberName, memberNo, employeeId) {
    const modal = document.getElementById('modalMemberCreditDetails');
    const loading = document.getElementById('mcdLoading');
    const content = document.getElementById('mcdContent');
    const empty = document.getElementById('mcdEmpty');
    const btnPay = document.getElementById('mcdBtnPayPayroll');
    const linkReport = document.getElementById('mcdLinkCreditReport');

    document.getElementById('mcdMemberName').textContent = memberName;
    document.getElementById('mcdMemberId').textContent = memberNo;
    document.getElementById('mcdEmployeeId').textContent = employeeId;
    document.getElementById('mcdTotalCutoff').textContent = 'Rp 0';

    // Deep link to /pos/credits
    const url = new URL("{{ route('pos.credits') }}", window.location.origin);
    url.searchParams.set('member_search', memberName);
    url.searchParams.set('from_date', "{{ $startDate }}");
    url.searchParams.set('to_date', "{{ $endDate }}");
    linkReport.href = url.toString();

    loading.classList.remove('hidden');
    content.classList.add('hidden');
    empty.classList.add('hidden');
    btnPay.disabled = true;
    modal.classList.remove('hidden');

    fetch(`{{ route('reports.payroll-deductions.member-credits') }}?member_id=${memberId}&start_date={{ $startDate }}&end_date={{ $endDate }}`)
        .then(res => res.json())
        .then(res => {
            loading.classList.add('hidden');
            if (res.success && res.transactions && res.transactions.length > 0) {
                currentMcdData = res;
                renderMemberCredits(res);
                content.classList.remove('hidden');
                btnPay.disabled = res.unpaid_count === 0;
                if (res.unpaid_count === 0) {
                    btnPay.innerHTML = '<span>✓ Sudah Lunas</span>';
                    btnPay.classList.remove('btn-primary');
                    btnPay.classList.add('btn-secondary');
                } else {
                    btnPay.innerHTML = '<span>⚡ Proses Potong Gaji (Payroll)</span>';
                    btnPay.classList.remove('btn-secondary');
                    btnPay.classList.add('btn-primary');
                }
            } else {
                empty.classList.remove('hidden');
                btnPay.disabled = true;
            }
        })
        .catch(err => {
            loading.classList.add('hidden');
            alert('Gagal memuat rincian transaksi: ' + err.message);
        });
};

window.renderMemberCredits = function(data) {
    const tbody = document.getElementById('mcdTableBody');
    tbody.innerHTML = '';
    document.getElementById('mcdTotalCutoff').textContent = 'Rp ' + Math.round(data.total_cutoff).toLocaleString('id-ID');

    data.transactions.forEach((trx, idx) => {
        const tr = document.createElement('tr');
        tr.className = 'hover:bg-gray-50/50 dark:hover:bg-gray-800/50';
        
        let statusBadge = '';
        if (trx.is_paid) {
            statusBadge = '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">Lunas</span>';
        } else {
            statusBadge = '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">Belum Lunas</span>';
        }

        tr.innerHTML = `
            <td class="py-2 px-3 text-center text-gray-400">${idx + 1}</td>
            <td class="py-2 px-3 font-mono font-semibold text-primary-600">${trx.invoice_number}</td>
            <td class="py-2 px-3 text-gray-500">${trx.date}</td>
            <td class="py-2 px-3 text-gray-700 dark:text-gray-300 max-w-[180px] truncate" title="${trx.items_summary}">${trx.items_summary}</td>
            <td class="py-2 px-3 text-right font-mono font-bold text-gray-900 dark:text-white">Rp ${Math.round(trx.cutoff_amount).toLocaleString('id-ID')}</td>
            <td class="py-2 px-3 text-center">${statusBadge}</td>
        `;
        tbody.appendChild(tr);
    });
};

window.closeMemberCreditModal = function() {
    document.getElementById('modalMemberCreditDetails').classList.add('hidden');
    currentMcdData = null;
};

window.payMemberCreditsFromModal = function() {
    if (!currentMcdData || !currentMcdData.transactions) return;

    const unpaidTrxIds = currentMcdData.transactions
        .filter(t => !t.is_paid)
        .map(t => t.id);

    if (unpaidTrxIds.length === 0) {
        alert('Semua tagihan untuk anggota ini sudah lunas.');
        return;
    }

    const confirmMsg = `Konfirmasi pelunasan potong gaji (Payroll) untuk ${currentMcdData.member.name}?\n` +
                       `Jumlah: ${unpaidTrxIds.length} nota\n` +
                       `Total: Rp ${Math.round(currentMcdData.total_unpaid).toLocaleString('id-ID')}`;

    if (!confirm(confirmMsg)) return;

    const btn = document.getElementById('mcdBtnPayPayroll');
    btn.disabled = true;
    btn.innerHTML = `
        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span>Memproses...</span>
    `;

    fetch("{{ route('reports.payroll-deductions.pay-member-credits') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': "{{ csrf_token() }}",
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            member_id: currentMcdData.member.id,
            transaction_ids: unpaidTrxIds,
            start_date: "{{ $startDate }}",
            end_date: "{{ $endDate }}",
            notes: "Pelunasan Potong Gaji Periode {{ $monthLabel }}"
        })
    })
    .then(res => res.json())
    .then(res => {
        if (res.success) {
            alert(res.message);
            window.location.reload();
        } else {
            alert('Gagal: ' + res.message);
            btn.disabled = false;
            btn.innerHTML = '<span>⚡ Proses Potong Gaji (Payroll)</span>';
        }
    })
    .catch(err => {
        alert('Terjadi kesalahan: ' + err.message);
        btn.disabled = false;
        btn.innerHTML = '<span>⚡ Proses Potong Gaji (Payroll)</span>';
    });
};
</script>
@endpush
