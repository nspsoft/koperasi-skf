@extends('layouts.app')

@section('title', __('messages.consignment.settlement.show_title'))

@section('content')
    {{-- Back Button & Header --}}
    <div class="mb-6">
        <a href="{{ route('consignment.settlements.index') }}" class="inline-flex items-center text-gray-500 hover:text-purple-600 mb-4 transition-colors text-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            {{ __('messages.back') }}
        </a>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                    <span class="p-2 bg-purple-100 dark:bg-purple-900/50 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </span>
                    {{ $settlement->transaction_number }}
                </h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1 ml-12">{{ __('messages.consignment.settlement.show_subtitle') }}</p>
            </div>
            <button onclick="window.print()" class="btn-secondary inline-flex items-center gap-2 print:hidden">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                {{ __('messages.consignment.settlement.btn_print_report') }}
            </button>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        {{-- Period --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-100 dark:border-gray-700 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-gray-100 dark:bg-gray-700 rounded-lg">
                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <div>
                    <div class="text-xs text-gray-500 uppercase">{{ __('messages.consignment.settlement.table_period') }}</div>
                    <div class="font-bold text-gray-900 dark:text-white text-sm">{{ $settlement->period_start->format('d M') }} - {{ $settlement->period_end->format('d M Y') }}</div>
                </div>
            </div>
        </div>

        {{-- Total Sales --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-100 dark:border-gray-700 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-green-100 dark:bg-green-900/50 rounded-lg">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                </div>
                <div>
                    <div class="text-xs text-gray-500 uppercase">{{ __('messages.consignment.settlement.card_turnover') }}</div>
                    <div class="font-bold text-green-600 dark:text-green-400">Rp {{ number_format($settlement->total_sales_amount, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        {{-- Paid to Partner --}}
        <div class="bg-red-50 dark:bg-red-900/20 rounded-xl p-4 border border-red-200 dark:border-red-800 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-red-100 dark:bg-red-900/50 rounded-lg">
                    <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <div class="text-xs text-gray-500 uppercase">{{ __('messages.consignment.settlement.card_paid') }}</div>
                    <div class="font-bold text-red-600 dark:text-red-400">Rp {{ number_format($settlement->total_payable_amount, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        {{-- Profit --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-100 dark:border-gray-700 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-purple-100 dark:bg-purple-900/50 rounded-lg">
                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                </div>
                <div>
                    <div class="text-xs text-gray-500 uppercase">{{ __('messages.consignment.settlement.card_coop_profit') }}</div>
                    <div class="font-bold text-purple-600 dark:text-purple-400">Rp {{ number_format($settlement->total_profit_amount, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left: Info Cards --}}
        <div class="lg:col-span-1 space-y-4">
            {{-- Consignor Info --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm">
                <h3 class="font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-3 mb-4">{{ __('messages.consignment.settlement.section_partner') }}</h3>
                
                <div class="flex items-center gap-4 mb-4">
                    @php
                        $name = $settlement->consignor->name ?? 'Unknown';
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
                            @if($settlement->consignor_type === 'member')
                                ID: {{ $settlement->consignor->member_id ?? '-' }}
                            @else
                                Supplier
                            @endif
                        </div>
                    </div>
                </div>

                <div class="space-y-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">{{ __('messages.consignment.settlement.label_payment_status') }}</span>
                        @if($settlement->status === 'paid')
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-300">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                            {{ __('messages.consignment.settlement.status_paid') }}
                        </span>
                        @else
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">{{ __('messages.consignment.settlement.status_pending') }}</span>
                        @endif
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">{{ __('messages.consignment.settlement.label_payment_date') }}</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $settlement->paid_at ? $settlement->paid_at->format('d/m/Y H:i') : '-' }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">{{ __('messages.consignment.settlement.label_processed_by') }}</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $settlement->paidBy->name ?? '-' }}</span>
                    </div>
                </div>
            </div>

            {{-- Notes/Info --}}
            @if($settlement->notes)
            <div class="bg-amber-50 dark:bg-amber-900/20 rounded-xl p-4 border border-amber-200 dark:border-amber-800">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-amber-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div>
                        <div class="text-xs font-semibold text-amber-700 dark:text-amber-400 uppercase mb-1">{{ __('messages.consignment.inbound.label_note') }}</div>
                        <p class="text-sm text-amber-800 dark:text-amber-300">{{ $settlement->notes }}</p>
                    </div>
                </div>
            </div>
            @endif

            {{-- Legal Notice --}}
            <div class="bg-gray-100 dark:bg-gray-700 rounded-xl p-4">
                <p class="text-xs text-gray-500 dark:text-gray-400 text-center">
                    {{ __('messages.consignment.settlement.legal_notice') }}
                </p>
            </div>
        </div>

        {{-- Right: Items Table --}}
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 flex justify-between items-center">
                    <div>
                        <h3 class="font-bold text-gray-900 dark:text-white">{{ __('messages.consignment.settlement.section_sales_detail') }}</h3>
                        <p class="text-sm text-gray-500">{{ __('messages.consignment.settlement.sales_count', ['count' => $settlement->items->count()]) }}</p>
                    </div>
                    <div class="text-right">
                        <div class="text-xs text-gray-500">{{ __('messages.consignment.settlement.table_period') }}</div>
                        <div class="font-medium">{{ $settlement->start_date->format('d/m/Y') }} - {{ $settlement->end_date->format('d/m/Y') }}</div>
                    </div>
                </div>
                
                <div class="overflow-x-auto max-h-[500px]">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-700/30 sticky top-0">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">{{ __('messages.consignment.settlement.table_product') }}</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">{{ __('messages.consignment.settlement.table_invoice') }}</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">{{ __('messages.consignment.settlement.table_qty') }}</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">{{ __('messages.consignment.settlement.table_price') }}</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">{{ __('messages.consignment.settlement.table_cost') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($settlement->items as $item)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-gray-900 dark:text-white text-sm">{{ $item->product->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $item->product->code }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="text-xs font-mono text-blue-600 dark:text-blue-400">{{ $item->transaction->invoice_number ?? '-' }}</div>
                                        <div class="text-xs text-gray-400">{{ $item->created_at->format('d/m/Y') }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                            {{ $item->quantity }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right text-sm text-gray-700 dark:text-gray-300">
                                        Rp {{ number_format($item->quantity * $item->price, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-right text-sm font-medium text-gray-900 dark:text-white">
                                        Rp {{ number_format($item->quantity * $item->product->consignment_price, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        {{-- Footer Summary --}}
                        <tfoot class="bg-purple-50 dark:bg-purple-900/20 border-t border-purple-100 dark:border-purple-800">
                           <tr>
                                <td colspan="4" class="px-6 py-4 text-right font-bold text-gray-700 dark:text-gray-300">
                                    {{ __('messages.consignment.settlement.footer_total') }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-2xl font-bold text-purple-600 dark:text-purple-400">Rp {{ number_format($settlement->total_payable_amount, 0, ',', '.') }}</span>
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
