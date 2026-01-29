@extends('layouts.app')

@section('title', __('messages.titles.pos_history'))

@section('content')
<div x-data="salesHistory()">
    <div class="page-header flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="page-title">{{ __('messages.titles.pos_history') }}</h1>
            <p class="page-subtitle">{{ __('messages.sidebar.sales_history_subtitle') ?? 'Daftar semua transaksi penjualan' }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            @if(auth()->user()->hasAdminAccess())
            <a href="{{ route('pos.history.print', request()->query()) }}" target="_blank" class="btn-secondary flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                <span class="hidden sm:inline">{{ __('messages.titles.print_recap') }}</span>
            </a>
            <a href="{{ route('pos.history.export', request()->query()) }}" class="btn-secondary flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span class="hidden sm:inline">Excel</span>
            </a>
            @endif
            <a href="{{ route('pos.index') }}" class="btn-primary inline-flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                <span>Transaksi Baru</span>
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="glass-card-solid p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">{{ __('messages.titles.sales_today') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">Rp {{ number_format($todaySales, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        <div class="glass-card-solid p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">{{ __('messages.titles.trx_today') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $todayCount }} {{ __('messages.sidebar.transactions') ?? 'Transaksi' }}</p>
                </div>
            </div>
        </div>
        <div class="glass-card-solid p-6">
            <form action="" method="GET" class="flex gap-2">
                <input type="date" name="date" value="{{ request('date') }}" class="form-input flex-1">
                <select name="type" class="form-input">
                    <option value="">{{ __('messages.titles.all_types') }}</option>
                    <option value="offline" {{ request('type') == 'offline' ? 'selected' : '' }}>Offline (POS)</option>
                    <option value="online" {{ request('type') == 'online' ? 'selected' : '' }}>Online</option>
                </select>
                <div class="flex gap-1">
                    <button type="submit" class="btn-secondary">{{ __('messages.search') }}</button>

                </div>
            </form>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="glass-card-solid overflow-hidden">
        <div class="table-scroll-container">
            <table class="table-modern w-full">
                <thead class="sticky top-0 z-10">
                    <tr class="text-gray-700 dark:text-gray-300">
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider">{{ __('messages.titles.invoice') }}</th>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider">{{ __('messages.date') }}</th>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider">{{ __('messages.status') }}</th>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider">{{ __('messages.sidebar.pos') }}</th>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider">{{ __('messages.titles.items') }}</th>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider">{{ __('messages.titles.payment_method') }}</th>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider">{{ __('messages.total') }}</th>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider">{{ __('messages.status') }}</th>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $trx)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-6 py-4 font-medium">
                            <div class="flex flex-col">
                                <span class="text-gray-900 dark:text-white">{{ $trx->invoice_number }}</span>
                                <span class="text-[10px] text-gray-500 dark:text-gray-400 font-bold uppercase">{{ $trx->user->name ?? 'Tamu' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-700 dark:text-gray-300">{{ $trx->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4">
                            @if($trx->type == 'offline')
                                <span class="badge badge-info">POS</span>
                            @else
                                <span class="badge badge-purple">Online</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-700 dark:text-gray-300">{{ $trx->cashier->name ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <button @click='openModal(@json($trx))' class="text-primary-600 hover:text-primary-800 hover:underline font-medium decoration-dashed underline-offset-4">
                                {{ $trx->items->count() }} item
                            </button>
                        </td>
                        <td class="px-6 py-4 capitalize text-gray-700 dark:text-gray-300">{{ str_replace('_', ' ', $trx->payment_method) }}</td>
                        <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">Rp {{ number_format($trx->total_amount, 0, ',', '.') }}</td>
                        <td class="px-6 py-4">
                            @if($trx->status == 'completed' || $trx->status == 'paid')
                                <span class="badge badge-success">{{ __('messages.titles.paid_off') }}</span>
                            @elseif($trx->status == 'pending')
                                <span class="badge badge-warning">Pending</span>
                            @else
                                <span class="badge badge-danger">{{ $trx->status }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex items-center gap-1">
                                <a href="{{ route('pos.receipt', $trx) }}" target="_blank" class="p-1.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded hover:bg-gray-200" title="Cetak Struk">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                </a>
                                
                                @if($trx->type == 'online')
                                    @if($trx->status == 'pending')
                                        <form action="{{ route('pos.orders.process', $trx) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="paid">
                                            <button type="submit" class="p-1.5 bg-blue-100 text-blue-600 rounded hover:bg-blue-600 hover:text-white transition-colors" title="Konfirmasi Bayar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            </button>
                                        </form>
                                    @endif
                                    @if($trx->status == 'paid')
                                        <form action="{{ route('pos.orders.process', $trx) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="completed">
                                            <button type="submit" class="p-1.5 bg-green-100 text-green-600 rounded hover:bg-green-600 hover:text-white transition-colors" title="Selesaikan Pesanan">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-8 text-gray-500">Belum ada transaksi</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100 dark:border-gray-700">
            {{ $transactions->links() }}
        </div>
    </div>

    <!-- Detail Modal -->
    <div x-show="showModal" style="display: none;" 
         class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            
            <div x-show="showModal" @click="showModal = false" 
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showModal" 
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">
                
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                                {{ __('messages.titles.trx_detail') }}
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1" x-text="selectedTrx?.invoice_number"></p>
                        </div>
                        <button @click="showModal = false" class="text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <!-- Pipeline / Status Tracker -->
                    <div class="mb-8" x-show="selectedTrx">
                        <div class="relative">
                            <!-- Line -->
                            <div class="absolute top-1/2 left-0 w-full h-1 bg-gray-200 dark:bg-gray-700 -translate-y-1/2 rounded-full"></div>
                            <div class="absolute top-1/2 left-0 h-1 bg-primary-500 -translate-y-1/2 rounded-full transition-all duration-500"
                                 :style="getProgressWidth()"></div>

                            <!-- Steps (4 Steps) -->
                            <div class="relative flex justify-between">
                                <!-- Step 1: Pesanan Masuk -->
                                <div class="flex flex-col items-center gap-2 bg-white dark:bg-gray-800 px-1" :class="getStepClass('pending')">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center border-2 transition-colors duration-300 z-10"
                                         :class="getStepCircleClass('pending')">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"></path></svg>
                                    </div>
                                    <span class="text-[10px] sm:text-xs font-medium text-center" :class="getStepTextClass('pending')">{{ __('messages.titles.order_entered') }}</span>
                                </div>
                                
                                <!-- Step 2: Persiapan -->
                                <div class="flex flex-col items-center gap-2 bg-white dark:bg-gray-800 px-1" :class="getStepClass('processing')">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center border-2 transition-colors duration-300 z-10"
                                         :class="getStepCircleClass('processing')">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                    </div>
                                    <span class="text-[10px] sm:text-xs font-medium text-center" :class="getStepTextClass('processing')">{{ __('messages.titles.preparation') }}</span>
                                </div>

                                <!-- Step 3: Siap Diambil/Diantar -->
                                <div class="flex flex-col items-center gap-2 bg-white dark:bg-gray-800 px-1" :class="getStepClass('ready')">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center border-2 transition-colors duration-300 z-10"
                                         :class="getStepCircleClass('ready')">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    </div>
                                    <span class="text-[10px] sm:text-xs font-medium text-center" :class="getStepTextClass('ready')">{{ __('messages.titles.ready_to_deliver') }}</span>
                                </div>

                                <!-- Step 4: Selesai -->
                                <div class="flex flex-col items-center gap-2 bg-white dark:bg-gray-800 px-1" :class="getStepClass('completed')">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center border-2 transition-colors duration-300 z-10"
                                         :class="getStepCircleClass('completed')">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    <span class="text-[10px] sm:text-xs font-medium text-center" :class="getStepTextClass('completed')">{{ __('messages.titles.completed') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Items Table -->
                    <div class="overflow-x-auto rounded-lg border border-gray-100 dark:border-gray-700">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs text-gray-500 uppercase">
                                <tr>
                                    <th class="px-4 py-3">{{ __('messages.titles.product') }}</th>
                                    <th class="px-4 py-3 text-center">Qty</th>
                                    <th class="px-4 py-3 text-right">Harga</th>
                                    <th class="px-4 py-3 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                <template x-for="item in selectedTrx?.items" :key="item.id">
                                    <tr class="text-sm">
                                        <td class="px-4 py-3">
                                            <div class="font-medium text-gray-900 dark:text-white" x-text="item.product?.name || 'Produk dihapus'"></div>
                                            <div class="text-xs text-gray-500" x-text="item.product?.code"></div>
                                        </td>
                                        <td class="px-4 py-3 text-center" x-text="item.quantity"></td>
                                        <td class="px-4 py-3 text-right" x-text="formatRupiah(item.price)"></td>
                                        <td class="px-4 py-3 text-right font-medium" x-text="formatRupiah(item.quantity * item.price)"></td>
                                    </tr>
                                </template>
                            </tbody>
                            <tfoot class="bg-gray-50 dark:bg-gray-700/30">
                                <tr>
                                    <td colspan="3" class="px-4 py-3 text-right text-gray-500 font-medium">{{ __('messages.total') }}</td>
                                    <td class="px-4 py-3 text-right font-bold text-gray-900 dark:text-white text-lg" x-text="formatRupiah(selectedTrx?.total_amount)"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Info Section -->
                    <div class="mt-6 grid grid-cols-2 gap-4 text-sm">
                        <div class="bg-gray-50 dark:bg-gray-700/30 p-3 rounded">
                            <span class="text-gray-500 block mb-1">{{ __('messages.titles.payment_method_label') }}</span>
                            <span class="font-medium text-gray-900 dark:text-white capitalize" x-text="selectedTrx?.payment_method?.replace('_', ' ')"></span>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700/30 p-3 rounded">
                            <span class="text-gray-500 block mb-1">{{ __('messages.titles.notes') }}</span>
                            <span class="text-gray-900 dark:text-white" x-text="selectedTrx?.notes || '-'"></span>
                        </div>
                    </div>

                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                    <button type="button" @click="showModal = false" class="btn-secondary w-full sm:w-auto">
                        {{ __('messages.titles.close') }}
                    </button>
                    
                    @if(auth()->user()->hasAdminAccess())
                    <a :href="'/pos/receipt/' + selectedTrx?.id" target="_blank" class="btn-secondary w-full sm:w-auto text-center">
                        {{ __('messages.titles.print_receipt') }}
                    </a>

                    <!-- DYNAMIC ACTION BUTTONS -->
                    <template x-if="selectedTrx?.type === 'online' && selectedTrx?.status !== 'completed' && selectedTrx?.status !== 'cancelled' && selectedTrx?.status !== 'delivered'">
                        <form :action="'/pos/orders/' + selectedTrx?.id + '/process'" method="POST" class="w-full sm:w-auto inline-block">
                            @csrf
                            
                            <!-- Case: Pending -> Paid -->
                             <template x-if="selectedTrx?.status === 'pending'">
                                <div class="w-full">
                                    <input type="hidden" name="status" value="paid">
                                    <button type="submit" class="btn-primary w-full bg-blue-600 hover:bg-blue-700">
                                        {{ __('messages.titles.confirm_payment') }}
                                    </button>
                                </div>
                            </template>

                             <!-- Case: Paid/Credit -> Processing -->
                             <template x-if="selectedTrx?.status === 'paid' || selectedTrx?.status === 'credit'">
                                <div class="w-full">
                                    <input type="hidden" name="status" value="processing">
                                    <button type="submit" class="btn-primary w-full bg-orange-500 hover:bg-orange-600">
                                        {{ __('messages.titles.start_preparation') }}
                                    </button>
                                </div>
                            </template>

                             <!-- Case: Processing -> Ready -->
                             <template x-if="selectedTrx?.status === 'processing'">
                                <div class="w-full">
                                    <input type="hidden" name="status" value="ready">
                                    <button type="submit" class="btn-primary w-full bg-purple-500 hover:bg-purple-600">
                                        {{ __('messages.titles.items_ready') }}
                                    </button>
                                </div>
                            </template>

                             <!-- Case: Ready -> Completed/Delivered -->
                             <template x-if="selectedTrx?.status === 'ready'">
                                <div class="w-full">
                                    <!-- Use 'delivered' for Credit (Goods received, debt remains) -->
                                    <!-- Use 'completed' for Paid (Goods received, fully done) -->
                                    <input type="hidden" name="status" :value="selectedTrx?.payment_method === 'kredit' ? 'delivered' : 'completed'">
                                    
                                    <button type="submit" class="btn-primary w-full bg-green-600 hover:bg-green-700">
                                        <span x-text="selectedTrx?.payment_method === 'kredit' ? '{{ __('messages.titles.order_received') }}' : '{{ __('messages.titles.complete_order') }}'"></span>
                                    </button>
                                </div>
                            </template>
                        </form>
                    </template>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function salesHistory() {
        return {
            showModal: false,
            selectedTrx: null,

            openModal(trx) {
                this.selectedTrx = trx;
                this.showModal = true;
            },

            formatRupiah(value) {
                if (!value) return 'Rp 0';
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
            },

            // Status Logic
            getStatusStep(status) {
                if (status === 'pending') return 1;
                if (status === 'paid') return 1; 
                if (status === 'credit') return 1;
                
                if (status === 'processing') return 2;
                if (status === 'ready') return 3;
                if (status === 'completed') return 4;
                if (status === 'delivered') return 4;
                return 0;
            },

            getProgressWidth() {
                if (!this.selectedTrx) return 'width: 0%';
                const step = this.getStatusStep(this.selectedTrx.status);
                // Step 1: 0%
                // Step 2: 33%
                // Step 3: 66%
                // Step 4: 100%
                
                if (status === 'cancelled') return 'width: 0%; background-color: #EF4444'; 

                // Custom logic for Paid/Credit (Visual enhancement)
                if (this.selectedTrx.status === 'paid' || this.selectedTrx.status === 'credit') return 'width: 10%'; 

                if (step === 1) return 'width: 0%';
                if (step === 2) return 'width: 33%';
                if (step === 3) return 'width: 66%';
                if (step === 4) return 'width: 100%';
                
                return 'width: 0%';
            },

            getStepCircleClass(stepName) {
                if (!this.selectedTrx) return '';
                const currentStep = this.getStatusStep(this.selectedTrx.status);
                const stepLevel = this.getStatusStep(stepName);

                if (currentStep >= stepLevel) {
                    return 'bg-primary-600 border-primary-600 text-white';
                }
                return 'bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 text-gray-300';
            },

            getStepTextClass(stepName) {
                if (!this.selectedTrx) return '';
                const currentStep = this.getStatusStep(this.selectedTrx.status);
                const stepLevel = this.getStatusStep(stepName);

                if (currentStep >= stepLevel) {
                    return 'text-primary-600 font-bold';
                }
                return 'text-gray-500';
            },
            
            getStepClass(stepName) {
                 // Wrapper class if needed
                 return '';
            }
        }
    }
</script>
@endsection
