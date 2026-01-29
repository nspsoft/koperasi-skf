@extends('layouts.app')

@section('title', 'Buat Role Baru')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Buat Role Baru</h1>
            <p class="page-subtitle">Tambah role custom dengan permission yang sesuai kebutuhan</p>
        </div>
        <a href="{{ route('roles.index') }}" class="btn-secondary">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali
        </a>
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

    <form action="{{ route('roles.store') }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Role Details --}}
            <div class="glass-card-solid p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Detail Role</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Nama Role (Kode) <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}" 
                               placeholder="contoh: kasir_senior"
                               class="form-input" required pattern="[a-z_]+">
                        <p class="text-xs text-gray-500 mt-1">Hanya huruf kecil dan underscore</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Label (Nama Tampilan) <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="label" value="{{ old('label') }}" 
                               placeholder="contoh: Kasir Senior"
                               class="form-input" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Deskripsi
                        </label>
                        <textarea name="description" rows="3" class="form-input"
                                  placeholder="Deskripsi singkat tentang role ini...">{{ old('description') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Warna Badge <span class="text-red-500">*</span>
                        </label>
                        <div class="flex items-center gap-3">
                            <input type="color" name="color" value="{{ old('color', '#6366f1') }}" 
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
                                       {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
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
                Simpan Role
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
