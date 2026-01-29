@extends('layouts.app')

@section('title', 'Dashboard')

@php
    $appUrl = \App\Models\Setting::get('app_url', url('/'));
@endphp

@section('content')
    <div class="page-header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="page-title">Dashboard Anggota</h1>
                <p class="page-subtitle">{{ $member->member_id }} - {{ auth()->user()->name }}</p>
            </div>
            <div class="flex gap-2">
                <button onclick="window.open('{{ route('members.digital-card', $member) }}', 'DigitalCard', 'width=400,height=800')" 
                        class="btn-primary flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0c0 .884-.896 1.763-2.24 1.763-1.344 0-2.24-.88-2.24-1.763 0-.88.9-1.763 2.24-1.763 1.344 0 2.24.88 2.24 1.763z"></path>
                    </svg>
                    Kartu Anggota
                </button>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
        <!-- Total Savings -->
        <div class="stat-card gradient-primary">
            <div class="relative z-10">
                <p class="text-white/80 text-sm font-medium mb-1">Total Simpanan</p>
                <h3 class="text-2xl font-bold mb-2">Rp {{ number_format($stats['total_savings'], 0, ',', '.') }}</h3>
                <p class="text-white/70 text-xs">Semua Jenis Simpanan</p>
            </div>
        </div>

        <!-- Simpanan Pokok -->
        <div class="stat-card gradient-success">
            <div class="relative z-10">
                <p class="text-white/80 text-sm font-medium mb-1">Simpanan Pokok</p>
                <h3 class="text-2xl font-bold mb-2">Rp {{ number_format($stats['simpanan_pokok'], 0, ',', '.') }}</h3>
                <p class="text-white/70 text-xs">Saat Mendaftar</p>
            </div>
        </div>

        <!-- Simpanan Wajib -->
        <div class="stat-card gradient-accent">
            <div class="relative z-10">
                <p class="text-white/80 text-sm font-medium mb-1">Simpanan Wajib</p>
                <h3 class="text-2xl font-bold mb-2">Rp {{ number_format($stats['simpanan_wajib'], 0, ',', '.') }}</h3>
                <p class="text-white/70 text-xs">Bulanan</p>
            </div>
        </div>

        <!-- Active Loans -->
        <div class="stat-card gradient-danger">
            <div class="relative z-10">
                <p class="text-white/80 text-sm font-medium mb-1">Pinjaman Aktif</p>
                <h3 class="text-2xl font-bold mb-2">Rp {{ number_format($stats['active_loans'], 0, ',', '.') }}</h3>
                <p class="text-white/70 text-xs">Belum Lunas</p>
            </div>
        </div>

        <!-- Loyalty Points -->
        <div class="stat-card bg-gradient-to-br from-purple-500 to-indigo-600 text-white">
            <div class="relative z-10">
                <p class="text-white/80 text-sm font-medium mb-1 flex items-center gap-1">
                    <span>⭐</span> Poin Belanja
                </p>
                <h3 class="text-2xl font-bold mb-2">{{ number_format($member->points ?? 0, 0, ',', '.') }}</h3>
                <p class="text-white/70 text-xs">≈ Rp {{ number_format($member->points_value ?? 0, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <!-- Credit Stats (Koperasi Mart) -->
    <div class="mb-8">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
            <span class="text-xl">🛒</span> Limit Kredit Belanja (Mart)
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Limit -->
            <div class="glass-card-solid p-5 relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <svg class="w-16 h-16 text-gray-900 dark:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Limit Kredit</p>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Rp {{ number_format($stats['credit_limit'], 0, ',', '.') }}</h3>
                <p class="text-xs text-gray-400">Plafon maksimal</p>
            </div>

            <!-- Terpakai -->
            <div class="glass-card-solid p-5 relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <svg class="w-16 h-16 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <p class="text-sm font-medium text-orange-600 dark:text-orange-400 mb-1">Terpakai (Belum Lunas)</p>
                <h3 class="text-2xl font-bold text-orange-600 dark:text-orange-400 mb-2">Rp {{ number_format($stats['credit_used'], 0, ',', '.') }}</h3>
                <a href="{{ route('members.credits') }}" class="text-xs text-orange-600 hover:text-orange-700 underline">Lihat Rincian →</a>
            </div>

            <!-- Tersedia -->
            <div class="glass-card-solid p-5 relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <svg class="w-16 h-16 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <p class="text-sm font-medium text-green-600 dark:text-green-400 mb-1">Sisa Limit</p>
                <h3 class="text-2xl font-bold text-green-600 dark:text-green-400 mb-2">Rp {{ number_format($stats['credit_available'], 0, ',', '.') }}</h3>
                <p class="text-xs text-gray-400">Siap digunakan</p>
            </div>
        </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Recent Savings Transactions & Chart -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Chart -->
            <div class="glass-card-solid p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Grafik Simpanan Saya</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Tahun {{ date('Y') }}</p>
                    </div>
                </div>
                <div id="savingsChart" class="w-full"></div>
            </div>

            <!-- Table -->
            <div class="glass-card-solid p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Transaksi Simpanan Terbaru</h2>
                    <a href="{{ route('savings.index') }}" class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400 font-medium">
                        Lihat Semua →
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Jenis</th>
                                <th>Transaksi</th>
                                <th class="text-right">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentSavings as $saving)
                            <tr>
                                <td>{{ $saving->transaction_date->format('d/m/Y') }}</td>
                                <td>
                                    <span class="badge badge-info">{{ $saving->type_label }}</span>
                                </td>
                                <td>
                                    <span class="badge {{ $saving->transaction_type === 'deposit' ? 'badge-success' : 'badge-warning' }}">
                                        {{ $saving->transaction_type_label }}
                                    </span>
                                </td>
                                <td class="text-right font-semibold {{ $saving->transaction_type === 'deposit' ? 'text-emerald-600' : 'text-red-600' }}">
                                    {{ $saving->transaction_type === 'deposit' ? '+' : '-' }} Rp {{ number_format($saving->amount, 0, ',', '.') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-8 text-gray-500 dark:text-gray-400">
                                    Belum ada transaksi simpanan
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Upcoming Payments -->
        <div class="glass-card-solid p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Angsuran Mendatang</h2>
            <div class="space-y-3">
                @forelse($upcomingPayments as $payment)
                <div class="p-4 rounded-xl border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-900 dark:text-white">Angsuran #{{ $payment->installment_number }}</span>
                        <span class="badge {{ $payment->due_date->isPast() ? 'badge-danger' : 'badge-warning' }}">
                            {{ $payment->status_label }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                        Jatuh Tempo: {{ $payment->due_date->format('d M Y') }}
                    </p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">
                        Rp {{ number_format($payment->amount, 0, ',', '.') }}
                    </p>
                </div>
                @empty
                <div class="text-center py-8 text-gray-500 dark:text-gray-400 text-sm">
                    Tidak ada angsuran
                </div>
                @endforelse
            </div>
            
            @if($upcomingPayments->count() > 0)
            <a href="{{ route('loan-payments.index') }}" class="btn-primary w-full mt-4">
                Bayar Angsuran
            </a>
            @endif
        </div>
    </div>

    <!-- Active Loans -->
    @if($activeLoans->count() > 0)
    <div class="glass-card-solid p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Pinjaman Saya</h2>
            <a href="{{ route('loans.create') }}" class="btn-primary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Ajukan Pinjaman Baru
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($activeLoans as $loan)
            <div class="p-6 rounded-xl border-2 border-gray-200 dark:border-gray-700 hover:border-primary-500 dark:hover:border-primary-500 transition-colors">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-900 dark:text-white">{{ $loan->loan_number }}</h3>
                    <span class="badge badge-{{ $loan->status_color }}">{{ $loan->status_label }}</span>
                </div>
                <div class="space-y-2 mb-4">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Jumlah Pinjaman</span>
                        <span class="font-semibold text-gray-900 dark:text-white">Rp {{ number_format($loan->amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Sisa Pinjaman</span>
                        <span class="font-semibold text-red-600">Rp {{ number_format($loan->remaining_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Tenor</span>
                        <span class="font-semibold text-gray-900 dark:text-white">{{ $loan->duration_months }} Bulan</span>
                    </div>
                </div>
                <!-- Progress Bar -->
                <div class="mb-2">
                    <div class="flex justify-between text-xs mb-1">
                        <span class="text-gray-500 dark:text-gray-400">Terbayar</span>
                        <span class="font-semibold text-gray-900 dark:text-white">{{ $loan->payment_progress }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div class="bg-gradient-to-r from-primary-500 to-primary-600 h-2 rounded-full transition-all duration-300" 
                             style="width: {{ $loan->payment_progress }}%"></div>
                    </div>
                </div>
                <a href="{{ route('loans.show', $loan) }}" class="btn-outline w-full mt-4">
                    Detail Pinjaman
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @else
    <div class="glass-card-solid p-12 text-center mb-8">
        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
        </svg>
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Belum Ada Pinjaman</h3>
        <p class="text-gray-500 dark:text-gray-400 mb-6">Anda belum memiliki pinjaman aktif.</p>
        <a href="{{ route('loans.create') }}" class="btn-primary">
            Ajukan Pinjaman
        </a>
    </div>
    @endif

    <!-- Announcements -->
    @if($announcements->count() > 0)
    <div class="glass-card-solid p-6">
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


    @push('scripts')
    <script>
        const savingsOptions = {
            chart: {
                type: 'area',
                height: 300,
                fontFamily: 'Inter, sans-serif',
                toolbar: { show: false },
                animations: { enabled: true }
            },
            series: [{
                name: 'Total Simpanan',
                data: @json($savingsChart)
            }],
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
                labels: { style: { colors: '#9ca3af' } }
            },
            yaxis: {
                labels: {
                    style: { colors: '#9ca3af' },
                    formatter: (value) => { return 'Rp ' + (value / 1000).toFixed(0) + 'k'; }
                }
            },
            tooltip: {
                theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light',
                y: {
                    formatter: function (val) {
                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
                    }
                }
            },
            grid: {
                borderColor: '#e5e7eb',
                strokeDashArray: 4
            }
        };
        
        const savingsChart = new ApexCharts(document.querySelector("#savingsChart"), savingsOptions);
        savingsChart.render();
    </script>
    @endpush
@endsection
