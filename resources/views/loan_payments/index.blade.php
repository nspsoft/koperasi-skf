@extends('layouts.app')

@section('title', __('messages.titles.loan_payments'))

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="page-title">Riwayat Angsuran</h1>
                <p class="page-subtitle">Daftar seluruh pembayaran angsuran pinjaman anggota</p>
            </div>
            @if(auth()->user()->hasAdminAccess())
            <a href="{{ route('loan-payments.create') }}" class="btn-primary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Input Pembayaran
            </a>
            @endif
        </div>
    </div>

    <!-- Filters -->
    <div class="glass-card-solid p-6 mb-6">
        <form method="GET" action="{{ route('loan-payments.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div class="md:col-span-2">
                <label class="form-label">Cari</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="No Bayar, Anggota, Pinjaman..." 
                           class="form-input pl-10">
                </div>
            </div>

            <!-- Payment Method Filter -->
            <div>
                <label class="form-label">Metode Pembayaran</label>
                <select name="payment_method" class="form-input">
                    <option value="">Semua Metode</option>
                    <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Tunai</option>
                    <option value="transfer" {{ request('payment_method') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                    <option value="salary_deduction" {{ request('payment_method') == 'salary_deduction' ? 'selected' : '' }}>Potong Gaji</option>
                </select>
            </div>

            <!-- Actions -->
            <div class="flex items-end gap-2">
                <button type="submit" class="btn-primary flex-1">
                    Filter
                </button>
                @if(auth()->user()->hasAdminAccess())
                <a href="{{ route('loan-payments.export', request()->query()) }}" class="btn-secondary">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Excel
                </a>
                @endif
                @if(request()->hasAny(['search', 'payment_method']))
                <a href="{{ route('loan-payments.index') }}" class="btn-secondary">
                    Reset
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Bulk Actions -->
    @if(auth()->user()->hasAdminAccess())
    <div x-data="{ selectedIds: [], selectAll: false }" x-cloak>
        <!-- Bulk Delete Bar -->
        <div x-show="selectedIds.length > 0" 
             x-transition
             class="mb-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl flex items-center justify-between">
            <span class="text-red-700 dark:text-red-300 font-medium">
                <span x-text="selectedIds.length"></span> item dipilih
            </span>
            <button type="button" 
                    @click="if(confirm('Apakah Anda yakin ingin menghapus ' + selectedIds.length + ' data pembayaran yang dipilih?')) { document.getElementById('bulk-delete-form').submit(); }"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                Hapus Terpilih
            </button>
        </div>

        <!-- Hidden form for bulk delete -->
        <form id="bulk-delete-form" method="POST" action="{{ route('loan-payments.bulk-delete') }}">
            @csrf
            @method('DELETE')
            <template x-for="id in selectedIds" :key="id">
                <input type="hidden" name="ids[]" :value="id">
            </template>
        </form>

        <div class="glass-card-solid overflow-hidden">
            <!-- Scrollable Table Container -->
            <div class="table-scroll-container">
                <table class="table-modern w-full">
                    <thead class="sticky top-0 bg-gray-50 dark:bg-gray-800 z-10">
                        <tr class="text-gray-700 dark:text-gray-300">
                            <th class="px-4 py-4 w-12">
                                <input type="checkbox" 
                                       x-model="selectAll"
                                       @change="selectedIds = selectAll ? [...document.querySelectorAll('.payment-checkbox')].map(el => el.value) : []"
                                       class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                            </th>
                            <th class="px-6 py-4 font-semibold uppercase tracking-wider">No. Pembayaran</th>
                            <th class="px-6 py-4 font-semibold uppercase tracking-wider">Anggota</th>
                            <th class="px-6 py-4 font-semibold uppercase tracking-wider">Pinjaman Ref.</th>
                            <th class="px-6 py-4 font-semibold uppercase tracking-wider">Tanggal Bayar</th>
                            <th class="px-6 py-4 font-semibold uppercase tracking-wider">Jumlah</th>
                            <th class="px-6 py-4 font-semibold uppercase tracking-wider">Metode</th>
                            <th class="px-6 py-4 font-semibold uppercase tracking-wider">Petugas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-4 py-4">
                                <input type="checkbox" 
                                       class="payment-checkbox rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                                       value="{{ $payment->id }}"
                                       x-model="selectedIds"
                                       @change="selectAll = selectedIds.length === {{ $payments->count() }}">
                            </td>
                            <td class="px-6 py-4 font-mono text-xs text-gray-500 dark:text-gray-400">{{ $payment->payment_number }}</td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $payment->loan->member->user->name }}</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $payment->loan->member->member_id }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('loans.show', $payment->loan) }}" class="text-primary-600 hover:underline text-xs font-mono">
                                    {{ $payment->loan->loan_number }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-gray-700 dark:text-gray-300">{{ $payment->payment_date->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 font-semibold text-green-600">
                                Rp {{ number_format($payment->amount, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="badge badge-gray">
                                    {{ $payment->payment_method_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-500 dark:text-gray-400">
                                {{ $payment->receiver->name ?? '-' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-12">
                                 <div class="empty-state">
                                    <svg class="empty-state-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                    </svg>
                                    <h3 class="empty-state-title">Belum Ada Pembayaran</h3>
                                    <p class="empty-state-text">
                                        Belum ada data angsuran yang tercatat.
                                    </p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($payments->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $payments->links() }}
            </div>
            @endif
        </div>
    </div>
    @else
    <!-- Non-admin view without checkboxes -->
    <div class="glass-card-solid overflow-hidden">
        <div class="table-scroll-container">
            <table class="table-modern w-full">
                <thead class="sticky top-0 bg-gray-50 dark:bg-gray-800 z-10">
                    <tr class="text-gray-700 dark:text-gray-300">
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider">No. Pembayaran</th>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider">Anggota</th>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider">Pinjaman Ref.</th>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider">Tanggal Bayar</th>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider">Jumlah</th>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider">Metode</th>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider">Petugas</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-6 py-4 font-mono text-xs text-gray-500 dark:text-gray-400">{{ $payment->payment_number }}</td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="font-medium text-gray-900 dark:text-white">{{ $payment->loan->member->user->name }}</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $payment->loan->member->member_id }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('loans.show', $payment->loan) }}" class="text-primary-600 hover:underline text-xs font-mono">
                                {{ $payment->loan->loan_number }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-gray-700 dark:text-gray-300">{{ $payment->payment_date->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 font-semibold text-green-600">
                            Rp {{ number_format($payment->amount, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="badge badge-gray">
                                {{ $payment->payment_method_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-xs text-gray-500 dark:text-gray-400">
                            {{ $payment->receiver->name ?? '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-12">
                             <div class="empty-state">
                                <svg class="empty-state-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                </svg>
                                <h3 class="empty-state-title">Belum Ada Pembayaran</h3>
                                <p class="empty-state-text">
                                    Belum ada data angsuran yang tercatat.
                                </p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($payments->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $payments->links() }}
        </div>
        @endif
    </div>
    @endif
@endsection
