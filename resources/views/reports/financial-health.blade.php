@extends('layouts.app')

@section('title', 'Kesehatan Keuangan')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">📊 Dashboard Kesehatan Keuangan</h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Monitoring indikator keuangan koperasi secara real-time</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-xs text-gray-400">Terakhir diperbarui: {{ now()->translatedFormat('d M Y, H:i') }}</span>
            <button onclick="location.reload()" class="btn-secondary text-sm">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Refresh
            </button>
        </div>
    </div>

    {{-- Alerts --}}
    @foreach($alerts as $alert)
    <div class="p-4 rounded-xl border {{ $alert['type'] === 'danger' ? 'bg-red-50 border-red-200 dark:bg-red-900/20 dark:border-red-800' : ($alert['type'] === 'warning' ? 'bg-amber-50 border-amber-200 dark:bg-amber-900/20 dark:border-amber-800' : 'bg-emerald-50 border-emerald-200 dark:bg-emerald-900/20 dark:border-emerald-800') }}">
        <div class="flex items-start gap-3">
            <span class="text-2xl">{{ $alert['icon'] }}</span>
            <div>
                <h3 class="font-bold {{ $alert['type'] === 'danger' ? 'text-red-700 dark:text-red-400' : ($alert['type'] === 'warning' ? 'text-amber-700 dark:text-amber-400' : 'text-emerald-700 dark:text-emerald-400') }}">{{ $alert['title'] }}</h3>
                <p class="text-sm {{ $alert['type'] === 'danger' ? 'text-red-600 dark:text-red-300' : ($alert['type'] === 'warning' ? 'text-amber-600 dark:text-amber-300' : 'text-emerald-600 dark:text-emerald-300') }}">{{ $alert['message'] }}</p>
            </div>
        </div>
    </div>
    @endforeach

    {{-- Gauge Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        {{-- NPL Ratio --}}
        <div class="glass-card p-5 text-center">
            <div class="relative w-24 h-24 mx-auto mb-3">
                <svg class="w-24 h-24 transform -rotate-90" viewBox="0 0 36 36">
                    <path class="text-gray-200 dark:text-gray-700" stroke="currentColor" stroke-width="3" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                    <path class="{{ $metrics['status_npl'] === 'green' ? 'text-emerald-500' : ($metrics['status_npl'] === 'yellow' ? 'text-amber-500' : 'text-red-500') }}" stroke="currentColor" stroke-width="3" stroke-linecap="round" fill="none" stroke-dasharray="{{ min($metrics['rasio_npl'], 100) }}, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-xl font-bold {{ $metrics['status_npl'] === 'green' ? 'text-emerald-600' : ($metrics['status_npl'] === 'yellow' ? 'text-amber-600' : 'text-red-600') }}">{{ $metrics['rasio_npl'] }}%</span>
                </div>
            </div>
            <h3 class="font-semibold text-gray-700 dark:text-gray-300 text-sm">Rasio NPL</h3>
            <p class="text-xs text-gray-400 mt-1">Target: < 5%</p>
        </div>

        {{-- Liquidity Ratio --}}
        <div class="glass-card p-5 text-center">
            <div class="relative w-24 h-24 mx-auto mb-3">
                <svg class="w-24 h-24 transform -rotate-90" viewBox="0 0 36 36">
                    <path class="text-gray-200 dark:text-gray-700" stroke="currentColor" stroke-width="3" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                    <path class="{{ $metrics['status_likuiditas'] === 'green' ? 'text-emerald-500' : ($metrics['status_likuiditas'] === 'yellow' ? 'text-amber-500' : 'text-red-500') }}" stroke="currentColor" stroke-width="3" stroke-linecap="round" fill="none" stroke-dasharray="{{ min($metrics['rasio_likuiditas'] * 20, 100) }}, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-xl font-bold {{ $metrics['status_likuiditas'] === 'green' ? 'text-emerald-600' : ($metrics['status_likuiditas'] === 'yellow' ? 'text-amber-600' : 'text-red-600') }}">{{ $metrics['rasio_likuiditas'] }}x</span>
                </div>
            </div>
            <h3 class="font-semibold text-gray-700 dark:text-gray-300 text-sm">Rasio Likuiditas</h3>
            <p class="text-xs text-gray-400 mt-1">Target: > 1.5x</p>
        </div>

        {{-- Collection Rate --}}
        <div class="glass-card p-5 text-center">
            <div class="relative w-24 h-24 mx-auto mb-3">
                <svg class="w-24 h-24 transform -rotate-90" viewBox="0 0 36 36">
                    <path class="text-gray-200 dark:text-gray-700" stroke="currentColor" stroke-width="3" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                    <path class="{{ $metrics['status_collection'] === 'green' ? 'text-emerald-500' : ($metrics['status_collection'] === 'yellow' ? 'text-amber-500' : 'text-red-500') }}" stroke="currentColor" stroke-width="3" stroke-linecap="round" fill="none" stroke-dasharray="{{ $metrics['collection_rate'] }}, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-xl font-bold {{ $metrics['status_collection'] === 'green' ? 'text-emerald-600' : ($metrics['status_collection'] === 'yellow' ? 'text-amber-600' : 'text-red-600') }}">{{ $metrics['collection_rate'] }}%</span>
                </div>
            </div>
            <h3 class="font-semibold text-gray-700 dark:text-gray-300 text-sm">Collection Rate</h3>
            <p class="text-xs text-gray-400 mt-1">Target: > 90%</p>
        </div>

        {{-- Kredit Macet --}}
        <div class="glass-card p-5 text-center">
            <div class="relative w-24 h-24 mx-auto mb-3">
                <svg class="w-24 h-24 transform -rotate-90" viewBox="0 0 36 36">
                    <path class="text-gray-200 dark:text-gray-700" stroke="currentColor" stroke-width="3" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                    <path class="{{ $metrics['status_kredit_macet'] === 'green' ? 'text-emerald-500' : ($metrics['status_kredit_macet'] === 'yellow' ? 'text-amber-500' : 'text-red-500') }}" stroke="currentColor" stroke-width="3" stroke-linecap="round" fill="none" stroke-dasharray="{{ min($metrics['rasio_kredit_macet'], 100) }}, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-xl font-bold {{ $metrics['status_kredit_macet'] === 'green' ? 'text-emerald-600' : ($metrics['status_kredit_macet'] === 'yellow' ? 'text-amber-600' : 'text-red-600') }}">{{ $metrics['rasio_kredit_macet'] }}%</span>
                </div>
            </div>
            <h3 class="font-semibold text-gray-700 dark:text-gray-300 text-sm">Kredit Macet Mart</h3>
            <p class="text-xs text-gray-400 mt-1">Target: < 3%</p>
        </div>

        {{-- Pertumbuhan Anggota --}}
        <div class="glass-card p-5 text-center">
            <div class="relative w-24 h-24 mx-auto mb-3 flex items-center justify-center">
                <div class="text-4xl {{ $metrics['status_pertumbuhan'] === 'green' ? 'text-emerald-500' : ($metrics['status_pertumbuhan'] === 'yellow' ? 'text-amber-500' : 'text-red-500') }}">
                    @if($metrics['pertumbuhan_anggota'] > 0)
                        📈
                    @elseif($metrics['pertumbuhan_anggota'] < 0)
                        📉
                    @else
                        ➡️
                    @endif
                </div>
            </div>
            <span class="text-xl font-bold {{ $metrics['status_pertumbuhan'] === 'green' ? 'text-emerald-600' : ($metrics['status_pertumbuhan'] === 'yellow' ? 'text-amber-600' : 'text-red-600') }}">{{ $metrics['pertumbuhan_anggota'] > 0 ? '+' : '' }}{{ $metrics['pertumbuhan_anggota'] }}%</span>
            <h3 class="font-semibold text-gray-700 dark:text-gray-300 text-sm mt-2">Pertumbuhan Anggota</h3>
            <p class="text-xs text-gray-400 mt-1">+{{ $metrics['anggota_bulan_ini'] }} bulan ini</p>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="glass-card p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Simpanan</p>
                    <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($metrics['total_simpanan'], 0, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center text-2xl">💰</div>
            </div>
        </div>
        
        <div class="glass-card p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Pinjaman Aktif</p>
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">Rp {{ number_format($metrics['total_pinjaman'], 0, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center text-2xl">🏦</div>
            </div>
        </div>
        
        <div class="glass-card p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Pinjaman Bermasalah</p>
                    <p class="text-2xl font-bold text-red-600 dark:text-red-400">Rp {{ number_format($metrics['pinjaman_bermasalah'], 0, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-xl flex items-center justify-center text-2xl">⚠️</div>
            </div>
        </div>
        
        <div class="glass-card p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Anggota Aktif</p>
                    <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ number_format($metrics['total_anggota']) }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center text-2xl">👥</div>
            </div>
        </div>
    </div>

    {{-- Trend Chart --}}
    <div class="glass-card p-6">
        <h3 class="font-bold text-gray-800 dark:text-white mb-4">📈 Trend 6 Bulan Terakhir</h3>
        <div id="trendChart" class="h-72"></div>
    </div>

    {{-- Legend --}}
    <div class="glass-card p-4">
        <h3 class="font-semibold text-gray-700 dark:text-gray-300 mb-3">📋 Panduan Indikator</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                <span class="text-gray-600 dark:text-gray-400">Hijau = Sehat / Target Tercapai</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                <span class="text-gray-600 dark:text-gray-400">Kuning = Perlu Perhatian</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-red-500"></span>
                <span class="text-gray-600 dark:text-gray-400">Merah = Kritis / Butuh Tindakan</span>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    var trendData = @json($trends);
    
    var options = {
        series: [{
            name: 'Simpanan',
            type: 'column',
            data: trendData.map(t => t.simpanan)
        }, {
            name: 'Pinjaman',
            type: 'column',
            data: trendData.map(t => t.pinjaman)
        }, {
            name: 'Anggota Baru',
            type: 'line',
            data: trendData.map(t => t.anggota_baru)
        }],
        chart: {
            height: 280,
            type: 'line',
            toolbar: { show: false },
            fontFamily: 'Inter, sans-serif',
        },
        stroke: {
            width: [0, 0, 3],
            curve: 'smooth'
        },
        colors: ['#10b981', '#3b82f6', '#8b5cf6'],
        plotOptions: {
            bar: {
                borderRadius: 6,
                columnWidth: '50%',
            }
        },
        xaxis: {
            categories: trendData.map(t => t.bulan),
            labels: { style: { colors: '#9ca3af' } }
        },
        yaxis: [{
            title: { text: 'Rupiah', style: { color: '#9ca3af' } },
            labels: { 
                formatter: val => 'Rp ' + (val / 1000000).toFixed(1) + ' Jt',
                style: { colors: '#9ca3af' }
            }
        }, {
            opposite: true,
            title: { text: 'Anggota', style: { color: '#9ca3af' } },
            labels: { style: { colors: '#9ca3af' } }
        }],
        legend: {
            position: 'top',
            labels: { colors: '#9ca3af' }
        },
        tooltip: {
            y: {
                formatter: function(val, { seriesIndex }) {
                    if (seriesIndex === 2) return val + ' orang';
                    return 'Rp ' + val.toLocaleString('id-ID');
                }
            }
        }
    };

    var chart = new ApexCharts(document.querySelector("#trendChart"), options);
    chart.render();
</script>
@endpush
@endsection
