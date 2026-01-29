@extends('layouts.app')

@section('title', __('messages.titles.expenses'))

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('messages.expenses.title') }}</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">{{ __('messages.expenses.subtitle') }}</p>
        </div>
        <a href="{{ route('expenses.create') }}" class="btn-primary">
            {{ __('messages.expenses.btn_create') }}
        </a>
    </div>

    <!-- Filters -->
    <div class="glass-card-solid p-4">
        <form method="GET" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('messages.expenses.filter_search') }}" class="form-input">
            </div>
            <div class="w-full sm:w-48">
                <select name="category_id" class="form-input" onchange="this.form.submit()">
                    <option value="">{{ __('messages.expenses.filter_all_categories') }}</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="w-full sm:w-40">
                <input type="date" name="date_start" value="{{ request('date_start', date('Y-m-01')) }}" class="form-input" placeholder="{{ __('messages.expenses.filter_date_start') }}">
            </div>
            <div class="w-full sm:w-40">
                <input type="date" name="date_end" value="{{ request('date_end', date('Y-m-t')) }}" class="form-input" placeholder="{{ __('messages.expenses.filter_date_end') }}">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn-secondary">{{ __('messages.filter') ?? 'Filter' }}</button>
                <a href="{{ route('expenses.export', request()->query()) }}" class="btn-secondary flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Excel
                </a>
            </div>
        </form>
    </div>

    <!-- Stats Summary -->
    @if($totalExpense > 0)
    <div class="glass-card p-4 bg-gradient-to-r from-red-500/10 to-pink-500/10 border border-red-200 dark:border-red-800">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('messages.expenses.stats_total') }}</p>
                <p class="text-2xl font-bold text-red-600 dark:text-red-400 mt-1">Rp {{ number_format($totalExpense, 0, ',', '.') }}</p>
            </div>
            @if(request()->anyFilled(['search', 'category_id', 'date_start', 'date_end']))
            <a href="{{ route('expenses.index') }}" class="text-red-500 hover:text-red-700 text-sm flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                {{ __('messages.expenses.btn_reset') }}
            </a>
            @endif
        </div>
    </div>
    @endif

    <!-- Table -->
    <div class="glass-card-solid overflow-hidden">
        <div class="table-scroll-container">
            <table class="table-modern">
                <thead class="sticky top-0 z-10">
                    <tr>
                        <th>{{ __('messages.expenses.table_date') }}</th>
                        <th>{{ __('messages.expenses.table_category') }}</th>
                        <th>{{ __('messages.expenses.table_description') }}</th>
                        <th class="text-right">{{ __('messages.expenses.table_amount') }}</th>
                        <th>{{ __('messages.expenses.table_proof') }}</th>
                        <th>{{ __('messages.expenses.table_creator') }}</th>
                        <th>{{ __('messages.expenses.table_action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $expense)
                    <tr>
                        <td class="text-gray-600 dark:text-gray-400 font-mono text-sm">
                            {{ $expense->expense_date->format('d/m/Y') }}
                        </td>
                        <td>
                            <span class="badge badge-gray">{{ $expense->category->name }}</span>
                        </td>
                        <td class="font-medium text-gray-900 dark:text-white max-w-xs truncate" title="{{ $expense->description }}">
                            {{ $expense->description }}
                        </td>
                        <td class="text-right font-bold text-red-600 dark:text-red-400">
                            {{ number_format($expense->amount, 0, ',', '.') }}
                        </td>
                        <td class="text-center">
                            @if($expense->proof)
                            <a href="{{ \Storage::url($expense->proof) }}" target="_blank" class="text-primary-600 hover:text-primary-700 text-xs underline">
                                {{ __('messages.expenses.view_proof') }}
                            </a>
                            @else
                            <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>
                        <td class="text-xs text-gray-500">
                            {{ $expense->creator->name ?? 'System' }}
                        </td>
                        <td>
                            @can('delete-data')
                            <form action="{{ route('expenses.destroy', $expense) }}" method="POST" onsubmit="return confirm('{{ __('messages.expenses.delete_confirm') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 p-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-12 text-gray-500">
                            {{ __('messages.expenses.empty_data') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100 dark:border-gray-700">
            {{ $expenses->links() }}
        </div>
    </div>
</div>
@endsection
