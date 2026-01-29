@extends('layouts.app')

@section('title', 'Notulen Rapat')

@section('content')
<div class="space-y-6" x-data="{ showForm: false }">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                <a href="{{ route('organization.index') }}" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                Notulen & Jadwal Rapat
            </h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1 ml-8">Manajemen agenda rapat, kehadiran, dan hasil keputusan (notulen)</p>
        </div>
        <button @click="showForm = !showForm" class="btn-primary flex items-center gap-2">
            <span x-text="showForm ? 'Tutup Form' : 'Jadwalkan Rapat'"></span>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="!showForm"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="showForm" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>

    <!-- Create Form -->
    <div x-show="showForm" x-transition class="glass-card p-6 border-l-4 border-purple-500" style="display: none;">
        <h3 class="font-bold text-lg mb-4 text-gray-800 dark:text-white">📅 Jadwalkan Rapat Baru</h3>
        <form action="{{ route('organization.meetings.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf
            
            <div class="form-group md:col-span-2">
                <label class="form-label">Judul Rapat</label>
                <input type="text" name="title" class="form-input" required placeholder="Contoh: Rapat Pleno Bulan Januari 2024">
            </div>

            <div class="form-group">
                <label class="form-label">Tipe Rapat</label>
                <select name="type" class="form-select" required>
                    <option value="Rapat Pengurus Harian">Rapat Pengurus Harian</option>
                    <option value="Rapat Pleno">Rapat Pleno (Bulanan)</option>
                    <option value="RAT">Rapat Anggota Tahunan (RAT)</option>
                    <option value="Rapat Luar Biasa">Rapat Luar Biasa</option>
                    <option value="Koordinasi Tim">Koordinasi Tim</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Waktu Pelaksanaan</label>
                <input type="datetime-local" name="scheduled_at" class="form-input" required>
            </div>

            <div class="form-group">
                <label class="form-label">Lokasi</label>
                <input type="text" name="location" class="form-input" placeholder="Contoh: Ruang Meeting Lt. 2 / Zoom Meeting">
            </div>

            <div class="form-group md:col-span-2">
                <label class="form-label">Agenda Utama</label>
                <textarea name="agenda" class="form-input" rows="3" placeholder="Tuliskan poin-poin agenda rapat..."></textarea>
            </div>

            <div class="md:col-span-2 flex justify-end gap-2 mt-2">
                <button type="button" @click="showForm = false" class="px-4 py-2 text-gray-600 hover:text-gray-800">Batal</button>
                <button type="submit" class="btn-primary">Buat Jadwal</button>
            </div>
        </form>
    </div>

    <!-- Timeline / List View -->
    <div class="glass-card">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="font-bold text-gray-800 dark:text-white">Daftar Riwayat & Jadwal Rapat</h3>
        </div>
        <div class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($meetings as $meeting)
            <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors flex flex-col md:flex-row gap-6 relative group" x-data="{ showEditModal: false }">
                <!-- Date Box -->
                <div class="flex-shrink-0 flex flex-col items-center justify-center w-20 h-20 bg-gray-100 dark:bg-gray-700 rounded-xl border border-gray-200 dark:border-gray-600 text-center">
                    <span class="text-xs font-bold text-red-500 uppercase tracking-widest">{{ $meeting->scheduled_at->format('M') }}</span>
                    <span class="text-2xl font-black text-gray-800 dark:text-white leading-none my-1">{{ $meeting->scheduled_at->format('d') }}</span>
                    <span class="text-xs text-gray-500">{{ $meeting->scheduled_at->format('D') }}</span>
                </div>

                <!-- Content -->
                <div class="flex-1">
                    <div class="flex items-center justify-between mb-2">
                        <span class="px-2 py-1 text-xs rounded-lg font-medium 
                            {{ $meeting->type === 'RAT' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                            {{ $meeting->type }}
                        </span>
                        <div class="flex items-center gap-4">
                            <span class="text-xs {{ $meeting->status === 'completed' ? 'text-green-600' : ($meeting->status === 'scheduled' ? 'text-orange-500' : 'text-gray-500') }} capitalize font-bold flex items-center gap-1">
                                @if($meeting->status === 'scheduled') ⏳ @endif
                                @if($meeting->status === 'completed') ✅ @endif
                                {{ $meeting->status }}
                            </span>
                            
                            <!-- Actions -->
                            <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button @click="showEditModal = true" class="text-blue-500 hover:text-blue-700">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                                </button>
                                <form action="{{ route('organization.meetings.destroy', $meeting) }}" method="POST" onsubmit="return confirm('Hapus riwayat rapat ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <h3 class="font-bold text-lg text-gray-800 dark:text-white mb-1">{{ $meeting->title }}</h3>
                    <div class="flex items-center text-sm text-gray-500 gap-4 mb-3">
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            {{ $meeting->scheduled_at->format('H:i') }} WIB
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            {{ $meeting->location ?? 'Online' }}
                        </span>
                    </div>

                    @if($meeting->agenda)
                        <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded-lg text-sm text-gray-600 dark:text-gray-300">
                            <strong>Agenda:</strong> {{ Str::limit($meeting->agenda, 200) }}
                        </div>
                    @endif

                    <!-- Edit Modal -->
                    <div x-show="showEditModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
                        <div class="flex items-center justify-center min-h-screen px-4">
                            <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="showEditModal = false"></div>
                            <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-lg w-full p-6 text-left border border-gray-100 dark:border-gray-700">
                                <h3 class="text-lg font-bold mb-4">Edit Rapat</h3>
                                <form action="{{ route('organization.meetings.update', $meeting) }}" method="POST" class="space-y-4">
                                    @csrf
                                    @method('PUT')
                                    <div class="form-group">
                                        <label class="form-label">Judul Rapat</label>
                                        <input type="text" name="title" class="form-input" value="{{ $meeting->title }}" required>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="form-group">
                                            <label class="form-label">Tipe</label>
                                            <select name="type" class="form-select">
                                                <option value="Rapat Pengurus Harian" {{ $meeting->type == 'Rapat Pengurus Harian' ? 'selected' : '' }}>Rapat Pengurus Harian</option>
                                                <option value="Rapat Pleno" {{ $meeting->type == 'Rapat Pleno' ? 'selected' : '' }}>Rapat Pleno</option>
                                                <option value="RAT" {{ $meeting->type == 'RAT' ? 'selected' : '' }}>RAT</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Status</label>
                                            <select name="status" class="form-select">
                                                <option value="scheduled" {{ $meeting->status == 'scheduled' ? 'selected' : '' }}>Terjadwal</option>
                                                <option value="completed" {{ $meeting->status == 'completed' ? 'selected' : '' }}>Selesai</option>
                                                <option value="cancelled" {{ $meeting->status == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Waktu</label>
                                        <input type="datetime-local" name="scheduled_at" class="form-input" value="{{ $meeting->scheduled_at->format('Y-m-d\TH:i') }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Lokasi</label>
                                        <input type="text" name="location" class="form-input" value="{{ $meeting->location }}">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Notulen / Agenda</label>
                                        <textarea name="agenda" class="form-input" rows="4">{{ $meeting->agenda }}</textarea>
                                    </div>
                                    <div class="flex justify-end gap-2 pt-4">
                                        <button type="button" @click="showEditModal = false" class="px-4 py-2 text-gray-500">Batal</button>
                                        <button type="submit" class="btn-primary">Update Rapat</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-12 text-center text-gray-500">
                <div class="text-4xl mb-4">📅</div>
                <p>Belum ada jadwal rapat yang tercatat.</p>
            </div>
            @endforelse
        </div>
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $meetings->links() }}
        </div>
    </div>
</div>
@endsection
