@extends('layouts.app')

@section('title', 'Inventory Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Dashboard Gudang & Stok</h2>
            <p class="text-gray-500 dark:text-gray-400 text-sm">Kontrol stok barang dan rekomendasi pembelian cerdas.</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <form action="{{ route('inventory.dashboard') }}" method="GET" class="flex items-center gap-2">
                <select name="category_id" onchange="this.form.submit()" class="bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 text-sm rounded-xl focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 shadow-sm">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </form>
            <a href="{{ route('products.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl transition-all shadow-lg shadow-emerald-500/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Produk
            </a>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total SKU -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
                <span class="text-xs font-bold text-gray-400 uppercase">Total SKU</span>
            </div>
            <p class="text-3xl font-extrabold text-gray-900 dark:text-white">{{ number_format($totalSku) }}</p>
            <p class="text-xs text-gray-500 mt-2">Produk Aktif</p>
        </div>

        <!-- Stock Value -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <span class="text-xs font-bold text-gray-400 uppercase">Nilai Stok</span>
            </div>
            <p class="text-3xl font-extrabold text-gray-900 dark:text-white">Rp {{ number_format($totalStockValue / 1000000, 1) }}M</p>
            <p class="text-xs text-gray-500 mt-2">Total: Rp {{ number_format($totalStockValue) }}</p>
        </div>

        <!-- Low Stock -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <span class="text-xs font-bold text-gray-400 uppercase">Stok Menipis</span>
            </div>
            <p class="text-3xl font-extrabold text-amber-600">{{ number_format($lowStockCount) }}</p>
            <p class="text-xs text-gray-500 mt-2">Perlu Reorder</p>
        </div>

        <!-- Out of Stock -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <span class="text-xs font-bold text-gray-400 uppercase">Stok Habis</span>
            </div>
            <p class="text-3xl font-extrabold text-red-600">{{ number_format($outOfStockCount) }}</p>
            <p class="text-xs text-gray-500 mt-2">Segera Cek Gudang</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Charts Section -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Top Products Analysis -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700" x-data="{ metric: 'revenue' }">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">Analisis Produk Unggulan (Top 10)</h3>
                    <div class="flex p-1 bg-gray-100 dark:bg-gray-700 rounded-xl">
                        <button @click="metric = 'qty'; updateTopChart('qty')" :class="metric === 'qty' ? 'bg-white dark:bg-gray-600 shadow-sm text-indigo-600 dark:text-white' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400'" class="px-3 py-1.5 text-xs font-bold rounded-lg transition-all">Transaksi</button>
                        <button @click="metric = 'revenue'; updateTopChart('revenue')" :class="metric === 'revenue' ? 'bg-white dark:bg-gray-600 shadow-sm text-indigo-600 dark:text-white' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400'" class="px-3 py-1.5 text-xs font-bold rounded-lg transition-all">Omset</button>
                        <button @click="metric = 'profit'; updateTopChart('profit')" :class="metric === 'profit' ? 'bg-white dark:bg-gray-600 shadow-sm text-indigo-600 dark:text-white' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400'" class="px-3 py-1.5 text-xs font-bold rounded-lg transition-all">Profit</button>
                    </div>
                </div>
                <div id="topProductsChart" class="min-h-[350px]"></div>
                <p class="text-[10px] text-gray-400 mt-2 text-center italic">*Data berdasarkan transaksi 30 hari terakhir. Garis (Line) menunjukkan kontribusi profit relatif.</p>
            </div>

            <!-- Analisis Pergerakan Stok (In / Out) -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800 dark:text-white">Tren Pergerakan Stok (In & Out)</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Analisis perbandingan barang masuk vs barang keluar (7 hari terakhir).</p>
                    </div>
                    <span class="px-2.5 py-1 bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-400 text-xs font-bold rounded-lg uppercase tracking-wider">7 Hari Terakhir</span>
                </div>
                <div id="stockMovementChart" class="min-h-[300px]"></div>
                <div class="mt-4 grid grid-cols-2 gap-4 border-t border-gray-100 dark:border-gray-700 pt-4">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 13l-7 7-7-7m14-6l-7 7-7-7"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Total Stok Masuk</p>
                            <p class="text-lg font-bold text-gray-850 dark:text-white">{{ number_format($stockMovementTrend->sum('in')) }} Unit</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-red-50 dark:bg-red-950/40 text-red-600 dark:text-red-400 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 11l7-7 7 7M5 19l7-7 7 7"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Total Stok Keluar</p>
                            <p class="text-lg font-bold text-gray-850 dark:text-white">{{ number_format($stockMovementTrend->sum('out')) }} Unit</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stock Value by Category -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-6">Distribusi Nilai Stok per Kategori</h3>
                <div id="categoryChart" class="min-h-[300px]"></div>
            </div>

            <!-- Stock Value by Category -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-6">Analisis Stok Mengendap (Slow Moving)</h3>
                <div id="slowMovingChart" class="min-h-[300px]"></div>
                <p class="text-xs text-gray-400 mt-4 italic">*Menampilkan 5 produk dengan stok tertinggi yang penjualannya < 5 unit dalam 30 hari terakhir.</p>
            </div>

            <!-- Stock Value by Category -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-2">Analisis Prioritas (ABC)</h3>
                <p class="text-xs text-gray-500 mb-6">Mengklasifikasikan produk berdasarkan kontribusi profit 30 hari terakhir.</p>
                <div id="abcChart" class="min-h-[300px]"></div>
                <div class="mt-4 grid grid-cols-3 gap-2 text-center">
                    <div class="p-2 bg-indigo-50 dark:bg-indigo-900/20 rounded-xl">
                        <p class="text-[10px] text-indigo-600 font-bold uppercase">Grup A</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $abcAnalysis['A'] }}</p>
                    </div>
                    <div class="p-2 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl">
                        <p class="text-[10px] text-emerald-600 font-bold uppercase">Grup B</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $abcAnalysis['B'] }}</p>
                    </div>
                    <div class="p-2 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                        <p class="text-[10px] text-gray-500 font-bold uppercase">Grup C</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $abcAnalysis['C'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Overstock Analysis -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-red-100 dark:border-red-900/30">
                <div class="flex items-center gap-2 text-red-600 mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <h3 class="text-lg font-bold">Analisis Overstock (Kelebihan Beli)</h3>
                </div>
                <p class="text-xs text-gray-500 mb-6">Produk dengan jumlah stok melebihi 3x lipat batas aman (Min. Stok). Ini menandakan adanya modal yang mengendap terlalu besar.</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-1 gap-4">
                    @forelse($overstock as $product)
                    <div class="p-4 bg-red-50 dark:bg-red-900/10 rounded-xl border border-red-100 dark:border-red-900/20">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $product->name }}</p>
                                <p class="text-[10px] text-red-600 font-medium">Stok saat ini: {{ number_format($product->stock) }} ({{ number_format($product->stock / $product->min_stock, 1) }}x Min. Stok)</p>
                            </div>
                            <span class="px-2 py-1 bg-red-100 text-red-700 text-[10px] font-bold rounded-lg uppercase">Overstock</span>
                        </div>
                        <div class="w-full bg-red-200 dark:bg-red-900/30 rounded-full h-1.5 overflow-hidden">
                            <div class="bg-red-500 h-1.5 rounded-full" style="width: 100%"></div>
                        </div>
                    </div>
                    @empty
                    <div class="py-6 text-center text-gray-500 italic text-sm">
                        Tidak ditemukan pembelian barang yang berlebihan (overstock).
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Smart Purchase Recommendations -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800 dark:text-white">Rekomendasi Pembelian</h3>
                        <p class="text-xs text-gray-500 mt-1 italic">Dihitung otomatis berdasarkan stok minimum.</p>
                    </div>
                    <a href="{{ route('purchases.create') }}" class="text-sm font-bold text-indigo-600 hover:text-indigo-700">Buat Pembelian</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-6 py-3 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Produk</th>
                                <th class="px-6 py-3 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-center">Stok Saat Ini</th>
                                <th class="px-6 py-3 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-center">Min. Stok</th>
                                <th class="px-6 py-3 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-center">Rata2 (Hari/Mgg)</th>
                                <th class="px-6 py-3 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-center">Estimasi Habis</th>
                                <th class="px-6 py-3 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-center">Saran Order</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($recommendations as $product)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center overflow-hidden">
                                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $product->name }}</p>
                                            @if($product->abc == 'A')
                                                <span class="px-1.5 py-0.5 bg-indigo-100 text-indigo-700 text-[10px] font-bold rounded uppercase">A</span>
                                            @elseif($product->abc == 'B')
                                                <span class="px-1.5 py-0.5 bg-emerald-100 text-emerald-700 text-[10px] font-bold rounded uppercase">B</span>
                                            @else
                                                <span class="px-1.5 py-0.5 bg-gray-100 text-gray-500 text-[10px] font-bold rounded uppercase">C</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-sm font-bold {{ $product->stock <= 0 ? 'text-red-600' : 'text-amber-600' }}">
                                        {{ number_format($product->stock) }} {{ $product->unit }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center text-sm text-gray-500">
                                    {{ number_format($product->min_stock) }} {{ $product->unit }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex flex-col items-center">
                                        <span class="text-xs font-bold text-gray-700 dark:text-gray-300">{{ $product->daily_avg }} / hr</span>
                                        <span class="text-[10px] text-gray-400">{{ $product->weekly_avg }} / mgg</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($product->stock <= 0)
                                        <span class="px-2 py-1 bg-red-100 text-red-700 text-[10px] font-bold rounded-lg uppercase">Habis</span>
                                    @elseif($product->days_remaining >= 999)
                                        <span class="text-xs text-emerald-600 font-bold">Aman</span>
                                    @else
                                        <span class="text-xs font-bold {{ $product->days_remaining <= 3 ? 'text-red-600' : 'text-amber-600' }}">
                                            {{ $product->days_remaining }} Hari Lagi
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2 py-1 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 text-xs font-bold rounded-lg">
                                        +{{ number_format(max(10, $product->min_stock * 2 - $product->stock)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('products.edit', $product) }}" class="text-gray-400 hover:text-indigo-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-gray-500">Semua stok aman terjaga.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar Section -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-indigo-600 dark:bg-indigo-900/50 p-6 rounded-2xl text-white shadow-lg shadow-indigo-200 dark:shadow-none">
                <h3 class="font-bold mb-4 text-white">Aksi Cepat</h3>
                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ route('stock-opname.create') }}" class="p-3 bg-white/20 hover:bg-white/30 rounded-xl text-center transition backdrop-blur-sm border border-white/10">
                        <svg class="w-6 h-6 mx-auto mb-2 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        <span class="text-xs font-bold text-white">Opname</span>
                    </a>
                    <a href="{{ route('products.bulk') }}" class="p-3 bg-white/20 hover:bg-white/30 rounded-xl text-center transition backdrop-blur-sm border border-white/10">
                        <svg class="w-6 h-6 mx-auto mb-2 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        <span class="text-xs font-bold text-white">Import</span>
                    </a>
                </div>
            </div>

            <!-- Recent Outgoing Stock -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <h3 class="font-bold text-gray-800 dark:text-white mb-4">Penjualan Terkini</h3>
                <div class="space-y-4">
                    @forelse($recentSales as $item)
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-gray-50 dark:bg-gray-700/50 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ $item->product->name }}</p>
                            <p class="text-xs text-gray-500">{{ $item->created_at->diffForHumans() }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-red-500">-{{ number_format($item->quantity) }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-xs text-gray-500 text-center py-4">Belum ada transaksi hari ini.</p>
                    @endforelse
                </div>
            </div>

            <!-- Slow Moving Products -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-gray-800 dark:text-white">Stock Slow Moving</h3>
                    <span class="px-2 py-0.5 bg-amber-100 text-amber-600 text-[10px] font-bold rounded-full uppercase tracking-tighter">30 Hari Terakhir</span>
                </div>
                <p class="text-xs text-gray-500 mb-4 italic">Produk dengan stok tinggi namun penjualan rendah.</p>
                <div class="space-y-4">
                    @forelse($slowMoving as $product)
                    @php
                        $sold = $product->transactionItems->where('created_at', '>=', now()->subDays(30))->sum('quantity');
                        $percentage = $product->stock > 0 ? max(0, min(100, (($product->stock - $sold) / $product->stock) * 100)) : 100;
                    @endphp
                    <div class="p-3 bg-gray-50 dark:bg-gray-700/30 rounded-xl border border-gray-100/80 dark:border-gray-700/50">
                        <div class="flex justify-between items-start mb-2">
                            <div class="min-w-0 flex-1 pr-2">
                                <div class="flex items-center gap-1.5 mb-0.5">
                                    <span class="text-sm font-bold text-gray-900 dark:text-white truncate block" title="{{ $product->name }}">{{ $product->name }}</span>
                                    @if(($product->abc ?? 'C') == 'A')
                                        <span class="px-1.5 py-0.5 bg-indigo-100 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-400 text-[8px] font-extrabold rounded leading-none shrink-0">A</span>
                                    @endif
                                </div>
                                <span class="text-xs text-gray-500 dark:text-gray-400">Stok: <strong class="text-gray-800 dark:text-gray-200">{{ number_format($product->stock) }} {{ $product->unit }}</strong></span>
                            </div>
                            <span class="px-2 py-0.5 bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 text-[9px] font-bold rounded-lg uppercase tracking-wider shrink-0">Slow Moving</span>
                        </div>
                        
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5 overflow-hidden">
                            <div class="bg-amber-500 h-1.5 rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>
                        
                        <div class="flex justify-between items-center mt-2">
                            <span class="text-[10px] text-gray-500 dark:text-gray-400 font-medium">
                                Terjual: <strong class="text-gray-800 dark:text-gray-200">{{ number_format($sold) }}</strong>
                            </span>
                            <span class="text-[10px] text-amber-600 dark:text-amber-400 font-bold">Stok Mengendap ({{ number_format($percentage, 0) }}%)</span>
                        </div>
                    </div>
                    @empty
                    <p class="text-xs text-gray-500 text-center py-4">Tidak ada data stok mengendap.</p>
                    @endforelse
                </div>
                @if($slowMoving->count() > 0)
                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <button class="w-full py-2 bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 text-xs font-bold text-gray-600 dark:text-gray-400 rounded-xl transition">
                        Lihat Analisis Lengkap
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const isDark = document.documentElement.classList.contains('dark');
        const themeMode = isDark ? 'dark' : 'light';

        // Helper to safely render charts
        function renderChart(selector, options) {
            const el = document.querySelector(selector);
            if (el) {
                const chart = new ApexCharts(el, options);
                chart.render();
                return chart;
            }
            return null;
        }

        // 1. Top Products Combo Chart
        const topData = @json($topProductsData);
        let topChart;

        window.updateTopChart = function(metric = 'revenue') {
            // Sort data based on metric
            const sortedData = [...topData].sort((a, b) => {
                if (metric === 'qty') return b.qty - a.qty;
                if (metric === 'profit') return b.profit - a.profit;
                return b.revenue - a.revenue;
            });

            let seriesName = 'Omset (Rp)';
            let yData = sortedData.map(d => d.revenue);
            
            if (metric === 'qty') {
                seriesName = 'Jumlah Transaksi';
                yData = sortedData.map(d => d.qty);
            } else if (metric === 'profit') {
                seriesName = 'Profit (Rp)';
                yData = sortedData.map(d => d.profit);
            }

            const options = {
                series: [{
                    name: seriesName,
                    type: 'column',
                    data: yData
                }, {
                    name: 'Kontribusi Profit (%)',
                    type: 'line',
                    data: sortedData.map(d => d.profit_pct)
                }],
                chart: {
                    height: 350,
                    type: 'line',
                    stacked: false,
                    fontFamily: 'Inter, sans-serif',
                    toolbar: { show: false }
                },
                stroke: { width: [0, 4], curve: 'smooth' },
                plotOptions: { bar: { columnWidth: '50%', borderRadius: 6 } },
                colors: ['#4f46e5', '#10b981'],
                dataLabels: {
                    enabled: true,
                    enabledOnSeries: [0],
                    formatter: function(val) {
                        return metric === 'qty' ? val : 'Rp ' + (val / 1000).toFixed(0) + 'k';
                    },
                    style: { fontSize: '10px' }
                },
                labels: sortedData.map(d => d.name),
                xaxis: {
                    type: 'category',
                    labels: { rotate: -45, maxHeight: 100, style: { fontSize: '10px' } }
                },
                yaxis: [{
                    title: { text: seriesName, style: { color: '#4f46e5' } },
                    labels: {
                        formatter: function(val) {
                            return metric === 'qty' ? val : (val / 1000).toFixed(0) + 'k';
                        }
                    }
                }, {
                    opposite: true,
                    title: { text: 'Kontribusi Profit (%)', style: { color: '#10b981' } },
                    labels: {
                        formatter: function(val) {
                            return val + '%';
                        }
                    }
                }],
                legend: { position: 'top', horizontalAlign: 'right' },
                theme: { mode: themeMode }
            };

            if (topChart) {
                topChart.updateOptions(options);
            } else {
                topChart = renderChart("#topProductsChart", options);
            }
        };

        // Initialize Top Chart
        window.updateTopChart('revenue');

        // 2. Stock Movement (In & Out) Chart
        const movementData = @json($stockMovementTrend);
        renderChart("#stockMovementChart", {
            series: [{
                name: 'Barang Masuk (Stock In)',
                type: 'column',
                data: movementData.map(d => d.in)
            }, {
                name: 'Barang Keluar (Stock Out)',
                type: 'area',
                data: movementData.map(d => d.out)
            }],
            chart: {
                height: 320,
                type: 'line',
                stacked: false,
                fontFamily: 'Inter, sans-serif',
                toolbar: { show: false },
                zoom: { enabled: false }
            },
            colors: ['#10b981', '#ef4444'], // green for in, red for out
            stroke: {
                width: [0, 3],
                curve: 'smooth'
            },
            plotOptions: {
                bar: {
                    columnWidth: '45%',
                    borderRadius: 4
                }
            },
            fill: {
                type: ['solid', 'gradient'],
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.3,
                    opacityTo: 0.05,
                    stops: [0, 90, 100]
                }
            },
            dataLabels: {
                enabled: true,
                enabledOnSeries: [0, 1],
                style: { fontSize: '10px' }
            },
            xaxis: {
                categories: movementData.map(d => d.date),
                labels: { style: { colors: '#6b7280' } }
            },
            yaxis: {
                title: { text: 'Jumlah Unit' },
                labels: { style: { colors: '#6b7280' } }
            },
            grid: { borderColor: '#f1f1f1', strokeDashArray: 4 },
            legend: { position: 'top', horizontalAlign: 'right' },
            theme: { mode: themeMode }
        });

        // 3. Category Distribution Chart
        const categories = @json($stockValueByCategory);
        renderChart("#categoryChart", {
            series: categories.map(c => c.value),
            labels: categories.map(c => c.name),
            chart: {
                type: 'donut',
                height: 350,
                fontFamily: 'Inter, sans-serif'
            },
            colors: ['#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4'],
            stroke: { show: false },
            plotOptions: {
                pie: {
                    donut: {
                        size: '75%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Total Nilai',
                                formatter: function (w) {
                                    const total = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                    return 'Rp ' + (total / 1000000).toFixed(1) + 'M';
                                }
                            }
                        }
                    }
                }
            },
            legend: { position: 'bottom', fontSize: '12px', markers: { radius: 12 } },
            theme: { mode: themeMode }
        });

        // 4. Slow Moving Chart
        const slowMovingData = @json($slowMoving);
        renderChart("#slowMovingChart", {
            series: [{
                name: 'Jumlah Stok',
                data: slowMovingData.map(p => p.stock)
            }],
            chart: {
                type: 'bar',
                height: 300,
                fontFamily: 'Inter, sans-serif',
                toolbar: { show: false }
            },
            plotOptions: {
                bar: { horizontal: true, borderRadius: 8, barHeight: '60%', distributed: true }
            },
            colors: ['#f59e0b', '#fbbf24', '#fcd34d', '#fde68a', '#fef3c7'],
            dataLabels: {
                enabled: true,
                formatter: function(val) { return val + ' unit'; },
                style: { fontSize: '10px' }
            },
            xaxis: {
                categories: slowMovingData.map(p => p.name),
                labels: { style: { colors: '#6b7280' } }
            },
            yaxis: {
                labels: { maxWidth: 150, style: { colors: '#6b7280', fontWeight: 600 } }
            },
            grid: { borderColor: '#f1f1f1', strokeDashArray: 4 },
            legend: { show: false },
            theme: { mode: themeMode }
        });

        // 5. ABC Analysis Chart
        const abcData = @json($abcAnalysis);
        renderChart("#abcChart", {
            series: [abcData.A, abcData.B, abcData.C],
            labels: ['Grup A (Prioritas)', 'Grup B (Menengah)', 'Grup C (Pelengkap)'],
            chart: {
                type: 'donut',
                height: 300,
                fontFamily: 'Inter, sans-serif'
            },
            colors: ['#4f46e5', '#10b981', '#9ca3af'],
            stroke: { show: false },
            plotOptions: {
                pie: {
                    donut: {
                        size: '70%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Total Produk',
                                formatter: function (w) {
                                    return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                }
                            }
                        }
                    }
                }
            },
            legend: { position: 'bottom', fontSize: '11px' },
            theme: { mode: themeMode }
        });
    });
</script>
@endpush
@endsection
