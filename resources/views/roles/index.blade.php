@extends('layouts.app')

@section('title', __('messages.titles.roles'))

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Kelola Role & Hak Akses</h1>
            <p class="page-subtitle">Atur role dan permission untuk setiap user</p>
        </div>
        <a href="{{ route('roles.create') }}" class="btn-primary">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Buat Role Baru
        </a>
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
        @foreach($roles as $role)
        <div class="glass-card-solid p-6 border-l-4 hover:shadow-lg transition-shadow cursor-pointer" 
             style="border-left-color: {{ $role->color }}"
             onclick="window.location='{{ route('roles.edit', $role) }}'">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center" style="background-color: {{ $role->color }}20; color: {{ $role->color }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="font-bold text-lg text-gray-900 dark:text-white">{{ $role->label }}</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $role->name }}</p>
                    </div>
                </div>
                @if($role->is_system)
                <span class="badge badge-secondary text-xs">Sistem</span>
                @endif
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-300 mb-3">{{ $role->description ?? 'Tidak ada deskripsi' }}</p>
            <div class="flex items-center justify-between text-xs text-gray-500">
                <span>{{ $role->users_count ?? 0 }} user</span>
                <span>{{ $role->permissions->count() }} permissions</span>
            </div>
            <div class="border-t border-gray-200 dark:border-gray-700 pt-3 mt-3">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-2">Permissions:</p>
                <ul class="space-y-1" x-data="{ expanded: false }">
                    @foreach($role->permissions->take(4) as $permission)
                    <li class="text-xs text-gray-600 dark:text-gray-400 flex items-center">
                        <svg class="w-3 h-3 mr-1 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                        {{ $permission->label }}
                    </li>
                    @endforeach
                    @if($role->permissions->count() > 4)
                    <li class="pt-1">
                        <span class="text-xs text-primary-600 font-medium">+{{ $role->permissions->count() - 4 }} lainnya</span>
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
                            <div class="flex items-center justify-center gap-2">
                                <form action="{{ route('roles.update-user', $user) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <select name="role_id" onchange="this.form.submit()" class="form-input text-xs py-1">
                                        @foreach($roles as $role)
                                        <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>{{ $role->label }}</option>
                                        @endforeach
                                    </select>
                                </form>
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
@endsection
