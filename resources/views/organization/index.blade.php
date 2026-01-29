@extends('layouts.app')

@section('title', 'Manajemen Organisasi')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="glass-card p-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Manajemen Organisasi</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Pengelolaan Administrasi Organisasi Koperasi</p>
        </div>
        <div class="text-3xl">🏛️</div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Assets Card -->
        <a href="{{ route('organization.assets') }}" class="glass-card p-6 hover:shadow-lg transition-all group cursor-pointer relative overflow-hidden">
            <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <span class="text-6xl">🏢</span>
            </div>
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center text-2xl">
                        🏢
                    </div>
                    <h3 class="font-bold text-lg text-gray-800 dark:text-white">Inventaris Aset</h3>
                </div>
                <div class="space-y-2">
                    <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $assets_count }} <span class="text-sm text-gray-500 font-normal">Aset</span></p>
                    <p class="text-sm text-gray-500">Total Nilai: <span class="font-medium text-gray-700 dark:text-gray-300">Rp {{ number_format($assets_value, 0, ',', '.') }}</span></p>
                </div>
                <div class="mt-6 flex items-center text-sm text-blue-600 dark:text-blue-400 font-medium">
                    Kelola Aset 
                    <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </div>
            </div>
        </a>

        <!-- Meetings Card -->
        <a href="{{ route('organization.meetings') }}" class="glass-card p-6 hover:shadow-lg transition-all group cursor-pointer relative overflow-hidden">
             <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <span class="text-6xl">📆</span>
            </div>
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center text-2xl">
                        📆
                    </div>
                    <h3 class="font-bold text-lg text-gray-800 dark:text-white">Notulen Rapat</h3>
                </div>
                <div class="space-y-2">
                    <p class="text-3xl font-bold text-purple-600 dark:text-purple-400">{{ $upcoming_meetings->count() }} <span class="text-sm text-gray-500 font-normal">Rapat Terjadwal</span></p>
                    <p class="text-sm text-gray-500">Agenda Terdekat: <span class="font-medium text-gray-700 dark:text-gray-300">{{ $upcoming_meetings->count() > 0 ? $upcoming_meetings->first()->scheduled_at->format('d M') : '-' }}</span></p>
                </div>
                <div class="mt-6 flex items-center text-sm text-purple-600 dark:text-purple-400 font-medium">
                    Lihat Jadwal 
                    <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </div>
            </div>
        </a>

        <!-- Profiles Card -->
        <a href="{{ route('organization.profiles') }}" class="glass-card p-6 hover:shadow-lg transition-all group cursor-pointer relative overflow-hidden">
             <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <span class="text-6xl">👥</span>
            </div>
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center text-2xl">
                        👥
                    </div>
                    <h3 class="font-bold text-lg text-gray-800 dark:text-white">Daftar Pengurus</h3>
                </div>
                <div class="space-y-2">
                    <p class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $active_profiles->where('position', '!=', 'Manager')->count() }} <span class="text-sm text-gray-500 font-normal">Pengurus Inti</span></p>
                    <p class="text-sm text-gray-500">Status: <span class="font-medium text-green-600 bg-green-100 px-2 py-0.5 rounded text-xs">Periode Aktif</span></p>
                </div>
                <div class="mt-6 flex items-center text-sm text-green-600 dark:text-green-400 font-medium">
                    Lihat Struktur
                    <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </div>
            </div>
        </a>
    </div>

    <!-- Quick Preview Section -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Upcoming Meetings Preview -->
        <div class="glass-card p-6">
            <h3 class="font-bold text-lg mb-4 text-gray-800 dark:text-white">🗓️ Agenda Rapat Terdekat</h3>
            @if($upcoming_meetings->count() > 0)
                <div class="space-y-4">
                    @foreach($upcoming_meetings as $meeting)
                        <div class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <div class="flex-shrink-0 w-12 text-center bg-white dark:bg-gray-900 rounded p-1 border border-gray-200 dark:border-gray-600">
                                <span class="block text-xs text-red-500 font-bold uppercase">{{ $meeting->scheduled_at->format('M') }}</span>
                                <span class="block text-lg font-bold text-gray-800 dark:text-white">{{ $meeting->scheduled_at->format('d') }}</span>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800 dark:text-white text-sm">{{ $meeting->title }}</h4>
                                <p class="text-xs text-gray-500">{{ $meeting->scheduled_at->format('H:i') }} | {{ $meeting->location ?? 'Online/TBA' }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <p>Tidak ada agenda rapat dalam waktu dekat.</p>
                </div>
            @endif
        </div>

        <!-- Structure Preview -->
        <div class="glass-card p-6">
            <h3 class="font-bold text-lg mb-4 text-gray-800 dark:text-white">👔 Pengurus Inti Aktif</h3>
            @if($active_profiles->count() > 0)
                <div class="space-y-3">
                    @foreach($active_profiles as $profile)
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold overflow-hidden">
                                @if($profile->user->avatar)
                                    <img src="{{ asset('storage/' . $profile->user->avatar) }}" class="w-full h-full object-cover">
                                @else
                                    {{ substr($profile->user->name, 0, 1) }}
                                @endif
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-bold text-gray-800 dark:text-white">{{ $profile->user->name }}</p>
                                <p class="text-xs text-gray-500">{{ $profile->position }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <p>Belum ada data pengurus aktif.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
