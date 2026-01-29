@extends('layouts.app')

@section('title', __('messages.consignment.settlement.create_title'))

@section('content')
    <div class="page-header">
        <a href="{{ route('consignment.settlements.index') }}" class="flex items-center text-gray-500 hover:text-primary-600 mb-4 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            {{ __('messages.back') }}
        </a>
        <h1 class="page-title">{{ __('messages.consignment.settlement.create_title') }}</h1>
    </div>

    <!-- Filter -->
    <div class="glass-card p-6 mb-6">
        <form action="{{ route('consignment.settlements.create') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div class="form-group">
                <label class="form-label">{{ __('messages.consignment.settlement.filter_start') }}</label>
                <input type="date" name="start_date" class="form-input" value="{{ $startDate }}">
            </div>
            <div class="form-group">
                <label class="form-label">{{ __('messages.consignment.settlement.filter_end') }}</label>
                <input type="date" name="end_date" class="form-input" value="{{ $endDate }}">
            </div>
            <div class="form-group">
                <button type="submit" class="btn-primary w-full">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    {{ __('messages.consignment.settlement.btn_filter') }}
                </button>
            </div>
        </form>
    </div>

    @if(count($report) > 0)
        <div class="grid grid-cols-1 gap-6">
            @foreach($report as $row)
                <div class="glass-card overflow-hidden">
                    <div class="p-4 bg-gray-50 dark:bg-gray-800 border-b flex justify-between items-center">
                        <div>
                            <div class="text-xs text-gray-500 uppercase tracking-wider">{{ ucfirst($row['consignor_type']) }}</div>
                            <div class="font-bold text-lg text-gray-900 dark:text-white">{{ $row['consignor']->name ?? 'Unknown' }}</div>
                        </div>
                        <div class="text-right flex items-center justify-end gap-2">
                            <form action="{{ route('consignment.settlements.store') }}" method="POST" onsubmit="return confirm('{{ __('messages.consignment.settlement.confirm_process') }}')">
                                @csrf
                                <input type="hidden" name="consignor_type" value="{{ $row['consignor_type'] }}">
                                <input type="hidden" name="consignor_id" value="{{ $row['consignor_id'] }}">
                                <input type="hidden" name="start_date" value="{{ $startDate }}">
                                <input type="hidden" name="end_date" value="{{ $endDate }}">
                                
                                <div class="flex gap-2">
                                    <select name="payment_method" class="form-input py-1 px-2 text-sm w-40">
                                        <option value="cash">{{ __('messages.consignment.settlement.payment_method.cash') }}</option>
                                        <option value="transfer">{{ __('messages.consignment.settlement.payment_method.transfer') }}</option>
                                        @if($row['consignor_type'] === 'member')
                                        <option value="savings" class="font-bold text-green-600">{{ __('messages.consignment.settlement.payment_method.savings') }}</option>
                                        @endif
                                    </select>
                                    <button type="submit" class="btn-primary btn-sm whitespace-nowrap">
                                        💰 {{ __('messages.consignment.settlement.btn_process') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                            <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                <div class="text-xs text-gray-500 mb-1">{{ __('messages.consignment.settlement.card_sold') }}</div>
                                <div class="font-bold text-xl">{{ number_format($row['total_qty']) }}</div>
                            </div>
                            <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                                <div class="text-xs text-gray-500 mb-1">{{ __('messages.consignment.settlement.card_turnover') }}</div>
                                <div class="font-bold text-xl text-green-600">IDR {{ number_format($row['total_sales']) }}</div>
                            </div>
                            <div class="p-4 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-100 dark:border-red-800">
                                <div class="text-xs text-gray-500 mb-1">{{ __('messages.consignment.settlement.card_payable') }}</div>
                                <div class="font-bold text-xl text-red-600">IDR {{ number_format($row['total_payable']) }}</div>
                            </div>
                            <div class="p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                                <div class="text-xs text-gray-500 mb-1">{{ __('messages.consignment.settlement.card_coop_profit') }}</div>
                                <div class="font-bold text-xl text-purple-600">IDR {{ number_format($row['total_profit']) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="glass-card p-12 text-center text-gray-500">
            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
            <p class="text-lg">{{ __('messages.consignment.settlement.no_pending') }}</p>
        </div>
    @endif
@endsection
