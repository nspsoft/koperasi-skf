@extends('layouts.app')

@section('title', __('messages.journals_page.title'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('messages.journals_page.title') }}</h1>
        <p class="page-subtitle">{{ __('messages.journals_page.subtitle') }}</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('journals.tutorial') }}" class="btn-secondary">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ __('messages.journals_page.btn_guide') }}
        </a>
        <a href="{{ route('journals.create') }}" class="btn-primary">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            {{ __('messages.journals_page.btn_create') }}
        </a>
    </div>
</div>

<div class="glass-card-solid p-6 mb-6">
    <form action="{{ route('journals.index') }}" method="GET" class="flex flex-wrap gap-4 items-end">
        <div class="w-full md:w-48">
            <label class="form-label">{{ __('messages.journals_page.start_date') }}</label>
            <input type="date" name="start_date" value="{{ $startDate }}" class="form-input">
        </div>
        <div class="w-full md:w-48">
            <label class="form-label">{{ __('messages.journals_page.end_date') }}</label>
            <input type="date" name="end_date" value="{{ $endDate }}" class="form-input">
        </div>
        <div>
            <button type="submit" class="btn-primary py-2.5">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                {{ __('messages.journals_page.filter') }}
            </button>
        </div>
        <div>
            <a href="{{ route('journals.index') }}" class="btn-secondary py-2.5">{{ __('messages.journals_page.reset') }}</a>
        </div>
    </form>
</div>

<div class="glass-card-solid overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-50 dark:bg-gray-800/50">
                <tr>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">{{ __('messages.journals_page.table_date') }}</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">{{ __('messages.journals_page.table_journal_no') }}</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">{{ __('messages.journals_page.table_desc') }}</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-right">{{ __('messages.journals_page.table_debit') }}</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-right">{{ __('messages.journals_page.table_credit') }}</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-center">{{ __('messages.journals_page.table_type') }}</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-center">{{ __('messages.journals_page.table_action') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($journals as $journal)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $journal->transaction_date->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap font-medium text-primary-600 dark:text-primary-400">
                        {{ $journal->journal_number }}
                    </td>
                    <td class="px-6 py-4 text-sm max-w-xs truncate">{{ $journal->description }}</td>
                    <td class="px-6 py-4 text-sm text-right font-medium">Rp {{ number_format($journal->total_debit, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-sm text-right font-medium">Rp {{ number_format($journal->total_credit, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-sm text-center">
                        @if($journal->reference_type === 'Manual')
                            <span class="badge-success">{{ __('messages.journals_page.type_manual') }}</span>
                        @else
                            <span class="badge-primary">{{ __('messages.journals_page.type_auto') }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex justify-center gap-2">
                            <a href="{{ route('journals.show', $journal) }}" 
                               class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="{{ __('messages.journals_page.view_detail') }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </a>
                            @if($journal->reference_type === 'Manual')
                                @can('delete-data')
                                <form action="{{ route('journals.destroy', $journal) }}" method="POST" onsubmit="return confirm('{{ __('messages.journals_page.delete_confirm') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition" title="{{ __('messages.journals_page.delete_tooltip') }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                                @endcan
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-10 text-center text-gray-500">
                        {{ __('messages.journals_page.empty_data') }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($journals->hasPages())
    <div class="p-6 border-t border-gray-100 dark:border-gray-700">
        {{ $journals->links() }}
    </div>
    @endif
</div>
@endsection
