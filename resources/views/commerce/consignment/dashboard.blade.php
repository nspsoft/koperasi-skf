@extends('layouts.app')

@section('title', 'Dashboard Konsinyasi')

@section('content')

    <!-- Page Header -->
    <div class="page-header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="page-title">Dashboard Konsinyasi</h1>
                <p class="page-subtitle">Ringkasan performa penjualan barang titipan</p>
            </div>
            <div class="text-sm text-gray-500 dark:text-gray-400">
                {{ now()->isoFormat('MMMM Y') }}
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Profit Berjalan -->
        <div class="stat-card gradient-success">
            <div class="relative z-10">
                <div class="flex justify-between items-start">
                    <p class="text-white/80 text-sm font-medium mb-1">Profit Konsinyasi Berjalan</p>
                    @php
                        $sales = $realtimeConsignment->total_sales ?? 0;
                        $profit = $realtimeConsignment->total_profit ?? 0;
                        $margin = $sales > 0 ? ($profit / $sales) * 100 : 0;
                    @endphp
                    <span class="px-2 py-1 bg-white/20 rounded-lg text-xs font-bold text-white">
                        {{ number_format($margin, 1) }}%
                    </span>
                </div>
                <h3 class="text-3xl font-bold mb-2">Rp {{ number_format($profit, 0, ',', '.') }}</h3>
                <p class="text-white/70 text-xs">Penjualan (Sales) Bulan Ini: Rp {{ number_format($sales, 0, ',', '.') }}</p>
            </div>
            <svg class="stat-card-icon" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"></path>
            </svg>
        </div>

        <!-- Hutang Berjalan -->
        <div class="stat-card gradient-danger">
            <div class="relative z-10">
                <p class="text-white/80 text-sm font-medium mb-1">Hutang ke Supplier</p>
                <h3 class="text-3xl font-bold mb-2">Rp {{ number_format($totalUnpaid, 0, ',', '.') }}</h3>
                <p class="text-white/70 text-xs">Menunggu Penyelesaian (Settlement)</p>
            </div>
            <svg class="stat-card-icon" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
            </svg>
        </div>

        <!-- Stok Konsinyasi -->
        <div class="stat-card gradient-primary">
            <div class="relative z-10">
                <p class="text-white/80 text-sm font-medium mb-1">Total Nilai Stok</p>
                <h3 class="text-3xl font-bold mb-2">Rp {{ number_format($stockValue, 0, ',', '.') }}</h3>
                <p class="text-white/70 text-xs">Nilai Barang Titipan Tersedia</p>
            </div>
            <svg class="stat-card-icon" fill="currentColor" viewBox="0 0 20 20">
                <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
            </svg>
        </div>

        <!-- Inbound / Return -->
        <div class="stat-card gradient-accent">
            <div class="relative z-10">
                <p class="text-white/80 text-sm font-medium mb-1">Barang Masuk / Retur</p>
                <h3 class="text-3xl font-bold mb-2">{{ number_format($inboundMonth) }} / {{ number_format($returnMonth) }}</h3>
                <p class="text-white/70 text-xs">Kuantitas Item (Bulan Ini)</p>
            </div>
            <svg class="stat-card-icon" fill="currentColor" viewBox="0 0 20 20">
                <path d="M8 5a1 1 0 100 2h5.586l-1.293 1.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L13.586 5H8zM12 15a1 1 0 100-2H6.414l1.293-1.293a1 1 0 10-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L6.414 15H12z"></path>
            </svg>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Sales & Profit Trend (Line Chart) -->
        <div class="glass-card-solid p-6 lg:col-span-2">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Tren Konsinyasi Tahun Ini</h2>
                    <p class="text-sm text-gray-500">Perkembangan Omset Penjualan dan Keuntungan (Klik batang/garis untuk detail per supplier)</p>
                </div>
            </div>
            <div id="trendChart"></div>
        </div>

        <!-- Top Suppliers Chart -->
        <div class="glass-card-solid p-6" x-data="{ sortBy: 'profit' }">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Top 5 Supplier</h2>
                    <p class="text-xs text-gray-500">Supplier Konsinyasi Terbaik</p>
                </div>
                
                <!-- Toggle -->
                <div class="flex bg-gray-100 dark:bg-gray-800 rounded-lg p-1">
                    <button @click="sortBy = 'sales'; updateSupplierChart('sales')" 
                            :class="sortBy === 'sales' ? 'bg-white dark:bg-gray-700 shadow text-gray-900 dark:text-white' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'"
                            class="px-3 py-1 text-xs font-medium rounded-md transition-all">
                        Omset
                    </button>
                    <button @click="sortBy = 'profit'; updateSupplierChart('profit')" 
                            :class="sortBy === 'profit' ? 'bg-white dark:bg-gray-700 shadow text-gray-900 dark:text-white' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'"
                            class="px-3 py-1 text-xs font-medium rounded-md transition-all">
                        Profit
                    </button>
                </div>
            </div>
            
            <div id="supplierChart" class="flex justify-center mt-2"></div>
            
            <div class="mt-4 text-center">
                <a href="{{ route('consignment.settlements.index') }}" class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400 font-medium">
                    Lihat Pembayaran Supplier →
                </a>
            </div>
        </div>
    </div>

    <!-- Drilldown Modal -->
    <div id="supplierDrilldownModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
        <div class="w-full max-w-4xl rounded-2xl bg-white dark:bg-gray-900 shadow-2xl border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div>
                    <h3 id="drilldownTitle" class="text-lg font-semibold text-gray-900 dark:text-white">Detail Supplier</h3>
                    <p class="text-xs text-gray-500">Omset dan profit berdasarkan supplier</p>
                </div>
                <button id="closeDrilldownModal" type="button" class="px-3 py-1.5 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700">Tutup</button>
            </div>
            <div class="p-6">
                <div id="drilldownChart"></div>
            </div>
        </div>
    </div>

    <!-- Data Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Pending Settlements -->
        <div class="glass-card-solid p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                    <span class="text-2xl">⏳</span> Tagihan Menunggu Pembayaran
                </h2>
                <a href="{{ route('consignment.settlements.index') }}" class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400 font-medium">
                    Kelola →
                </a>
            </div>
            <div class="space-y-4">
                @forelse($pendingSettlements as $settlement)
                <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700 hover:shadow-md transition-all">
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white text-sm">{{ $settlement->consignor->name ?? 'Unknown' }}</p>
                        <p class="text-xs text-gray-500">No: {{ $settlement->transaction_number }} | Periode: {{ $settlement->period_end->format('d M Y') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-red-600">Rp {{ number_format($settlement->total_payable_amount, 0, ',', '.') }}</p>
                        <span class="badge badge-warning text-[10px]">Belum Dibayar</span>
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-gray-500 dark:text-gray-400 border border-dashed border-gray-200 dark:border-gray-700 rounded-xl">
                    <p class="text-3xl mb-2 opacity-50">👍</p>
                    <p class="text-sm font-medium">Semua tagihan lunas!</p>
                </div>
                @endforelse

                @if(isset($pendingSettlementsCount) && $pendingSettlementsCount > 5)
                <div class="text-center pt-2">
                    <a href="{{ route('consignment.settlements.create') }}" class="text-xs font-medium text-gray-500 hover:text-primary-600 transition-colors">
                        Lihat {{ $pendingSettlementsCount - 5 }} tagihan lainnya &rarr;
                    </a>
                </div>
                @endif
            </div>
        </div>

        <!-- Highest Stock Value -->
        <div class="glass-card-solid p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                    <span class="text-2xl">📦</span> Stok Terbesar (Nilai)
                </h2>
                <a href="{{ route('products.index', ['is_consignment' => 1]) }}" class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400 font-medium">
                    Lihat Produk →
                </a>
            </div>
            <div class="space-y-4">
                @forelse($highestStockValue as $product)
                <div class="flex items-center gap-4 p-3 rounded-xl bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700 hover:shadow-md transition-all">
                    <div class="w-12 h-12 rounded-lg overflow-hidden bg-white dark:bg-gray-700 flex-shrink-0">
                        @if($product->image)
                            <img src="{{ Storage::url($product->image) }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-xl">🛒</div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-sm text-gray-900 dark:text-white truncate">{{ $product->name }}</p>
                        <p class="text-xs text-gray-500">Stok: {{ $product->stock }} {{ $product->unit }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-gray-700 dark:text-gray-300">Rp {{ number_format($product->total_value, 0, ',', '.') }}</p>
                        <p class="text-[10px] text-gray-400 font-bold uppercase">Estimasi Nilai</p>
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-gray-500 dark:text-gray-400 border border-dashed border-gray-200 dark:border-gray-700 rounded-xl">
                    Belum ada stok barang titipan.
                </div>
                @endforelse
            </div>
        </div>
    </div>

@endsection

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
                animations: { enabled: true }
            },
            dataLabels: { enabled: false },
            tooltip: {
                theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light',
                shared: true,
                intersect: false,
                y: {
                    formatter: function (val, { series, seriesIndex, dataPointIndex, w }) {
                        let base = 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
                        // If it's the profit series (index 1), let's calculate the margin dynamically from sales (index 0)
                        if (seriesIndex === 1 && series[0][dataPointIndex] > 0) {
                            const omset = series[0][dataPointIndex];
                            const margin = (val / omset) * 100;
                            return base + ' <span class="ml-1 text-xs font-bold text-emerald-500">(' + margin.toFixed(1) + '%)</span>';
                        }
                        return base;
                    }
                }
            }
        };

        const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        // 1. Trend Chart (Line/Bar)
        const trendDataSales = @json(array_values($monthlySales));
        const trendDataProfit = @json(array_values($monthlyProfit));

        const trendOptions = {
            ...commonOptions,
            series: [{
                name: 'Omset Konsinyasi',
                type: 'column',
                data: trendDataSales
            }, {
                name: 'Profit Konsinyasi',
                type: 'line',
                data: trendDataProfit
            }],
            chart: {
                height: 350,
                type: 'line',
                toolbar: { show: false },
                events: {
                    dataPointSelection: function(event, chartContext, config) {
                        openSupplierModal(config.dataPointIndex);
                    }
                }
            },
            stroke: {
                width: [0, 4],
                curve: 'smooth'
            },
            plotOptions: {
                bar: { borderRadius: 4, columnWidth: '40%' }
            },
            colors: ['#8b5cf6', '#10b981'], // Violet for sales, emerald for profit
            fill: { opacity: [1, 1] },
            labels: monthNames,
            xaxis: {
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: { style: { colors: '#9ca3af' } }
            },
            yaxis: [{
                title: { text: 'Omset', style: { color: '#8b5cf6' } },
                labels: {
                    style: { colors: '#9ca3af' },
                    formatter: (value) => 'Rp ' + (value / 1000000).toFixed(1) + 'jt'
                }
            }, {
                opposite: true,
                title: { text: 'Profit', style: { color: '#10b981' } },
                labels: {
                    style: { colors: '#9ca3af' },
                    formatter: (value) => 'Rp ' + (value / 1000000).toFixed(1) + 'jt'
                }
            }]
        };

        new ApexCharts(document.querySelector("#trendChart"), trendOptions).render();

        // 2. Top Supplier Chart
        const suppliersBySales = @json($topSuppliersBySales);
        const suppliersByProfit = @json($topSuppliersByProfit);

        let supplierChart = null;

        window.updateSupplierChart = function(mode) {
            const data = mode === 'sales' ? suppliersBySales : suppliersByProfit;
            const seriesData = data.map(item => mode === 'sales' ? item.sales : item.profit);
            const categoriesData = data.map(item => item.name);
            const color = mode === 'sales' ? '#8b5cf6' : '#10b981';
            const labelTitle = mode === 'sales' ? 'Omset' : 'Profit';

            if (supplierChart) {
                supplierChart.updateSeries([{ name: labelTitle, data: seriesData }]);
                supplierChart.updateOptions({ 
                    xaxis: { categories: categoriesData },
                    colors: [color]
                });
            } else {
                const supplierOptions = {
                    ...commonOptions,
                    series: [{
                        name: labelTitle,
                        data: seriesData
                    }],
                    chart: {
                        type: 'bar',
                        height: 350,
                        toolbar: { show: false }
                    },
                    plotOptions: {
                        bar: {
                            horizontal: true,
                            borderRadius: 4,
                        }
                    },
                    colors: [color],
                    xaxis: {
                        categories: categoriesData,
                        labels: {
                            formatter: function(val) {
                                return 'Rp ' + (val / 1000000).toFixed(1) + 'jt';
                            }
                        }
                    }
                };
                supplierChart = new ApexCharts(document.querySelector("#supplierChart"), supplierOptions);
                supplierChart.render();
            }
        };

        // Init supplier chart with default (profit)
        updateSupplierChart('profit');

        // 3. Drilldown Chart Logic
        const monthlySupplierData = @json($monthlySupplierData);
        const modalEl = document.getElementById('supplierDrilldownModal');
        const closeModalButton = document.getElementById('closeDrilldownModal');
        const drilldownTitle = document.getElementById('drilldownTitle');
        let drilldownChart = null;

        window.openSupplierModal = function(monthIndex) {
            // ApexCharts month index starts from 0, PHP array starts from 1
            const monthNumber = monthIndex + 1;
            const monthName = monthNames[monthIndex];
            const dataset = monthlySupplierData[monthNumber] || [];

            if (dataset.length === 0) {
                // You could show a toast or alert if no data, but empty chart is fine too
            }

            drilldownTitle.textContent = `Performa Supplier - ${monthName}`;
            modalEl.classList.remove('hidden');
            modalEl.classList.add('flex');

            const categories = dataset.map(item => item.name);
            const salesData = dataset.map(item => item.sales);
            const profitData = dataset.map(item => item.profit);

            if (drilldownChart) {
                drilldownChart.destroy();
            }

            const drilldownOptions = {
                ...commonOptions,
                series: [{
                    name: 'Omset',
                    type: 'column',
                    data: salesData
                }, {
                    name: 'Profit',
                    type: 'line',
                    data: profitData
                }],
                chart: {
                    height: 350,
                    type: 'line',
                    toolbar: { show: false }
                },
                stroke: {
                    width: [0, 3],
                    curve: 'smooth'
                },
                plotOptions: {
                    bar: { borderRadius: 4, columnWidth: '50%' }
                },
                colors: ['#8b5cf6', '#10b981'],
                labels: categories,
                xaxis: {
                    labels: { style: { colors: '#9ca3af' } }
                },
                yaxis: [{
                    title: { text: 'Omset', style: { color: '#8b5cf6' } },
                    labels: {
                        style: { colors: '#9ca3af' },
                        formatter: (value) => 'Rp ' + (value / 1000).toFixed(0) + 'k'
                    }
                }, {
                    opposite: true,
                    title: { text: 'Profit', style: { color: '#10b981' } },
                    labels: {
                        style: { colors: '#9ca3af' },
                        formatter: (value) => 'Rp ' + (value / 1000).toFixed(0) + 'k'
                    }
                }]
            };

            drilldownChart = new ApexCharts(document.querySelector("#drilldownChart"), drilldownOptions);
            drilldownChart.render();
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
    });
</script>
@endpush
