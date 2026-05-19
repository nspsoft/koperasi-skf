@extends('layouts.app')

@section('title', __('messages.titles.roles'))

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Kelola Role & Hak Akses</h1>
            <p class="page-subtitle">Atur role dan permission untuk setiap user</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('roles.user.create') }}" class="btn-secondary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                </svg>
                Tambah User Sistem
            </a>
            <a href="{{ route('roles.create') }}" class="btn-primary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Buat Role Baru
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    {{-- Role Information Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        @foreach($roles as $roleKey => $role)
        @php
            $roleLabel = data_get($role, 'label');
            $roleName = is_object($role) ? $role->name : $roleKey;
            $roleColor = data_get($role, 'color', '#6366f1');
            $roleDesc = data_get($role, 'description', 'Tidak ada deskripsi');
            $usersCount = is_object($role) ? ($role->users_count ?? 0) : 0;
            $permissions = is_object($role) ? $role->permissions : collect(data_get($role, 'permissions', []));
            $isSystem = is_object($role) ? $role->is_system : true; // Old array key roles are system roles
            
            // Generate link
            $editLink = is_object($role) ? route('roles.edit', $role) : '#';
            $cardStyle = is_object($role) 
                ? 'border-left-color: ' . $roleColor 
                : 'border-l-4 ' . match($roleKey) {
                    'admin' => 'border-red-500',
                    'pengurus' => 'border-blue-500',
                    'manager_toko' => 'border-purple-500',
                    default => 'border-green-500'
                };
        @endphp
        <div class="glass-card-solid p-6 border-l-4 hover:shadow-lg transition-shadow cursor-pointer" 
             style="border-left-color: {{ $roleColor }}"
             onclick="window.location='{{ $editLink }}'">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center" style="background-color: {{ $roleColor }}20; color: {{ $roleColor }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            @if($roleName === 'admin')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            @elseif($roleName === 'pengurus')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                            @elseif(in_array($roleName, ['manager_toko', 'kasir']))
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            @else
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            @endif
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="font-bold text-lg text-gray-900 dark:text-white">{{ $roleLabel }}</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $roleName }}</p>
                    </div>
                </div>
                @if($isSystem)
                <span class="badge badge-secondary text-xs">Sistem</span>
                @endif
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-300 mb-3">{{ $roleDesc }}</p>
            <div class="flex items-center justify-between text-xs text-gray-500">
                <span>{{ $usersCount }} user</span>
                <span>{{ count($permissions) }} permissions</span>
            </div>
            <div class="border-t border-gray-200 dark:border-gray-700 pt-3 mt-3">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-2">Permissions:</p>
                <ul class="space-y-1">
                    @foreach($permissions->take(4) as $permission)
                    <li class="text-xs text-gray-600 dark:text-gray-400 flex items-center">
                        <svg class="w-3 h-3 mr-1 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                        {{ is_string($permission) ? $permission : $permission->label }}
                    </li>
                    @endforeach
                    @if(count($permissions) > 4)
                    <li class="pt-1">
                        <span class="text-xs text-primary-600 font-medium">+{{ count($permissions) - 4 }} lainnya</span>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
        @endforeach
    </div>

    {{-- User List with Role Management --}}
    <div class="glass-card-solid overflow-hidden">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Daftar User & Role</h3>
                <form method="GET" action="{{ route('roles.index') }}" class="flex flex-col md:flex-row items-stretch md:items-center gap-3">
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Cari nama, email..." 
                               class="form-input pl-10 w-full md:w-64">
                        <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <select name="role" class="form-input w-full md:w-40">
                        <option value="">Semua Role</option>
                        @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>{{ $role->label }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn-primary">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Filter
                    </button>
                    @if(request()->hasAny(['search', 'role']))
                    <a href="{{ route('roles.index') }}" class="btn-secondary">Reset</a>
                    @endif
                </form>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>ID Anggota</th>
                        <th>Role Saat Ini</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td class="font-medium">{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if($user->member)
                                <span class="font-mono text-xs">{{ $user->member->member_id }}</span>
                            @else
                                <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge" style="background-color: {{ $user->role_color }}20; color: {{ $user->role_color }}">
                                {{ $user->role_label }}
                            </span>
                        </td>
                        <td>
                            <div class="flex items-center justify-center gap-2" x-data="{}">
                                <form action="{{ route('roles.update-user', $user) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <select name="role_id" onchange="this.form.submit()" class="form-input text-xs py-1">
                                        @foreach($roles as $role)
                                        <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>{{ $role->label }}</option>
                                        @endforeach
                                    </select>
                                </form>
                                <button @click="$dispatch('open-reset-password', { id: {{ $user->id }}, name: '{{ addslashes($user->name) }}' })"
                                        type="button"
                                        class="btn-secondary py-1 px-2.5 text-xs flex items-center gap-1 hover:bg-amber-50 hover:text-amber-600 hover:border-amber-200"
                                        title="Reset Password">
                                    <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m-5 4a5 5 0 01-5-5 5 5 0 015-5 5 5 0 015 5 5 5 0 01-5 5zm0 0v1a2 2 0 01-2 2H9a2 2 0 00-2 2v3m2-3h.01"></path>
                                    </svg>
                                    <span>Reset</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if($users->hasPages())
        <div class="p-4 border-t border-gray-200 dark:border-gray-700">
            {{ $users->links() }}
        </div>
        @endif
    </div>

    {{-- Reset Password Modal --}}
    <div x-data="{ 
            showModal: false, 
            userId: null, 
            userName: '', 
            password: '', 
            password_confirmation: '',
            errors: []
         }"
         @open-reset-password.window="
            showModal = true;
            userId = $event.detail.id;
            userName = $event.detail.name;
            password = '';
            password_confirmation = '';
            errors = [];
         "
         x-show="showModal"
         class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4"
         style="display: none;">
        
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="showModal = false"></div>
        
        {{-- Modal Content --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 max-w-md w-full overflow-hidden transform transition-all relative z-10">
            <div class="p-6">
                {{-- Header --}}
                <div class="flex items-center justify-between mb-4 border-b border-gray-100 dark:border-gray-700 pb-3">
                    <div class="flex items-center gap-2 text-amber-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m-5 4a5 5 0 01-5-5 5 5 0 015-5 5 5 0 015 5 5 5 0 01-5 5zm0 0v1a2 2 0 01-2 2H9a2 2 0 00-2 2v3m2-3h.01"></path>
                        </svg>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Reset Password</h3>
                    </div>
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    Mereset password untuk user: <strong class="text-gray-900 dark:text-white" x-text="userName"></strong>
                </p>
                
                {{-- Form --}}
                <form :action="'/roles/user/' + userId + '/reset-password'" method="POST" @submit="
                    errors = [];
                    if (password.length < 8) {
                        errors.push('Password harus minimal 8 karakter!');
                    }
                    if (password !== password_confirmation) {
                        errors.push('Konfirmasi password tidak cocok!');
                    }
                    if (errors.length > 0) {
                        $event.preventDefault();
                    }
                ">
                    @csrf
                    @method('PUT')
                    
                    {{-- Local validation error --}}
                    <template x-if="errors.length > 0">
                        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-2.5 rounded-xl text-xs space-y-1 mb-4">
                            <template x-for="err in errors">
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span x-text="err"></span>
                                </div>
                            </template>
                        </div>
                    </template>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="form-label font-semibold text-xs mb-1 block">Password Baru</label>
                            <input type="password" name="password" x-model="password" required minlength="8"
                                   placeholder="Minimal 8 karakter"
                                   class="form-input w-full">
                        </div>
                        <div>
                            <label class="form-label font-semibold text-xs mb-1 block">Konfirmasi Password Baru</label>
                            <input type="password" name="password_confirmation" x-model="password_confirmation" required
                                   placeholder="Ulangi password baru"
                                   class="form-input w-full">
                        </div>
                    </div>
                    
                    <div class="flex justify-end gap-3 mt-6 border-t border-gray-100 dark:border-gray-700 pt-4">
                        <button type="button" @click="showModal = false" class="btn-secondary">
                            Batal
                        </button>
                        <button type="submit" class="btn-primary bg-amber-500 hover:bg-amber-600 border-amber-500 hover:border-amber-600 text-white">
                            Simpan Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
