@extends('layouts.app')

@section('title', __('messages.stock_opname.index_title'))

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('messages.stock_opname.index_title') }}</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">{{ __('messages.stock_opname.index_subtitle') }}</p>
        </div>
        <a href="{{ route('stock-opname.create') }}" class="btn-primary inline-flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            {{ __('messages.stock_opname.btn_create') }}
        </a>
    </div>

    <!-- Filters -->
    <div class="glass-card-solid p-4">
        <form method="GET" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('messages.stock_opname.filter_placeholder') }}" class="form-input">
            </div>
            <div class="w-full sm:w-48">
                <select name="status" class="form-input" onchange="this.form.submit()">
                    <option value="">{{ __('messages.stock_opname.all_status') }}</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>{{ __('messages.stock_opname.status_draft') }}</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>{{ __('messages.stock_opname.status_completed') }}</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>{{ __('messages.stock_opname.status_cancelled') }}</option>
                </select>
            </div>
            <button type="submit" class="btn-secondary">{{ __('messages.loans_page.filter') ?? 'Filter' }}</button>
        </form>
    </div>

    <!-- Table -->
    <div class="glass-card-solid overflow-hidden">
        <div class="table-scroll-container">
            <table class="table-modern w-full">
                <thead class="sticky top-0 z-10">
                    <tr>
                        <th>{{ __('messages.stock_opname.table_no') }}</th>
                        <th>{{ __('messages.stock_opname.table_date') }}</th>
                        <th>{{ __('messages.stock_opname.table_items') }}</th>
                        <th>{{ __('messages.stock_opname.table_diff') }}</th>
                        <th>{{ __('messages.stock_opname.table_status') }}</th>
                        <th>{{ __('messages.stock_opname.table_creator') }}</th>
                        <th>{{ __('messages.stock_opname.table_action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($opnames as $opname)
                    <tr>
                        <td class="font-mono font-bold text-primary-600">{{ $opname->opname_number }}</td>
                        <td class="text-gray-600 dark:text-gray-400">{{ $opname->opname_date->format('d/m/Y') }}</td>
                        <td>
                            <span class="badge badge-info">{{ __('messages.stock_opname.badge_items', ['count' => $opname->total_items]) }}</span>
                        </td>
                        <td>
                            @if($opname->items_with_difference > 0)
                                <span class="badge badge-warning">{{ __('messages.stock_opname.badge_diff', ['count' => $opname->items_with_difference]) }}</span>
                            @else
                                <span class="badge badge-success">{{ __('messages.stock_opname.badge_match') }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-{{ $opname->status_color }}">{{ $opname->status_label }}</span>
                        </td>
                        <td class="text-gray-600 dark:text-gray-400 text-sm">{{ $opname->creator->name ?? '-' }}</td>
                        <td>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('stock-opname.show', $opname) }}" class="btn-icon text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30" title="Detail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </a>
                                @if($opname->status !== 'completed')
                                <form action="{{ route('stock-opname.destroy', $opname) }}" method="POST" onsubmit="return confirm('{{ __('messages.stock_opname.confirm_delete') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-12 text-gray-500">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                            <p class="font-medium">{{ __('messages.stock_opname.empty_data') }}</p>
                            <a href="{{ route('stock-opname.create') }}" class="btn-primary mt-4">{{ __('messages.stock_opname.btn_create_first') }}</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($opnames->hasPages())
        <div class="p-4 border-t border-gray-100 dark:border-gray-700">
            {{ $opnames->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
