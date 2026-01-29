@extends('layouts.app')

@section('title', __('messages.shop_history.title'))

@section('content')
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                <span class="p-2 bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                </span>
                {{ __('messages.shop_history.title') }}
            </h1>
            <div class="flex gap-3">
                <a href="{{ route('members.credits') }}" class="btn-secondary !py-2 !px-4 text-xs font-bold uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                    {{ __('messages.shop_history.btn_credit_history') }}
                </a>
            </div>
        </div>

        <div class="glass-card-solid overflow-hidden shadow-xl shadow-gray-200/50 dark:shadow-none">
            <div class="table-scroll-container">
                <table class="w-full">
                    <thead class="sticky top-0 z-10">
                        <tr class="bg-gray-50/50 dark:bg-gray-800/50 border-b border-gray-100 dark:border-gray-700">
                            <th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ __('messages.shop_history.table_transaction') }}</th>
                            <th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest hidden sm:table-cell">{{ __('messages.shop_history.table_method') }}</th>
                            <th class="px-6 py-4 text-right text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ __('messages.shop_history.table_total') }}</th>
                            <th class="px-6 py-4 text-center text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ __('messages.shop_history.table_status') }}</th>
                            <th class="px-6 py-4 text-center text-[10px] font-bold text-gray-400 uppercase tracking-widest hidden sm:table-cell">{{ __('messages.shop_history.table_action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($transactions as $trx)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors group">
                            <!-- Transaction Details -->
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-lg bg-primary-50 dark:bg-primary-900/20 flex items-center justify-center text-lg shrink-0 border border-primary-100 dark:border-primary-800">
                                        {{ $trx->type == 'online' ? '🛒' : '🏪' }}
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-mono text-sm font-bold text-gray-900 dark:text-white group-hover:text-primary-600 transition-colors">{{ $trx->invoice_number }}</span>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="text-[10px] text-gray-400 bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded font-medium">{{ $trx->type }}</span>
                                            <span class="text-[11px] text-gray-500">{{ $trx->created_at->format('d M Y, H:i') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-2 sm:hidden flex flex-wrap items-center gap-2 pl-14">
                                    <!-- Mobile Actions -->
                                    <a href="{{ route('shop.track', $trx) }}" class="text-[10px] font-bold text-primary-600 hover:text-primary-700 uppercase tracking-wider flex items-center gap-1 border border-primary-200 bg-primary-50 px-2 py-0.5 rounded-md">
                                        {{ in_array($trx->status, ['pending', 'processed']) ? '🔍' : '📄' }} {{ __('messages.shop_history.action.detail') }}
                                    </a>

                                    @if(($trx->status == 'completed' || $trx->status == 'paid') && $trx->items->isNotEmpty())
                                        <a href="{{ route('shop.show', $trx->items->first()->product_id ?? '#') }}" 
                                           class="text-[10px] font-bold text-yellow-600 hover:text-yellow-700 uppercase tracking-wider flex items-center gap-1 border border-yellow-200 bg-yellow-50 px-2 py-0.5 rounded-md">
                                            ⭐ {{ __('messages.shop_history.action.review') }}
                                        </a>
                                    @endif
                                    
                                    @if($trx->status == 'credit')
                                        <a href="{{ route('members.credits') }}" class="text-[10px] font-bold text-white uppercase tracking-wider flex items-center gap-1 bg-primary-600 hover:bg-primary-700 px-2 py-0.5 rounded-md shadow-sm">
                                            💰 {{ __('messages.shop_history.action.pay') }}
                                        </a>
                                    @endif
                                </div>
                            </td>

                            <!-- Payment Method -->
                            <td class="px-6 py-5 hidden sm:table-cell">
                                <div class="flex items-center gap-2">
                                    @if($trx->payment_method == 'cash' || $trx->payment_method == 'cash_pickup')
                                        <div class="w-6 h-6 rounded-full bg-green-50 text-green-600 flex items-center justify-center text-xs">💵</div>
                                        <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">{{ __('messages.shop_history.payment_method.cash') }}</span>
                                    @elseif($trx->payment_method == 'kredit')
                                        <div class="w-6 h-6 rounded-full bg-orange-50 text-orange-600 flex items-center justify-center text-xs">📝</div>
                                        <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">{{ __('messages.shop_history.payment_method.credit') }}</span>
                                    @else
                                        <div class="w-6 h-6 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-xs">💳</div>
                                        <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">{{ __('messages.shop_history.payment_method.balance') }}</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Total -->
                            <td class="px-6 py-5 text-right">
                                <span class="font-black text-gray-900 dark:text-white text-base font-mono">
                                    Rp {{ number_format($trx->total_amount, 0, ',', '.') }}
                                </span>
                                <div class="text-[10px] text-gray-400 mt-0.5">{{ $trx->items->count() }} {{ __('messages.shop_history.items') }}</div>
                            </td>

                            <!-- Status -->
                            <td class="px-6 py-5 text-center">
                                @if($trx->status == 'completed' || $trx->status == 'delivered')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-green-50 text-green-600 border border-green-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span>
                                        {{ __('messages.shop_history.status.completed') }}
                                    </span>
                                @elseif($trx->status == 'processing' || $trx->status == 'ready' || $trx->status == 'paid')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-blue-50 text-blue-600 border border-blue-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500 mr-1.5 animate-pulse"></span>
                                        {{ __('messages.shop_history.status.processing') }}
                                    </span>
                                @elseif($trx->status == 'credit')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-orange-50 text-orange-600 border border-orange-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-orange-500 mr-1.5 animate-pulse"></span>
                                        {{ __('messages.shop_history.status.credit') }}
                                    </span>
                                @elseif($trx->status == 'pending')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-yellow-50 text-yellow-600 border border-yellow-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 mr-1.5 animate-pulse"></span>
                                        {{ __('messages.shop_history.status.pending') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-red-50 text-red-600 border border-red-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500 mr-1.5"></span>
                                        {{ $trx->status == 'cancelled' ? __('messages.shop_history.status.cancelled') : $trx->status }}
                                    </span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-5 text-center hidden sm:table-cell">
                                <div class="flex justify-center gap-2">
                                    <a href="{{ route('shop.track', $trx) }}" class="btn-secondary !py-1.5 !px-3 !text-[10px] uppercase font-bold tracking-wider hover:!text-primary-600 hover:!border-primary-300 transition-all flex items-center gap-1">
                                        {{ in_array($trx->status, ['pending', 'processed']) ? '🔍 ' . __('messages.shop_history.action.track') : '📄 ' . __('messages.shop_history.action.detail') }}
                                    </a>

                                    @if(($trx->status == 'completed' || $trx->status == 'paid') && $trx->items->isNotEmpty())
                                        <a href="{{ route('shop.show', $trx->items->first()->product_id ?? '#') }}" 
                                           class="btn-secondary !py-1.5 !px-3 !text-[10px] font-bold uppercase tracking-wider hover:!text-yellow-600 hover:!border-yellow-300 transition-all flex items-center gap-1 group/btn"
                                           title="{{ __('messages.shop_history.action.review') }}">
                                            <span class="group-hover/btn:scale-110 transition-transform">⭐</span> {{ __('messages.shop_history.action.review') }}
                                        </a>
                                    @endif
                                    
                                    @if($trx->status == 'credit')
                                        <a href="{{ route('members.credits') }}" class="btn-primary !py-1.5 !px-3 !text-[10px] uppercase font-bold tracking-wider">
                                            {{ __('messages.shop_history.action.pay') }}
                                        </a>
                                    @endif
                                </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center text-gray-400">
                                <div class="w-16 h-16 bg-gray-50 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                </div>
                                <h3 class="font-bold text-gray-900 dark:text-white mb-1">{{ __('messages.shop_history.empty_title') }}</h3>
                                <p class="text-xs mb-4">{{ __('messages.shop_history.empty_desc') }}</p>
                                <a href="{{ route('shop.index') }}" class="text-primary-600 text-xs font-bold hover:underline uppercase tracking-wider">{{ __('messages.shop_history.btn_start_shopping') }} &rarr;</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($transactions->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50">
                {{ $transactions->links() }}
            </div>
            @endif
        </div>
    </div>
@endsection
