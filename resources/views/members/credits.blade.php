@extends('layouts.app')

@section('title', 'Riwayat Kredit - Koperasi Mart')

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Riwayat Kredit Belanja</h1>
            <p class="page-subtitle">Daftar transaksi kredit Anda di Koperasi Mart</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('members.transactions.print', ['member' => $member->id]) }}" target="_blank" class="btn-primary flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Print History
            </a>
            <a href="{{ route('dashboard') }}" class="btn-secondary">
                Kembali ke Dashboard
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Limit -->
        <div class="glass-card-solid p-5">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Limit Kredit</p>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Rp {{ number_format($member->credit_limit ?? 500000, 0, ',', '.') }}</h3>
        </div>

        <!-- Terpakai -->
        <div class="glass-card-solid p-5">
            <p class="text-sm font-medium text-orange-600 dark:text-orange-400 mb-1">Terpakai (Belum Lunas)</p>
            <h3 class="text-2xl font-bold text-orange-600 dark:text-orange-400">Rp {{ number_format($creditUsed, 0, ',', '.') }}</h3>
        </div>

        <!-- Tersedia -->
        <div class="glass-card-solid p-5">
            <p class="text-sm font-medium text-green-600 dark:text-green-400 mb-1">Sisa Limit</p>
            <h3 class="text-2xl font-bold text-green-600 dark:text-green-400">Rp {{ number_format($creditAvailable, 0, ',', '.') }}</h3>
        </div>
    </div>

    <!-- Transactions List -->
    <div class="glass-card-solid overflow-hidden">
        <div class="p-6 border-b border-gray-100 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Riwayat Transaksi Kredit</h2>
        </div>
        <!-- Mobile View (Cards) -->
        <div class="md:hidden space-y-4 p-4">
            @forelse($transactions as $trx)
            <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4 border border-gray-100 dark:border-gray-700">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <div class="text-xs text-gray-400">{{ $trx->created_at->format('d M Y, H:i') }}</div>
                        <div class="font-mono text-sm font-bold text-primary-600 mt-1">{{ $trx->invoice_number }}</div>
                    </div>
                    @if($trx->status === 'credit')
                        <span class="px-2 py-1 rounded-full text-xs font-bold bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300">
                            Belum Lunas
                        </span>
                    @else
                        <span class="px-2 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300">
                            Lunas
                        </span>
                    @endif
                </div>
                
                <div class="space-y-2 mb-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Total Items</span>
                        <span class="text-gray-900 dark:text-white font-medium">{{ $trx->items->count() }} item</span>
                    </div>
                    <div class="text-xs text-gray-500 bg-white dark:bg-gray-900 p-2 rounded border border-gray-200 dark:border-gray-700">
                         {{ $trx->items->take(3)->pluck('product.name')->join(', ') }}{{ $trx->items->count() > 3 ? '...' : '' }}
                    </div>
                </div>

                <div class="flex justify-between items-center pt-3 border-t border-gray-200 dark:border-gray-700">
                    <span class="text-sm font-medium text-gray-500">Total Tagihan</span>
                    <span class="text-lg font-bold text-gray-900 dark:text-white">Rp {{ number_format($trx->total_amount, 0, ',', '.') }}</span>
                </div>
                
                @if($trx->notes)
                <div class="mt-2 text-xs text-gray-400 italic">
                    Note: {{ $trx->notes }}
                </div>
                @endif
            </div>
            @empty
            <div class="text-center py-8 text-gray-500">
                <span class="text-4xl block mb-2">📝</span>
                <p>Belum ada riwayat transaksi</p>
            </div>
            @endforelse
        </div>

        <!-- Desktop View (Table) -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Invoice</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Items</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($transactions as $trx)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $trx->created_at->format('d/m/Y') }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $trx->created_at->format('H:i') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-mono text-sm text-primary-600">{{ $trx->invoice_number }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-600 dark:text-gray-300">
                                {{ $trx->items->count() }} item
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 truncate max-w-[200px]" title="{{ $trx->items->pluck('product.name')->join(', ') }}">
                                {{ $trx->items->take(2)->pluck('product.name')->join(', ') }}{{ $trx->items->count() > 2 ? '...' : '' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <span class="text-sm font-bold text-gray-900 dark:text-white">Rp {{ number_format($trx->total_amount, 0, ',', '.') }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($trx->status === 'credit')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300">
                                    Belum Lunas
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                    Lunas
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $trx->notes ?? '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                            <span class="text-4xl block mb-3">📝</span>
                            <p>Belum ada riwayat transaksi kredit</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($transactions->hasPages())
        <div class="p-4 border-t border-gray-100 dark:border-gray-700">
            {{ $transactions->links() }}
        </div>
        @endif
    </div>
@endsection
