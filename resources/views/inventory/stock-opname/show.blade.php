@extends('layouts.app')

@section('title', __('messages.stock_opname.show_title'))

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('stock-opname.index') }}" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                    {{ $stockOpname->opname_number }}
                    <span class="badge badge-{{ $stockOpname->status_color }} text-sm">{{ $stockOpname->status_label }}</span>
                </h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">
                    {{ __('messages.stock_opname.show_date') }}: {{ $stockOpname->opname_date->format('d F Y') }} | 
                    {{ __('messages.stock_opname.show_created') }}: {{ $stockOpname->creator->name ?? '-' }}
                </p>
            </div>
        </div>
        <div class="flex gap-2">
            @if($stockOpname->status === 'draft')
                <form action="{{ route('stock-opname.complete', $stockOpname) }}" method="POST" onsubmit="return confirm('{{ __('messages.stock_opname.confirm_complete') }}')">
                    @csrf
                    <button type="submit" class="btn-success flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        {{ __('messages.stock_opname.btn_complete_stock') }}
                    </button>
                </form>
                <form action="{{ route('stock-opname.cancel', $stockOpname) }}" method="POST" onsubmit="return confirm('{{ __('messages.stock_opname.confirm_cancel') }}')">
                    @csrf
                    <button type="submit" class="btn-danger flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        {{ __('messages.stock_opname.btn_cancel') }}
                    </button>
                </form>
            @endif
            <a href="{{ route('stock-opname.export', $stockOpname) }}" class="btn-secondary flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                {{ __('messages.stock_opname.btn_export_excel') }}
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="glass-card-solid p-4 text-center">
            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stockOpname->items->count() }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ __('messages.stock_opname.card_total_item') }}</p>
        </div>
        <div class="glass-card-solid p-4 text-center">
            <p class="text-3xl font-bold text-green-600">{{ $stockOpname->items->where('difference', '>', 0)->count() }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ __('messages.stock_opname.card_surplus') }}</p>
        </div>
        <div class="glass-card-solid p-4 text-center">
            <p class="text-3xl font-bold text-red-600">{{ $stockOpname->items->where('difference', '<', 0)->count() }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ __('messages.stock_opname.card_deficit') }}</p>
        </div>
        <div class="glass-card-solid p-4 text-center">
            <p class="text-3xl font-bold text-gray-600">{{ $stockOpname->items->where('difference', 0)->count() }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ __('messages.stock_opname.card_match') }}</p>
        </div>
    </div>

    <!-- Notes -->
    @if($stockOpname->notes)
    <div class="glass-card-solid p-4 bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400">
        <p class="text-sm text-gray-700 dark:text-gray-300"><strong>{{ __('messages.stock_opname.label_notes') }}:</strong> {{ $stockOpname->notes }}</p>
    </div>
    @endif

    <!-- Items Table -->
    <div class="glass-card-solid overflow-hidden">
        <div class="p-4 border-b border-gray-100 dark:border-gray-700">
            <h3 class="font-bold text-gray-900 dark:text-white">{{ __('messages.stock_opname.header_product_list') }}</h3>
        </div>
        <div class="table-scroll-container">
            <table class="table-modern w-full">
                <thead class="sticky top-0 z-10">
                    <tr>
                        <th>{{ __('messages.stock_opname.table_col_no') }}</th>
                        <th>{{ __('messages.stock_opname.table_col_code') }}</th>
                        <th>{{ __('messages.stock_opname.table_col_name') }}</th>
                        <th class="text-center">{{ __('messages.stock_opname.table_col_system') }}</th>
                        <th class="text-center">{{ __('messages.stock_opname.table_col_actual') }}</th>
                        <th class="text-center">{{ __('messages.stock_opname.table_col_diff') }}</th>
                        <th>{{ __('messages.stock_opname.table_col_notes') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stockOpname->items as $index => $item)
                    <tr class="{{ $item->difference != 0 ? ($item->difference > 0 ? 'bg-green-50 dark:bg-green-900/10' : 'bg-red-50 dark:bg-red-900/10') : '' }}">
                        <td>{{ $index + 1 }}</td>
                        <td class="font-mono text-xs text-gray-500">{{ $item->product->code ?? '-' }}</td>
                        <td class="font-medium text-gray-900 dark:text-white">{{ $item->product->name ?? '-' }}</td>
                        <td class="text-center">
                            <span class="badge badge-gray">{{ $item->system_stock }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-info">{{ $item->actual_stock }}</span>
                        </td>
                        <td class="text-center">
                            @if($item->difference > 0)
                                <span class="badge badge-success">+{{ $item->difference }}</span>
                            @elseif($item->difference < 0)
                                <span class="badge badge-danger">{{ $item->difference }}</span>
                            @else
                                <span class="badge badge-gray">0</span>
                            @endif
                        </td>
                        <td class="text-gray-500 text-sm">{{ $item->notes ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-8 text-gray-500">{{ __('messages.stock_opname.table_empty_items') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Completed Info -->
    @if($stockOpname->status === 'completed' && $stockOpname->completed_at)
    <div class="glass-card-solid p-4 bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500">
        <p class="text-sm text-green-700 dark:text-green-300">
            <strong>{{ __('messages.stock_opname.info_completed') }}:</strong> {{ __('messages.stock_opname.info_completed_msg', ['date' => $stockOpname->completed_at->format('d F Y H:i')]) }}
        </p>
    </div>
    @endif
</div>
@endsection
