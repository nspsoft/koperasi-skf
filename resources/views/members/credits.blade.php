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
    <div class="glass-card-solid overflow-hidden" x-data="{ openTrx: null }">
        <div class="p-6 border-b border-gray-100 dark:border-gray-700 font-bold text-gray-900 dark:text-white flex justify-between items-center">
            <span>Riwayat Transaksi Kredit</span>
            <span class="text-xs text-gray-400 font-normal sm:hidden">Klik kartu untuk detail cicilan</span>
        </div>

        <!-- Mobile View (Cards) -->
        <div class="md:hidden space-y-4 p-4">
            @forelse($transactions as $trx)
            <div class="space-y-4">
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 border-2 transition-all" 
                     :class="openTrx === {{ $trx->id }} ? 'border-primary-500 shadow-lg' : 'border-gray-100 dark:border-gray-700'"
                     @click="openTrx === {{ $trx->id }} ? openTrx = null : openTrx = {{ $trx->id }}">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <div class="text-[10px] text-gray-400 uppercase tracking-widest font-bold">{{ $trx->created_at->format('d M Y, H:i') }}</div>
                            <div class="font-mono text-sm font-bold text-primary-600 mt-0.5">{{ $trx->invoice_number }}</div>
                        </div>
                        @if($trx->payment_method === 'kredit' && !in_array($trx->status, ['completed', 'cancelled']))
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300">
                                BELUM LUNAS
                            </span>
                        @else
                            <span class="px-2 py-1 rounded-full text-[10px] font-bold bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300">
                                LUNAS
                            </span>
                        @endif
                    </div>
                    
                    <div class="flex justify-between items-center pt-3 border-t border-gray-100 dark:border-gray-700">
                        <span class="text-xs font-bold text-gray-400 uppercase">Total Tagihan</span>
                        <span class="text-base font-black text-gray-900 dark:text-white">Rp {{ number_format($trx->total_amount, 0, ',', '.') }}</span>
                    </div>
                    
                    <div class="mt-3 flex items-center justify-center gap-1 text-[10px] font-bold text-gray-400 uppercase">
                        <svg class="w-3 h-3 transition-transform" :class="openTrx === {{ $trx->id }} ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"></path></svg>
                        <span x-text="openTrx === {{ $trx->id }} ? 'Tutup Detail' : 'Lihat Detail Cicilan'"></span>
                    </div>
                </div>

                <!-- Installment Schedule (Mobile) -->
                <div x-show="openTrx === {{ $trx->id }}" x-cloak x-transition class="space-y-3 pl-4 border-l-2 border-primary-200 ml-4 pb-4">
                    @php 
                        $sortedInstallments = $trx->creditInstallments->sortBy('installment_number');
                        $nextToPay = $sortedInstallments->where('status', 'pending')->first();
                    @endphp
                    @foreach($sortedInstallments as $inst)
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border {{ $inst->status === 'paid' ? 'border-green-100' : ($nextToPay?->id === $inst->id ? 'border-primary-200' : 'border-gray-100') }} shadow-sm">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-[10px] font-bold text-gray-400 uppercase">Bulan #{{ $inst->installment_number }}</span>
                            @if($inst->status === 'paid')
                                <span class="text-[10px] font-bold text-green-600">LUNAS</span>
                            @else
                                <span class="text-[10px] font-bold text-orange-500">PENDING</span>
                            @endif
                        </div>
                        <div class="text-sm font-bold text-gray-900 dark:text-white">Rp {{ number_format($inst->amount, 0, ',', '.') }}</div>
                        <div class="text-[10px] text-gray-500 mt-1">Jatuh Tempo: {{ $inst->due_date->format('d M Y') }}</div>
                        
                        @if($inst->status === 'pending' && $nextToPay?->id === $inst->id)
                            <form action="{{ route('installments.pay', $inst) }}" method="POST" class="mt-3">
                                @csrf
                                <button type="submit" class="w-full py-2 bg-primary-600 text-white text-[10px] font-bold rounded-lg uppercase tracking-wider">
                                    Bayar Sekarang
                                </button>
                            </form>
                        @endif
                    </div>
                    @endforeach
                </div>
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
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Tanggal</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Invoice</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Items</th>
                        <th class="px-6 py-4 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">Total</th>
                        <th class="px-6 py-4 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Status</th>
                        <th class="px-6 py-4 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($transactions as $trx)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors cursor-pointer" @click="openTrx === {{ $trx->id }} ? openTrx = null : openTrx = {{ $trx->id }}">
                        <td class="px-6 py-5">
                            <div class="font-bold text-gray-900 dark:text-white leading-none">{{ $trx->created_at->format('d/m/Y') }}</div>
                            <div class="text-[10px] text-gray-400 mt-1 uppercase">{{ $trx->created_at->format('H:i') }}</div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-2">
                                <span class="font-mono font-bold text-primary-600">{{ $trx->invoice_number }}</span>
                                <svg class="w-3 h-3 text-gray-300 transition-transform" :class="openTrx === {{ $trx->id }} ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="font-bold text-gray-700 dark:text-gray-300 truncate max-w-[150px]">
                                {{ $trx->items->count() }} Item
                            </div>
                            <div class="text-[10px] text-gray-400 mt-1 uppercase truncate max-w-[150px]">
                                {{ $trx->items->take(2)->pluck('product.name')->join(', ') }}
                            </div>
                        </td>
                        <td class="px-6 py-5 text-right">
                            <div class="text-base font-black text-gray-900 dark:text-white font-mono">Rp {{ number_format($trx->total_amount, 0, ',', '.') }}</div>
                        </td>
                        <td class="px-6 py-5 text-center">
                            @if($trx->payment_method === 'kredit' && !in_array($trx->status, ['completed', 'cancelled']))
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black tracking-widest bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400">
                                    BELUM LUNAS
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black tracking-widest bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                    LUNAS
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-5 text-right">
                            <span class="text-[10px] font-black text-primary-600 uppercase tracking-widest">Detail &rarr;</span>
                        </td>
                    </tr>
                    
                    <!-- Installment Schedule Expandable (Desktop) -->
                    <tr x-show="openTrx === {{ $trx->id }}" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="bg-gray-50/50 dark:bg-gray-900/20">
                        <td colspan="6" class="px-8 py-6">
                            <div class="p-6 bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-xl" @click.stop>
                                <div class="flex justify-between items-center mb-6">
                                    <h4 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-lg bg-primary-100 dark:bg-primary-900/30 text-primary-600 flex items-center justify-center">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                        Jadwal Angsuran Marketplace
                                    </h4>
                                    <div class="text-[10px] font-bold text-gray-400">TENOR {{ $trx->credit_tenor_months }} BULAN</div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                    @php 
                                        $sortedInstallments = $trx->creditInstallments->sortBy('installment_number');
                                        $nextToPay = $sortedInstallments->where('status', 'pending')->first();
                                    @endphp
                                    @foreach($sortedInstallments as $inst)
                                    <div class="relative group p-5 rounded-2xl border-2 transition-all duration-300 {{ $inst->status === 'paid' ? 'border-green-100 bg-green-50/20' : ($nextToPay?->id === $inst->id ? 'border-primary-200 bg-primary-50/30 shadow-md scale-[1.02]' : 'border-gray-50 opacity-60') }}">
                                        <div class="flex justify-between items-start mb-3">
                                            <div class="px-2 py-0.5 rounded-lg bg-gray-100 dark:bg-gray-700 text-[9px] font-black text-gray-500 uppercase tracking-widest">BULAN {{ $inst->installment_number }}</div>
                                            @if($inst->status === 'paid')
                                                <div class="w-5 h-5 rounded-full bg-green-500 text-white flex items-center justify-center">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="text-xl font-black text-gray-900 dark:text-white mb-1">Rp {{ number_format($inst->amount, 0, ',', '.') }}</div>
                                        <div class="text-[10px] font-bold text-gray-400 mb-5 uppercase tracking-wider">Jatuh Tempo: {{ $inst->due_date->format('d M Y') }}</div>

                                        @if($inst->status === 'pending' && $nextToPay?->id === $inst->id)
                                            <form action="{{ route('installments.pay', $inst) }}" method="POST" onsubmit="return confirm('Bayar angsuran ini menggunakan Saldo Sukarela?')">
                                                @csrf
                                                <button type="submit" class="w-full py-3 bg-gradient-to-r from-primary-600 to-indigo-600 hover:from-primary-700 hover:to-indigo-700 text-white text-[10px] font-black uppercase tracking-[0.1em] rounded-xl transition-all shadow-lg shadow-primary-200 dark:shadow-none active:scale-95">
                                                    Bayar Cicilan Sekarang
                                                </button>
                                            </form>
                                        @elseif($inst->status === 'pending')
                                            <div class="w-full py-3 bg-gray-50 dark:bg-gray-700/50 text-gray-400 text-[10px] font-black uppercase tracking-[0.1em] rounded-xl border border-gray-100 dark:border-gray-700 text-center">
                                                Menunggu Urutan
                                            </div>
                                        @else
                                            <div class="pt-3 border-t border-green-100 flex items-center gap-2">
                                                <span class="text-[9px] font-black text-green-600 uppercase tracking-widest">Lunas: {{ $inst->paid_at->format('d/m/Y') }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
        <div class="p-6 border-t border-gray-100 dark:border-gray-700 bg-gray-50/30">
            {{ $transactions->links() }}
        </div>
        @endif
    </div>
@endsection
