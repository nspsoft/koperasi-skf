@extends('layouts.app')

@section('title', __('messages.titles.members_list'))

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="page-title">{{ __('messages.members_page.title') }}</h1>
                <p class="page-subtitle">{{ __('messages.members_page.subtitle') }}</p>
            </div>
            @if(auth()->user()->isAdmin())
            <a href="{{ route('members.create') }}" class="btn-primary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                {{ __('messages.members_page.add_member') }}
            </a>
            @endif
        </div>
    </div>

    <!-- Filters -->
    <div class="glass-card-solid p-6 mb-6">
        <form method="GET" action="{{ route('members.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
                <label class="form-label">{{ __('messages.search') }}</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="{{ __('messages.members_page.search_placeholder') }}" 
                       class="form-input">
            </div>

            <!-- Status Filter -->
            <div>
                <label class="form-label">{{ __('messages.status') }}</label>
                <select name="status" class="form-input">
                    <option value="">{{ __('messages.members_page.all_status') }}</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>{{ __('messages.active') }}</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>{{ __('messages.inactive') }}</option>
                    <option value="resigned" {{ request('status') == 'resigned' ? 'selected' : '' }}>Resign</option>
                </select>
            </div>

            <!-- Department Filter -->
            <div>
                <label class="form-label">{{ __('messages.members_page.department') }}</label>
                <select name="department" class="form-input">
                    <option value="">{{ __('messages.members_page.all_department') }}</option>
                    @foreach($departments as $dept)
                    <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>
                        {{ $dept }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Actions -->
            <div class="flex items-end gap-2">
                <button type="submit" class="btn-primary flex-1">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Filter
                </button>
                <a href="{{ route('members.export', request()->query()) }}" class="btn-secondary flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Excel
                </a>
                @if(request()->hasAny(['search', 'status', 'department']))
                <a href="{{ route('members.index') }}" class="btn-secondary">
                    Reset
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Members Table -->
    <div class="glass-card-solid overflow-hidden" x-data="{ 
        selected: [], 
        allSelected: false,
        toggleAll() {
            this.allSelected = !this.allSelected;
            if (this.allSelected) {
                this.selected = [{{ $members->pluck('id')->implode(',') }}];
            } else {
                this.selected = [];
            }
        },
        toggle(id) {
            if (this.selected.includes(id)) {
                this.selected = this.selected.filter(item => item !== id);
            } else {
                this.selected.push(id);
            }
            this.allSelected = this.selected.length === {{ $members->count() }};
        }
    }">
        
        <!-- Bulk Actions Toolbar -->
        <div x-show="selected.length > 0" x-transition class="bg-primary-50 dark:bg-primary-900/20 p-4 border-b border-primary-100 dark:border-primary-800 flex items-center justify-between">
            <div class="flex items-center gap-2 text-primary-700 dark:text-primary-300">
                <span class="font-bold text-lg" x-text="selected.length"></span>
                <span>anggota dipilih</span>
            </div>
            
            @if(auth()->user()->isAdmin())
            <form action="{{ route('members.bulk_destroy') }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data yang dipilih?')">
                @csrf
                @method('DELETE')
                <template x-for="id in selected" :key="id">
                    <input type="hidden" name="ids[]" :value="id">
                </template>
                <button type="submit" class="btn-danger flex items-center gap-2 py-2 px-4 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Hapus Terpilih
                </button>
            </form>
            @endif
        </div>

        <div class="table-scroll-container">
            <table class="table-modern">
                <thead class="sticky top-0 z-10">
                    <tr>
                        <th class="w-10">
                            <input type="checkbox" 
                                   class="form-checkbox rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                                   @click="toggleAll()"
                                   :checked="allSelected">
                        </th>
                        <th>ID Anggota</th>
                        <th>Nama</th>
                        <th>NIK Karyawan</th>
                        <th>Departemen</th>
                        <th>Tgl Bergabung</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($members as $member)
                    <tr :class="selected.includes({{ $member->id }}) ? 'bg-primary-50 dark:bg-primary-900/10' : ''">
                        <td class="text-center">
                            <input type="checkbox" 
                                   class="form-checkbox rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                                   value="{{ $member->id }}"
                                   @click="toggle({{ $member->id }})"
                                   :checked="selected.includes({{ $member->id }})">
                        </td>
                        <td class="font-semibold text-primary-600 dark:text-primary-400">
                            {{ $member->member_id }}
                        </td>
                        <td>
                            <div class="flex items-center gap-3">
                                @if($member->photo)
                                <img src="{{ Storage::url($member->photo) }}" 
                                     alt="{{ $member->user->name }}"
                                     class="w-10 h-10 rounded-full object-cover">
                                @else
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white font-semibold flex-shrink-0">
                                    {{ strtoupper(substr($member->user->name, 0, 1)) }}
                                </div>
                                @endif
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $member->user->name }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $member->user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="text-gray-600 dark:text-gray-300">{{ $member->employee_id ?? '-' }}</span>
                        </td>
                        <td>
                            <span class="text-gray-600 dark:text-gray-300">{{ $member->department ?? '-' }}</span>
                        </td>
                        <td>
                            <span class="text-gray-600 dark:text-gray-300">{{ $member->join_date->format('d/m/Y') }}</span>
                        </td>
                        <td>
                            @if($member->status === 'active')
                            <span class="badge badge-success">Aktif</span>
                            @elseif($member->status === 'inactive')
                            <span class="badge badge-warning">Tidak Aktif</span>
                            @else
                            <span class="badge badge-danger">Resign</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('members.show', $member) }}" 
                                   class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors"
                                   title="Detail">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                
                                <button onclick="window.open('{{ route('members.card', $member) }}', 'MemberCard', 'width=800,height=600')" 
                                   class="p-2 text-purple-600 hover:bg-purple-50 dark:hover:bg-purple-900/20 rounded-lg transition-colors"
                                   title="Cetak Kartu">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                    </svg>
                                </button>

                                @if(auth()->user()->isAdmin())
                                <a href="{{ route('members.edit', $member) }}" 
                                   class="p-2 text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 rounded-lg transition-colors"
                                   title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>

                                <form action="{{ route('members.destroy', $member) }}" method="POST" 
                                      onsubmit="return confirm('Yakin ingin menghapus anggota {{ $member->user->name }}?')"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                                            title="Hapus">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-12">
                            <div class="empty-state">
                                <svg class="empty-state-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <h3 class="empty-state-title">Tidak Ada Data</h3>
                                <p class="empty-state-text">
                                    @if(request()->hasAny(['search', 'status', 'department']))
                                        Tidak ada anggota yang sesuai dengan filter.
                                    @else
                                        Belum ada anggota terdaftar.
                                    @endif
                                </p>
                                @if(auth()->user()->isAdmin())
                                <a href="{{ route('members.create') }}" class="btn-primary mt-4">
                                    Tambah Anggota Pertama
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($members->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $members->links() }}
        </div>
        @endif
    </div>
@endsection
