@extends('layouts.app')

@section('title', 'Rekonsiliasi Bank')

@section('content')
<div x-data="reconciliationData()">
    <!-- Page Header -->
    <div class="page-header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="page-title">Rekonsiliasi Bank</h1>
                <p class="page-subtitle">Cocokkan mutasi bank dengan pencatatan sistem</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('reconciliation.auto-match') }}" class="btn-secondary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    Auto Match
                </a>
                <button @click="showUploadModal = true" class="btn-primary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                    </svg>
                    Import Mutasi
                </button>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="glass-card p-6 bg-gradient-to-br from-orange-500/10 to-red-500/10 border-orange-200/20">
            <p class="text-sm text-gray-600 dark:text-gray-300">Belum Dicocokkan</p>
            <p class="text-3xl font-bold text-orange-600 dark:text-orange-400">{{ $pendingCount }} <span class="text-base font-normal text-gray-500">transaksi</span></p>
        </div>
        <div class="glass-card p-6 bg-gradient-to-br from-green-500/10 to-emerald-500/10 border-green-200/20">
            <p class="text-sm text-gray-600 dark:text-gray-300">Sudah Dicocokkan</p>
            <p class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $reconciledCount }} <span class="text-base font-normal text-gray-500">transaksi</span></p>
        </div>
    </div>

    <!-- Transaction List -->
    <div class="glass-card-solid overflow-hidden">
        <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
            <div class="flex gap-4">
                <a href="{{ route('reconciliation.index', ['status' => 'pending']) }}" 
                   class="pb-2 text-sm font-medium {{ $status == 'pending' ? 'text-primary-600 border-b-2 border-primary-600' : 'text-gray-500 hover:text-gray-700' }}">
                    Pending
                </a>
                <a href="{{ route('reconciliation.index', ['status' => 'reconciled']) }}" 
                   class="pb-2 text-sm font-medium {{ $status == 'reconciled' ? 'text-primary-600 border-b-2 border-primary-600' : 'text-gray-500 hover:text-gray-700' }}">
                    Reconciled
                </a>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 dark:bg-gray-800 text-xs uppercase text-gray-500 font-semibold">
                    <tr>
                        <th class="px-6 py-4">Tanggal (Bank)</th>
                        <th class="px-6 py-4">Keterangan (Bank)</th>
                        <th class="px-6 py-4 text-right">Nominal</th>
                        <th class="px-6 py-4">Status / Jurnal</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($bankTransactions as $trx)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors group">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $trx->transaction_date->format('d/m/Y') }}</div>
                            <div class="text-xs text-gray-500">{{ $trx->reference_number ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-700 dark:text-gray-300 max-w-xs break-words">{{ $trx->description }}</div>
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <span class="font-mono font-medium {{ $trx->type == 'credit' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $trx->type == 'credit' ? '+' : '-' }} {{ number_format($trx->amount, 0, ',', '.') }}
                            </span>
                             <div class="text-xs text-gray-400 uppercase tracking-wider">{{ $trx->type == 'credit' ? 'Masuk' : 'Keluar' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($trx->status == 'reconciled' && $trx->journalEntry)
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-green-500"></span>
                                    <div>
                                        <div class="text-xs font-bold text-gray-900 dark:text-white">{{ $trx->journalEntry->transaction_number }}</div>
                                        <div class="text-xs text-gray-500">{{ Str::limit($trx->journalEntry->description, 30) }}</div>
                                    </div>
                                </div>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200">
                                    Pending
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                             @if($trx->status == 'pending')
                                <button @click="openMatchModal({{ $trx }})" 
                                        class="btn-sm btn-outline-primary">
                                    Cocokkan
                                </button>
                             @elseif($trx->status == 'reconciled')
                                <form action="{{ route('reconciliation.unmatch', $trx->id) }}" method="POST" class="inline" onsubmit="return confirm('Batalkan pencocokan?')">
                                    @csrf
                                    <button type="submit" class="text-gray-400 hover:text-red-500 transition-colors" title="Batalkan Pencocokan">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </form>
                             @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                             <p class="mb-2">Tidak ada transaksi {{ $status }}</p>
                             @if($status == 'pending')
                             <button @click="showUploadModal = true" class="text-primary-600 hover:underline">Import Data Baru</button>
                             @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="p-4 border-t border-gray-100 dark:border-gray-700">
            {{ $bankTransactions->links() }}
        </div>
    </div>

    <!-- Upload Modal -->
    <div x-show="showUploadModal" style="display: none;" 
         class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showUploadModal" @click="showUploadModal = false" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showUploadModal" 
                 class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('reconciliation.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                            Import Mutasi Bank (Excel)
                        </h3>
                        <div class="mt-4">
                            <p class="text-sm text-gray-500 mb-4">
                                Upload file Excel (.xlsx) mutasi bank. Format kolom yang didukung:
                                <ul class="list-disc list-inside text-xs mt-1 mb-2">
                                    <li><b>Tanggal:</b> Wajib (DD/MM/YYYY)</li>
                                    <li><b>Keterangan:</b> Wajib</li>
                                    <li><b>Kredit / Masuk:</b> Nominal uang masuk (+)</li>
                                    <li><b>Debit / Keluar:</b> Nominal uang keluar (-)</li>
                                </ul>
                                <a href="{{ route('reconciliation.template') }}" class="text-primary-600 hover:text-primary-800 text-sm font-medium inline-flex items-center gap-1 mb-3">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4-4m0 0l-4 4m4-4v12"></path></svg>
                                    Download Contoh Template Excel
                                </a>
                            </p>
                            <input type="file" name="file" accept=".xlsx,.xls,.csv" required
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="btn-primary w-full sm:w-auto sm:ml-3">
                            Upload
                        </button>
                        <button type="button" @click="showUploadModal = false" class="btn-secondary w-full sm:w-auto sm:mt-0 mt-3">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Match Modal -->
    <div x-show="showMatchModal" style="display: none;" 
         class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showMatchModal" @click="showMatchModal = false" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showMatchModal" 
                 class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full">
                
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6">
                    <div class="mb-4">
                        <span class="text-xs font-semibold text-gray-500 uppercase">Transaksi Bank</span>
                        <div class="flex justify-between items-start mt-1 bg-gray-50 dark:bg-gray-700 p-3 rounded">
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white" x-text="selectedTrx?.transaction_date ? new Date(selectedTrx.transaction_date).toLocaleDateString('id-ID') : ''"></p>
                                <p class="text-sm text-gray-600 dark:text-gray-300" x-text="selectedTrx?.description"></p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-lg" :class="selectedTrx?.type == 'credit' ? 'text-green-600' : 'text-red-600'" 
                                   x-text="(selectedTrx?.type == 'credit' ? '+' : '-') + ' ' + formatRupiah(selectedTrx?.amount)"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Tabs -->
                    <div class="border-b border-gray-200 dark:border-gray-700 mb-4">
                        <nav class="-mb-px flex space-x-8">
                            <a href="#" @click.prevent="activeTab = 'create'" 
                               :class="activeTab === 'create' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                               class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                                Buat Jurnal Baru
                            </a>
                            <a href="#" @click.prevent="activeTab = 'match'" 
                               :class="activeTab === 'match' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                               class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                                Cari Existing
                            </a>
                        </nav>
                    </div>

                    <!-- Create Tab -->
                    <div x-show="activeTab === 'create'">
                        <form :action="'/accounting/reconciliation/' + selectedTrx?.id + '/create-journal'" method="POST">
                            @csrf
                            <div class="space-y-4">
                                <p class="text-sm text-gray-500">
                                    Buat jurnal baru otomatis. Akun Bank akan diisi sesuai tipe transaksi (Db/Cr). Pilih akun lawan.
                                </p>
                                <div>
                                    <label class="form-label">Akun Lawan / Contra Account</label>
                                    <select name="contra_account_id" class="form-input" required>
                                        <option value="">-- Pilih Akun --</option>
                                        @php
                                            $accounts = \App\Models\Account::where('code', '!=', '1102')->orderBy('code')->get();
                                        @endphp
                                        @foreach($accounts as $acc)
                                            <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="form-label">Keterangan</label>
                                    <input type="text" name="description" class="form-input" required :value="selectedTrx?.description">
                                </div>
                            </div>
                            <div class="mt-6 flex justify-end gap-3">
                                <button type="button" @click="showMatchModal = false" class="btn-secondary">Batal</button>
                                <button type="submit" class="btn-primary">Simpan Jurnal</button>
                            </div>
                        </form>
                    </div>

                    <!-- Match Tab -->
                    <div x-show="activeTab === 'match'">
                        <form :action="'/accounting/reconciliation/' + selectedTrx?.id + '/match'" method="POST">
                            @csrf
                             <div class="space-y-4">
                                <p class="text-sm text-gray-500">
                                    Masukkan ID Jurnal yang sudah ada di sistem. Fitur pencarian lanjutan belum tersedia.
                                </p>
                                <div>
                                    <label class="form-label">Journal Entry ID</label>
                                    <input type="number" name="journal_entry_id" class="form-input" placeholder="Contoh: 123" required>
                                </div>
                            </div>
                             <div class="mt-6 flex justify-end gap-3">
                                <button type="button" @click="showMatchModal = false" class="btn-secondary">Batal</button>
                                <button type="submit" class="btn-primary">Link Jurnal</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function reconciliationData() {
        return {
            showUploadModal: false,
            showMatchModal: false,
            selectedTrx: null,
            activeTab: 'create',
            
            openMatchModal(trx) {
                this.selectedTrx = trx;
                this.showMatchModal = true;
                this.activeTab = 'create';
            },
            
            formatRupiah(value) {
                if (!value) return '0';
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
            }
        }
    }
</script>
@endsection
