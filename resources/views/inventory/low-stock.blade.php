@extends('layouts.app')

@section('title', __('messages.titles.low_stock'))

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('messages.low_stock.title') }}</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">
                <span class="text-red-600 font-semibold">{{ $totalLowStock }}</span> {{ __('messages.low_stock.subtitle', ['count' => '']) }}
            </p>
        </div>
        <a href="{{ route('products.index') }}" class="btn-secondary">
            ← {{ __('messages.low_stock.back_to_products') }}
        </a>
    </div>

    <!-- Alert Info -->
    @if($totalLowStock > 0)
    <div class="bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-800 rounded-xl p-4">
        <div class="flex gap-3">
            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <div>
                <p class="font-medium text-amber-800 dark:text-amber-300">{{ __('messages.low_stock.alert_title') }}</p>
                <p class="text-sm text-amber-700 dark:text-amber-400">{{ __('messages.low_stock.alert_msg') }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Search -->
    <div class="glass-card-solid p-4">
        <form method="GET" class="flex gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('messages.low_stock.search_placeholder') }}" class="form-input">
            </div>
            <button type="submit" class="btn-primary">{{ __('messages.low_stock.search_btn') }}</button>
        </form>
    </div>

    <!-- Products Table -->
    <div class="glass-card-solid overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>{{ __('messages.low_stock.header_product') }}</th>
                        <th>{{ __('messages.low_stock.header_category') }}</th>
                        <th class="text-center">{{ __('messages.low_stock.header_stock') }}</th>
                        <th class="text-center">{{ __('messages.low_stock.header_min_stock') }}</th>
                        <th class="text-center">Rata-rata Mingguan</th>
                        <th class="text-center">{{ __('messages.low_stock.header_status') }}</th>
                        <th>{{ __('messages.low_stock.header_action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-12 h-12 rounded-lg object-cover bg-gray-100">
                                <div>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $product->name }}</span>
                                    <div class="text-xs text-gray-500">{{ $product->code }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="text-gray-600 dark:text-gray-400">{{ $product->category->name ?? '-' }}</td>
                        <td class="text-center">
                            <span class="text-2xl font-bold {{ $product->stock <= 0 ? 'text-red-600' : ($product->stock <= $product->min_stock * 0.5 ? 'text-orange-600' : 'text-amber-600') }}">
                                {{ $product->stock }}
                            </span>
                        </td>
                        <td class="text-center text-gray-500">{{ $product->min_stock }}</td>
                        <td class="text-center">
                            @php
                                $sales30Days = $product->transactionItems->sum('quantity');
                                $weeklyAvg = ($sales30Days / 30) * 7;
                            @endphp
                            <span class="text-sm font-bold text-gray-800 dark:text-gray-200">
                                {{ number_format($weeklyAvg, 1) }}
                            </span>
                            <span class="text-[10px] text-gray-400 block">{{ $product->unit }} / minggu</span>
                        </td>
                        <td class="text-center">
                            @if($product->stock <= 0)
                            <span class="badge badge-danger">{{ __('messages.low_stock.status_empty') }}</span>
                            @elseif($product->stock <= $product->min_stock * 0.5)
                            <span class="badge badge-danger">{{ __('messages.low_stock.status_critical') }}</span>
                            @else
                            <span class="badge badge-warning">{{ __('messages.low_stock.status_low') }}</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('purchases.create') }}" class="btn-primary-sm flex items-center justify-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                {{ __('messages.low_stock.btn_buy') ?? 'Beli' }}
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-12">
                            <svg class="w-16 h-16 mx-auto text-green-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-green-600 font-medium">{{ __('messages.low_stock.empty_title') }}</p>
                            <p class="text-gray-500 text-sm">{{ __('messages.low_stock.empty_desc') }}</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($products->hasPages())
        <div class="p-4 border-t border-gray-100 dark:border-gray-700">
            {{ $products->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
