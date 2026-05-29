@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')

    <!-- Page Header -->
    <div class="page-header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="page-title">Dashboard</h1>
                <p class="page-subtitle">Selamat datang, {{ auth()->user()->name }}</p>
            </div>
            <div class="text-sm text-gray-500 dark:text-gray-400">
                {{ now()->isoFormat('dddd, D MMMM Y') }}
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-6 mb-8">
        <!-- Total Members -->
        <div class="stat-card gradient-primary">
            <div class="relative z-10">
                <p class="text-white/80 text-sm font-medium mb-1">Total Anggota</p>
                <h3 class="text-3xl font-bold mb-2">{{ number_format($stats['total_members']) }}</h3>
                <p class="text-white/70 text-xs">Anggota Aktif</p>
            </div>
            <svg class="stat-card-icon" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"></path>
            </svg>
        </div>

        <!-- Total Savings -->
        <div class="stat-card gradient-success">
            <div class="relative z-10">
                <p class="text-white/80 text-sm font-medium mb-1">Total Simpanan</p>
                <h3 class="text-3xl font-bold mb-2">Rp {{ number_format($stats['total_savings'], 0, ',', '.') }}</h3>
                <p class="text-white/70 text-xs">Saldo Simpanan</p>
            </div>
            <svg class="stat-card-icon" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
            </svg>
        </div>

        <!-- Total Loans -->
        <div class="stat-card gradient-accent">
            <div class="relative z-10">
                <p class="text-white/80 text-sm font-medium mb-1">Total Pinjaman</p>
                <h3 class="text-3xl font-bold mb-2">Rp {{ number_format($stats['total_loans'], 0, ',', '.') }}</h3>
                <p class="text-white/70 text-xs">Pinjaman Dicairkan</p>
            </div>
            <svg class="stat-card-icon" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
            </svg>
        </div>

        <!-- Outstanding Loans -->
        <div class="stat-card gradient-danger">
            <div class="relative z-10">
                <p class="text-white/80 text-sm font-medium mb-1">Sisa Pinjaman</p>
                <h3 class="text-3xl font-bold mb-2">Rp {{ number_format($stats['total_outstanding'], 0, ',', '.') }}</h3>
                <p class="text-white/70 text-xs">Belum Lunas</p>
            </div>
            <svg class="stat-card-icon" fill="currentColor" viewBox="0 0 20 20">
                <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"></path>
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"></path>
            </svg>
        </div>

        <!-- Pending Shop Orders -->
        <div class="stat-card gradient-primary">
            <div class="relative z-10">
                <p class="text-white/80 text-sm font-medium mb-1">Pesanan Online</p>
                <h3 class="text-3xl font-bold mb-2">{{ number_format($stats['pending_orders']) }}</h3>
                <p class="text-white/70 text-xs">Menunggu Proses</p>
            </div>
            <svg class="stat-card-icon opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
            </svg>
        </div>

        <!-- Kredit Mart Outstanding -->
        <div class="stat-card" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
            <div class="relative z-10">
                <p class="text-white/80 text-sm font-medium mb-1">Kredit Mart</p>
                <h3 class="text-2xl font-bold mb-2">Rp {{ number_format($stats['total_kredit'], 0, ',', '.') }}</h3>
                <p class="text-white/70 text-xs">{{ $stats['kredit_member_count'] }} Anggota</p>
            </div>
            <svg class="stat-card-icon opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
            </svg>
        </div>
    </div>

    <!-- Main Content Grid -->
    
    <!-- Row 1: Revenue & Profit (Wide) -->
    <div class="glass-card-solid p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Omset & Profit Bulanan</h2>
                <p class="text-sm text-gray-500">Perbandingan pendapatan vs profit akuntansi tahun ini (klik titik/batang untuk detail harian)</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="flex items-center text-xs text-gray-500">
                    <span class="w-2 h-2 rounded-full bg-indigo-500 mr-1"></span> Pendapatan
                </span>
                <span class="flex items-center text-xs text-gray-500">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 mr-1"></span> Profit
                </span>
                <label class="flex items-center text-xs text-gray-500 cursor-pointer select-none">
                    <input id="toggleExConsignmentSeries" type="checkbox" class="mr-2 rounded border-gray-300 text-amber-500 focus:ring-amber-500">
                    Tampilkan Profit ex-Settlement Konsinyasi
                </label>
                <div class="w-px h-4 bg-gray-200 dark:bg-gray-700 mx-1"></div>
                <label class="flex items-center text-xs text-gray-500 cursor-pointer select-none group">
                    <input id="toggleAccumulated" type="checkbox" class="mr-2 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    <span class="group-hover:text-primary-600 transition-colors font-semibold">Tampilkan Akumulasi</span>
                </label>
            </div>
        </div>
        <div id="revenueProfitChart"></div>
    </div>

    <!-- Row: Operational Expenses (Wide) -->
    <div class="glass-card-solid p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Biaya Operasional Bulanan</h2>
                <p class="text-sm text-gray-500">Total beban operasional (gaji, listrik, pemeliharaan, dll) diluar HPP</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="flex items-center text-xs text-gray-500">
                    <span class="w-2 h-2 rounded-full bg-amber-500 mr-1"></span> Biaya Ops
                </span>
            </div>
        </div>
        <div id="operationalExpenseChart"></div>
    </div>

    <div id="dailyRevenueModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
        <div class="w-full max-w-5xl rounded-2xl bg-white dark:bg-gray-900 shadow-2xl border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div>
                    <h3 id="dailyRevenueTitle" class="text-lg font-semibold text-gray-900 dark:text-white">Detail Harian</h3>
                    <p class="text-xs text-gray-500">Omset dan profit akuntansi per hari</p>
                </div>
                <button id="closeDailyRevenueModal" type="button" class="px-3 py-1.5 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700">Tutup</button>
            </div>
            <div class="p-6">
                <div id="dailyRevenueChart"></div>
            </div>
        </div>
    </div>

    <!-- Expense Breakdown Modal -->
    <div id="expenseBreakdownModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 backdrop-blur-sm p-4 transition-all duration-300">
        <div class="w-full max-w-2xl rounded-2xl bg-white dark:bg-gray-900 shadow-2xl border border-gray-200 dark:border-gray-700 transform scale-95 opacity-0 transition-all duration-300" id="expenseBreakdownModalContent">
            <!-- Modal Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/30 rounded-t-2xl">
                <div>
                    <h3 id="expenseBreakdownTitle" class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <span class="p-1.5 bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </span>
                        Breakdown Biaya Operasional
                    </h3>
                    <p class="text-xs text-gray-500 mt-0.5">Rincian pengeluaran operasional berdasarkan kategori akun</p>
                </div>
                <button id="closeExpenseBreakdownModal" type="button" class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Modal Body -->
            <div class="p-6">
                <!-- Total Card -->
                <div class="mb-6 p-4 rounded-xl bg-gradient-to-br from-amber-500/10 to-orange-500/10 border border-amber-500/20 flex justify-between items-center">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-amber-700 dark:text-amber-400">Total Biaya Operasional</p>
                        <p id="expenseBreakdownTotal" class="text-2xl font-black text-gray-900 dark:text-white mt-1">Rp 0</p>
                    </div>
                    <div class="p-3 bg-amber-500 text-white rounded-xl shadow-lg shadow-amber-500/20">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>

                <!-- Breakdown List -->
                <div id="expenseBreakdownList" class="space-y-4 max-h-[300px] overflow-y-auto pr-1">
                    <!-- Dynamic Items will be rendered here -->
                </div>

                <!-- Empty State -->
                <div id="expenseBreakdownEmpty" class="hidden flex flex-col items-center justify-center py-10 text-gray-400">
                    <span class="text-5xl mb-3 opacity-30">📊</span>
                    <p class="text-sm font-semibold uppercase tracking-wider text-gray-500">Tidak ada pengeluaran</p>
                    <p class="text-xs text-gray-400 mt-1">Tidak tercatat biaya operasional pada bulan ini</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Expense Line Details Modal -->
    <div id="expenseLineDetailsModal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-black/60 backdrop-blur-sm p-4 transition-all duration-300">
        <div class="w-full max-w-2xl rounded-2xl bg-white dark:bg-gray-900 shadow-2xl border border-gray-200 dark:border-gray-700 transform scale-95 opacity-0 transition-all duration-300" id="expenseLineDetailsModalContent">
            <!-- Modal Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/30 rounded-t-2xl">
                <div>
                    <h3 id="expenseLineDetailsTitle" class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <span class="p-1.5 bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                            </svg>
                        </span>
                        Rincian Transaksi Biaya
                    </h3>
                    <p class="text-xs text-gray-500 mt-0.5">Daftar jurnal pengeluaran rincian transaksi</p>
                </div>
                <button id="closeExpenseLineDetailsModal" type="button" class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Modal Body -->
            <div class="p-6">
                <!-- Loading Spinner -->
                <div id="expenseLineDetailsLoading" class="flex flex-col items-center justify-center py-12">
                    <svg class="animate-spin h-8 w-8 text-amber-500 mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="text-sm text-gray-500 font-medium">Memuat rincian transaksi...</p>
                </div>

                <!-- Details Table Container -->
                <div id="expenseLineDetailsContainer" class="hidden">
                    <div class="max-h-[350px] overflow-y-auto pr-1">
                        <table class="w-full text-left border-collapse text-sm">
                            <thead>
                                <tr class="border-b border-gray-200 dark:border-gray-800 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    <th class="py-3 px-2">Keterangan</th>
                                    <th class="py-3 px-2 text-right">Total Jumlah</th>
                                </tr>
                            </thead>
                            <tbody id="expenseLineDetailsList" class="divide-y divide-gray-100 dark:divide-gray-800/50">
                                <!-- Dynamic Items will be rendered here -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Empty State -->
                <div id="expenseLineDetailsEmpty" class="hidden flex flex-col items-center justify-center py-12 text-gray-400">
                    <span class="text-5xl mb-3 opacity-30">📋</span>
                    <p class="text-sm font-semibold uppercase tracking-wider text-gray-500">Tidak ada rincian</p>
                    <p class="text-xs text-gray-400 mt-1">Tidak ada detail transaksi untuk kategori ini</p>
                </div>
            </div>
            
            <!-- Modal Footer -->
            <div class="flex justify-end px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/30 rounded-b-2xl">
                <button id="backToBreakdownButton" type="button" class="px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors text-sm font-semibold">Kembali</button>
            </div>
        </div>
    </div>

    <!-- Row 2: Savings, Sales & Loans -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Savings Chart -->
        <div class="glass-card-solid p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Pertumbuhan Simpanan</h2>
                </div>
            </div>
            <div id="savingsChart" class="w-full"></div>
        </div>

        <!-- Sales Channel (Donut) -->
        <div class="glass-card-solid p-6">
             <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Penjualan</h2>
            </div>
            <div id="salesChannelChart" class="flex justify-center"></div>
             <div class="mt-4 space-y-3">
                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center">
                        <span class="w-3 h-3 rounded-full bg-indigo-500 mr-2"></span>
                        <span class="text-gray-600 dark:text-gray-400">Offline (POS)</span>
                    </div>
                    <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $salesChannelData[0] }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center">
                        <span class="w-3 h-3 rounded-full bg-cyan-500 mr-2"></span>
                        <span class="text-gray-600 dark:text-gray-400">Online Store</span>
                    </div>
                    <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $salesChannelData[1] }}</span>
                </div>
            </div>
        </div>

        <!-- Loan Distribution -->
        <div class="glass-card-solid p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Distribusi Pinjaman</h2>
            <div id="loanChart" class="w-full flex justify-center"></div>
            <div class="mt-4 space-y-2">
                @foreach($loanDistribution as $loan)
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">{{ ucfirst($loan->loan_type) }}</span>
                    <span class="font-semibold text-gray-900 dark:text-white">{{ $loan->count }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Tables Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Top 5 Customers -->
        <div class="glass-card-solid p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">🏆 Top 5 Pelanggan</h2>
            </div>
            <div class="space-y-4">
                @forelse($topCustomers as $customer)
                <div class="flex items-center gap-4 p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-yellow-400 to-orange-500 flex items-center justify-center text-white font-bold shadow-sm">
                        {{ substr($customer->user->name ?? '?', 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                            {{ $customer->user->name ?? 'Unknown' }}
                        </p>
                        <p class="text-xs text-gray-500 truncate">
                            {{ $customer->user->member->member_id ?? '-' }}
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-emerald-600">
                            Rp {{ number_format($customer->total_spent, 0, ',', '.') }}
                        </p>
                        <p class="text-[10px] text-gray-400 uppercase">Belanja</p>
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                    Belum ada data pelanggan
                </div>
                @endforelse
            </div>
        </div>

        <!-- Pending Loans -->
        <div class="glass-card-solid p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Pengajuan Pinjaman</h2>
                <a href="{{ route('loans.index') }}" class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400 font-medium">
                    Lihat Semua →
                </a>
            </div>
            <div class="space-y-4">
                @forelse($pendingLoans as $loan)
                <div class="flex items-center gap-4 p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-gray-900 dark:text-white truncate">{{ $loan->member->user->name }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Rp {{ number_format($loan->amount, 0, ',', '.') }}</p>
                    </div>
                    <span class="badge badge-warning">Pending</span>
                </div>
                @empty
                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                    Tidak ada pengajuan
                </div>
                @endforelse
            </div>
        </div>

        <!-- Top Selling Products -->
        <div class="glass-card-solid p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Produk Terlaris 🔥</h2>
            <div class="space-y-4">
                @forelse($topProducts as $item)
                <div class="flex items-center gap-4 p-2 rounded-xl bg-gray-50/50 dark:bg-gray-800/50 border border-transparent hover:border-primary-200 transition-colors">
                    <div class="w-12 h-12 rounded-lg overflow-hidden bg-white dark:bg-gray-700 flex-shrink-0">
                        @if($item->product->image)
                            <img src="{{ Storage::url($item->product->image) }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-xl">📦</div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-sm text-gray-900 dark:text-white truncate">{{ $item->product->name }}</p>
                        <p class="text-xs text-gray-500 font-medium tracking-tight">{{ $item->product->category->name }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-black text-primary-600 tracking-tighter">{{ number_format($item->total_qty) }}</p>
                        <p class="text-[10px] text-gray-400 font-bold uppercase">Sold</p>
                    </div>
                </div>
                @empty
                <div class="text-center py-12 text-gray-400">
                    <p class="text-4xl mb-2 opacity-20">🛒</p>
                    <p class="text-xs uppercase font-bold tracking-widest">Belum ada data</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Kredit Mart Section -->
    <div class="glass-card-solid p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">💳 Monitor Kredit Mart</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Tagihan kredit anggota yang belum lunas</p>
            </div>
            <a href="{{ route('pos.credits') }}" class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400 font-medium">
                Lihat Detail →
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Top Debtors -->
            <div>
                <h3 class="font-medium text-gray-700 dark:text-gray-300 mb-3 text-sm">Top 5 Tagihan Tertinggi</h3>
                <div class="space-y-3">
                    @forelse($topKreditDebtors as $debtor)
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center text-white font-semibold flex-shrink-0">
                            {{ strtoupper(substr($debtor->user->name ?? 'A', 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-900 dark:text-white truncate">{{ $debtor->user->name ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $debtor->user->member->member_id ?? '-' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-amber-700 dark:text-amber-400">Rp {{ number_format($debtor->total_tagihan, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 text-gray-400">
                        <p class="text-3xl mb-2 opacity-30">💳</p>
                        <p class="text-xs uppercase font-bold tracking-widest">Tidak ada kredit aktif</p>
                    </div>
                    @endforelse
                </div>
            </div>
            <!-- Quick Summary -->
            <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-5 space-y-4">
                <h3 class="font-medium text-gray-700 dark:text-gray-300 text-sm">Ringkasan Kredit</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="text-center">
                        <p class="text-3xl font-bold text-amber-600 dark:text-amber-400">{{ $stats['kredit_member_count'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Anggota Memiliki Kredit</p>
                    </div>
                    <div class="text-center">
                        <p class="text-3xl font-bold text-amber-600 dark:text-amber-400">Rp {{ number_format($stats['total_kredit'], 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Total Tagihan Beredar</p>
                    </div>
                </div>
                <a href="{{ route('pos.credits') }}" class="btn-primary w-full text-center !bg-amber-600 hover:!bg-amber-700">
                    Kelola Tagihan Kredit
                </a>
            </div>
        </div>
    </div>

    <!-- Announcements -->
    @if($announcements->count() > 0)
    <div class="glass-card-solid p-6 mb-8">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Pengumuman</h2>
        <div class="space-y-3">
            @foreach($announcements as $announcement)
            <div class="flex items-start gap-4 p-4 rounded-xl bg-{{ $announcement->type_color }}-50 dark:bg-{{ $announcement->type_color }}-900/20 border border-{{ $announcement->type_color }}-200 dark:border-{{ $announcement->type_color }}-800">
                <div class="flex-1">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-1">{{ $announcement->title }}</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ Str::limit($announcement->content, 150) }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ $announcement->publish_date?->format('d M Y') }}</p>
                </div>
                <span class="badge badge-{{ $announcement->type_color }}">{{ $announcement->type_label }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Recent Activity Log (Admin Only) -->
    @if(auth()->user()->isAdmin())
    <div class="glass-card-solid p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Aktivitas Terbaru
            </h2>
            <a href="{{ route('settings.audit-logs') }}" class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400 font-medium">
                Lihat Semua Log →
            </a>
        </div>
        
        <div class="relative">
            <!-- Timeline Line -->
            <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-100 dark:bg-gray-800"></div>

            <div class="space-y-6">
                @forelse($recentActivities as $activity)
                <div class="relative pl-10">
                    <!-- Dot -->
                    <div class="absolute left-2.5 top-1.5 w-3 h-3 rounded-full border-2 border-white dark:border-gray-900 bg-{{ $activity->action_color }}-500 ring-4 ring-{{ $activity->action_color }}-100 dark:ring-{{ $activity->action_color }}-900/30"></div>
                    
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                        <div>
                            <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $activity->user->name ?? 'System' }}</span>
                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                @if($activity->action == 'create')
                                    menambahkan data baru
                                @elseif($activity->action == 'update')
                                    memperbarui data
                                @elseif($activity->action == 'delete')
                                    menghapus data
                                @elseif($activity->action == 'login')
                                    masuk ke sistem
                                @elseif($activity->action == 'logout')
                                    keluar dari sistem
                                @else
                                    melakukan aksi {{ $activity->action }}
                                @endif
                            </span>
                        </div>
                        <time class="text-xs font-medium text-gray-400 uppercase tracking-wider">{{ $activity->created_at->diffForHumans() }}</time>
                    </div>
                    
                    <div class="mt-1 p-3 rounded-lg bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-700/50 text-sm text-gray-700 dark:text-gray-300">
                        {{ $activity->description }}
                        @if($activity->model_type)
                            <span class="text-xs text-gray-400 dark:text-gray-500 block mt-1 font-mono italic">
                                {{ class_basename($activity->model_type) }} #{{ $activity->model_id }}
                            </span>
                        @endif
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                    Belum ada aktivitas tercatat.
                </div>
                @endforelse
            </div>
        </div>
    </div>
    @endif

    @push('scripts')
    <script>
        (function (initCharts) {
            if (typeof window.runApex === 'function') {
                window.runApex(initCharts);
                return;
            }
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initCharts);
            } else {
                initCharts();
            }
        })(function () {
        // Common Options
        const commonOptions = {
            chart: {
                fontFamily: 'Inter, sans-serif',
                toolbar: { show: false },
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800,
                }
            },
            dataLabels: { enabled: false },
            track: { background: 'transparent' }
        };

        // Savings Chart (Area)
        const savingsOptions = {
            ...commonOptions,
            series: [{
                name: 'Total Simpanan',
                data: @json($savingsChart)
            }],
            chart: {
                type: 'area',
                height: 300,
                toolbar: { show: false },
                fontFamily: 'Inter, sans-serif',
            },
            colors: ['#6366f1'],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.1,
                    stops: [0, 90, 100]
                }
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            xaxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: {
                    style: { colors: '#9ca3af' }
                }
            },
            yaxis: {
                labels: {
                    style: { colors: '#9ca3af' },
                    formatter: (value) => {
                        return 'Rp ' + (value / 1000000).toFixed(1) + 'jt';
                    }
                }
            },
            grid: {
                strokeDashArray: 4,
                borderColor: '#e5e7eb',
                padding: { top: 0, right: 0, bottom: 0, left: 10 } 
            },
            tooltip: {
                theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light',
                y: {
                    formatter: function (val) {
                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
                    }
                }
            }
        };
        
        const savingsChart = new ApexCharts(document.querySelector("#savingsChart"), savingsOptions);
        savingsChart.render();

        const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        const dailyRevenueProfit = @json($dailyRevenueProfit);
        const modalEl = document.getElementById('dailyRevenueModal');
        const closeModalButton = document.getElementById('closeDailyRevenueModal');
        const modalTitle = document.getElementById('dailyRevenueTitle');
        const toggleExConsignmentSeries = document.getElementById('toggleExConsignmentSeries');
        const toggleAccumulated = document.getElementById('toggleAccumulated');
        
        // Data Sources
        const rawMonthlyRevenue = @json($monthlyRevenue);
        const rawMonthlyProfit = @json($monthlyProfit);
        const rawMonthlyOperationalProfit = @json($monthlyOperationalProfit);
        const rawMonthlyOperationalExpense = @json($monthlyOperationalExpense);
        const monthlyExpenseBreakdown = @json($monthlyExpenseBreakdown);

        let dailyChart = null;
        let revenueProfitChart = null;
        let operationalExpenseChart = null;

        const calculateCumulative = (values) => {
            let sum = 0;
            return values.map(v => {
                sum += (v || 0);
                return sum;
            });
        };

        const updateChartMode = () => {
            if (!revenueProfitChart) return;
            
            const isAccumulated = toggleAccumulated && toggleAccumulated.checked;
            
            const revenueData = isAccumulated ? calculateCumulative(rawMonthlyRevenue) : rawMonthlyRevenue;
            const profitData = isAccumulated ? calculateCumulative(rawMonthlyProfit) : rawMonthlyProfit;
            const operationalProfitData = isAccumulated ? calculateCumulative(rawMonthlyOperationalProfit) : rawMonthlyOperationalProfit;
            const expenseData = isAccumulated ? calculateCumulative(rawMonthlyOperationalExpense) : rawMonthlyOperationalExpense;

            revenueProfitChart.updateSeries([
                { name: 'Omset', data: revenueData },
                { name: 'Profit', data: profitData },
                { name: 'Profit ex-Settlement Konsinyasi', data: operationalProfitData }
            ]);

            if (operationalExpenseChart) {
                operationalExpenseChart.updateSeries([
                    { name: 'Biaya Ops', data: expenseData }
                ]);
            }

            // Adjust Y Axis if needed
            const maxVal = Math.max(...revenueData);
            revenueProfitChart.updateOptions({
                yaxis: [{
                    title: { text: isAccumulated ? 'Total Omset' : 'Omset', style: { color: '#6366f1' } },
                    labels: {
                        style: { colors: '#9ca3af' },
                        formatter: (value) => 'Rp ' + (value / 1000000).toFixed(value >= 10000000 ? 0 : 1) + 'jt'
                    }
                }, {
                    opposite: true,
                    title: { text: isAccumulated ? 'Total Profit' : 'Profit', style: { color: '#10b981' } },
                    labels: {
                        style: { colors: '#9ca3af' },
                        formatter: (value) => 'Rp ' + (value / 1000000).toFixed(value >= 10000000 ? 0 : 1) + 'jt'
                    }
                }]
            });

            if (operationalExpenseChart) {
                operationalExpenseChart.updateOptions({
                    yaxis: {
                        title: { text: isAccumulated ? 'Total Biaya' : 'Biaya Ops', style: { color: '#f59e0b' } }
                    }
                });
            }
        };

        const syncExConsignmentSeriesVisibility = (chartInstance) => {
            if (!chartInstance) {
                return;
            }
            if (toggleExConsignmentSeries && toggleExConsignmentSeries.checked) {
                chartInstance.showSeries('Profit ex-Settlement Konsinyasi');
            } else {
                chartInstance.hideSeries('Profit ex-Settlement Konsinyasi');
            }
        };

        const openDailyRevenueModal = (monthIndex) => {
            const dataset = dailyRevenueProfit[monthIndex] ? dailyRevenueProfit[monthIndex] : null;
            if (!dataset) {
                return;
            }

            modalTitle.textContent = `Detail Harian Omset & Profit - ${monthNames[monthIndex]}`;
            modalEl.classList.remove('hidden');
            modalEl.classList.add('flex');

            if (dailyChart) {
                dailyChart.destroy();
            }

            dailyChart = new ApexCharts(document.querySelector("#dailyRevenueChart"), {
                ...commonOptions,
                series: [{
                    name: 'Omset',
                    type: 'column',
                    data: dataset.revenue
                }, {
                    name: 'Profit',
                    type: 'line',
                    data: dataset.profit
                }, {
                    name: 'Profit ex-Settlement Konsinyasi',
                    type: 'line',
                    data: dataset.operational_profit
                }],
                chart: {
                    height: 360,
                    type: 'line',
                    toolbar: { show: false },
                    fontFamily: 'Inter, sans-serif',
                },
                stroke: {
                    width: [0, 3, 3],
                    curve: 'smooth'
                },
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        columnWidth: '55%'
                    }
                },
                colors: ['#6366f1', '#10b981', '#f59e0b'],
                labels: dataset.labels,
                xaxis: {
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                    labels: {
                        style: { colors: '#9ca3af' }
                    }
                },
                yaxis: [{
                    title: { text: 'Omset', style: { color: '#6366f1' } },
                    labels: {
                        style: { colors: '#9ca3af' },
                        formatter: (value) => 'Rp ' + new Intl.NumberFormat('id-ID').format(value)
                    }
                }, {
                    opposite: true,
                    title: { text: 'Profit', style: { color: '#10b981' } },
                    labels: {
                        style: { colors: '#9ca3af' },
                        formatter: (value) => 'Rp ' + new Intl.NumberFormat('id-ID').format(value)
                    }
                }],
                grid: {
                    borderColor: '#e5e7eb',
                    strokeDashArray: 4
                },
                tooltip: {
                    theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light',
                    shared: true,
                    intersect: false,
                    y: {
                        formatter: function (val, { series, seriesIndex, dataPointIndex, w }) {
                            let base = 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
                            if ((seriesIndex === 1 || seriesIndex === 2) && series[0][dataPointIndex] > 0) {
                                const omset = series[0][dataPointIndex];
                                const margin = (val / omset) * 100;
                                return base + ' <span class="ml-1 text-xs font-bold text-emerald-500">(' + margin.toFixed(1) + '%)</span>';
                            }
                            return base;
                        }
                    }
                }
            });
            dailyChart.render().then(() => {
                syncExConsignmentSeriesVisibility(dailyChart);
            });
        };

        if (closeModalButton && modalEl) {
            closeModalButton.addEventListener('click', () => {
                modalEl.classList.add('hidden');
                modalEl.classList.remove('flex');
            });

            modalEl.addEventListener('click', (event) => {
                if (event.target === modalEl) {
                    modalEl.classList.add('hidden');
                    modalEl.classList.remove('flex');
                }
            });
        }

        const expenseModalEl = document.getElementById('expenseBreakdownModal');
        const expenseModalContentEl = document.getElementById('expenseBreakdownModalContent');
        const closeExpenseModalButton = document.getElementById('closeExpenseBreakdownModal');
        const expenseModalTitle = document.getElementById('expenseBreakdownTitle');
        const expenseModalTotal = document.getElementById('expenseBreakdownTotal');
        const expenseModalList = document.getElementById('expenseBreakdownList');
        const expenseModalEmpty = document.getElementById('expenseBreakdownEmpty');

        const openExpenseBreakdownModal = (monthIndex) => {
            const breakdown = monthlyExpenseBreakdown[monthIndex] || [];
            const monthName = monthNames[monthIndex];
            const totalExpense = rawMonthlyOperationalExpense[monthIndex] || 0;

            expenseModalTitle.innerHTML = `
                <span class="p-1.5 bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </span>
                Breakdown Biaya Operasional - ${monthName}
            `;
            
            expenseModalTotal.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(totalExpense);

            // Clear previous items
            expenseModalList.innerHTML = '';

            if (breakdown.length === 0) {
                expenseModalList.classList.add('hidden');
                expenseModalEmpty.classList.remove('hidden');
                expenseModalEmpty.classList.add('flex');
            } else {
                expenseModalEmpty.classList.add('hidden');
                expenseModalEmpty.classList.remove('flex');
                expenseModalList.classList.remove('hidden');

                breakdown.forEach(item => {
                    const percentage = totalExpense > 0 ? ((item.amount / totalExpense) * 100).toFixed(1) : 0;
                    
                    const itemHTML = `
                        <div onclick="fetchExpenseLineDetails(${monthIndex + 1}, '${item.code}', '${item.name}', '${monthName}')" class="p-4 rounded-xl border border-gray-100 dark:border-gray-800 bg-gray-50/30 dark:bg-gray-800/10 hover:border-amber-400 dark:hover:border-amber-600 hover:bg-amber-50/10 dark:hover:bg-amber-950/10 transition-all duration-200 cursor-pointer hover:scale-[1.01] transform hover:shadow-sm">
                            <div class="flex justify-between items-start gap-4 mb-2">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 font-mono">
                                            ${item.code}
                                        </span>
                                        <span class="font-semibold text-sm text-gray-900 dark:text-white truncate">
                                            ${item.name}
                                        </span>
                                    </div>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <span class="font-bold text-sm text-gray-900 dark:text-white">
                                        Rp ${new Intl.NumberFormat('id-ID').format(item.amount)}
                                    </span>
                                    <span class="block text-[10px] font-bold text-amber-600 dark:text-amber-400 uppercase tracking-wider mt-0.5">
                                        ${percentage}%
                                    </span>
                                </div>
                            </div>
                            <!-- Visual Progress Bar -->
                            <div class="w-full h-1.5 bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-amber-500 to-orange-500 rounded-full transition-all duration-500" style="width: ${percentage}%"></div>
                            </div>
                        </div>
                    `;
                    expenseModalList.insertAdjacentHTML('beforeend', itemHTML);
                });
            }

            // Show Modal with smooth animation
            expenseModalEl.classList.remove('hidden');
            expenseModalEl.classList.add('flex');
            // Allow browser to render then scale up
            setTimeout(() => {
                expenseModalContentEl.classList.remove('scale-95', 'opacity-0');
                expenseModalContentEl.classList.add('scale-100', 'opacity-100');
            }, 10);
        };

        const closeExpenseBreakdownModal = () => {
            expenseModalContentEl.classList.remove('scale-100', 'opacity-100');
            expenseModalContentEl.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                expenseModalEl.classList.add('hidden');
                expenseModalEl.classList.remove('flex');
            }, 300);
        };

        if (closeExpenseModalButton && expenseModalEl) {
            closeExpenseModalButton.addEventListener('click', closeExpenseBreakdownModal);

            expenseModalEl.addEventListener('click', (event) => {
                if (event.target === expenseModalEl) {
                    closeExpenseBreakdownModal();
                }
            });
        }

        const lineDetailsModalEl = document.getElementById('expenseLineDetailsModal');
        const lineDetailsModalContentEl = document.getElementById('expenseLineDetailsModalContent');
        const closeLineDetailsButton = document.getElementById('closeExpenseLineDetailsModal');
        const backToBreakdownButton = document.getElementById('backToBreakdownButton');
        const lineDetailsTitle = document.getElementById('expenseLineDetailsTitle');
        const lineDetailsLoading = document.getElementById('expenseLineDetailsLoading');
        const lineDetailsContainer = document.getElementById('expenseLineDetailsContainer');
        const lineDetailsList = document.getElementById('expenseLineDetailsList');
        const lineDetailsEmpty = document.getElementById('expenseLineDetailsEmpty');

        const fetchExpenseLineDetails = (month, accountCode, accountName, monthName) => {
            // Set Header Title
            lineDetailsTitle.innerHTML = `
                <span class="p-1.5 bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                </span>
                Rincian ${accountName} - ${monthName}
            `;

            // Reset States
            lineDetailsLoading.classList.remove('hidden');
            lineDetailsContainer.classList.add('hidden');
            lineDetailsEmpty.classList.add('hidden');
            lineDetailsList.innerHTML = '';

            // Open Modal
            lineDetailsModalEl.classList.remove('hidden');
            lineDetailsModalEl.classList.add('flex');
            setTimeout(() => {
                lineDetailsModalContentEl.classList.remove('scale-95', 'opacity-0');
                lineDetailsModalContentEl.classList.add('scale-100', 'opacity-100');
            }, 10);

            // Fetch Data
            fetch(`/dashboard/expense-details?month=${month}&account_code=${accountCode}`)
                .then(response => response.json())
                .then(res => {
                    lineDetailsLoading.classList.add('hidden');
                    if (res.status === 'success' && res.data.length > 0) {
                        lineDetailsContainer.classList.remove('hidden');
                        res.data.forEach(item => {
                            const row = `
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/25 transition-colors border-b border-gray-100 dark:border-gray-800/50">
                                    <td class="py-3 px-2 text-gray-900 dark:text-white font-semibold">${item.description}</td>
                                    <td class="py-3 px-2 text-right text-gray-900 dark:text-white font-bold whitespace-nowrap">
                                        Rp ${new Intl.NumberFormat('id-ID').format(item.amount)}
                                    </td>
                                </tr>
                            `;
                            lineDetailsList.insertAdjacentHTML('beforeend', row);
                        });
                    } else {
                        lineDetailsEmpty.classList.remove('hidden');
                    }
                })
                .catch(err => {
                    console.error('Error fetching expense details:', err);
                    lineDetailsLoading.classList.add('hidden');
                    lineDetailsEmpty.classList.remove('hidden');
                });
        };

        window.fetchExpenseLineDetails = fetchExpenseLineDetails;

        const closeLineDetailsModal = () => {
            lineDetailsModalContentEl.classList.remove('scale-100', 'opacity-100');
            lineDetailsModalContentEl.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                lineDetailsModalEl.classList.add('hidden');
                lineDetailsModalEl.classList.remove('flex');
            }, 300);
        };

        if (closeLineDetailsButton && lineDetailsModalEl) {
            closeLineDetailsButton.addEventListener('click', closeLineDetailsModal);
            
            backToBreakdownButton.addEventListener('click', closeLineDetailsModal);

            lineDetailsModalEl.addEventListener('click', (event) => {
                if (event.target === lineDetailsModalEl) {
                    closeLineDetailsModal();
                }
            });
        }

        if (toggleExConsignmentSeries) {
            toggleExConsignmentSeries.addEventListener('change', () => {
                syncExConsignmentSeriesVisibility(revenueProfitChart);
                syncExConsignmentSeriesVisibility(dailyChart);
            });
        }

        if (toggleAccumulated) {
            toggleAccumulated.addEventListener('change', () => {
                updateChartMode();
            });
        }

        // Revenue & Profit Chart (Bar + Line)
        const revenueProfitOptions = {
            ...commonOptions,
            series: [{
                name: 'Omset',
                type: 'column',
                data: @json($monthlyRevenue)
            }, {
                name: 'Profit',
                type: 'line',
                data: @json($monthlyProfit)
            }, {
                name: 'Profit ex-Settlement Konsinyasi',
                type: 'line',
                data: @json($monthlyOperationalProfit)
            }],
            chart: {
                height: 350,
                type: 'line',
                toolbar: { show: false },
                fontFamily: 'Inter, sans-serif',
                events: {
                    dataPointSelection: function(event, chartContext, config) {
                        openDailyRevenueModal(config.dataPointIndex);
                    }
                }
            },
            stroke: {
                width: [0, 4, 3],
                curve: 'smooth'
            },
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    columnWidth: '40%'
                }
            },
            colors: ['#6366f1', '#10b981', '#f59e0b'],
            fill: {
                opacity: [1, 1, 1]
            },
            labels: monthNames,
            xaxis: {
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: {
                    style: { colors: '#9ca3af' }
                }
            },
            yaxis: [{
                title: { text: 'Omset', style: { color: '#6366f1' } },
                labels: {
                    style: { colors: '#9ca3af' },
                    formatter: (value) => 'Rp ' + (value / 1000000).toFixed(0) + 'jt'
                }
            }, {
                opposite: true,
                title: { text: 'Profit', style: { color: '#10b981' } },
                labels: {
                    style: { colors: '#9ca3af' },
                    formatter: (value) => 'Rp ' + (value / 1000000).toFixed(0) + 'jt'
                }
            }],
            grid: {
                borderColor: '#e5e7eb',
                strokeDashArray: 4
            },
            tooltip: {
                theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light',
                shared: true,
                intersect: false,
                y: {
                    formatter: function (val, { series, seriesIndex, dataPointIndex, w }) {
                        let base = 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
                        if ((seriesIndex === 1 || seriesIndex === 2) && series[0][dataPointIndex] > 0) {
                            const omset = series[0][dataPointIndex];
                            const margin = (val / omset) * 100;
                            return base + ' <span class="ml-1 text-xs font-bold text-emerald-500">(' + margin.toFixed(1) + '%)</span>';
                        }
                        return base;
                    }
                }
            }
        };

        revenueProfitChart = new ApexCharts(document.querySelector("#revenueProfitChart"), revenueProfitOptions);
        revenueProfitChart.render().then(() => {
            syncExConsignmentSeriesVisibility(revenueProfitChart);
        });

        // Operational Expense Chart (Bar Only)
        const expenseOptions = {
            ...commonOptions,
            series: [{
                name: 'Biaya Ops',
                data: rawMonthlyOperationalExpense
            }],
            chart: {
                height: 250,
                type: 'bar',
                toolbar: { show: false },
                fontFamily: 'Inter, sans-serif',
                events: {
                    dataPointSelection: function(event, chartContext, config) {
                        openExpenseBreakdownModal(config.dataPointIndex);
                    }
                }
            },
            colors: ['#f59e0b'],
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    columnWidth: '50%',
                    distributed: false
                }
            },
            labels: monthNames,
            xaxis: {
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: {
                    style: { colors: '#9ca3af' }
                }
            },
            yaxis: {
                title: { text: 'Biaya Ops', style: { color: '#f59e0b' } },
                labels: {
                    style: { colors: '#9ca3af' },
                    formatter: (value) => 'Rp ' + (value / 1000000).toFixed(1) + 'jt'
                }
            },
            grid: {
                borderColor: '#e5e7eb',
                strokeDashArray: 4
            },
            tooltip: {
                theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light',
                y: {
                    formatter: function (val) {
                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
                    }
                }
            }
        };

        operationalExpenseChart = new ApexCharts(document.querySelector("#operationalExpenseChart"), expenseOptions);
        operationalExpenseChart.render();

        // Sales Channel Chart (Donut)
        const salesChannelOptions = {
            ...commonOptions,
            series: @json($salesChannelData),
            labels: ['Offline (POS)', 'Online Store'],
            chart: {
                type: 'donut',
                height: 280,
                fontFamily: 'Inter, sans-serif',
            },
            colors: ['#6366f1', '#06b6d4'],
            plotOptions: {
                 pie: {
                    donut: {
                        size: '75%',
                        labels: {
                            show: true,
                            name: { show: true, fontSize: '14px', fontFamily: 'Inter, sans-serif', color: '#6b7280' },
                            value: { 
                                show: true, 
                                fontSize: '24px', 
                                fontFamily: 'Inter, sans-serif', 
                                fontWeight: 700,
                                color: document.documentElement.classList.contains('dark') ? '#f3f4f6' : '#111827'
                            },
                             total: {
                                show: true,
                                label: 'Total',
                                color: '#6b7280'
                            }
                        }
                    }
                }
            },
            stroke: { show: false },
            dataLabels: { enabled: false },
            legend: { show: false },
            tooltip: {
                theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light',
            }
        };

        const salesChannelChart = new ApexCharts(document.querySelector("#salesChannelChart"), salesChannelOptions);
        salesChannelChart.render();

        // Loan Distribution Chart (Donut)
        const loanLabels = @json($loanDistribution->pluck('loan_type')->map(fn($type) => ucfirst($type)));
        const loanData = @json($loanDistribution->pluck('count'));
        
        const loanOptions = {
            ...commonOptions,
            series: loanData,
            labels: loanLabels,
            chart: {
                type: 'donut',
                height: 280,
                fontFamily: 'Inter, sans-serif',
            },
            colors: ['#6366f1', '#f59e0b', '#10b981', '#ef4444'],
            plotOptions: {
                pie: {
                    donut: {
                        size: '75%',
                        labels: {
                            show: true,
                            name: { show: true, fontSize: '14px', fontFamily: 'Inter, sans-serif', color: '#6b7280' },
                            value: { 
                                show: true, 
                                fontSize: '24px', 
                                fontFamily: 'Inter, sans-serif', 
                                fontWeight: 700,
                                color: document.documentElement.classList.contains('dark') ? '#f3f4f6' : '#111827'
                            },
                            total: {
                                show: true,
                                label: 'Total',
                                color: '#6b7280',
                                formatter: function (w) {
                                    return w.globals.seriesTotals.reduce((a, b) => {
                                        return a + b
                                    }, 0)
                                }
                            }
                        }
                    }
                }
            },
            stroke: { show: false },
            dataLabels: { enabled: false },
            legend: { show: false }, // Custom legend used in HTML
            tooltip: {
                theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light',
            }
        };

        const loanChart = new ApexCharts(document.querySelector("#loanChart"), loanOptions);
        loanChart.render();
        
        // Dark mode adapter for Charts
        window.addEventListener('dark-mode-toggle', event => {
            const isDark = event.detail; // true or false
            // Update ApexCharts theme/colors if needed
            // Currently utilizing CSS vars or simple checks
        });
        });
    </script>
    @endpush
@endsection
