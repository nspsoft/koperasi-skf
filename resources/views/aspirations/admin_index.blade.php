@extends('layouts.app')

@section('title', 'Hasil Aspirasi Anggota')

@section('content')
<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Hasil Aspirasi Anggota</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Analisis masukan dan permintaan barang dari seluruh anggota.</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('aspirations.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest shadow-sm hover:bg-emerald-700 transition duration-150">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Isi Aspirasi
        </a>
        <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-xl font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Cetak Laporan
        </button>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
    {{-- Top Requested Items --}}
    <div class="lg:col-span-1 bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
            Top 10 Request Barang
        </h3>
        <div class="space-y-3">
            @forelse($stats['top_items'] as $item => $data)
            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-900/50 rounded-2xl">
                <div>
                    <span class="block text-sm font-semibold text-gray-700 dark:text-gray-300 capitalize">{{ $item }}</span>
                    <span class="text-[10px] text-gray-500">{{ $data['count'] }} Request</span>
                </div>
                <div class="text-right">
                    <span class="block px-3 py-1 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 text-xs font-bold rounded-full">
                        Rp {{ number_format($data['total_potential'], 0, ',', '.') }}
                    </span>
                    <span class="text-[10px] text-gray-400">Potensi /bulan</span>
                </div>
            </div>
            @empty
            <p class="text-sm text-gray-500 italic text-center py-4">Belum ada data</p>
            @endforelse
        </div>
    </div>

    {{-- System Preference Charts --}}
    <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-8">
        {{-- Manual vs Digital --}}
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex flex-col items-center justify-center">
            <h3 class="text-sm font-bold text-gray-500 dark:text-gray-400 mb-4 uppercase tracking-wider text-center">Pilihan Sistem</h3>
            <div id="systemChart" class="w-full"></div>
            <div class="mt-4 grid grid-cols-2 gap-4 w-full">
                <div class="text-center">
                    <p class="text-[10px] text-gray-400 uppercase">Digital</p>
                    <p class="text-xl font-bold text-emerald-600">{{ $stats['system_pref']['digital'] }}</p>
                </div>
                <div class="text-center">
                    <p class="text-[10px] text-gray-400 uppercase">Manual</p>
                    <p class="text-xl font-bold text-amber-500">{{ $stats['system_pref']['manual'] }}</p>
                </div>
            </div>
        </div>

        {{-- Cash vs Digital --}}
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex flex-col items-center justify-center">
            <h3 class="text-sm font-bold text-gray-500 dark:text-gray-400 mb-4 uppercase tracking-wider text-center">Sistem Pembayaran</h3>
            <div id="paymentChart" class="w-full"></div>
            <div class="mt-4 grid grid-cols-2 gap-4 w-full">
                <div class="text-center">
                    <p class="text-[10px] text-gray-400 uppercase">Digital</p>
                    <p class="text-xl font-bold text-blue-600">{{ $stats['payment_pref']['digital'] }}</p>
                </div>
                <div class="text-center">
                    <p class="text-[10px] text-gray-400 uppercase">Tunai</p>
                    <p class="text-xl font-bold text-gray-500">{{ $stats['payment_pref']['cash'] }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Detail List --}}
<div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
    <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between bg-gray-50/50 dark:bg-gray-900/50">
        <h3 class="font-bold text-gray-900 dark:text-white">Daftar Feedback Terbaru</h3>
        <span class="px-4 py-1.5 bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs font-bold rounded-full">
            Total: {{ $aspirations->total() }} Data
        </span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-xs font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700">
                    <th class="px-6 py-4">Anggota</th>
                    <th class="px-6 py-4">Tipe</th>
                    <th class="px-6 py-4">Isi Data</th>
                    <th class="px-6 py-4">Waktu</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700 italic">
                @foreach($aspirations as $aspiration)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/50 transition duration-150">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-gray-200 dark:bg-gray-700 flex items-center justify-center font-bold text-gray-500">
                                {{ substr($aspiration->member->user->name, 0, 1) }}
                            </div>
                            <div class="text-sm">
                                <div class="font-bold text-gray-900 dark:text-white">{{ $aspiration->member->user->name }}</div>
                                <div class="text-[10px] text-gray-500">{{ $aspiration->member->nik }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span @class([
                            'px-3 py-1 rounded-lg text-[10px] font-bold uppercase',
                            'bg-emerald-100 text-emerald-600' => $aspiration->type === 'item_request',
                            'bg-amber-100 text-amber-600' => $aspiration->type === 'system_eval',
                        ])>
                            {{ $aspiration->type === 'item_request' ? 'Request' : 'Evaluasi' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 min-w-[300px]">
                        @if($aspiration->type === 'item_request')
                            <div class="text-sm">
                                <span class="font-bold text-emerald-600 uppercase text-[10px]">{{ $aspiration->data['category'] }}</span>
                                <div class="text-gray-900 dark:text-white font-medium text-base">{{ $aspiration->data['item_name'] }}</div>
                                <div class="flex gap-3 mt-1 text-xs text-gray-500">
                                    <span class="bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded">Freq: {{ $aspiration->data['frequency'] }}</span>
                                    <span class="bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded">Qty: {{ $aspiration->data['qty'] ?? 1 }}</span>
                                    @if(isset($aspiration->data['estimated_price']))
                                    <span class="bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 px-2 py-0.5 rounded">Est: Rp {{ number_format($aspiration->data['estimated_price']) }}</span>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="text-sm">
                                <div class="flex gap-2 mb-1">
                                    <span class="text-[10px] font-bold text-blue-500 uppercase">Sistem: {{ $aspiration->data['system_choice'] }}</span>
                                    <span class="text-[10px] font-bold text-amber-500 uppercase">Bayar: {{ $aspiration->data['payment_choice'] }}</span>
                                </div>
                                <div class="text-gray-600 dark:text-gray-400 italic">"{{ $aspiration->data['reason'] ?? '-' }}"</div>
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-xs text-gray-400 font-medium">
                        {{ $aspiration->created_at->format('d/m/Y H:i') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-6 border-t border-gray-100 dark:border-gray-700">
        {{ $aspirations->links() }}
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // System Preference Chart
        const systemOptions = {
            series: [{{ $stats['system_pref']['digital'] }}, {{ $stats['system_pref']['manual'] }}],
            chart: { type: 'donut', height: 180 },
            colors: ['#059669', '#f59e0b'],
            labels: ['Digital', 'Manual'],
            legend: { show: false },
            dataLabels: { enabled: false },
            plotOptions: {
                pie: {
                    donut: { size: '75%' }
                }
            }
        };
        new ApexCharts(document.querySelector("#systemChart"), systemOptions).render();

        // Payment Preference Chart
        const paymentOptions = {
            series: [{{ $stats['payment_pref']['digital'] }}, {{ $stats['payment_pref']['cash'] }}],
            chart: { type: 'donut', height: 180 },
            colors: ['#2563eb', '#9ca3af'],
            labels: ['Digital', 'Cash'],
            legend: { show: false },
            dataLabels: { enabled: false },
            plotOptions: {
                pie: {
                    donut: { size: '75%' }
                }
            }
        };
        new ApexCharts(document.querySelector("#paymentChart"), paymentOptions).render();
    });
</script>
@endpush
@endsection
