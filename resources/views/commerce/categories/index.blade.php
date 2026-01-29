@extends('layouts.app')

@section('title', __('messages.categories_page.title'))

@section('content')
    <div class="page-header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="page-title">{{ __('messages.categories_page.title') }}</h1>
                <p class="page-subtitle">{{ __('messages.categories_page.subtitle') }}</p>
            </div>
            <a href="{{ route('categories.create') }}" class="btn-primary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                {{ __('messages.categories_page.add_category') }}
            </a>
        </div>
    </div>

    <div class="glass-card-solid overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-modern w-full">
                <thead>
                    <tr>
                        <th class="w-16">No</th>
                        <th>{{ __('messages.categories_page.name') }}</th>
                        <th>{{ __('messages.categories_page.slug') }}</th>
                        <th>{{ __('messages.categories_page.products_count') }}</th>
                        <th class="text-right">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="font-medium text-gray-900 dark:text-white">
                            <div class="flex items-center gap-2">
                                @if($category->icon) <span>{{ $category->icon }}</span> @endif
                                {{ $category->name }}
                            </div>
                        </td>
                        <td class="text-gray-500">{{ $category->slug }}</td>
                        <td>
                            <span class="badge badge-info">{{ $category->products_count }} {{ __('messages.categories_page.products_unit') }}</span>
                        </td>
                        <td>
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('categories.edit', $category) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>
                                @can('delete-data')
                                <form action="{{ route('categories.destroy', $category) }}" method="POST" onsubmit="return confirm('{{ __('messages.categories_page.delete_confirm') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-8 text-gray-500">{{ __('messages.categories_page.no_categories') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
