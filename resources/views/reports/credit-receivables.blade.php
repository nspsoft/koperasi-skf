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

    <div class="glass-card-solid p-6" x-data="{ showPayModal: false, selectedInstallment: null, memberName: '', amount: 0 }">
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
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($details as $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                                <div class="font-medium @if($item->due_date->isPast()) text-red-600 dark:text-red-400 @endif">
                                    {{ $item->due_date->format('d/m/Y') }}
                                </div>
                                @if($item->due_date->isPast())
                                    <span class="text-[10px] font-bold text-red-500 uppercase">Terlambat</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-mono text-xs text-gray-600 dark:text-gray-400">{{ $item->transaction->invoice_number ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                                <div class="font-medium">{{ $item->transaction->user->name ?? '-' }}</div>
                                <div class="text-[10px] text-gray-400 font-mono">{{ $item->transaction->user->member->member_id ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3 text-right font-semibold text-gray-900 dark:text-white">Rp {{ number_format($item->amount, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400 uppercase tracking-wider">
                                    BELUM LUNAS
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <button @click="selectedInstallment = {{ $item->id }}; memberName = '{{ $item->transaction->user->name }}'; amount = {{ $item->amount }}; showPayModal = true" 
                                        class="btn-primary !py-1 !px-3 !text-[10px] uppercase font-black tracking-widest">
                                    Input Bayar
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">Tidak ada data piutang</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Payment Modal -->
        <template x-if="showPayModal">
            <div class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm">
                <div class="bg-white dark:bg-gray-800 rounded-3xl w-full max-w-md shadow-2xl overflow-hidden border border-gray-100 dark:border-gray-700 animate-in fade-in zoom-in duration-200">
                    <form :action="'{{ url('installments') }}/' + selectedInstallment + '/pay'" method="POST" class="p-6">
                        @csrf
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tighter">Input Pelunasan</h3>
                            <button type="button" @click="showPayModal = false" class="text-gray-400 hover:text-gray-600">&times;</button>
                        </div>

                        <div class="space-y-4">
                            <div class="bg-gray-50 dark:bg-gray-900/50 p-4 rounded-2xl border border-gray-100 dark:border-gray-800">
                                <label class="text-[10px] font-black text-gray-400 uppercase block mb-1">Anggota</label>
                                <div class="text-base font-bold text-gray-900 dark:text-white" x-text="memberName"></div>
                                <div class="mt-2 pt-2 border-t border-gray-200 dark:border-gray-800 flex justify-between items-center">
                                    <span class="text-[10px] font-black text-gray-400 uppercase">Jumlah Bayar</span>
                                    <span class="text-lg font-black text-primary-600" x-text="'Rp ' + new Number(amount).toLocaleString('id-ID')"></span>
                                </div>
                            </div>

                            <div>
                                <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest block mb-1">Metode Pembayaran</label>
                                <select name="payment_method" class="form-input w-full font-bold">
                                    <option value="cash">TUNAI (CASH)</option>
                                    <option value="bank">TRANSFER BANK</option>
                                    <option value="saldo_sukarela">POTONG SALDO SUKARELA</option>
                                </select>
                            </div>

                            <div>
                                <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest block mb-1">Tanggal Bayar</label>
                                <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" class="form-input w-full font-bold">
                            </div>

                            <div>
                                <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest block mb-1">Catatan</label>
                                <textarea name="notes" class="form-input w-full" rows="2" placeholder="Contoh: Bayar via Kasir..."></textarea>
                            </div>
                        </div>

                        <div class="mt-8 flex gap-3">
                            <button type="button" @click="showPayModal = false" class="btn-secondary flex-1 uppercase font-black text-[10px] tracking-widest py-3">Batal</button>
                            <button type="submit" class="btn-primary flex-1 uppercase font-black text-[10px] tracking-widest py-3">Simpan Pelunasan</button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    </div>
@endsection

