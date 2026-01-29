@extends('layouts.app')

@section('title', __('messages.consignment.settlement.title'))

@section('content')
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                <span class="p-2 bg-purple-100 dark:bg-purple-900/50 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </span>
                {{ __('messages.consignment.settlement.title') }}
            </h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1 ml-12">{{ __('messages.consignment.settlement.subtitle') }}</p>
        </div>
        <a href="{{ route('consignment.settlements.create') }}" class="btn-primary inline-flex items-center gap-2 whitespace-nowrap">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            {{ __('messages.consignment.settlement.btn_create') }}
        </a>
    </div>

    {{-- Summary Cards --}}
    @php
        $totalPaid = \App\Models\ConsignmentSettlement::where('status', 'paid')->sum('total_payable_amount');
        $totalProfit = \App\Models\ConsignmentSettlement::where('status', 'paid')->sum('total_profit_amount');
        $thisMonthPaid = \App\Models\ConsignmentSettlement::where('status', 'paid')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('total_payable_amount');
    @endphp
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        {{-- Total Paid to Partners --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-100 dark:border-gray-700 shadow-sm flex items-center gap-4">
            <div class="p-3 bg-red-100 dark:bg-red-900/50 rounded-xl">
                <svg class="w-7 h-7 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.consignment.settlement.card_paid') }}</p>
                <p class="text-xl font-bold text-red-600 dark:text-red-400">Rp {{ number_format($totalPaid, 0, ',', '.') }}</p>
            </div>
        </div>

        {{-- Total Profit --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-100 dark:border-gray-700 shadow-sm flex items-center gap-4">
            <div class="p-3 bg-green-100 dark:bg-green-900/50 rounded-xl">
                <svg class="w-7 h-7 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.consignment.settlement.card_profit') }}</p>
                <p class="text-xl font-bold text-green-600 dark:text-green-400">Rp {{ number_format($totalProfit, 0, ',', '.') }}</p>
            </div>
        </div>

        {{-- This Month --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-100 dark:border-gray-700 shadow-sm flex items-center gap-4">
            <div class="p-3 bg-blue-100 dark:bg-blue-900/50 rounded-xl">
                <svg class="w-7 h-7 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.consignment.settlement.card_month') }}</p>
                <p class="text-xl font-bold text-blue-600 dark:text-blue-400">Rp {{ number_format($thisMonthPaid, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    {{-- Data Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.consignment.settlement.table_transaction') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.consignment.settlement.table_period') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.consignment.settlement.table_partner') }}</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.consignment.settlement.table_paid') }}</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.consignment.settlement.table_profit') }}</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.consignment.settlement.table_status') }}</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.consignment.inbound.table_action') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($settlements as $settlement)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            {{-- Transaction Number --}}
                            <td class="px-6 py-4">
                                <span class="text-sm font-medium text-purple-600 dark:text-purple-400">{{ $settlement->transaction_number }}</span>
                            </td>
                            
                            {{-- Period --}}
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 dark:text-white">
                                    {{ $settlement->period_start->format('d M') }} - {{ $settlement->period_end->format('d M Y') }}
                                </div>
                            </td>
                            
                            {{-- Consignor with Avatar --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @php
                                        $name = $settlement->consignor->name ?? 'Unknown';
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
                                            @if($settlement->consignor_type === 'member')
                                                {{ $settlement->consignor->member_id ?? '-' }}
                                            @else
                                                Supplier
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            
                            {{-- Total Payable --}}
                            <td class="px-6 py-4 text-right">
                                <span class="text-sm font-bold text-red-600 dark:text-red-400">Rp {{ number_format($settlement->total_payable_amount, 0, ',', '.') }}</span>
                            </td>
                            
                            {{-- Profit --}}
                            <td class="px-6 py-4 text-right">
                                <span class="text-sm font-bold text-green-600 dark:text-green-400">Rp {{ number_format($settlement->total_profit_amount, 0, ',', '.') }}</span>
                            </td>
                            
                            {{-- Status --}}
                            <td class="px-6 py-4 text-center">
                                @if($settlement->status === 'paid')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-300">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                    {{ __('messages.consignment.settlement.status_paid') }}
                                </span>
                                @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 dark:bg-yellow-900/50 text-yellow-700 dark:text-yellow-300">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path></svg>
                                    {{ __('messages.consignment.settlement.status_pending') }}
                                </span>
                                @endif
                            </td>
                            
                            {{-- Actions --}}
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('consignment.settlements.show', $settlement) }}" class="inline-flex items-center px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white text-xs font-medium rounded-lg transition-colors">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    {{ __('messages.consignment.inbound.btn_detail') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <p class="mt-4 text-gray-500 dark:text-gray-400">{{ __('messages.consignment.settlement.empty_data') }}</p>
                                <a href="{{ route('consignment.settlements.create') }}" class="mt-4 inline-flex items-center text-purple-600 hover:text-purple-700 font-medium">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    {{ __('messages.consignment.settlement.empty_action') }}
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if($settlements->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
            {{ $settlements->links() }}
        </div>
        @endif
    </div>
@endsection
