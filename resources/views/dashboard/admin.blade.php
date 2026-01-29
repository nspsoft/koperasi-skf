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
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Savings Chart -->
        <div class="lg:col-span-2 glass-card-solid p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Grafik Simpanan</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Tahun {{ date('Y') }}</p>
                </div>
            </div>
            <div id="savingsChart" class="w-full"></div>
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
        <!-- Recent Members -->
        <div class="glass-card-solid p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Anggota Terbaru</h2>
                <a href="{{ route('members.index') }}" class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400 font-medium">
                    Lihat Semua →
                </a>
            </div>
            <div class="space-y-4">
                @forelse($recentMembers as $member)
                <div class="flex items-center gap-4 p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white font-semibold flex-shrink-0">
                        {{ strtoupper(substr($member->user->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-gray-900 dark:text-white truncate">{{ $member->user->name }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $member->member_id }}</p>
                    </div>
                    <span class="badge badge-success">{{ ucfirst($member->status) }}</span>
                </div>
                @empty
                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                    Belum ada anggota
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
    </script>
    @endpush
@endsection
