@extends('layouts.app')

@section('title', 'Laporan Transaksi')

@section('content')
    <!-- Page Header -->
    <div class="page-header">
         <div class="flex items-center gap-4">
            <a href="{{ route('reports.index') }}" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                <svg class="w-6 h-6 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h1 class="page-title">Laporan Transaksi & Penjualan</h1>
                <p class="page-subtitle">Periode: {{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}</p>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="glass-card-solid p-6 mb-6">
        <form method="GET" action="{{ route('reports.transactions') }}" id="reportFilterForm" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-7 gap-4 items-end">
            <input type="hidden" name="chart_period" id="chartPeriodInput" value="{{ request('chart_period', $chartPeriod ?? 'daily') }}">
             <div>
                <label class="form-label">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ request('start_date', $startDate->format('Y-m-d')) }}" class="form-input">
            </div>
            <div>
                <label class="form-label">Tanggal Selesai</label>
                <input type="date" name="end_date" value="{{ request('end_date', $endDate->format('Y-m-d')) }}" class="form-input">
            </div>
            <div>
                 <label class="form-label">Tipe Transaksi</label>
                <select name="type" class="form-input">
                    <option value="">Semua Tipe</option>
                    <option value="pos" {{ request('type') == 'pos' ? 'selected' : '' }}>POS (Kasir)</option>
                    <option value="online" {{ request('type') == 'online' ? 'selected' : '' }}>Online Shop</option>
                </select>
            </div>
            <div>
                <label class="form-label">Jenis Produk</label>
                <select name="product_type" class="form-input">
                    <option value="">Semua Produk</option>
                    <option value="regular" {{ request('product_type', $productType ?? '') == 'regular' ? 'selected' : '' }}>Milik Koperasi</option>
                    <option value="consignment" {{ request('product_type', $productType ?? '') == 'consignment' ? 'selected' : '' }}>Konsinyasi (Titipan)</option>
                </select>
            </div>
            <div>
                 <label class="form-label">Status</label>
                <select name="status" class="form-input">
                    <option value="">Semua Status</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Diproses</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>
            <div>
                <label class="form-label">Periode Grafik</label>
                <select onchange="document.getElementById('chartPeriodInput').value=this.value; this.form.submit();" class="form-input">
                    <option value="daily" {{ request('chart_period', $chartPeriod) == 'daily' ? 'selected' : '' }}>Harian</option>
                    <option value="weekly" {{ request('chart_period', $chartPeriod) == 'weekly' ? 'selected' : '' }}>Mingguan</option>
                    <option value="monthly" {{ request('chart_period', $chartPeriod) == 'monthly' ? 'selected' : '' }}>Bulanan</option>
                    <option value="quarterly" {{ request('chart_period', $chartPeriod) == 'quarterly' ? 'selected' : '' }}>Kuartalan</option>
                    <option value="semi_annually" {{ request('chart_period', $chartPeriod) == 'semi_annually' ? 'selected' : '' }}>Semesteran</option>
                </select>
            </div>
            <button type="submit" class="btn-primary">
                Terapkan Filter
            </button>
        </form>
    </div>

     <!-- Summary Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <div class="glass-card p-5 border-l-4 border-green-500 flex flex-col justify-between">
            <div>
                <p class="text-sm text-gray-500 mb-1">Total Penjualan</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">Rp {{ number_format($totalSales, 0, ',', '.') }}</p>
            </div>
            <p class="text-xs text-gray-400 mt-2">Transaksi selesai</p>
        </div>
        <div class="glass-card p-5 border-l-4 border-teal-500 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-1">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Margin</p>
                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ $marginPercentage >= 0 ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300' : 'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300' }}">
                        {{ number_format($marginPercentage, 1) }}%
                    </span>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">Rp {{ number_format($totalMargin, 0, ',', '.') }}</p>
            </div>
            <p class="text-xs text-gray-400 mt-2">Laba kotor penjualan</p>
        </div>
        <div class="glass-card p-5 border-l-4 border-blue-500 flex flex-col justify-between">
            <div>
                <p class="text-sm text-gray-500 mb-1">Total Transaksi</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($totalTransactions) }}</p>
            </div>
            <p class="text-xs text-gray-400 mt-2">Transaksi selesai</p>
        </div>
        <div class="glass-card p-5 border-l-4 border-purple-500 flex flex-col justify-between">
            <div>
                <p class="text-sm text-gray-500 mb-1">Rata-rata Transaksi</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">Rp {{ number_format($averageTransaction, 0, ',', '.') }}</p>
            </div>
            <p class="text-xs text-gray-400 mt-2">Per transaksi</p>
        </div>
        <div class="glass-card p-5 border-l-4 border-amber-500 flex flex-col justify-between">
            <div>
                <p class="text-sm text-gray-500 mb-1">Order Pending</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $pendingOrders }}</p>
            </div>
            <p class="text-xs text-gray-400 mt-2">Perlu diproses</p>
        </div>
    </div>

    <!-- Breakdown Comparisons: Channel (POS vs Online) & Product Type (Consignment vs Regular) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- 1. Channel Comparison (POS vs Online) -->
        <div class="glass-card-solid p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <div class="p-2 rounded-lg bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-900 dark:text-white">Kanal Penjualan (Kasir vs Online)</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Distribusi omzet kasir fisik (POS) dan toko online</p>
                        </div>
                    </div>
                </div>

                <!-- Progress Bar -->
                @php $totalChannel = $posSales + $onlineSales; @endphp
                <div class="w-full bg-gray-100 dark:bg-gray-800 rounded-full h-3 overflow-hidden flex my-4">
                    <div class="bg-emerald-500 h-full transition-all duration-500" style="width: {{ $posPercentage }}%" title="Kasir POS: {{ number_format($posPercentage, 1) }}%"></div>
                    <div class="bg-blue-500 h-full transition-all duration-500" style="width: {{ $onlinePercentage }}%" title="Online Shop: {{ number_format($onlinePercentage, 1) }}%"></div>
                </div>

                <!-- Sub-cards -->
                <div class="grid grid-cols-2 gap-3 mt-4">
                    <!-- POS -->
                    <div class="p-3.5 rounded-xl bg-emerald-50/60 dark:bg-emerald-950/20 border border-emerald-100 dark:border-emerald-900/40">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-semibold text-emerald-800 dark:text-emerald-300 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Kasir (POS)
                            </span>
                            <span class="text-xs font-bold text-emerald-700 dark:text-emerald-400">{{ number_format($posPercentage, 1) }}%</span>
                        </div>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">Rp {{ number_format($posSales, 0, ',', '.') }}</p>
                        <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">{{ number_format($posTrxCount) }} transaksi selesai</p>
                    </div>

                    <!-- Online Shop -->
                    <div class="p-3.5 rounded-xl bg-blue-50/60 dark:bg-blue-950/20 border border-blue-100 dark:border-blue-900/40">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-semibold text-blue-800 dark:text-blue-300 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-blue-500"></span> Online Shop
                            </span>
                            <span class="text-xs font-bold text-blue-700 dark:text-blue-400">{{ number_format($onlinePercentage, 1) }}%</span>
                        </div>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">Rp {{ number_format($onlineSales, 0, ',', '.') }}</p>
                        <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">{{ number_format($onlineTrxCount) }} transaksi selesai</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Product Ownership Comparison (Consignment vs Regular) -->
        <div class="glass-card-solid p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <div class="p-2 rounded-lg bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-900 dark:text-white">Barang Toko vs Konsinyasi</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Perbandingan produk milik koperasi vs barang titipan</p>
                        </div>
                    </div>
                </div>

                <!-- Progress Bar -->
                @php $totalProduct = $regularSales + $consignmentSales; @endphp
                <div class="w-full bg-gray-100 dark:bg-gray-800 rounded-full h-3 overflow-hidden flex my-4">
                    <div class="bg-indigo-500 h-full transition-all duration-500" style="width: {{ $regularPercentage }}%" title="Barang Toko: {{ number_format($regularPercentage, 1) }}%"></div>
                    <div class="bg-amber-500 h-full transition-all duration-500" style="width: {{ $consignmentPercentage }}%" title="Konsinyasi: {{ number_format($consignmentPercentage, 1) }}%"></div>
                </div>

                <!-- Sub-cards -->
                <div class="grid grid-cols-2 gap-3 mt-4">
                    <!-- Regular / Store -->
                    <div class="p-3.5 rounded-xl bg-indigo-50/60 dark:bg-indigo-950/20 border border-indigo-100 dark:border-indigo-900/40">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-semibold text-indigo-800 dark:text-indigo-300 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-indigo-500"></span> Milik Koperasi
                            </span>
                            <span class="text-xs font-bold text-indigo-700 dark:text-indigo-400">{{ number_format($regularPercentage, 1) }}%</span>
                        </div>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">Rp {{ number_format($regularSales, 0, ',', '.') }}</p>
                        <div class="mt-2 pt-2 border-t border-indigo-100/80 dark:border-indigo-900/40 text-[11px] space-y-0.5">
                            <div class="flex justify-between text-gray-500 dark:text-gray-400">
                                <span>HPP Pokok:</span>
                                <span>Rp {{ number_format($regularCogs, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between font-semibold text-emerald-600 dark:text-emerald-400">
                                <span>Laba Toko:</span>
                                <span>Rp {{ number_format($regularMargin, 0, ',', '.') }} ({{ number_format($regularMarginPercent, 1) }}%)</span>
                            </div>
                            <div class="text-[10px] text-gray-400 pt-0.5">{{ number_format($regularQty) }} item terjual</div>
                        </div>
                    </div>

                    <!-- Consignment -->
                    <div class="p-3.5 rounded-xl bg-amber-50/60 dark:bg-amber-950/20 border border-amber-100 dark:border-amber-900/40">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-semibold text-amber-800 dark:text-amber-300 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span> Konsinyasi (Titipan)
                            </span>
                            <span class="text-xs font-bold text-amber-700 dark:text-amber-400">{{ number_format($consignmentPercentage, 1) }}%</span>
                        </div>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">Rp {{ number_format($consignmentSales, 0, ',', '.') }}</p>
                        <div class="mt-2 pt-2 border-t border-amber-100/80 dark:border-amber-900/40 text-[11px] space-y-0.5">
                            <div class="flex justify-between text-gray-500 dark:text-gray-400">
                                <span>Hak Supplier:</span>
                                <span>Rp {{ number_format($consignmentPayable, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between font-semibold text-emerald-600 dark:text-emerald-400">
                                <span>Laba Koperasi:</span>
                                <span>Rp {{ number_format($consignmentMargin, 0, ',', '.') }} ({{ number_format($consignmentMarginPercent, 1) }}%)</span>
                            </div>
                            <div class="text-[10px] text-gray-400 pt-0.5">{{ number_format($consignmentQty) }} item terjual</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Payment Method Distribution -->
        <div class="glass-card-solid p-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Metode Pembayaran</h3>
            <div class="space-y-4">
                @php $totalPayments = $byPaymentMethod->sum('total'); @endphp
                @forelse($byPaymentMethod as $method)
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="capitalize">{{ str_replace('_', ' ', $method->payment_method ?? 'Lainnya') }}</span>
                        <span class="font-semibold">{{ $method->count }} (Rp {{ number_format($method->total, 0, ',', '.') }})</span>
                    </div>
                    @php $percentage = ($totalPayments > 0) ? ($method->total / $totalPayments) * 100 : 0; @endphp
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                         <div class="h-2 rounded-full 
                            @if($method->payment_method == 'cash') bg-green-500 
                            @elseif($method->payment_method == 'transfer') bg-blue-500
                            @elseif($method->payment_method == 'credit') bg-amber-500
                            @else bg-purple-500 @endif" 
                            style="width: {{ $percentage }}%"></div>
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-500 text-center italic py-4">Tidak ada data transaksi.</p>
                @endforelse
            </div>
        </div>

        <!-- Top Selling Products -->
        <div class="lg:col-span-2 glass-card-solid p-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Produk Terlaris</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left py-2 px-2">#</th>
                            <th class="text-left py-2 px-2">Kode</th>
                            <th class="text-left py-2 px-2">Produk</th>
                            <th class="text-right py-2 px-2">Qty</th>
                            <th class="text-right py-2 px-2">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topProducts as $index => $product)
                        <tr class="border-b border-gray-100 dark:border-gray-700/50">
                            <td class="py-2 px-2 text-gray-500">{{ $index + 1 }}</td>
                            <td class="py-2 px-2 font-mono text-xs">{{ $product->code }}</td>
                            <td class="py-2 px-2 font-medium text-gray-900 dark:text-white">
                                <div class="flex items-center gap-2">
                                    <span class="truncate max-w-[180px] sm:max-w-xs" title="{{ $product->name }}">{{ $product->name }}</span>
                                    @if($product->is_consignment)
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300 border border-amber-200 dark:border-amber-800 flex-shrink-0">
                                        Konsinyasi
                                    </span>
                                    @else
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300 flex-shrink-0">
                                        Toko
                                    </span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-2 px-2 text-right">{{ number_format($product->total_qty) }}</td>
                            <td class="py-2 px-2 text-right font-medium">Rp {{ number_format($product->total_sales, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-gray-500">Tidak ada data produk terjual.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Sales & Margin Chart -->
    <div class="glass-card-solid p-6 mb-6">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-4">
            <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Grafik Penjualan & Margin</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Mode Pengelompokan: <span class="font-semibold text-primary capitalize">{{ str_replace('_', ' ', $chartPeriod ?? 'Harian') }}</span>
                </p>
            </div>
            
            <div class="flex flex-wrap items-center gap-3">
                <!-- Mode Akumulasi vs Periodik Toggle -->
                <div class="flex items-center gap-1 bg-gray-100 dark:bg-gray-800 p-1 rounded-lg text-xs">
                    <button type="button" 
                            id="btnModePeriodic" 
                            onclick="switchChartMode('periodic')" 
                            class="px-3 py-1.5 rounded-md font-medium transition-colors bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm">
                        Periodik
                    </button>
                    <button type="button" 
                            id="btnModeCumulative" 
                            onclick="switchChartMode('cumulative')" 
                            class="px-3 py-1.5 rounded-md font-medium transition-colors text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                        Akumulasi
                    </button>
                </div>

                <!-- Quick Period Tabs -->
                <div class="flex flex-wrap gap-1 bg-gray-100 dark:bg-gray-800 p-1 rounded-lg text-xs">
                    @php
                        $periods = [
                            'daily' => 'Harian',
                            'weekly' => 'Mingguan',
                            'monthly' => 'Bulanan',
                            'quarterly' => 'Kuartalan',
                            'semi_annually' => 'Semesteran'
                        ];
                        $currentPeriod = request('chart_period', $chartPeriod ?? 'daily');
                    @endphp
                    @foreach($periods as $key => $label)
                    <button type="button" 
                            onclick="document.getElementById('chartPeriodInput').value='{{ $key }}'; document.getElementById('reportFilterForm').submit();"
                            class="px-3 py-1.5 rounded-md font-medium transition-colors {{ $currentPeriod == $key ? 'bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                        {{ $label }}
                    </button>
                    @endforeach
                </div>
            </div>
        </div>
        
        @if(isset($chartData) && $chartData->count() > 0)
        <div id="salesMarginChart"></div>
        @else
        <p class="text-sm text-gray-500 text-center italic py-8">Tidak ada data penjualan pada grafik periode ini.</p>
        @endif
    </div>

    <!-- Transactions Table -->
    <div class="glass-card-solid overflow-hidden">
        <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
             <h3 class="text-lg font-bold text-gray-900 dark:text-white">Daftar Transaksi</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Invoice</th>
                        <th>Customer</th>
                        <th>Tipe</th>
                        <th>Pembayaran</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $trx)
                    <tr>
                        <td>{{ $trx->created_at->format('d/m/Y H:i') }}</td>
                        <td class="font-mono text-xs">{{ $trx->invoice_number }}</td>
                        <td>
                            <div class="flex flex-col">
                                <span class="font-medium text-gray-900 dark:text-white">{{ $trx->user->name ?? 'Walk-in' }}</span>
                                @if($trx->cashier)
                                <span class="text-xs text-gray-500">Kasir: {{ $trx->cashier->name }}</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="badge {{ $trx->type == 'pos' ? 'badge-info' : 'badge-purple' }}">
                                {{ strtoupper($trx->type) }}
                            </span>
                        </td>
                        <td class="capitalize">{{ str_replace('_', ' ', $trx->payment_method) }}</td>
                        <td class="font-medium">Rp {{ number_format($trx->total_amount, 0, ',', '.') }}</td>
                        <td>
                            @php
                                $statusColors = [
                                    'completed' => 'success',
                                    'pending' => 'warning',
                                    'processing' => 'info',
                                    'cancelled' => 'danger',
                                ];
                                $statusLabels = [
                                    'completed' => 'Selesai',
                                    'pending' => 'Pending',
                                    'processing' => 'Dipproses',
                                    'cancelled' => 'Dibatalkan',
                                ];
                            @endphp
                            <span class="badge badge-{{ $statusColors[$trx->status] ?? 'secondary' }}">
                                {{ $statusLabels[$trx->status] ?? $trx->status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-gray-500">Tidak ada transaksi pada periode ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($transactions->hasPages())
        <div class="p-4 border-t border-gray-100 dark:border-gray-700">
            {{ $transactions->withQueryString()->links() }}
        </div>
        @endif
    </div>
@endsection

@push('styles')
<style>
    .badge-purple {
        background-color: rgba(147, 51, 234, 0.1);
        color: rgb(147, 51, 234);
    }
</style>
@endpush

@if(isset($chartData) && $chartData->count() > 0)
@push('scripts')
<script>
window.runApex(() => {
document.addEventListener('DOMContentLoaded', function() {
    const rawSales = @json($chartData->pluck('sales'));
    const rawMargin = @json($chartData->pluck('margin'));

    // Compute cumulative data
    let cumulativeSales = [];
    let cumulativeMargin = [];
    let runS = 0, runM = 0;
    for (let i = 0; i < rawSales.length; i++) {
        runS += parseFloat(rawSales[i] || 0);
        runM += parseFloat(rawMargin[i] || 0);
        cumulativeSales.push(Math.round(runS * 100) / 100);
        cumulativeMargin.push(Math.round(runM * 100) / 100);
    }

    var options = {
        series: [
            {
                name: 'Total Penjualan',
                data: rawSales
            },
            {
                name: 'Margin (Laba Kotor)',
                data: rawMargin
            }
        ],
        chart: {
            type: 'area',
            height: 330,
            toolbar: { show: false },
            fontFamily: 'Inter, sans-serif'
        },
        colors: ['#10b981', '#3b82f6'],
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.4,
                opacityTo: 0.05,
                stops: [50, 100]
            }
        },
        stroke: {
            curve: 'smooth',
            width: [2, 2]
        },
        xaxis: {
            categories: @json($chartData->pluck('label')),
            labels: { style: { colors: '#9ca3af' } }
        },
        yaxis: {
            labels: {
                formatter: function(val) {
                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
                },
                style: { colors: '#9ca3af' }
            }
        },
        dataLabels: { enabled: false },
        grid: {
            borderColor: '#374151',
            strokeDashArray: 4
        },
        tooltip: {
            shared: true,
            intersect: false,
            y: {
                formatter: function(val) {
                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
                }
            }
        },
        legend: {
            position: 'top',
            horizontalAlign: 'right',
            labels: {
                colors: '#9ca3af'
            }
        }
    };

    var chart = new ApexCharts(document.querySelector("#salesMarginChart"), options);
    chart.render();

    window.switchChartMode = function(mode) {
        const btnP = document.getElementById('btnModePeriodic');
        const btnC = document.getElementById('btnModeCumulative');
        const activeClass = "px-3 py-1.5 rounded-md font-medium transition-colors bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm";
        const inactiveClass = "px-3 py-1.5 rounded-md font-medium transition-colors text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white";
        
        if (mode === 'cumulative') {
            if (btnC) btnC.className = activeClass;
            if (btnP) btnP.className = inactiveClass;
            chart.updateSeries([
                { name: 'Penjualan (Akumulasi)', data: cumulativeSales },
                { name: 'Margin (Akumulasi)', data: cumulativeMargin }
            ]);
        } else {
            if (btnP) btnP.className = activeClass;
            if (btnC) btnC.className = inactiveClass;
            chart.updateSeries([
                { name: 'Total Penjualan', data: rawSales },
                { name: 'Margin (Laba Kotor)', data: rawMargin }
            ]);
        }
    };
});
});
</script>
@endpush
@endif
