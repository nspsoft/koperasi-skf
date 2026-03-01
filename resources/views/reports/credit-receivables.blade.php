@extends('layouts.app')

@section('title', 'Piutang Jatuh Tempo per Bulan')

@section('content')
    <div class="page-header">
        <div class="flex items-center gap-4">
            <a href="{{ route('reports.index') }}" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                <svg class="w-6 h-6 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h1 class="page-title">Piutang Jatuh Tempo per Bulan</h1>
                <p class="page-subtitle">Periode: {{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}</p>
            </div>
        </div>
    </div>

    <div class="glass-card-solid p-6 mb-6">
        <form method="GET" action="{{ route('reports.credit-receivables') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <div>
                <label class="form-label">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ request('start_date', $startDate->format('Y-m-d')) }}" class="form-input">
            </div>
            <div>
                <label class="form-label">Tanggal Selesai</label>
                <input type="date" name="end_date" value="{{ request('end_date', $endDate->format('Y-m-d')) }}" class="form-input">
            </div>
            <div>
                <button type="submit" class="btn-primary w-full">Tampilkan</button>
            </div>
        </form>
    </div>

    <div class="glass-card-solid p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Ringkasan per Bulan</h2>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-4 py-3 text-left">Bulan</th>
                        <th class="px-4 py-3 text-right">Total Piutang</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($summary as $row)
                        @php $month = \Carbon\Carbon::parse($row->month); @endphp
                        <tr>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $month->translatedFormat('F Y') }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-gray-900 dark:text-white">Rp {{ number_format($row->total, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="px-4 py-8 text-center text-gray-500">Tidak ada data piutang</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="glass-card-solid p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Detail Piutang</h2>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-4 py-3 text-left">Jatuh Tempo</th>
                        <th class="px-4 py-3 text-left">Invoice</th>
                        <th class="px-4 py-3 text-left">Anggota</th>
                        <th class="px-4 py-3 text-right">Nilai</th>
                        <th class="px-4 py-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($details as $item)
                        <tr>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $item->due_date->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 font-mono text-xs text-gray-600 dark:text-gray-400">{{ $item->transaction->invoice_number ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                                {{ $item->transaction->user->name ?? '-' }}
                                <div class="text-[10px] text-gray-400">{{ $item->transaction->user->member->member_id ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3 text-right font-semibold text-gray-900 dark:text-white">Rp {{ number_format($item->amount, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400">
                                    BELUM LUNAS
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">Tidak ada data piutang</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

