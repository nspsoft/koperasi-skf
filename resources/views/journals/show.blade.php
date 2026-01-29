@extends('layouts.app')

@section('title', __('messages.journals_page.view_detail'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('messages.journals_page.view_detail') }}: {{ $journal->journal_number }}</h1>
        <p class="page-subtitle">{{ __('messages.journals_page.subtitle') }}</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('journals.index') }}" class="btn-secondary">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            {{ __('messages.back') }}
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Journal Info --}}
    <div class="lg:col-span-1">
        <div class="glass-card-solid p-6 h-full">
            <h3 class="text-lg font-bold mb-4 border-b border-gray-100 dark:border-gray-700 pb-2">{{ __('messages.journals_page.info_title') }}</h3>
            <div class="space-y-4">
                <div>
                    <label class="text-xs text-gray-500 uppercase font-bold">{{ __('messages.journals_page.table_journal_no') }}</label>
                    <p class="font-medium text-primary-600">{{ $journal->journal_number }}</p>
                </div>
                <div>
                    <label class="text-xs text-gray-500 uppercase font-bold">{{ __('messages.journals_page.transaction_date') }}</label>
                    <p class="font-medium">{{ $journal->transaction_date->format('d F Y') }}</p>
                </div>
                <div>
                    <label class="text-xs text-gray-500 uppercase font-bold">{{ __('messages.journals_page.table_desc') }}</label>
                    <p class="font-medium">{{ $journal->description }}</p>
                </div>
                <div>
                    <label class="text-xs text-gray-500 uppercase font-bold">{{ __('messages.journals_page.source_type') }}</label>
                    <p class="font-medium">
                        @if($journal->reference_type === 'Manual')
                            <span class="badge-success">{{ __('messages.journals_page.type_manual') }}</span>
                        @else
                            <span class="badge-primary">{{ class_basename($journal->reference_type) }}</span>
                        @endif
                    </p>
                </div>
                <div>
                    <label class="text-xs text-gray-500 uppercase font-bold">{{ __('messages.journals_page.created_by') }}</label>
                    <p class="font-medium text-sm">{{ $journal->creator->name ?? 'System' }}</p>
                    <p class="text-xs text-gray-400">{{ $journal->created_at->format('d/m/Y H:i') }}</p>
                </div>
                @if($journal->reference_type === 'Manual')
                <div class="pt-4 mt-4 border-t border-gray-100 dark:border-gray-700">
                    <form action="{{ route('journals.destroy', $journal) }}" method="POST" onsubmit="return confirm('{{ __('messages.journals_page.delete_confirm') }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full btn-danger py-2 text-sm justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            {{ __('messages.delete') }}
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Journal Lines --}}
    <div class="lg:col-span-2">
        <div class="glass-card-solid overflow-hidden h-full">
            <div class="bg-gray-50 dark:bg-gray-800/50 px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                <h3 class="font-bold text-lg">{{ __('messages.journals_page.entries_detail') }}</h3>
                <span class="text-sm font-medium {{ abs($journal->total_debit - $journal->total_credit) < 0.01 ? 'text-green-600' : 'text-red-600' }}">
                    {{ abs($journal->total_debit - $journal->total_credit) < 0.01 ? 'Balanced ✅' : 'Unbalanced ⚠️' }}
                </span>
            </div>
            <div class="p-0">
                <table class="w-full text-left">
                    <thead class="bg-gray-50/50 dark:bg-gray-700/30">
                        <tr>
                            <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">{{ __('messages.journals_page.account') }}</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">{{ __('messages.journals_page.table_desc') }}</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase text-right">{{ __('messages.journals_page.debit') }}</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase text-right">{{ __('messages.journals_page.credit') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($journal->lines as $line)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/20">
                            <td class="px-6 py-4">
                                <div class="font-medium text-sm text-gray-900 dark:text-white">{{ $line->account->code }}</div>
                                <div class="text-xs text-gray-500">{{ $line->account->name }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                {{ $line->description }}
                            </td>
                            <td class="px-6 py-4 text-sm text-right font-medium">
                                {{ $line->debit > 0 ? 'Rp ' . number_format($line->debit, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-right font-medium text-primary-600">
                                {{ $line->credit > 0 ? 'Rp ' . number_format($line->credit, 0, ',', '.') : '-' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50/50 dark:bg-gray-800/50 font-bold border-t border-gray-200 dark:border-gray-600">
                        <tr>
                            <td colspan="2" class="px-6 py-4 text-right text-sm">TOTAL</td>
                            <td class="px-6 py-4 text-right text-sm">Rp {{ number_format($journal->total_debit, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right text-sm">Rp {{ number_format($journal->total_credit, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
