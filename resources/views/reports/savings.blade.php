@extends('layouts.app')

@section('title', 'Laporan Simpanan')

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
                <h1 class="page-title">Laporan Simpanan</h1>
                <p class="page-subtitle">Periode: {{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}</p>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="glass-card-solid p-6 mb-6">
        <form method="GET" action="{{ route('reports.savings') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
             <div>
                <label class="form-label">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ request('start_date', $startDate->format('Y-m-d')) }}" class="form-input">
            </div>
            <div>
                <label class="form-label">Tanggal Selesai</label>
                <input type="date" name="end_date" value="{{ request('end_date', $endDate->format('Y-m-d')) }}" class="form-input">
            </div>
            <div>
                 <label class="form-label">Jenis Simpanan</label>
                <select name="type" class="form-input">
                    <option value="">Semua Jenis</option>
                    <option value="pokok" {{ request('type') == 'pokok' ? 'selected' : '' }}>Pokok</option>
                    <option value="wajib" {{ request('type') == 'wajib' ? 'selected' : '' }}>Wajib</option>
                    <option value="sukarela" {{ request('type') == 'sukarela' ? 'selected' : '' }}>Sukarela</option>
                </select>
            </div>
            <button type="submit" class="btn-primary">
                Terapkan Filter
            </button>
            <a href="{{ route('reports.savings.pdf', request()->query()) }}" class="btn-secondary flex items-center justify-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                Export PDF
            </a>
            <a href="{{ route('reports.savings.excel', request()->query()) }}" class="btn-secondary flex items-center justify-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Export Excel
            </a>
        </form>
    </div>

    <!-- Financial Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="glass-card p-6 bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/10 dark:to-emerald-900/10 border border-green-100 dark:border-green-800">
            <p class="text-sm text-green-700 dark:text-green-300 font-medium mb-1">Total Pemasukan (Setoran)</p>
            <p class="text-2xl font-bold text-green-800 dark:text-green-400">+ Rp {{ number_format($totalDeposits, 0, ',', '.') }}</p>
        </div>
        <div class="glass-card p-6 bg-gradient-to-br from-red-50 to-rose-50 dark:from-red-900/10 dark:to-rose-900/10 border border-red-100 dark:border-red-800">
             <p class="text-sm text-red-700 dark:text-red-300 font-medium mb-1">Total Pengeluaran (Penarikan)</p>
            <p class="text-2xl font-bold text-red-800 dark:text-red-400">- Rp {{ number_format($totalWithdrawals, 0, ',', '.') }}</p>
        </div>
        <div class="glass-card p-6 bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/10 dark:to-indigo-900/10 border border-blue-100 dark:border-blue-800">
             <p class="text-sm text-blue-700 dark:text-blue-300 font-medium mb-1">Arus Kas Bersih (Net Flow)</p>
            <p class="text-2xl font-bold {{ $netFlow >= 0 ? 'text-blue-800 dark:text-blue-400' : 'text-red-600' }}">
                Rp {{ number_format($netFlow, 0, ',', '.') }}
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Balance Breakdown (All Time) -->
        <div class="lg:col-span-1 glass-card-solid p-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Total Saldo Tersimpan</h3>
            <div class="space-y-4">
                @foreach($balanceByType as $balance)
                <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700">
                    <p class="text-xs text-gray-500 uppercase font-medium mb-1">Simpanan {{ ucfirst($balance->type) }}</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">Rp {{ number_format($balance->balance, 0, ',', '.') }}</p>
                </div>
                @endforeach
                 <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <p class="text-sm text-center text-gray-500 italic">*Saldo akumulatif sepanjang waktu</p>
                </div>
            </div>
        </div>

        <!-- Transaction Table -->
         <div class="lg:col-span-2 glass-card-solid overflow-hidden">
            <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                 <h3 class="text-lg font-bold text-gray-900 dark:text-white">Riwayat Transaksi (Periode Ini)</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="table-modern">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Anggota</th>
                            <th>Jenis</th>
                            <th>Tipe</th>
                            <th class="text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $trx)
                        <tr>
                            <td>{{ $trx->transaction_date->format('d/m/Y') }}</td>
                            <td>
                                <div class="flex flex-col">
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $trx->member->user->name }}</span>
                                    <span class="text-xs text-gray-500">{{ $trx->member->member_id }}</span>
                                </div>
                            </td>
                            <td class="capitalize">{{ $trx->type }}</td>
                            <td>
                                 @if($trx->transaction_type === 'deposit')
                                <span class="badge badge-success">Setoran</span>
                                @else
                                <span class="badge badge-danger">Penarikan</span>
                                @endif
                            </td>
                            <td class="text-right font-medium {{ $trx->transaction_type === 'deposit' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $trx->transaction_type === 'deposit' ? '+' : '-' }} Rp {{ number_format($trx->amount, 0, ',', '.') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-gray-500">Tidak ada transaksi pada periode ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
