@extends('layouts.app')

@section('title', __('messages.expenses.create_title'))

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('expenses.index') }}" class="btn-secondary-sm">
            {{ __('messages.back') ?? __('messages.savings_page.back') ?? '← Kembali' }}
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('messages.expenses.create_subtitle') }}</h1>
    </div>

    <form action="{{ route('expenses.store') }}" method="POST" enctype="multipart/form-data" class="glass-card p-6 space-y-6">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="form-label">{{ __('messages.expenses.label_date') }} <span class="text-red-500">*</span></label>
                <input type="date" name="expense_date" class="form-input" value="{{ old('expense_date', date('Y-m-d')) }}" required>
            </div>
            <div>
                <label class="form-label">{{ __('messages.expenses.label_category') }} <span class="text-red-500">*</span></label>
                <select name="expense_category_id" class="form-input" required>
                    <option value="">{{ __('messages.expense_categories.select_category') }}</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('expense_category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                    @endforeach
                </select>
                <div class="mt-1 text-xs text-right">
                    <a href="{{ route('expenses.categories.index') }}" class="text-primary-600 hover:underline">{{ __('messages.expense_categories.manage_categories') }}</a>
                </div>
            </div>
        </div>

        <div>
            <label class="form-label">{{ __('messages.expenses.label_amount') }} <span class="text-red-500">*</span></label>
            <div class="relative">
                <span class="absolute left-3 top-2 text-gray-500">Rp</span>
                <input type="number" name="amount" class="form-input pl-10 text-lg font-bold" placeholder="{{ __('messages.expenses.placeholder_amount') }}" value="{{ old('amount') }}" required min="0">
            </div>
        </div>

        <div>
            <label class="form-label">{{ __('messages.expenses.label_description') }} <span class="text-red-500">*</span></label>
            <textarea name="description" rows="3" class="form-input" placeholder="{{ __('messages.expenses.placeholder_description') }}" required>{{ old('description') }}</textarea>
        </div>

        <div>
            <label class="form-label">{{ __('messages.expenses.label_proof') }}</label>
            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                <div class="space-y-1 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <div class="flex text-sm text-gray-600 dark:text-gray-400">
                        <label for="file-upload" class="relative cursor-pointer bg-white dark:bg-gray-800 rounded-md font-medium text-primary-600 hover:text-primary-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-primary-500">
                            <span>{{ __('messages.expenses.upload_help') }}</span>
                            <input id="file-upload" name="proof" type="file" class="sr-only" accept="image/*">
                        </label>
                        <p class="pl-1">{{ __('messages.expenses.upload_or_drag') }}</p>
                    </div>
                    <p class="text-xs text-gray-500">{{ __('messages.expenses.upload_size') }}</p>
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-4">
            <button type="submit" class="btn-primary w-full md:w-auto">{{ __('messages.expenses.btn_save') }}</button>
        </div>
    </form>
</div>
@endsection
