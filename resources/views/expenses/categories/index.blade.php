@extends('layouts.app')

@section('title', __('messages.expense_categories.title'))

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('messages.expense_categories.title') }}</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">{{ __('messages.expense_categories.subtitle') }}</p>
        </div>
        <button onclick="document.getElementById('createModal').showModal()" class="btn-primary">
            {{ __('messages.expense_categories.add_category') }}
        </button>
    </div>

    <!-- Table -->
    <div class="glass-card-solid overflow-hidden">
        <div class="table-scroll-container">
            <table class="table-modern">
                <thead class="sticky top-0 z-10">
                    <tr>
                        <th>{{ __('messages.expense_categories.name') }}</th>
                        <th>{{ __('messages.expense_categories.description') }}</th>
                        <th>{{ __('messages.expense_categories.trx_count') }}</th>
                        <th>{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                    <tr>
                        <td class="font-medium text-gray-900 dark:text-white">{{ $category->name }}</td>
                        <td class="text-gray-600 dark:text-gray-400">{{ $category->description ?? '-' }}</td>
                        <td>
                            <span class="badge badge-gray">{{ $category->expenses_count }} {{ __('messages.expense_categories.trx_unit') }}</span>
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <button onclick="openEditModal('{{ $category->id }}', '{{ $category->name }}', '{{ $category->description }}')" class="btn-icon text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </button>
                                <form action="{{ route('expenses.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('{{ __('messages.expense_categories.delete_confirm') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-12 text-gray-500">{{ __('messages.expense_categories.no_data') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Modal -->
<dialog id="createModal" class="modal">
    <div class="modal-box glass-card p-6 max-w-lg w-full">
        <h3 class="font-bold text-lg mb-4 text-gray-900 dark:text-white">{{ __('messages.expense_categories.new_category') }}</h3>
        <form action="{{ route('expenses.categories.store') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="form-label">{{ __('messages.expense_categories.name') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="name" class="form-input" required placeholder="{{ __('messages.expense_categories.placeholder_name') }}">
                </div>
                <div>
                    <label class="form-label">{{ __('messages.expense_categories.description') }}</label>
                    <textarea name="description" rows="3" class="form-input" placeholder="{{ __('messages.expense_categories.placeholder_desc') }}"></textarea>
                </div>
            </div>
            <div class="modal-action flex justify-end gap-2 mt-6">
                <button type="button" class="btn-secondary" onclick="document.getElementById('createModal').close()">{{ __('messages.cancel') }}</button>
                <button type="submit" class="btn-primary">{{ __('messages.save') }}</button>
            </div>
        </form>
    </div>
</dialog>

<!-- Edit Modal -->
<dialog id="editModal" class="modal">
    <div class="modal-box glass-card p-6 max-w-lg w-full">
        <h3 class="font-bold text-lg mb-4 text-gray-900 dark:text-white">{{ __('messages.expense_categories.edit_category') }}</h3>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="form-label">{{ __('messages.expense_categories.name') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="editName" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">{{ __('messages.expense_categories.description') }}</label>
                    <textarea name="description" id="editDescription" rows="3" class="form-input"></textarea>
                </div>
            </div>
            <div class="modal-action flex justify-end gap-2 mt-6">
                <button type="button" class="btn-secondary" onclick="document.getElementById('editModal').close()">{{ __('messages.cancel') }}</button>
                <button type="submit" class="btn-primary">{{ __('messages.update') }}</button>
            </div>
        </form>
    </div>
</dialog>

<script>
    function openEditModal(id, name, description) {
        document.getElementById('editName').value = name;
        document.getElementById('editDescription').value = description;
        document.getElementById('editForm').action = `/expenses/categories/${id}`;
        document.getElementById('editModal').showModal();
    }
</script>
@endsection
