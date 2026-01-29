@extends('layouts.app')

@section('title', __('messages.consignment.inbound.show_title'))

@section('content')
    {{-- Back Button & Header --}}
    <div class="mb-6">
        <a href="{{ route('consignment.inbounds.index') }}" class="inline-flex items-center text-gray-500 hover:text-blue-600 mb-4 transition-colors text-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            {{ __('messages.back') }}
        </a>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                    <span class="p-2 bg-blue-100 dark:bg-blue-900/50 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </span>
                    {{ $inbound->transaction_number }}
                </h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1 ml-12">{{ __('messages.consignment.inbound.show_subtitle') }}</p>
            </div>
            <button onclick="window.print()" class="btn-secondary inline-flex items-center gap-2 print:hidden">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                {{ __('messages.consignment.inbound.btn_print') }}
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left: Info Card --}}
        <div class="lg:col-span-1 space-y-4">
            {{-- Transaction Info --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm">
                <h3 class="font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-3 mb-4">{{ __('messages.consignment.inbound.section_transaction') }}</h3>
                
                <div class="space-y-3">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">{{ __('messages.consignment.inbound.label_transaction_no') }}</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $inbound->transaction_number }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">{{ __('messages.consignment.inbound.table_date') }}</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $inbound->inbound_date->format('d/m/Y') }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">{{ __('messages.consignment.inbound.label_status') }}</span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-300">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                            {{ __('messages.consignment.inbound.status_completed') }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">{{ __('messages.consignment.inbound.label_creator') }}</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $inbound->creator->name ?? '-' }}</span>
                    </div>
                </div>
            </div>

            {{-- Consignor Info --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm">
                <h3 class="font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-3 mb-4">{{ __('messages.consignment.inbound.section_partner') }}</h3>
                
                <div class="flex items-center gap-4">
                    @php
                        $name = $inbound->consignor->name ?? 'Unknown';
                        $initials = strtoupper(substr($name, 0, 1));
                        $bgColors = ['bg-red-500', 'bg-blue-500', 'bg-green-500', 'bg-yellow-500', 'bg-purple-500', 'bg-pink-500', 'bg-indigo-500'];
                        $bgColor = $bgColors[crc32($name) % count($bgColors)];
                    @endphp
                    <div class="w-14 h-14 rounded-full {{ $bgColor }} flex items-center justify-center text-white font-bold text-xl">
                        {{ $initials }}
                    </div>
                    <div>
                        <div class="font-bold text-lg text-gray-900 dark:text-white">{{ $name }}</div>
                        <div class="text-sm text-gray-500">
                            @if($inbound->consignor_type === 'member')
                                ID: {{ $inbound->consignor->member_id ?? '-' }}
                            @else
                                Supplier
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Notes --}}
            @if($inbound->note)
            <div class="bg-amber-50 dark:bg-amber-900/20 rounded-xl p-4 border border-amber-200 dark:border-amber-800">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-amber-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path></svg>
                    <div>
                        <div class="text-xs font-semibold text-amber-700 dark:text-amber-400 uppercase mb-1">{{ __('messages.consignment.inbound.label_note') }}</div>
                        <p class="text-sm text-amber-800 dark:text-amber-300">{{ $inbound->note }}</p>
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- Right: Items Table --}}
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 flex justify-between items-center">
                    <div>
                        <h3 class="font-bold text-gray-900 dark:text-white">{{ __('messages.consignment.inbound.section_items_detail') }}</h3>
                        <p class="text-sm text-gray-500">{{ __('messages.consignment.inbound.items_count', ['count' => $inbound->items->count()]) }}</p>
                    </div>
                    <div class="text-2xl">📦</div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-700/30">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">{{ __('messages.consignment.inbound.table_item_product') }}</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">{{ __('messages.consignment.inbound.table_item_qty') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">{{ __('messages.consignment.inbound.table_item_cost') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">{{ __('messages.consignment.inbound.table_item_subtotal') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @php $totalValue = 0; @endphp
                            @foreach($inbound->items as $item)
                                @php 
                                    $subtotal = $item->quantity * $item->unit_cost; 
                                    $totalValue += $subtotal;
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900 dark:text-white">{{ $item->product->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $item->product->code }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300">
                                            {{ number_format($item->quantity) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm text-gray-700 dark:text-gray-300">
                                        Rp {{ number_format($item->unit_cost, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm font-medium text-gray-900 dark:text-white">
                                        Rp {{ number_format($subtotal, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-blue-50 dark:bg-blue-900/20">
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-right font-bold text-gray-700 dark:text-gray-300">
                                    {{ __('messages.consignment.inbound.total_value') }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-xl font-bold text-blue-600 dark:text-blue-400">Rp {{ number_format($totalValue, 0, ',', '.') }}</span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Print Styles --}}
    <style>
        @media print {
            body { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            .print\:hidden { display: none !important; }
            nav, footer, .sidebar { display: none !important; }
            main { margin: 0 !important; padding: 0 !important; }
        }
    </style>
@endsection
