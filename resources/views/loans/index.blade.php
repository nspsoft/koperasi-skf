@extends('layouts.app')

@section('title', __('messages.titles.loans_list'))

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="page-title">{{ __('messages.loans_page.title') }}</h1>
                <p class="page-subtitle">{{ __('messages.loans_page.subtitle') }}</p>
            </div>
            @if(auth()->user()->hasAdminAccess())
            <a href="{{ route('loans.create') }}" class="btn-primary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                {{ __('messages.loans_page.apply_loan') }}
            </a>
            @endif
        </div>
    </div>

    <!-- Filters -->
    <div class="glass-card-solid p-6 mb-6">
        <form method="GET" action="{{ route('loans.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
                <label class="form-label">{{ __('messages.search') }}</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="{{ __('messages.loans_page.search_placeholder') }}" 
                       class="form-input">
            </div>

            <!-- Status Filter -->
            <div>
                <label class="form-label">{{ __('messages.status') }}</label>
                <select name="status" class="form-input">
                    <option value="">{{ __('messages.loans_page.all_status') }}</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('messages.pending') }}</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>{{ __('messages.approved') }}</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>{{ __('messages.active') }}</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>{{ __('messages.titles.completed') }}</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>{{ __('messages.rejected') }}</option>
                </select>
            </div>

            <!-- Loan Type Filter -->
            <div>
                <label class="form-label">{{ __('messages.loans_page.type') }}</label>
                <select name="loan_type" class="form-input">
                    <option value="">{{ __('messages.loans_page.all_types') }}</option>
                    <option value="regular" {{ request('loan_type') == 'regular' ? 'selected' : '' }}>Reguler</option>
                    <option value="emergency" {{ request('loan_type') == 'emergency' ? 'selected' : '' }}>Darurat</option>
                    <option value="education" {{ request('loan_type') == 'education' ? 'selected' : '' }}>Pendidikan</option>
                    <option value="special" {{ request('loan_type') == 'special' ? 'selected' : '' }}>Khusus</option>
                </select>
            </div>

            <!-- Actions -->
            <div class="flex items-end gap-2">
                <button type="submit" class="btn-primary flex-1">
                    {{ __('messages.loans_page.filter') }}
                </button>
                @if(auth()->user()->hasAdminAccess())
                <a href="{{ route('loans.export', request()->query()) }}" class="btn-secondary">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    {{ __('messages.loans_page.excel') }}
                </a>
                @endif
                @if(request()->hasAny(['search', 'status', 'loan_type']))
                <a href="{{ route('loans.index') }}" class="btn-secondary">
                    {{ __('messages.loans_page.reset') }}
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Loans Table -->
    <div class="glass-card-solid overflow-hidden" x-data="{ 
        selected: [], 
        allSelected: false,
        toggleAll() {
            this.allSelected = !this.allSelected;
            if (this.allSelected) {
                this.selected = [{{ $loans->filter(fn($l) => in_array($l->status, ['pending', 'rejected']))->pluck('id')->implode(',') }}];
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
            // Logic check allSelected is complex with partial selectable items, simplified:
            this.allSelected = false; 
        }
    }">
        
        <!-- Bulk Actions Toolbar -->
        <div x-show="selected.length > 0" x-transition class="bg-primary-50 dark:bg-primary-900/20 p-4 border-b border-primary-100 dark:border-primary-800 flex items-center justify-between">
            <div class="flex items-center gap-2 text-primary-700 dark:text-primary-300">
                <span class="font-bold text-lg" x-text="selected.length"></span>
                <span>{{ __('messages.loans_page.selected_loans') }}</span>
            </div>
            
            @if(auth()->user()->hasAdminAccess())
            <form action="{{ route('loans.bulk_destroy') }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pengajuan yang dipilih?')">
                @csrf
                @method('DELETE')
                <template x-for="id in selected" :key="id">
                    <input type="hidden" name="ids[]" :value="id">
                </template>
                <button type="submit" class="btn-danger flex items-center gap-2 py-2 px-4 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    {{ __('messages.loans_page.bulk_delete') }}
                </button>
            </form>
            @endif
        </div>

        <div class="table-scroll-container">
            <table class="table-modern">
                <thead class="sticky top-0 z-10">
                    <tr class="text-gray-700 dark:text-gray-300">
                        <th class="w-10 px-6 py-4">
                            <input type="checkbox" 
                                   class="form-checkbox rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                                   @click="toggleAll()"
                                   :checked="allSelected"
                                   title="Pilih semua yang bisa dihapus (Pending/Rejected)">
                        </th>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider">{{ __('messages.loans_page.loan_no') }}</th>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider">{{ __('messages.loans_page.member') }}</th>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider">{{ __('messages.loans_page.type') }}</th>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider">{{ __('messages.loans_page.amount') }}</th>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider">{{ __('messages.loans_page.interest') }}</th>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider">{{ __('messages.loans_page.tenor') }}</th>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider">{{ __('messages.loans_page.status') }}</th>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider text-center">{{ __('messages.loans_page.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($loans as $loan)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
                        :class="selected.includes({{ $loan->id }}) ? 'bg-primary-50 dark:bg-primary-900/10' : ''">
                        <td class="px-6 py-4 text-center">
                            @if(in_array($loan->status, ['pending', 'rejected']))
                            <input type="checkbox" 
                                   class="form-checkbox rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                                   value="{{ $loan->id }}"
                                   @click="toggle({{ $loan->id }})"
                                   :checked="selected.includes({{ $loan->id }})">
                            @else
                            <input type="checkbox" disabled class="form-checkbox rounded border-gray-200 text-gray-400 cursor-not-allowed opacity-50">
                            @endif
                        </td>
                        <td class="px-6 py-4 font-mono text-xs text-gray-500 dark:text-gray-400">{{ $loan->loan_number }}</td>
                         <td class="px-6 py-4">
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $loan->member->user->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $loan->member->member_id }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-700 dark:text-gray-300">{{ $loan->loan_type_label }}</td>
                        <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">
                            Rp {{ number_format($loan->amount, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-gray-700 dark:text-gray-300">{{ $loan->interest_rate }}%</td>
                        <td class="px-6 py-4 text-gray-700 dark:text-gray-300">{{ $loan->duration_months }} bln</td>
                        <td>
                            <span class="badge badge-{{ $loan->status_color }}">
                                {{ $loan->status_label }}
                            </span>
                        </td>
                        <td>
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('loans.show', $loan) }}" 
                                   class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors"
                                   title="Detail & Approval">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                
                                @if($loan->status === 'pending' && auth()->user()->hasAdminAccess())
                                <form action="{{ route('loans.destroy', $loan) }}" method="POST" 
                                      onsubmit="return confirm('Yakin ingin membatalkan pengajuan ini?')"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                                            title="Batalkan Pengajuan">
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
                        <td colspan="9" class="text-center py-12">
                             <div class="empty-state">
                                <svg class="empty-state-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <h3 class="empty-state-title">Tidak Ada Pinjaman</h3>
                                <p class="empty-state-text">
                                    Belum ada data pengajuan pinjaman yang ditemukan.
                                </p>
                                @if(auth()->user()->hasAdminAccess())
                                <a href="{{ route('loans.create') }}" class="btn-primary mt-4">
                                    Ajukan Pinjaman Baru
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
        @if($loans->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $loans->links() }}
        </div>
        @endif
    </div>
@endsection
