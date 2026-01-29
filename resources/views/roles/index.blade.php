@extends('layouts.app')

@section('title', __('messages.titles.roles'))

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Kelola Role & Hak Akses</h1>
            <p class="page-subtitle">Atur role dan permission untuk setiap user</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    {{-- Role Information Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        @foreach($roles as $roleKey => $roleData)
        <div class="glass-card-solid p-6 {{ $roleKey === 'admin' ? 'border-l-4 border-red-500' : ($roleKey === 'pengurus' ? 'border-l-4 border-blue-500' : ($roleKey === 'manager_toko' ? 'border-l-4 border-purple-500' : 'border-l-4 border-green-500')) }}">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 rounded-full {{ $roleKey === 'admin' ? 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400' : ($roleKey === 'pengurus' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' : ($roleKey === 'manager_toko' ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400' : 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400')) }} flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        @if($roleKey === 'admin')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        @elseif($roleKey === 'pengurus')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                        @else
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        @endif
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="font-bold text-lg text-gray-900 dark:text-white">{{ $roleData['label'] }}</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ ucfirst($roleKey) }}</p>
                </div>
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-300 mb-3">{{ $roleData['description'] }}</p>
            <div class="border-t border-gray-200 dark:border-gray-700 pt-3">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-2">Permissions:</p>
                <ul class="space-y-1" x-data="{ expanded: false }">
                    @foreach($roleData['permissions'] as $index => $permission)
                    <li class="text-xs text-gray-600 dark:text-gray-400 flex items-center" 
                        x-show="{{ $loop->index }} < 4 || expanded" 
                        x-transition.opacity>
                        <svg class="w-3 h-3 mr-1 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                        {{ $permission }}
                    </li>
                    @endforeach
                    
                    @if(count($roleData['permissions']) > 4)
                    <li class="pt-1">
                        <button @click="expanded = !expanded" class="text-xs text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 font-medium focus:outline-none flex items-center gap-1 transition-colors">
                            <span x-show="!expanded">+{{ count($roleData['permissions']) - 4 }} lainnya... (Lihat Semua)</span>
                            <span x-show="expanded" class="text-red-500">Tutup</span>
                        </button>
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
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="pengurus" {{ request('role') == 'pengurus' ? 'selected' : '' }}>Pengurus</option>
                        <option value="manager_toko" {{ request('role') == 'manager_toko' ? 'selected' : '' }}>Manager Toko</option>
                        <option value="member" {{ request('role') == 'member' ? 'selected' : '' }}>Member</option>
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
                            @php
                                $badgeClass = match($user->role) {
                                    'admin' => 'badge-danger',
                                    'pengurus' => 'badge-info',
                                    'manager_toko' => 'badge-purple',
                                    default => 'badge-success'
                                };
                                $roleLabel = match($user->role) {
                                    'manager_toko' => 'Manager Toko',
                                    default => ucfirst($user->role)
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }}">
                                {{ $roleLabel }}
                            </span>
                        </td>
                        <td>
                            <div class="flex items-center justify-center gap-2">
                                <form action="{{ route('roles.update', $user) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <select name="role" onchange="this.form.submit()" class="form-input text-xs py-1">
                                        <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                        <option value="pengurus" {{ $user->role === 'pengurus' ? 'selected' : '' }}>Pengurus</option>
                                        <option value="manager_toko" {{ $user->role === 'manager_toko' ? 'selected' : '' }}>Manager Toko</option>
                                        <option value="member" {{ $user->role === 'member' ? 'selected' : '' }}>Member</option>
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
