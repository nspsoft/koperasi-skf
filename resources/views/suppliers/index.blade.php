@extends('layouts.app')

@section('title', __('messages.titles.suppliers'))

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('messages.suppliers_page.title') }}</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">{{ __('messages.suppliers_page.subtitle') }}</p>
        </div>
        <a href="{{ route('suppliers.create') }}" class="btn-primary">
            {{ __('messages.suppliers_page.add_supplier') }}
        </a>
    </div>

    <!-- Search -->
    <div class="glass-card-solid p-4">
        <form method="GET" class="flex gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('messages.suppliers_page.search_placeholder') }}" class="form-input">
            </div>
            <button type="submit" class="btn-secondary">{{ __('messages.search') }}</button>
        </form>
    </div>

    <!-- Table -->
    <div class="glass-card-solid overflow-hidden">
        <div class="table-scroll-container">
            <table class="table-modern">
                <thead class="sticky top-0 z-10">
                    <tr>
                        <th>#</th>
                        <th>{{ __('messages.suppliers_page.name') }}</th>
                        <th>{{ __('messages.suppliers_page.contact_person') }}</th>
                        <th>{{ __('messages.suppliers_page.phone_email') }}</th>
                        <th>{{ __('messages.suppliers_page.address') }}</th>
                        <th>{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $index => $supplier)
                    <tr>
                        <td>{{ $suppliers->firstItem() + $index }}</td>
                        <td class="font-medium text-gray-900 dark:text-white">{{ $supplier->name }}</td>
                        <td class="text-gray-600 dark:text-gray-400">{{ $supplier->contact_person ?? '-' }}</td>
                        <td>
                            <div class="text-sm">
                                @if($supplier->phone)
                                <div class="flex items-center gap-1 text-gray-600 dark:text-gray-400">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                    {{ $supplier->phone }}
                                </div>
                                @endif
                                @if($supplier->email)
                                <div class="flex items-center gap-1 text-gray-500 text-xs mt-0.5">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                    {{ $supplier->email }}
                                </div>
                                @endif
                            </div>
                        </td>
                        <td class="text-sm text-gray-500 max-w-xs truncate">{{ $supplier->address ?? '-' }}</td>
                        <td>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('suppliers.edit', $supplier) }}" class="btn-icon text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </a>
                                @can('delete-data')
                                <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" onsubmit="return confirm('{{ __('messages.suppliers_page.delete_confirm') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-12 text-gray-500">
                            {{ __('messages.suppliers_page.no_data') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100 dark:border-gray-700">
            {{ $suppliers->links() }}
        </div>
    </div>
</div>
@endsection
