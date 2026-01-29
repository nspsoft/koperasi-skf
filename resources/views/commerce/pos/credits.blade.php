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
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.credit_report.unpaid_count') }}</p>
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
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.credit_report.total_unpaid') }}</p>
                    <p class="text-2xl font-bold text-red-600">Rp {{ number_format($totalPending, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        <div class="glass-card-solid p-5">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                    <span class="text-2xl">✅</span>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.credit_report.total_paid') }}</p>
                    <p class="text-2xl font-bold text-green-600">Rp {{ number_format($totalPaid, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="glass-card-solid p-4 mb-6">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
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

    <!-- Credits Table -->
    <div class="glass-card-solid overflow-hidden">
        <!-- Mobile View (Cards) -->
        <div class="md:hidden space-y-4 p-4">
            @forelse($credits as $credit)
            <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4 border border-gray-100 dark:border-gray-700 shadow-sm">
                <!-- Header: Invoice & Status -->
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <span class="font-mono text-sm font-bold text-primary-600 block">{{ $credit->invoice_number }}</span>
                        <div class="text-[10px] text-gray-500 mt-1">{{ $credit->created_at->format('d/m/Y H:i') }}</div>
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
                    <div class="text-xl font-black text-gray-900 dark:text-white">
                        Rp {{ number_format($credit->total_amount, 0, ',', '.') }}
                    </div>
                </div>

                <!-- Action Button -->
                @if($credit->status !== 'completed')
                    <button onclick="openPaymentModal({{ $credit->id }}, '{{ $credit->invoice_number }}', {{ $credit->total_amount }}, '{{ addslashes($credit->user->name ?? 'Unknown') }}')" 
                            class="w-full btn-primary py-2.5 flex justify-center items-center gap-2 text-sm">
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
                            <span class="text-lg font-bold text-gray-900 dark:text-white">Rp {{ number_format($credit->total_amount, 0, ',', '.') }}</span>
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
                                <button onclick="openPaymentModal({{ $credit->id }}, '{{ $credit->invoice_number }}', {{ $credit->total_amount }}, '{{ addslashes($credit->user->name ?? 'Unknown') }}')" 
                                        class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
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
                        <td colspan="7" class="text-center py-12 text-gray-500">
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

    <!-- Payment Modal -->
    <div id="paymentModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closePaymentModal()"></div>
            <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md mx-auto transform transition-all">
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
                        
                        <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-4 mb-5 space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400">{{ __('messages.credit_report.invoice') }}</span>
                                <span id="modalInvoice" class="font-mono font-semibold text-gray-900 dark:text-white"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400">{{ __('messages.credit_report.member') }}</span>
                                <span id="modalMember" class="font-medium text-gray-900 dark:text-white"></span>
                            </div>
                            <div class="flex justify-between pt-2 border-t border-gray-200 dark:border-gray-700">
                                <span class="text-lg font-bold text-gray-900 dark:text-white">{{ __('messages.credit_report.total_bayar') }}</span>
                                <span id="modalAmount" class="text-lg font-bold text-green-600"></span>
                            </div>
                        </div>

                        <div class="mb-5">
                            <label class="form-label mb-2">{{ __('messages.credit_report.pilih_metode') }}</label>
                            <div class="grid grid-cols-3 gap-3">
                                <label class="cursor-pointer">
                                    <input type="radio" name="payment_method" value="cash" class="sr-only peer" checked>
                                    <div class="p-4 text-center border-2 rounded-xl peer-checked:border-green-500 peer-checked:bg-green-50 dark:peer-checked:bg-green-900/20 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <span class="text-2xl block mb-1">💵</span>
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.pos.cash') }}</span>
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="payment_method" value="transfer" class="sr-only peer">
                                    <div class="p-4 text-center border-2 rounded-xl peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <span class="text-2xl block mb-1">🏦</span>
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Transfer</span>
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="payment_method" value="saldo" class="sr-only peer">
                                    <div class="p-4 text-center border-2 rounded-xl peer-checked:border-purple-500 peer-checked:bg-purple-50 dark:peer-checked:bg-purple-900/20 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <span class="text-2xl block mb-1">💳</span>
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.pos.balance') }}</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="form-label">{{ __('messages.shu_calculator.notes') }} ({{ __('messages.shu_calculator.notes_placeholder') }})</label>
                            <textarea name="notes" rows="2" class="form-input" placeholder="{{ __('messages.credit_report.placeholder_notes') }}"></textarea>
                        </div>
                    </div>
                    <div class="flex gap-3 p-5 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 rounded-b-2xl">
                        <button type="button" onclick="closePaymentModal()" class="flex-1 btn-secondary py-2.5">{{ __('messages.cancel') }}</button>
                        <button type="submit" class="flex-1 btn-primary py-2.5">✅ {{ __('messages.credit_report.confirm_success') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function openPaymentModal(id, invoice, amount, member) {
            document.getElementById('paymentForm').action = '/pos/credits/' + id + '/pay';
            document.getElementById('modalInvoice').textContent = invoice;
            document.getElementById('modalMember').textContent = member;
            document.getElementById('modalAmount').textContent = 'Rp ' + amount.toLocaleString('id-ID');
            document.getElementById('paymentModal').classList.remove('hidden');
        }

        function closePaymentModal() {
            document.getElementById('paymentModal').classList.add('hidden');
        }
    </script>
    @endpush
@endsection
