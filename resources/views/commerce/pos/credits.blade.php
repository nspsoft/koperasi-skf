@extends('layouts.app')

@section('title', __('messages.credit_report.title'))

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ __('messages.credit_report.header') }}</h1>
            <p class="page-subtitle">{{ __('messages.credit_report.subtitle') }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('pos.index') }}" class="btn-secondary">
                🛒 {{ __('messages.sidebar.pos') }}
            </a>
            <a href="{{ route('pos.history') }}" class="btn-secondary">
                📋 {{ __('messages.sidebar.sales_history') }}
            </a>
            @if($totalPending > 0)
            <form action="{{ route('pos.credits.remind-all') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengirim email tagihan ke semua anggota yang berhutang?');">
                @csrf
                <button type="submit" class="btn-primary flex items-center gap-2">
                    📧 Kirim Tagihan Massal
                </button>
            </form>
            @endif
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="glass-card-solid p-5">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center">
                    <span class="text-2xl">📋</span>
                </div>
                <div>
                    <div class="flex items-center gap-1.5">
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.credit_report.unpaid_count') }}</p>
                        @if(!empty($isFiltered))
                            <span class="px-1.5 py-0.2 rounded text-[10px] bg-orange-100 dark:bg-orange-950/50 text-orange-700 dark:text-orange-300 font-semibold">Filter</span>
                        @endif
                    </div>
                    <p class="text-2xl font-bold text-orange-600">{{ $pendingCount }}</p>
                    <p class="text-xs text-gray-400">{{ __('messages.shu.transactions') }}</p>
                </div>
            </div>
        </div>
        <div class="glass-card-solid p-5">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                    <span class="text-2xl">💰</span>
                </div>
                <div>
                    <div class="flex items-center gap-1.5">
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.credit_report.total_unpaid') }}</p>
                        @if(!empty($isFiltered))
                            <span class="px-1.5 py-0.2 rounded text-[10px] bg-red-100 dark:bg-red-950/50 text-red-700 dark:text-red-300 font-semibold">Filter</span>
                        @endif
                    </div>
                    <p class="text-2xl font-bold text-red-600">Rp {{ number_format($totalPending, 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-400">Sisa belum lunas</p>
                </div>
            </div>
        </div>
        <div class="glass-card-solid p-5">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                    <span class="text-2xl">✅</span>
                </div>
                <div>
                    <div class="flex items-center gap-1.5">
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.credit_report.total_paid') }}</p>
                        @if(!empty($isFiltered))
                            <span class="px-1.5 py-0.2 rounded text-[10px] bg-green-100 dark:bg-green-950/50 text-green-700 dark:text-green-300 font-semibold">Filter</span>
                        @endif
                    </div>
                    <p class="text-2xl font-bold text-green-600">Rp {{ number_format($totalPaid, 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-400">Tagihan terselesaikan</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="glass-card-solid p-4 mb-6">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div class="min-w-[220px] flex-1">
                <label class="form-label text-sm">{{ __('messages.credit_report.filter_member') ?? 'Nama / ID Member' }}</label>
                <div class="relative">
                    <input type="text" name="member_search" list="members-list" 
                           value="{{ request('member_search', request('search')) }}" 
                           placeholder="{{ __('messages.credit_report.placeholder_member') ?? 'Cari nama atau ID member...' }}" 
                           class="form-input py-2 pl-9 pr-8 w-full" autocomplete="off">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">👤</span>
                    @if(request('member_search') || request('search'))
                        <a href="{{ request()->fullUrlWithQuery(['member_search' => null, 'search' => null, 'page' => 1]) }}" 
                           class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200" 
                           title="Hapus filter member">
                            ✕
                        </a>
                    @endif
                </div>
                @if(isset($members) && $members->isNotEmpty())
                <datalist id="members-list">
                    @foreach($members as $m)
                        <option value="{{ $m->name }}">{{ $m->name }} ({{ $m->member->member_id ?? '-' }})</option>
                    @endforeach
                </datalist>
                @endif
            </div>
            <div class="min-w-[150px]">
                <label class="form-label text-sm">{{ __('messages.credit_report.status') }}</label>
                <select name="status" class="form-input py-2 w-full">
                    <option value="">{{ __('messages.credit_report.belum_lunas') }}</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>{{ __('messages.credit_report.lunas') }}</option>
                    <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>{{ __('messages.pos.all_types') ?? 'Semua' }}</option>
                </select>
            </div>
            <div class="min-w-[150px]">
                <label class="form-label text-sm">{{ __('messages.credit_report.dari_tanggal') }}</label>
                <input type="date" name="from_date" value="{{ request('from_date') }}" class="form-input py-2 w-full">
            </div>
            <div class="min-w-[150px]">
                <label class="form-label text-sm">{{ __('messages.credit_report.sampai_tanggal') }}</label>
                <input type="date" name="to_date" value="{{ request('to_date') }}" class="form-input py-2 w-full">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn-primary py-2 px-4">
                    🔍 {{ __('messages.shop_page.search_btn') ?? 'Filter' }}
                </button>
                <a href="{{ route('pos.credits') }}" class="btn-secondary py-2 px-4">{{ __('messages.products_page.reset_filter') ?? 'Reset' }}</a>
            </div>
        </form>
    </div>

    <!-- Bulk Pay Quick Banner (Appears when unpaid credits exist in current filter) -->
    @if(isset($unpaidCreditsForBulk) && $unpaidCreditsForBulk->isNotEmpty())
        @php
            $bulkTotalUnpaid = $unpaidCreditsForBulk->sum(fn($t) => max(0, (float)$t->total_amount - (float)$t->paid_amount));
        @endphp
        <div class="mb-6 p-4 sm:p-5 rounded-2xl bg-gradient-to-r from-amber-500/10 via-primary-500/10 to-emerald-500/10 border border-primary-200 dark:border-primary-900/50 flex flex-col md:flex-row items-start md:items-center justify-between gap-4 shadow-sm">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-xl bg-primary-100 dark:bg-primary-900/30 text-primary-600 flex items-center justify-center text-2xl flex-shrink-0">
                    ⚡
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white">
                            @if($bulkMember)
                                Tagihan Member: <span class="text-primary">{{ $bulkMember->name }}</span> ({{ $bulkMember->member->member_id ?? '-' }})
                            @else
                                Tagihan Kredit Belum Lunas (Sesuai Filter)
                            @endif
                        </h3>
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-300">
                            {{ $unpaidCreditsForBulk->count() }} Nota
                        </span>
                    </div>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                        Total Sisa Tagihan: <span class="font-bold text-red-600 dark:text-red-400 font-mono">Rp {{ number_format($bulkTotalUnpaid, 0, ',', '.') }}</span>
                        <span class="hidden sm:inline">• Klik tombol di samping untuk meninjau resume & melunasi sekaligus.</span>
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2 w-full md:w-auto">
                <button type="button" 
                        onclick="openBulkPayFromFiltered()" 
                        class="btn-primary py-2.5 px-4 text-xs font-bold flex items-center justify-center gap-2 w-full md:w-auto shadow-sm">
                    <span>⚡ Lunasi Semua Tagihan (Bulk Pay)</span>
                </button>
            </div>
        </div>
    @endif

    <!-- Floating Bulk Selection Bar -->
    <div id="bulkActionBar" class="fixed bottom-6 left-1/2 -translate-x-1/2 z-40 hidden bg-gray-900/95 dark:bg-gray-800/95 text-white px-5 py-3 rounded-2xl shadow-2xl backdrop-blur-md border border-gray-700 items-center gap-4 transition-all">
        <span class="text-xs sm:text-sm">
            <strong id="selectedCount" class="text-primary-400 font-bold">0</strong> tagihan terpilih 
            (<span id="selectedTotal" class="font-mono font-bold text-emerald-400">Rp 0</span>)
        </span>
        <button type="button" onclick="openBulkPayFromSelection()" class="btn-primary py-1.5 px-3.5 text-xs font-bold flex items-center gap-1.5 shadow">
            <span>⚡ Lunasi Terpilih</span>
        </button>
        <button type="button" onclick="clearSelection()" class="text-xs text-gray-400 hover:text-white underline">
            Batal
        </button>
    </div>

    <!-- Credits Table -->
    <div class="glass-card-solid overflow-hidden">
        <!-- Mobile View (Cards) -->
        <div class="md:hidden space-y-4 p-4">
            @forelse($credits as $credit)
            <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4 border border-gray-100 dark:border-gray-700 shadow-sm relative">
                <!-- Header: Checkbox, Invoice & Status -->
                <div class="flex justify-between items-start mb-3">
                    <div class="flex items-start gap-2.5">
                        @if($credit->status !== 'completed')
                            <input type="checkbox" 
                                   class="credit-checkbox mt-0.5 rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500"
                                   value="{{ $credit->id }}"
                                   data-id="{{ $credit->id }}"
                                   data-invoice="{{ $credit->invoice_number }}"
                                   data-date="{{ $credit->created_at->format('d/m/Y H:i') }}"
                                   data-member="{{ $credit->user->name ?? '-' }}"
                                   data-member-id="{{ $credit->user->member->member_id ?? '-' }}"
                                   data-amount="{{ max(0, (float)$credit->total_amount - (float)$credit->paid_amount) }}"
                                   onchange="updateSelectedCount()">
                        @endif
                        <div>
                            <span class="font-mono text-sm font-bold text-primary-600 block">{{ $credit->invoice_number }}</span>
                            <div class="text-[10px] text-gray-500 mt-0.5">{{ $credit->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                    @if($credit->status !== 'completed')
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-[10px] font-bold bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400 uppercase tracking-wide">
                            ⏳ Belum Lunas
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-[10px] font-bold bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 uppercase tracking-wide">
                            ✓ Lunas
                        </span>
                    @endif
                </div>

                <!-- Member Info -->
                <div class="flex items-center gap-3 mb-3 p-2 bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700">
                     @if($credit->user)
                        <div class="w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900/30 text-primary-600 flex items-center justify-center font-bold text-xs">
                            {{ strtoupper(substr($credit->user->name, 0, 1)) }}
                        </div>
                        <div class="overflow-hidden">
                            <div class="font-bold text-gray-900 dark:text-white text-sm truncate">{{ $credit->user->name }}</div>
                            <div class="text-[10px] text-gray-500">ID: {{ $credit->user->member->member_id ?? '-' }}</div>
                        </div>
                    @else
                        <span class="text-gray-400 text-sm">-</span>
                    @endif
                </div>

                <!-- Total & Items -->
                <div class="flex justify-between items-end mb-4">
                    <div class="text-xs text-gray-500">
                        Total {{ $credit->items->count() }} Items
                    </div>
                    <div class="text-xl font-black text-gray-900 dark:text-white font-mono">
                        Rp {{ number_format($credit->total_amount, 0, ',', '.') }}
                    </div>
                </div>

                <!-- Action Button -->
                @if($credit->status !== 'completed')
                    <button onclick="openPaymentModal({{ $credit->id }}, '{{ $credit->invoice_number }}', {{ $credit->total_amount - $credit->paid_amount }}, '{{ addslashes($credit->user->name ?? 'Unknown') }}')" 
                            class="w-full btn-primary py-2.5 flex justify-center items-center gap-2 text-sm font-semibold">
                        💰 Lunasi Sekarang
                    </button>
                @endif
            </div>
            @empty
            <div class="text-center py-10 text-gray-400">
                <p>Tidak ada data tagihan.</p>
            </div>
            @endforelse
        </div>

        <!-- Desktop View (Table) -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="w-10 px-3 py-3 text-center">
                            <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll(this)" 
                                   class="rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500"
                                   title="Pilih Semua Tagihan Belum Lunas">
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.credit_report.invoice') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.credit_report.tanggal') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.credit_report.member') }}</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.credit_report.total') }}</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.credit_report.status') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.credit_report.kasir') }}</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider w-28">{{ __('messages.credit_report.aksi') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($credits as $credit)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                        <td class="px-3 py-4 text-center">
                            @if($credit->status !== 'completed')
                                <input type="checkbox" 
                                       class="credit-checkbox rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500"
                                       value="{{ $credit->id }}"
                                       data-id="{{ $credit->id }}"
                                       data-invoice="{{ $credit->invoice_number }}"
                                       data-date="{{ $credit->created_at->format('d/m/Y H:i') }}"
                                       data-member="{{ $credit->user->name ?? '-' }}"
                                       data-member-id="{{ $credit->user->member->member_id ?? '-' }}"
                                       data-amount="{{ max(0, (float)$credit->total_amount - (float)$credit->paid_amount) }}"
                                       onchange="updateSelectedCount()">
                            @else
                                <span class="text-gray-300 dark:text-gray-600 text-xs">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            <span class="font-mono text-sm font-semibold text-primary-600">{{ $credit->invoice_number }}</span>
                            <div class="text-xs text-gray-400 mt-0.5">{{ $credit->items->count() }} {{ __('messages.credit_report.item_unit') }}</div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $credit->created_at->format('d/m/Y') }}</div>
                            <div class="text-xs text-gray-500">{{ $credit->created_at->format('H:i') }} WIB</div>
                        </td>
                        <td class="px-4 py-4">
                            @if($credit->user)
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-primary-100 dark:bg-primary-900/30 text-primary-600 flex items-center justify-center font-bold text-sm">
                                        {{ strtoupper(substr($credit->user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-white text-sm">{{ $credit->user->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $credit->user->member->member_id ?? '-' }}</div>
                                    </div>
                                </div>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-right">
                            <span class="text-lg font-bold text-gray-900 dark:text-white font-mono">Rp {{ number_format($credit->total_amount, 0, ',', '.') }}</span>
                        </td>
                        <td class="px-4 py-4 text-center">
                            @if($credit->status !== 'completed')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400">
                                    ⏳ {{ __('messages.credit_report.belum_lunas') }}
                                </span>
                                @if($credit->status !== 'credit')
                                    <div class="text-[10px] text-gray-400 mt-1 uppercase">{{ $credit->status }}</div>
                                @endif
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                    ✓ {{ __('messages.credit_report.lunas') }}
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ $credit->cashier->name ?? '-' }}</span>
                        </td>
                        <td class="px-4 py-4 text-center">
                            @if($credit->status !== 'completed')
                                <button onclick="openPaymentModal({{ $credit->id }}, '{{ $credit->invoice_number }}', {{ $credit->total_amount - $credit->paid_amount }}, '{{ addslashes($credit->user->name ?? 'Unknown') }}')" 
                                        class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                                    💰 {{ __('messages.credit_report.lunasi') }}
                                </button>
                            @else
                                <span class="text-green-500" title="{{ $credit->notes }}">
                                    <svg class="w-6 h-6 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-12 text-gray-500">
                            <span class="text-5xl block mb-3">📝</span>
                            <p class="text-lg font-medium">{{ __('messages.credit_report.tidak_ada_data') }}</p>
                            <p class="text-sm text-gray-400 mt-1">{{ __('messages.credit_report.tidak_ada_data_desc') }}</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($credits->hasPages())
        <div class="p-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
            {{ $credits->withQueryString()->links() }}
        </div>
        @endif
    </div>

    <!-- Single Payment Modal -->
    <div id="paymentModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" onclick="closePaymentModal()"></div>
            <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md mx-auto transform transition-all border border-gray-100 dark:border-gray-700 overflow-hidden">
                <form id="paymentForm" method="POST">
                    @csrf
                    <div class="p-6">
                        <div class="flex items-center gap-3 mb-5">
                            <div class="w-12 h-12 rounded-xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                                <span class="text-2xl">💰</span>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('messages.credit_report.pelunasan_title') }}</h3>
                                <p class="text-sm text-gray-500">{{ __('messages.credit_report.pelunasan_subtitle') }}</p>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-4 mb-5 space-y-2 border border-gray-200 dark:border-gray-700">
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400 text-xs">{{ __('messages.credit_report.invoice') }}</span>
                                <span id="modalInvoice" class="font-mono font-semibold text-gray-900 dark:text-white text-xs"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400 text-xs">{{ __('messages.credit_report.member') }}</span>
                                <span id="modalMember" class="font-medium text-gray-900 dark:text-white text-xs"></span>
                            </div>
                            <div class="flex justify-between pt-2 border-t border-gray-200 dark:border-gray-700">
                                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ __('messages.credit_report.total_bayar') }}</span>
                                <span id="modalAmount" class="text-base font-bold text-green-600 font-mono"></span>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-xs mb-1">Jumlah Bayar (Rp)</label>
                            <input type="number" name="amount" id="modalAmountInput" class="form-input text-sm" min="1" step="1">
                            <p class="text-[11px] text-gray-500 mt-1">Kosongkan atau isi sesuai sisa tagihan untuk melunasi.</p>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-xs mb-2">{{ __('messages.credit_report.pilih_metode') }}</label>
                            <div class="grid grid-cols-2 gap-2.5">
                                <label class="cursor-pointer">
                                    <input type="radio" name="payment_method" value="payroll" class="sr-only peer" checked>
                                    <div class="p-3 text-center border-2 rounded-xl peer-checked:border-primary-500 peer-checked:bg-primary-50 dark:peer-checked:bg-primary-950/30 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <span class="text-xl block mb-1">💼</span>
                                        <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">Potong Gaji</span>
                                        <span class="text-[10px] text-gray-400 block">Payroll</span>
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="payment_method" value="cash" class="sr-only peer">
                                    <div class="p-3 text-center border-2 rounded-xl peer-checked:border-emerald-500 peer-checked:bg-emerald-50 dark:peer-checked:bg-emerald-950/30 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <span class="text-xl block mb-1">💵</span>
                                        <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">{{ __('messages.pos.cash') }}</span>
                                        <span class="text-[10px] text-gray-400 block">Kasir POS</span>
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="payment_method" value="transfer" class="sr-only peer">
                                    <div class="p-3 text-center border-2 rounded-xl peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-950/30 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <span class="text-xl block mb-1">🏦</span>
                                        <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">Transfer</span>
                                        <span class="text-[10px] text-gray-400 block">Bank</span>
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="payment_method" value="saldo" class="sr-only peer">
                                    <div class="p-3 text-center border-2 rounded-xl peer-checked:border-purple-500 peer-checked:bg-purple-50 dark:peer-checked:bg-purple-950/30 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <span class="text-xl block mb-1">💳</span>
                                        <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">{{ __('messages.pos.balance') }}</span>
                                        <span class="text-[10px] text-gray-400 block">Sukarela</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="form-label text-xs mb-1">{{ __('messages.shu_calculator.notes') }} (Opsional)</label>
                            <input type="text" name="notes" class="form-input text-xs" placeholder="{{ __('messages.credit_report.placeholder_notes') }}">
                        </div>
                    </div>
                    <div class="flex gap-3 p-5 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 rounded-b-2xl">
                        <button type="button" onclick="closePaymentModal()" class="flex-1 btn-secondary py-2 text-xs font-semibold">{{ __('messages.cancel') }}</button>
                        <button type="submit" class="flex-1 btn-primary py-2 text-xs font-bold">✅ {{ __('messages.credit_report.confirm_success') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bulk Payment Resume Modal -->
    <div id="bulkPaymentModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="bulk-modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" onclick="closeBulkPaymentModal()"></div>
            <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-2xl mx-auto transform transition-all overflow-hidden border border-gray-100 dark:border-gray-700">
                <form id="bulkPaymentForm" action="{{ route('pos.credits.bulk-pay') }}" method="POST">
                    @csrf
                    <!-- Header -->
                    <div class="p-5 sm:p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-start bg-gray-50/50 dark:bg-gray-900/50">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-xl bg-primary-100 dark:bg-primary-900/30 text-primary-600 flex items-center justify-center text-xl flex-shrink-0">
                                📋
                            </div>
                            <div>
                                <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white">Resume Pelunasan Tagihan Massal</h3>
                                <p class="text-xs text-gray-500">Tinjau rincian nota yang akan dilunasi sebelum menyimpan ke sistem</p>
                            </div>
                        </div>
                        <button type="button" onclick="closeBulkPaymentModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 p-1">
                            ✕
                        </button>
                    </div>

                    <div class="p-5 sm:p-6 max-h-[60vh] overflow-y-auto space-y-4">
                        <!-- Member Info Summary -->
                        <div class="p-3.5 rounded-xl bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 flex flex-wrap items-center justify-between gap-3 text-xs">
                            <div>
                                <p class="text-gray-500 dark:text-gray-400">Anggota / Penerima Tagihan</p>
                                <p id="bulkMemberName" class="font-bold text-sm text-gray-900 dark:text-white mt-0.5">-</p>
                            </div>
                            <div class="text-right">
                                <p class="text-gray-500 dark:text-gray-400">Jumlah Nota</p>
                                <p id="bulkInvoiceCount" class="font-bold text-sm text-primary mt-0.5">0 Nota</p>
                            </div>
                        </div>

                        <!-- Invoices Table Preview -->
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">Rincian Nota Terpilih</p>
                            <div class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden max-h-56 overflow-y-auto">
                                <table class="w-full text-xs">
                                    <thead class="bg-gray-50 dark:bg-gray-900 text-gray-600 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700 sticky top-0">
                                        <tr>
                                            <th class="py-2 px-3 text-left">No</th>
                                            <th class="py-2 px-3 text-left">Invoice</th>
                                            <th class="py-2 px-3 text-left">Tanggal</th>
                                            <th class="py-2 px-3 text-left">Member</th>
                                            <th class="py-2 px-3 text-right">Sisa Tagihan</th>
                                        </tr>
                                    </thead>
                                    <tbody id="bulkInvoicesTableBody" class="divide-y divide-gray-100 dark:divide-gray-800">
                                        <!-- Dynamically generated rows -->
                                    </tbody>
                                    <tfoot class="bg-gray-50 dark:bg-gray-900 font-bold border-t border-gray-200 dark:border-gray-700 sticky bottom-0">
                                        <tr>
                                            <td colspan="4" class="py-2.5 px-3 text-right uppercase tracking-wider">TOTAL PELUNASAN:</td>
                                            <td id="bulkTotalAmountCell" class="py-2.5 px-3 text-right text-sm text-emerald-600 dark:text-emerald-400 font-black font-mono">Rp 0</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <!-- Hidden Inputs for Transaction IDs -->
                        <div id="bulkHiddenInputsContainer"></div>

                        <!-- Payment Method Selector -->
                        <div>
                            <label class="form-label text-xs mb-2">Pilih Metode Pelunasan <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                                <label class="cursor-pointer">
                                    <input type="radio" name="payment_method" value="payroll" class="sr-only peer" checked>
                                    <div class="p-3 text-center border-2 rounded-xl peer-checked:border-primary-500 peer-checked:bg-primary-50 dark:peer-checked:bg-primary-950/30 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                        <span class="text-xl block mb-1">💼</span>
                                        <span class="text-xs font-bold text-gray-800 dark:text-gray-200 block">Potong Gaji</span>
                                        <span class="text-[10px] text-gray-400">Payroll</span>
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="payment_method" value="transfer" class="sr-only peer">
                                    <div class="p-3 text-center border-2 rounded-xl peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-950/30 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                        <span class="text-xl block mb-1">🏦</span>
                                        <span class="text-xs font-bold text-gray-800 dark:text-gray-200 block">Transfer</span>
                                        <span class="text-[10px] text-gray-400">Bank</span>
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="payment_method" value="cash" class="sr-only peer">
                                    <div class="p-3 text-center border-2 rounded-xl peer-checked:border-emerald-500 peer-checked:bg-emerald-50 dark:peer-checked:bg-emerald-950/30 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                        <span class="text-xl block mb-1">💵</span>
                                        <span class="text-xs font-bold text-gray-800 dark:text-gray-200 block">Tunai</span>
                                        <span class="text-[10px] text-gray-400">Kasir POS</span>
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="payment_method" value="saldo" class="sr-only peer">
                                    <div class="p-3 text-center border-2 rounded-xl peer-checked:border-purple-500 peer-checked:bg-purple-50 dark:peer-checked:bg-purple-950/30 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                        <span class="text-xl block mb-1">💳</span>
                                        <span class="text-xs font-bold text-gray-800 dark:text-gray-200 block">Saldo</span>
                                        <span class="text-[10px] text-gray-400">Sukarela</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div>
                            <label class="form-label text-xs mb-1">Catatan Tambahan (Opsional)</label>
                            <input type="text" name="notes" class="form-input text-xs" placeholder="Contoh: Pelunasan potong gaji periode berjalan">
                        </div>
                    </div>

                    <!-- Footer Actions -->
                    <div class="flex items-center justify-between gap-3 p-4 sm:p-5 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                        <button type="button" onclick="closeBulkPaymentModal()" class="btn-secondary py-2 px-4 text-xs font-semibold">
                            Batal
                        </button>
                        <button type="submit" class="btn-primary py-2.5 px-5 text-xs font-bold flex items-center gap-2">
                            <span>✅ Konfirmasi & Simpan Pelunasan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Cache filtered unpaid items for instant bulk pay
        window.filteredUnpaidCredits = @json($unpaidCreditsForBulkJson ?? []);

        function openPaymentModal(id, invoice, amount, member) {
            document.getElementById('paymentForm').action = '/pos/credits/' + id + '/pay';
            document.getElementById('modalInvoice').textContent = invoice;
            document.getElementById('modalMember').textContent = member;
            document.getElementById('modalAmount').textContent = 'Rp ' + amount.toLocaleString('id-ID');
            document.getElementById('modalAmountInput').value = Math.round(amount);
            document.getElementById('paymentModal').classList.remove('hidden');
        }

        function closePaymentModal() {
            document.getElementById('paymentModal').classList.add('hidden');
        }

        function toggleSelectAll(master) {
            const checkboxes = document.querySelectorAll('.credit-checkbox');
            checkboxes.forEach(cb => cb.checked = master.checked);
            updateSelectedCount();
        }

        function updateSelectedCount() {
            const checkedBoxes = Array.from(document.querySelectorAll('.credit-checkbox:checked'));
            const bar = document.getElementById('bulkActionBar');
            const countLabel = document.getElementById('selectedCount');
            const totalLabel = document.getElementById('selectedTotal');

            if (checkedBoxes.length > 0) {
                let total = 0;
                checkedBoxes.forEach(cb => {
                    total += parseFloat(cb.dataset.amount || 0);
                });
                countLabel.textContent = checkedBoxes.length;
                totalLabel.textContent = 'Rp ' + Math.round(total).toLocaleString('id-ID');
                bar.classList.remove('hidden');
                bar.classList.add('flex');
            } else {
                bar.classList.add('hidden');
                bar.classList.remove('flex');
                const master = document.getElementById('selectAllCheckbox');
                if (master) master.checked = false;
            }
        }

        function clearSelection() {
            document.querySelectorAll('.credit-checkbox').forEach(cb => cb.checked = false);
            const master = document.getElementById('selectAllCheckbox');
            if (master) master.checked = false;
            updateSelectedCount();
        }

        function openBulkPayFromFiltered() {
            if (!window.filteredUnpaidCredits || window.filteredUnpaidCredits.length === 0) {
                alert('Tidak ada data tagihan belum lunas.');
                return;
            }
            showBulkResumeModal(window.filteredUnpaidCredits);
        }

        function openBulkPayFromSelection() {
            const checkedBoxes = Array.from(document.querySelectorAll('.credit-checkbox:checked'));
            if (checkedBoxes.length === 0) {
                alert('Pilih minimal satu tagihan untuk dilunasi.');
                return;
            }

            const items = checkedBoxes.map(cb => ({
                id: cb.dataset.id,
                invoice: cb.dataset.invoice,
                date: cb.dataset.date,
                member: cb.dataset.member,
                member_id: cb.dataset.memberId,
                amount: parseFloat(cb.dataset.amount || 0)
            }));

            showBulkResumeModal(items);
        }

        function showBulkResumeModal(items) {
            const tbody = document.getElementById('bulkInvoicesTableBody');
            const hiddenContainer = document.getElementById('bulkHiddenInputsContainer');
            const memberNameLabel = document.getElementById('bulkMemberName');
            const invoiceCountLabel = document.getElementById('bulkInvoiceCount');
            const totalAmountCell = document.getElementById('bulkTotalAmountCell');

            tbody.innerHTML = '';
            hiddenContainer.innerHTML = '';

            let total = 0;
            const uniqueMembers = new Set();

            items.forEach((item, index) => {
                total += item.amount;
                uniqueMembers.add(item.member);

                // Row in table
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-gray-50/50 dark:hover:bg-gray-800/50';
                tr.innerHTML = `
                    <td class="py-2 px-3 text-gray-400 text-center">${index + 1}</td>
                    <td class="py-2 px-3 font-mono font-semibold text-primary-600">${item.invoice}</td>
                    <td class="py-2 px-3 text-gray-500">${item.date}</td>
                    <td class="py-2 px-3 font-medium text-gray-900 dark:text-white">${item.member}</td>
                    <td class="py-2 px-3 text-right font-mono font-bold text-gray-900 dark:text-white">Rp ${Math.round(item.amount).toLocaleString('id-ID')}</td>
                `;
                tbody.appendChild(tr);

                // Hidden input
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'transaction_ids[]';
                input.value = item.id;
                hiddenContainer.appendChild(input);
            });

            // Member label
            if (uniqueMembers.size === 1) {
                memberNameLabel.textContent = Array.from(uniqueMembers)[0];
            } else if (uniqueMembers.size > 1) {
                memberNameLabel.textContent = `${uniqueMembers.size} Anggota Berbeda (${Array.from(uniqueMembers).slice(0, 2).join(', ')}${uniqueMembers.size > 2 ? '...' : ''})`;
            } else {
                memberNameLabel.textContent = '-';
            }

            invoiceCountLabel.textContent = `${items.length} Nota`;
            totalAmountCell.textContent = 'Rp ' + Math.round(total).toLocaleString('id-ID');

            document.getElementById('bulkPaymentModal').classList.remove('hidden');
        }

        function closeBulkPaymentModal() {
            document.getElementById('bulkPaymentModal').classList.add('hidden');
        }
    </script>
    @endpush
@endsection
