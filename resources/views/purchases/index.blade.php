@extends('layouts.app')

@section('title', __('messages.titles.purchases'))

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('messages.purchases.title') }}</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">{{ __('messages.purchases.subtitle') }}</p>
        </div>
        <a href="{{ route('purchases.create') }}" class="btn-primary">
            {{ __('messages.purchases.btn_new') }}
        </a>
    </div>

    <!-- Filters -->
    <div class="glass-card-solid p-4">
        <form method="GET" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('messages.purchases.filter_placeholder') }}" class="form-input">
            </div>
            <div class="w-full sm:w-48">
                <select name="status" class="form-input" onchange="this.form.submit()">
                    <option value="">{{ __('messages.purchases.all_status') }}</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>{{ __('messages.purchases.status_pending') }}</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>{{ __('messages.purchases.status_completed') }}</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>{{ __('messages.purchases.status_cancelled') }}</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn-secondary">{{ __('messages.purchases.table_action') === 'Aksi' ? 'Filter' : 'Filter' }}</button>
                <a href="{{ route('purchases.export', request()->query()) }}" class="btn-secondary flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Excel
                </a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="glass-card-solid overflow-hidden">
        <div class="table-scroll-container">
            <table class="table-modern">
                <thead class="sticky top-0 z-10">
                    <tr>
                        <th>{{ __('messages.purchases.table_no') }}</th>
                        <th>{{ __('messages.purchases.table_date') }}</th>
                        <th>{{ __('messages.purchases.table_supplier') }}</th>
                        <th class="text-right">{{ __('messages.purchases.table_total') }}</th>
                        <th>{{ __('messages.purchases.table_status') }}</th>
                        <th>{{ __('messages.purchases.table_creator') }}</th>
                        <th>{{ __('messages.purchases.table_action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchases as $purchase)
                    <tr>
                        <td class="font-medium text-gray-900 dark:text-white">{{ $purchase->reference_number }}</td>
                        <td class="text-gray-600 dark:text-gray-400">
                            {{ $purchase->purchase_date->format('d M Y') }}
                        </td>
                        <td>
                            <div class="font-medium text-gray-900 dark:text-white">{{ $purchase->supplier->name ?? '-' }}</div>
                            <div class="text-xs text-gray-500">{{ $purchase->supplier->contact_person ?? '' }}</div>
                        </td>
                        <td class="text-right font-bold text-gray-900 dark:text-white">
                            Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}
                        </td>
                        <td>
                            <span class="badge badge-{{ $purchase->status_color }}">
                                {{ $purchase->status_label }}
                            </span>
                        </td>
                        <td class="text-sm text-gray-600 dark:text-gray-400">
                            {{ $purchase->creator->name ?? '-' }}
                        </td>
                        <td>
                            <a href="{{ route('purchases.show', $purchase) }}" class="btn-secondary-sm text-xs">
                                {{ __('messages.purchases.btn_detail') }}
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-12 text-gray-500">
                            {{ __('messages.purchases.empty_data') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100 dark:border-gray-700">
            {{ $purchases->links() }}
        </div>
    </div>
</div>
@endsection
