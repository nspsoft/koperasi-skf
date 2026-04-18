@extends('layouts.app')

@section('title', 'Tambah User Sistem')

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="flex items-center gap-4">
            <a href="{{ route('roles.index') }}" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                <svg class="w-6 h-6 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h1 class="page-title">Tambah User Sistem</h1>
                <p class="page-subtitle">Daftarkan user pengelola (Non-Anggota)</p>
            </div>
        </div>
    </div>

    <form action="{{ route('roles.user.store') }}" method="POST" class="max-w-4xl">
        @csrf

        <div class="glass-card-solid p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 pb-3 border-b border-gray-200 dark:border-gray-700">Informasi Login</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6">
                <!-- Nama -->
                <div class="form-group">
                    <label for="name" class="form-label">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                           class="form-input @error('name') !border-red-500 @enderror"
                           placeholder="Masukkan nama lengkap">
                    @error('name')
                    <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="email" class="form-label">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                           class="form-input @error('email') !border-red-500 @enderror"
                           placeholder="nama@email.com">
                    @error('email')
                    <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Role -->
                <div class="form-group">
                    <label for="role_id" class="form-label">
                        Role / Hak Akses <span class="text-red-500">*</span>
                    </label>
                    <select id="role_id" name="role_id" required class="form-input @error('role_id') !border-red-500 @enderror">
                        <option value="">Pilih Role</option>
                        @foreach($roles as $role)
                            @if($role->name !== 'member')
                                <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                    {{ $role->label }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                    @error('role_id')
                    <p class="form-error">{{ $message }}</p>
                    @else
                    <p class="form-hint">Pilih role pengelola (selain Anggota)</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div class="form-group">
                    <label for="phone" class="form-label">Nomor Telepon</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
                           placeholder="08xxxxxxxxxx"
                           class="form-input @error('phone') !border-red-500 @enderror">
                    @error('phone')
                    <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password" class="form-label">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <input type="password" id="password" name="password" required autocomplete="new-password"
                           class="form-input @error('password') !border-red-500 @enderror"
                           placeholder="Minimal 8 karakter">
                    @error('password')
                    <p class="form-error">{{ $message }}</p>
                    @else
                    <p class="form-hint">Minimal 8 karakter</p>
                    @enderror
                </div>

                <!-- Password Confirmation -->
                <div class="form-group">
                    <label for="password_confirmation" class="form-label">
                        Konfirmasi Password <span class="text-red-500">*</span>
                    </label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                           class="form-input"
                           placeholder="Ulangi password">
                </div>
            </div>
            
            <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                <div class="flex">
                    <svg class="h-5 w-5 text-blue-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-sm text-blue-700 dark:text-blue-300">
                        <strong>Info:</strong> Menambahkan user dari sini <strong>tidak akan</strong> membuat profil di data Anggota. Gunakan ini untuk mendaftarkan staf, pengurus, atau admin tambahan yang bukan merupakan anggota koperasi.
                    </p>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('roles.index') }}" class="btn-secondary">
                Batal
            </a>
            <button type="submit" class="btn-primary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                </svg>
                Daftarkan User
            </button>
        </div>
    </form>
@endsection
