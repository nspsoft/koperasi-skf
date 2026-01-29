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
        <form method="GET" action="{{ route('reports.transactions') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
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
                 <label class="form-label">Status</label>
                <select name="status" class="form-input">
                    <option value="">Semua Status</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Diproses</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>
            <button type="submit" class="btn-primary">
                Terapkan Filter
            </button>
        </form>
    </div>

     <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="glass-card p-6 border-l-4 border-green-500">
            <p class="text-sm text-gray-500 mb-1">Total Penjualan</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white">Rp {{ number_format($totalSales, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-1">Transaksi selesai</p>
        </div>
        <div class="glass-card p-6 border-l-4 border-blue-500">
             <p class="text-sm text-gray-500 mb-1">Total Transaksi</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($totalTransactions) }}</p>
            <p class="text-xs text-gray-400 mt-1">Transaksi selesai</p>
        </div>
        <div class="glass-card p-6 border-l-4 border-purple-500">
             <p class="text-sm text-gray-500 mb-1">Rata-rata Transaksi</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white">Rp {{ number_format($averageTransaction, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-1">Per transaksi</p>
        </div>
        <div class="glass-card p-6 border-l-4 border-amber-500">
             <p class="text-sm text-gray-500 mb-1">Order Pending</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $pendingOrders }}</p>
            <p class="text-xs text-gray-400 mt-1">Perlu diproses</p>
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
                            <td class="py-2 px-2 font-medium text-gray-900 dark:text-white">{{ $product->name }}</td>
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

    <!-- Daily Sales Chart -->
    @if($dailySales->count() > 0)
    <div class="glass-card-solid p-6 mb-6">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Grafik Penjualan Harian</h3>
        <div id="dailySalesChart"></div>
    </div>
    @endif

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
                                    'processing' => 'Diproses',
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

@if($dailySales->count() > 0)
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var options = {
        series: [{
            name: 'Penjualan',
            data: @json($dailySales->pluck('total')->map(fn($v) => (float) $v))
        }],
        chart: {
            type: 'area',
            height: 300,
            toolbar: { show: false },
            fontFamily: 'Inter, sans-serif'
        },
        colors: ['#10b981'],
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.45,
                opacityTo: 0.05,
                stops: [50, 100]
            }
        },
        stroke: {
            curve: 'smooth',
            width: 2
        },
        xaxis: {
            categories: @json($dailySales->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d M'))),
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
            y: {
                formatter: function(val) {
                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
                }
            }
        }
    };

    var chart = new ApexCharts(document.querySelector("#dailySalesChart"), options);
    chart.render();
});
</script>
@endpush
@endif
