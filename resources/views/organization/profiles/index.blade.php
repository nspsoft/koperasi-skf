@extends('layouts.app')

@section('title', 'Daftar Pengurus')

@section('content')
<div class="space-y-6" x-data="{ showForm: false }">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                <a href="{{ route('organization.index') }}" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                Daftar Pengurus
            </h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1 ml-8">Arsip struktur organisasi dan masa jabatan pengurus</p>
        </div>
        <button @click="showForm = !showForm" class="btn-primary flex items-center gap-2">
            <span x-text="showForm ? 'Tutup Form' : 'Tambah Pengurus'"></span>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="!showForm"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="showForm" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>

    <!-- Create Form -->
    <div x-show="showForm" x-transition class="glass-card p-6 border-l-4 border-green-500" style="display: none;">
        <h3 class="font-bold text-lg mb-4 text-gray-800 dark:text-white">👔 Lantik Pengurus Baru</h3>
        <form action="{{ route('organization.profiles.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf
            
            <div class="form-group md:col-span-2">
                <label class="form-label">Pilih Anggota / User</label>
                <select name="user_id" class="form-select" required>
                    <option value="">-- Cari Nama --</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">Pastikan user sudah terdaftar di sistem.</p>
            </div>

            <div class="form-group">
                <label class="form-label">Posisi / Jabatan</label>
                <select name="position" class="form-select" required>
                    <option value="Ketua">Ketua</option>
                    <option value="Wakil Ketua">Wakil Ketua</option>
                    <option value="Sekretaris">Sekretaris</option>
                    <option value="Bendahara">Bendahara</option>
                    <option value="Pengawas">Pengawas</option>
                    <option value="Manager">Manager Usaha</option>
                    <option value="Staf">Staf Operasional</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Departemen / Bidang</label>
                <input type="text" name="department" class="form-input" placeholder="Contoh: Operasional / Simpan Pinjam / Umum">
            </div>

            <div class="form-group">
                <label class="form-label">Mulai Menjabat</label>
                <input type="date" name="start_date" class="form-input" required>
            </div>

            <div class="form-group">
                <label class="form-label">Periode Kepengurusan</label>
                <input type="text" name="period" class="form-input" required placeholder="Contoh: 2023-2028">
            </div>

            <div class="md:col-span-2 flex justify-end gap-2 mt-2">
                <button type="button" @click="showForm = false" class="px-4 py-2 text-gray-600 hover:text-gray-800">Batal</button>
                <button type="submit" class="btn-primary">Simpan Data</button>
            </div>
        </form>
    </div>

    <!-- Active Profiles Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($profiles as $profile)
        <div class="glass-card hover:shadow-xl transition-all duration-300 overflow-hidden relative group border-t-4 border-blue-500" 
             x-data="{ showEditModal: false }">
            <div class="p-6">
                <!-- Action Buttons (Hover) -->
                <div class="absolute top-2 right-2 flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity z-20">
                    <button @click="showEditModal = true" class="p-1.5 bg-white/90 dark:bg-gray-800/90 rounded-lg text-blue-600 hover:bg-blue-600 hover:text-white transition-all shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                    </button>
                    <form action="{{ route('organization.profiles.destroy', $profile) }}" method="POST" onsubmit="return confirm('Hapus data pengurus ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-1.5 bg-white/90 dark:bg-gray-800/90 rounded-lg text-red-600 hover:bg-red-600 hover:text-white transition-all shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </form>
                </div>

                <div class="flex items-start gap-4">
                    <!-- Avatar Section -->
                    <div class="relative shrink-0">
                        <div class="w-20 h-20 rounded-2xl border-2 border-blue-100 dark:border-blue-900 shadow-sm overflow-hidden bg-white dark:bg-gray-800">
                            @if($profile->user->avatar)
                                <img src="{{ Storage::url($profile->user->avatar) }}" class="w-full h-full object-cover">
                            @elseif($profile->user->member && $profile->user->member->photo)
                                <img src="{{ Storage::url($profile->user->member->photo) }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-2xl font-bold">
                                    {{ substr($profile->user->name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <div class="absolute -bottom-2 -right-2 bg-green-500 w-5 h-5 rounded-full border-2 border-white dark:border-gray-800" title="Aktif"></div>
                    </div>

                    <!-- Identity -->
                    <div class="flex-1 min-w-0">
                        <h3 class="font-bold text-lg text-gray-800 dark:text-white truncate leading-tight mb-1" title="{{ $profile->user->name }}">
                            {{ $profile->user->name }}
                        </h3>
                        <p class="text-blue-600 dark:text-blue-400 font-bold text-sm tracking-wide uppercase">{{ $profile->position }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $profile->department ?? 'Manajemen Koperasi' }}</p>
                    </div>
                </div>

                <!-- Info Grid -->
                <div class="mt-6 grid grid-cols-2 gap-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <div>
                        <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold">Periode</p>
                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $profile->period }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold">Mulai Jabat</p>
                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $profile->start_date->format('d M Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- Edit Modal (Inline for specific profile) -->
            <div x-show="showEditModal" 
                 class="fixed inset-0 z-[100] overflow-y-auto" 
                 x-cloak
                 @keydown.escape.window="showEditModal = false">
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 transition-opacity bg-gray-900/60 backdrop-blur-sm" @click="showEditModal = false"></div>
                    
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    
                    <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-gray-800 rounded-2xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100 dark:border-gray-700">
                        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-800/50">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white">Edit Data Pengurus: {{ $profile->user->name }}</h3>
                            <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                        </div>
                        <form action="{{ route('organization.profiles.update', $profile) }}" method="POST" class="p-6 space-y-4">
                            @csrf
                            @method('PUT')
                            
                            <div class="form-group">
                                <label class="form-label">Posisi / Jabatan</label>
                                <select name="position" class="form-select" required>
                                    <option value="Ketua" {{ $profile->position == 'Ketua' ? 'selected' : '' }}>Ketua</option>
                                    <option value="Wakil Ketua" {{ $profile->position == 'Wakil Ketua' ? 'selected' : '' }}>Wakil Ketua</option>
                                    <option value="Sekretaris" {{ $profile->position == 'Sekretaris' ? 'selected' : '' }}>Sekretaris</option>
                                    <option value="Bendahara" {{ $profile->position == 'Bendahara' ? 'selected' : '' }}>Bendahara</option>
                                    <option value="Pengawas" {{ $profile->position == 'Pengawas' ? 'selected' : '' }}>Pengawas</option>
                                    <option value="Manager" {{ $profile->position == 'Manager' ? 'selected' : '' }}>Manager Usaha</option>
                                    <option value="Staf" {{ $profile->position == 'Staf' ? 'selected' : '' }}>Staf Operasional</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Departemen / Bidang</label>
                                <input type="text" name="department" class="form-input" value="{{ $profile->department }}" placeholder="Operasional / Umum">
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="form-group">
                                    <label class="form-label">Mulai Menjabat</label>
                                    <input type="date" name="start_date" class="form-input" value="{{ $profile->start_date->format('Y-m-d') }}" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Periode</label>
                                    <input type="text" name="period" class="form-input" value="{{ $profile->period }}" required placeholder="2023-2028">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Status Jabatan</label>
                                <select name="status" class="form-select">
                                    <option value="active" {{ $profile->status == 'active' ? 'selected' : '' }}>Aktif</option>
                                    <option value="inactive" {{ $profile->status == 'inactive' ? 'selected' : '' }}>Tidak Aktif (Demisioner)</option>
                                </select>
                            </div>

                            <div class="flex justify-end gap-3 mt-6">
                                <button type="button" @click="showEditModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 shadow-sm transition-colors">Batal</button>
                                <button type="submit" class="btn-primary">Update Data</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Background Decoration -->
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-16 h-16 bg-blue-500/5 rounded-full blur-xl group-hover:bg-blue-500/10 transition-colors"></div>
        </div>
        @empty
        <div class="md:col-span-3 text-center py-20 glass-card border-2 border-dashed border-gray-300 dark:border-gray-700">
            <div class="text-6xl mb-4">👔</div>
            <p class="text-xl font-bold text-gray-800 dark:text-white mb-2">Belum ada pengurus terdaftar</p>
            <p class="text-gray-500">Silakan input data pengurus untuk periode saat ini melalui tombol di atas.</p>
        </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $profiles->links() }}
    </div>
</div>
@endsection
