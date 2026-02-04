@extends('layouts.app')

@section('title', __('messages.titles.savings_list'))

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="page-title">{{ __('messages.savings_page.title') }}</h1>
                <p class="page-subtitle">{{ __('messages.savings_page.subtitle') }}</p>
            </div>
            @if(auth()->user()->hasAdminAccess())
            <a href="{{ route('savings.create') }}" class="btn-primary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                {{ __('messages.savings_page.new_transaction') }}
            </a>
            @endif
        </div>
    </div>

    <!-- Filters -->
    <div class="glass-card-solid p-6 mb-6">
        <form method="GET" action="{{ route('savings.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
                <label class="form-label">{{ __('messages.search') }}</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="{{ __('messages.savings_page.search_placeholder') }}" 
                       class="form-input">
            </div>

            <!-- Transaction Type -->
            <div>
                <label class="form-label">{{ __('messages.savings_page.transaction_type') }}</label>
                <select name="transaction_type" class="form-input">
                    <option value="">{{ __('messages.savings_page.all') }}</option>
                    <option value="deposit" {{ request('transaction_type') == 'deposit' ? 'selected' : '' }}>{{ __('messages.savings_page.deposit') }}</option>
                    <option value="withdrawal" {{ request('transaction_type') == 'withdrawal' ? 'selected' : '' }}>{{ __('messages.savings_page.withdrawal') }}</option>
                </select>
            </div>

            <!-- Saving Type -->
            <div>
                <label class="form-label">{{ __('messages.savings_page.saving_type') }}</label>
                <select name="type" class="form-input">
                    <option value="">{{ __('messages.savings_page.all') }}</option>
                    <option value="pokok" {{ request('type') == 'pokok' ? 'selected' : '' }}>{{ __('messages.savings_page.principal') }}</option>
                    <option value="wajib" {{ request('type') == 'wajib' ? 'selected' : '' }}>{{ __('messages.savings_page.mandatory') }}</option>
                    <option value="sukarela" {{ request('type') == 'sukarela' ? 'selected' : '' }}>{{ __('messages.savings_page.voluntary') }}</option>
                </select>
            </div>

            <!-- Date Range -->
            <div>
                 <label class="form-label">{{ __('messages.date') }}</label>
                 <div class="flex gap-2">
                     <input type="date" name="date_start" value="{{ request('date_start') }}" class="form-input text-xs">
                     <input type="date" name="date_end" value="{{ request('date_end') }}" class="form-input text-xs">
                 </div>
            </div>

            <!-- Actions -->
            <div class="md:col-span-4 flex justify-end gap-2">
                <button type="submit" class="btn-primary w-full md:w-auto">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    {{ __('messages.savings_page.filter_data') }}
                </button>
                @if(auth()->user()->hasAdminAccess())
                <a href="{{ route('savings.export', request()->query()) }}" class="btn-secondary w-full md:w-auto text-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    {{ __('messages.savings_page.download_excel') }}
                </a>
                @endif
                @if(request()->hasAny(['search', 'type', 'transaction_type', 'date_start', 'date_end']))
                <a href="{{ route('savings.index') }}" class="btn-secondary w-full md:w-auto text-center">
                    {{ __('messages.savings_page.reset') }}
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Savings Table -->
    <div class="glass-card-solid overflow-hidden" x-data="{ 
        selected: [], 
        allSelected: false,
        toggleAll() {
            this.allSelected = !this.allSelected;
            if (this.allSelected) {
                this.selected = [{{ $savings->pluck('id')->implode(',') }}];
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
            this.allSelected = this.selected.length === {{ $savings->count() }};
        }
    }">
        
        <!-- Bulk Actions Toolbar -->
        <div x-show="selected.length > 0" x-transition x-cloak class="bg-primary-50 dark:bg-primary-900/20 p-4 border-b border-primary-100 dark:border-primary-800 flex items-center justify-between">
            <div class="flex items-center gap-2 text-primary-700 dark:text-primary-300">
                <span class="font-bold text-lg" x-text="selected.length"></span>
                <span>transaksi dipilih</span>
            </div>
            
            @can('delete-data')
            <form action="{{ route('savings.bulk_destroy') }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data yang dipilih?')">
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
            @endcan
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
                        <th>No. Referensi</th>
                        <th>Tanggal</th>
                        <th>Anggota</th>
                        <th>Jenis Simpanan</th>
                        <th class="text-right">Debit (Masuk)</th>
                        <th class="text-right">Kredit (Keluar)</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($savings as $saving)
                    <tr :class="selected.includes({{ $saving->id }}) ? 'bg-primary-50 dark:bg-primary-900/10' : ''">
                        <td class="text-center">
                            <input type="checkbox" 
                                   class="form-checkbox rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                                   value="{{ $saving->id }}"
                                   @click="toggle({{ $saving->id }})"
                                   :checked="selected.includes({{ $saving->id }})">
                        </td>
                        <td class="font-mono text-xs text-gray-500">{{ $saving->reference_number }}</td>
                        <td>{{ $saving->transaction_date->format('d/m/Y') }}</td>
                        <td>
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $saving->member->user->name }}</p>
                                <p class="text-xs text-gray-500">{{ $saving->member->member_id }}</p>
                            </div>
                        </td>
                        <td>
                            <span class="text-sm text-gray-600 dark:text-gray-300">{{ $saving->type_label }}</span>
                            <div class="text-xs text-gray-400">{{ $saving->description }}</div>
                        </td>
                        <td class="text-right font-semibold text-green-600">
                             @if($saving->transaction_type === 'deposit')
                                Rp {{ number_format($saving->amount, 0, ',', '.') }}
                             @else
                                -
                             @endif
                        </td>
                        <td class="text-right font-semibold text-red-600">
                             @if($saving->transaction_type === 'withdrawal')
                                Rp {{ number_format($saving->amount, 0, ',', '.') }}
                             @else
                                -
                             @endif
                        </td>
                        <td class="text-center">
                            <div class="flex items-center justify-center gap-2">
                                @can('delete-data')
                                <x-delete-confirm-modal 
                                    :action="route('savings.destroy', $saving)" 
                                    title="Hapus Transaksi Simpanan"
                                    message="Apakah Anda yakin ingin menghapus transaksi simpanan ini?"
                                    :related-data="['Jurnal akuntansi terkait akan ikut terhapus', 'Saldo anggota akan berubah', 'Laporan keuangan akan terpengaruh']"
                                />
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-12">
                            <div class="empty-state">
                                <svg class="empty-state-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <h3 class="empty-state-title">Tidak Ada Transaksi</h3>
                                <p class="empty-state-text">
                                    Belum ada data transaksi simpanan yang ditemukan.
                                </p>
                                @if(auth()->user()->hasAdminAccess())
                                <a href="{{ route('savings.create') }}" class="btn-primary mt-4">
                                    Buat Transaksi Baru
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
        @if($savings->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $savings->links() }}
        </div>
        @endif
    </div>
@endsection
