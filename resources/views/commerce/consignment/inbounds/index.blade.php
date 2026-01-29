@extends('layouts.app')

@section('title', __('messages.consignment.inbound.title'))

@section('content')
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                <span class="p-2 bg-blue-100 dark:bg-blue-900/50 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </span>
                {{ __('messages.consignment.inbound.title') }}
            </h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1 ml-12">{{ __('messages.consignment.inbound.subtitle') }}</p>
        </div>
        <a href="{{ route('consignment.inbounds.create') }}" class="btn-primary inline-flex items-center gap-2 whitespace-nowrap">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            {{ __('messages.consignment.inbound.btn_create') }}
        </a>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        {{-- Total Inbound --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-100 dark:border-gray-700 shadow-sm flex items-center gap-4">
            <div class="p-3 bg-indigo-100 dark:bg-indigo-900/50 rounded-xl">
                <svg class="w-7 h-7 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.consignment.inbound.card_total') }}</p>
                <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ $inbounds->total() }}</p>
            </div>
        </div>

        {{-- Total Items Received (This Month) --}}
        @php
            $totalItemsThisMonth = \App\Models\ConsignmentInboundItem::whereHas('inbound', function($q) {
                $q->whereMonth('inbound_date', now()->month)->whereYear('inbound_date', now()->year);
            })->sum('quantity');
        @endphp
        <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-100 dark:border-gray-700 shadow-sm flex items-center gap-4">
            <div class="p-3 bg-green-100 dark:bg-green-900/50 rounded-xl">
                <svg class="w-7 h-7 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.consignment.inbound.card_items') }}</p>
                <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($totalItemsThisMonth) }} pcs</p>
            </div>
        </div>

        {{-- Total Unique Consignors --}}
        @php
            $uniqueConsignors = \App\Models\ConsignmentInbound::distinct('consignor_id', 'consignor_type')->count();
        @endphp
        <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-100 dark:border-gray-700 shadow-sm flex items-center gap-4">
            <div class="p-3 bg-amber-100 dark:bg-amber-900/50 rounded-xl">
                <svg class="w-7 h-7 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.consignment.inbound.card_partners') }}</p>
                <p class="text-2xl font-bold text-amber-600 dark:text-amber-400">{{ $uniqueConsignors }}</p>
            </div>
        </div>
    </div>

    {{-- Data Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.consignment.inbound.table_transaction') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.consignment.inbound.table_date') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.consignment.inbound.table_partner') }}</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.consignment.inbound.table_total_items') }}</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.consignment.inbound.table_status') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.consignment.inbound.table_creator') }}</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.consignment.inbound.table_action') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($inbounds as $inbound)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            {{-- Transaction Number --}}
                            <td class="px-6 py-4">
                                <span class="text-sm font-medium text-blue-600 dark:text-blue-400">{{ $inbound->transaction_number }}</span>
                            </td>
                            
                            {{-- Date --}}
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $inbound->inbound_date->format('d/m/Y') }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $inbound->inbound_date->format('H:i') }} WIB</div>
                            </td>
                            
                            {{-- Consignor with Avatar --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @php
                                        $name = $inbound->consignor->name ?? 'Unknown';
                                        $initials = strtoupper(substr($name, 0, 1));
                                        $bgColors = ['bg-red-500', 'bg-blue-500', 'bg-green-500', 'bg-yellow-500', 'bg-purple-500', 'bg-pink-500', 'bg-indigo-500'];
                                        $bgColor = $bgColors[crc32($name) % count($bgColors)];
                                    @endphp
                                    <div class="w-10 h-10 rounded-full {{ $bgColor }} flex items-center justify-center text-white font-bold text-sm">
                                        {{ $initials }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $name }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            @if($inbound->consignor_type === 'member')
                                                {{ $inbound->consignor->member_id ?? '-' }}
                                            @else
                                                Supplier
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            
                            {{-- Total Items --}}
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                    {{ $inbound->items->sum('quantity') }} pcs
                                </span>
                            </td>
                            
                            {{-- Status --}}
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-300">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                    {{ __('messages.consignment.inbound.status_completed') }}
                                </span>
                            </td>
                            
                            {{-- Creator --}}
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ $inbound->creator->name ?? '-' }}</span>
                            </td>
                            
                            {{-- Actions --}}
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('consignment.inbounds.show', $inbound) }}" class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition-colors">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    {{ __('messages.consignment.inbound.btn_detail') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                                <p class="mt-4 text-gray-500 dark:text-gray-400">{{ __('messages.consignment.inbound.empty_data') }}</p>
                                <a href="{{ route('consignment.inbounds.create') }}" class="mt-4 inline-flex items-center text-blue-600 hover:text-blue-700 font-medium">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    {{ __('messages.consignment.inbound.empty_action') }}
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if($inbounds->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
            {{ $inbounds->links() }}
        </div>
        @endif
    </div>
@endsection
