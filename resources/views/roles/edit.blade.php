@extends('layouts.app')

@section('title', 'Edit Role - ' . $role->label)

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Edit Role: {{ $role->label }}</h1>
            <p class="page-subtitle">Ubah detail dan permission untuk role ini</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('roles.index') }}" class="btn-secondary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
            @if(!$role->is_system)
            <form action="{{ route('roles.destroy', $role) }}" method="POST" class="inline" 
                  onsubmit="return confirm('Yakin ingin menghapus role ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-danger">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Hapus
                </button>
            </form>
            @endif
        </div>
    </div>

    @if($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if($role->is_system)
    <div class="bg-amber-50 border border-amber-200 text-amber-700 px-4 py-3 rounded mb-4 flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
        </svg>
        <span>Ini adalah role sistem. Nama role tidak dapat diubah dan role tidak dapat dihapus.</span>
    </div>
    @endif

    <form action="{{ route('roles.update', $role) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Role Details --}}
            <div class="glass-card-solid p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Detail Role</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Nama Role (Kode) <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name', $role->name) }}" 
                               placeholder="contoh: kasir_senior"
                               class="form-input" 
                               {{ $role->is_system ? 'readonly' : '' }}
                               required pattern="[a-z_]+">
                        <p class="text-xs text-gray-500 mt-1">Hanya huruf kecil dan underscore</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Label (Nama Tampilan) <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="label" value="{{ old('label', $role->label) }}" 
                               placeholder="contoh: Kasir Senior"
                               class="form-input" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Deskripsi
                        </label>
                        <textarea name="description" rows="3" class="form-input"
                                  placeholder="Deskripsi singkat tentang role ini...">{{ old('description', $role->description) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Warna Badge <span class="text-red-500">*</span>
                        </label>
                        <div class="flex items-center gap-3">
                            <input type="color" name="color" value="{{ old('color', $role->color) }}" 
                                   class="w-12 h-10 rounded cursor-pointer">
                            <div class="flex gap-2">
                                <button type="button" onclick="document.querySelector('[name=color]').value='#ef4444'" class="w-6 h-6 rounded-full bg-red-500 hover:ring-2 ring-offset-2"></button>
                                <button type="button" onclick="document.querySelector('[name=color]').value='#f59e0b'" class="w-6 h-6 rounded-full bg-amber-500 hover:ring-2 ring-offset-2"></button>
                                <button type="button" onclick="document.querySelector('[name=color]').value='#10b981'" class="w-6 h-6 rounded-full bg-emerald-500 hover:ring-2 ring-offset-2"></button>
                                <button type="button" onclick="document.querySelector('[name=color]').value='#3b82f6'" class="w-6 h-6 rounded-full bg-blue-500 hover:ring-2 ring-offset-2"></button>
                                <button type="button" onclick="document.querySelector('[name=color]').value='#8b5cf6'" class="w-6 h-6 rounded-full bg-violet-500 hover:ring-2 ring-offset-2"></button>
                                <button type="button" onclick="document.querySelector('[name=color]').value='#ec4899'" class="w-6 h-6 rounded-full bg-pink-500 hover:ring-2 ring-offset-2"></button>
                            </div>
                        </div>
                    </div>

                    {{-- User count --}}
                    <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            <span class="font-semibold">{{ $role->users()->count() }}</span> user menggunakan role ini
                        </p>
                    </div>
                </div>
            </div>

            {{-- Permissions --}}
            <div class="lg:col-span-2 glass-card-solid p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                    Pilih Permissions <span class="text-red-500">*</span>
                </h3>
                <p class="text-sm text-gray-500 mb-4">Centang permission yang ingin diberikan untuk role ini</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($permissions as $group => $items)
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="font-semibold text-gray-800 dark:text-white">{{ $group }}</h4>
                            <button type="button" onclick="toggleGroup('{{ Str::slug($group) }}')" 
                                    class="text-xs text-primary-600 hover:underline">Pilih Semua</button>
                        </div>
                        <div class="space-y-2">
                            @foreach($items as $permission)
                            <label class="flex items-start gap-2 cursor-pointer group">
                                <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                       class="mt-0.5 rounded text-primary-600 group-{{ Str::slug($group) }}"
                                       {{ in_array($permission->id, old('permissions', $rolePermissions)) ? 'checked' : '' }}>
                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ $permission->label }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('roles.index') }}" class="btn-secondary">Batal</a>
            <button type="submit" class="btn-primary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Simpan Perubahan
            </button>
        </div>
    </form>

    <script>
        function toggleGroup(group) {
            const checkboxes = document.querySelectorAll('.group-' + group);
            const allChecked = [...checkboxes].every(cb => cb.checked);
            checkboxes.forEach(cb => cb.checked = !allChecked);
        }
    </script>
@endsection
