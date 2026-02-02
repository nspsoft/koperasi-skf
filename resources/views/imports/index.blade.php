@extends('layouts.app')

@section('title', __('messages.titles.import'))

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Import Data</h1>
            <p class="page-subtitle">Upload data dalam jumlah banyak menggunakan file Excel</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <p class="font-bold">{{ session('error') }}</p>
            
            @if(session('import_errors'))
                <div class="mt-3 max-h-60 overflow-y-auto border-t border-red-200 pt-3">
                    <p class="text-sm font-semibold mb-2">Detail Error:</p>
                    <ul class="list-disc list-inside text-sm space-y-1">
                        @foreach(session('import_errors') as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Import Members --}}
        <div class="glass-card-solid p-6">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold ml-3">Import Anggota</h3>
            </div>
            
            <form id="form-import-members" action="{{ route('import.members') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="form-label">File Excel (.xlsx, .xls)</label>
                    <input type="file" name="file" accept=".xlsx,.xls" class="form-input" required>
                    <p class="text-xs text-gray-500 mt-1">Kolom: nama, email, no_hp, id_anggota, nik, department, jabatan, jenis_kelamin, tanggal_bergabung</p>
                </div>
                
                <!-- Progress Bar -->
                <div id="progress-members" class="hidden mb-4">
                    <div class="flex justify-between text-sm mb-1">
                        <span class="status-text text-gray-600 dark:text-gray-400">Mengupload...</span>
                        <span class="progress-text font-bold text-primary-600">0%</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3 overflow-hidden">
                        <div class="progress-bar bg-gradient-to-r from-blue-500 to-primary-600 h-3 rounded-full transition-all duration-300" style="width: 0%"></div>
                    </div>
                </div>
                
                <button type="submit" class="btn-primary w-full mb-2 flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    Upload
                </button>
            </form>
            <a href="{{ route('import.template', 'members') }}" class="btn-secondary w-full text-center block">
                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Download Template
            </a>
        </div>

        {{-- Import Savings --}}
        <div class="glass-card-solid p-6">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold ml-3">Import Simpanan</h3>
            </div>
            
            <form id="form-import-savings" action="{{ route('import.savings') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="form-label">File Excel (.xlsx, .xls)</label>
                    <input type="file" name="file" accept=".xlsx,.xls" class="form-input" required>
                    <p class="text-xs text-gray-500 mt-1">Kolom: id_anggota, jenis, transaksi, jumlah, tanggal, keterangan (opsional)</p>
                </div>
                
                <!-- Progress Bar -->
                <div id="progress-savings" class="hidden mb-4">
                    <div class="flex justify-between text-sm mb-1">
                        <span class="status-text text-gray-600 dark:text-gray-400">Mengupload...</span>
                        <span class="progress-text font-bold text-green-600">0%</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3 overflow-hidden">
                        <div class="progress-bar bg-gradient-to-r from-green-500 to-emerald-600 h-3 rounded-full transition-all duration-300" style="width: 0%"></div>
                    </div>
                </div>
                
                <button type="submit" class="btn-primary w-full mb-2 flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    Upload
                </button>
            </form>
            <a href="{{ route('import.template', 'savings') }}" class="btn-secondary w-full text-center block">
                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Download Template
            </a>
        </div>

        {{-- Import Loans --}}
        <div class="glass-card-solid p-6">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 3.666V14h-6v-3.334H5V18h14v-7.334h-2.924M9 7V5a2 2 0 012-2h2a2 2 0 012 2v2m-6 0h6m-10 0h14"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold ml-3">Import Pinjaman</h3>
            </div>
            
            <form id="form-import-loans" action="{{ route('import.loans') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="form-label">File Excel (.xlsx, .xls)</label>
                    <input type="file" name="file" accept=".xlsx,.xls" class="form-input" required>
                    <p class="text-xs text-gray-500 mt-1">Kolom: id_anggota, no_pinjaman, jenis, jumlah, bunga, tenor, tujuan, status, tanggal_pengajuan</p>
                </div>
                
                <!-- Progress Bar -->
                <div id="progress-loans" class="hidden mb-4">
                    <div class="flex justify-between text-sm mb-1">
                        <span class="status-text text-gray-600 dark:text-gray-400">Mengupload...</span>
                        <span class="progress-text font-bold text-amber-600">0%</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3 overflow-hidden">
                        <div class="progress-bar bg-gradient-to-r from-amber-500 to-orange-600 h-3 rounded-full transition-all duration-300" style="width: 0%"></div>
                    </div>
                </div>
                
                <button type="submit" class="btn-primary w-full mb-2 flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    Upload
                </button>
            </form>
            <a href="{{ route('import.template', 'loans') }}" class="btn-secondary w-full text-center block">
                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Download Template
            </a>
        </div>

        {{-- Import Credit Payments --}}
        <div class="glass-card-solid p-6">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold ml-3">Pelunasan Kredit Mart</h3>
            </div>
            
            <form id="form-import-credits" action="{{ route('import.credit_payments') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="form-label">File Excel (.xlsx, .xls)</label>
                    <input type="file" name="file" accept=".xlsx,.xls" class="form-input" required>
                    <p class="text-xs text-gray-500 mt-1">Kolom: no_invoice, keterangan (opsional)</p>
                    <p class="text-xs text-purple-600 dark:text-purple-400 mt-1">💡 Template berisi daftar kredit yang belum lunas</p>
                </div>
                
                <!-- Progress Bar -->
                <div id="progress-credits" class="hidden mb-4">
                    <div class="flex justify-between text-sm mb-1">
                        <span class="status-text text-gray-600 dark:text-gray-400">Mengupload...</span>
                        <span class="progress-text font-bold text-purple-600">0%</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3 overflow-hidden">
                        <div class="progress-bar bg-gradient-to-r from-purple-500 to-pink-600 h-3 rounded-full transition-all duration-300" style="width: 0%"></div>
                    </div>
                </div>
                
                <button type="submit" class="btn-primary w-full mb-2 flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    Upload Pelunasan
                </button>
            </form>
            <a href="{{ route('import.template', 'credit_payments') }}" class="btn-secondary w-full text-center block">
                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Download Daftar Kredit
            </a>
        </div>
    </div>

    {{-- Instructions --}}
    <div class="glass-card-solid p-6 mt-6">
        <h3 class="text-lg font-bold mb-4">📋 Panduan Import Excel</h3>
        <div class="prose dark:prose-invert max-w-none">
            <p><strong>Format File:</strong> Pastikan file Excel memiliki heading row di baris pertama</p>
            
            <p><strong>Nilai yang Valid:</strong></p>
            <ul class="list-disc ml-5 space-y-1">
                <li><strong>role:</strong> admin, manager, member (default: member)</li>
                <li><strong>jenis (simpanan):</strong> pokok, wajib, sukarela</li>
                <li><strong>transaksi:</strong> setoran, penarikan</li>
                <li><strong>jenis (pinjaman):</strong> regular, emergency, education, special</li>
                <li><strong>jenis_kelamin:</strong> laki-laki, perempuan</li>
                <li><strong>status:</strong> pending, approved, active, completed</li>
            </ul>
            
            <p class="text-sm text-gray-500 mt-4"><strong>Tips:</strong> Export data yang sudah ada untuk melihat format yang benar!</p>
        </div>
    </div>

    {{-- Generate Journals Section --}}
    <div class="glass-card-solid p-6 mt-6 border-2 border-blue-200 dark:border-blue-900/50">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-bold text-blue-600 dark:text-blue-400">📊 Generate Jurnal Akuntansi</h3>
                <p class="text-sm text-gray-500">Buat jurnal otomatis untuk data yang diimport (agar muncul di laporan keuangan)</p>
            </div>
        </div>

        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4 mb-4">
            <div class="flex gap-3">
                <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="text-sm text-blue-700 dark:text-blue-300">
                    <p class="font-medium">Mengapa perlu Generate Jurnal?</p>
                    <p>Data yang diimport via Excel tidak otomatis membuat jurnal akuntansi. Fitur ini akan membuat jurnal untuk semua simpanan/pinjaman yang belum memiliki jurnal, agar data tersebut muncul di laporan keuangan (Neraca, Laba Rugi, dll).</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Generate Savings Journals --}}
            <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-xl">💰</span>
                    <div>
                        <h4 class="font-semibold text-gray-900 dark:text-white">Generate Jurnal Simpanan</h4>
                        <p class="text-xs text-gray-500">Untuk semua simpanan tanpa jurnal</p>
                    </div>
                </div>
                <form action="{{ route('import.generate.savings') }}" method="POST" 
                      onsubmit="return confirm('Generate jurnal untuk semua simpanan yang belum memiliki jurnal?')">
                    @csrf
                    <button type="submit" class="w-full py-2 px-4 bg-blue-100 hover:bg-blue-200 dark:bg-blue-900/50 dark:hover:bg-blue-900 text-blue-700 dark:text-blue-300 rounded-lg text-sm font-medium transition-colors flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Generate Jurnal Simpanan
                    </button>
                </form>
            </div>

            {{-- Generate Loans Journals --}}
            <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-xl">📋</span>
                    <div>
                        <h4 class="font-semibold text-gray-900 dark:text-white">Generate Jurnal Pinjaman</h4>
                        <p class="text-xs text-gray-500">Untuk pinjaman aktif/lunas tanpa jurnal</p>
                    </div>
                </div>
                <form action="{{ route('import.generate.loans') }}" method="POST" 
                      onsubmit="return confirm('Generate jurnal untuk semua pinjaman (aktif/lunas) yang belum memiliki jurnal?')">
                    @csrf
                    <button type="submit" class="w-full py-2 px-4 bg-blue-100 hover:bg-blue-200 dark:bg-blue-900/50 dark:hover:bg-blue-900 text-blue-700 dark:text-blue-300 rounded-lg text-sm font-medium transition-colors flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Generate Jurnal Pinjaman
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Reset Data Section (Admin Only) --}}
    @can('admin')
    <div class="glass-card-solid p-6 mt-6 border-2 border-red-200 dark:border-red-900/50" x-data="{ showResetSection: false }">
        <div class="flex items-center justify-between cursor-pointer" @click="showResetSection = !showResetSection">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-red-600 dark:text-red-400">⚠️ Reset Data</h3>
                    <p class="text-sm text-gray-500">Hapus data dalam jumlah banyak (AKUN ADMIN TIDAK AKAN DIHAPUS)</p>
                </div>
            </div>
            <svg class="w-5 h-5 text-gray-400 transition-transform" :class="showResetSection ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </div>

        <div x-show="showResetSection" x-transition class="mt-6 space-y-4">
            {{-- Warning Alert --}}
            <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-xl p-4">
                <div class="flex gap-3">
                    <svg class="w-6 h-6 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <div>
                        <p class="font-medium text-red-700 dark:text-red-300">Perhatian!</p>
                        <p class="text-sm text-red-600 dark:text-red-400">Data yang dihapus TIDAK DAPAT dikembalikan. Pastikan Anda sudah membackup data terlebih dahulu.</p>
                    </div>
                </div>
            </div>

            {{-- Reset Options Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Reset Members --}}
                <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="text-xl">👥</span>
                        <div>
                            <h4 class="font-semibold text-gray-900 dark:text-white">Reset Anggota</h4>
                            <p class="text-xs text-gray-500">{{ $counts['members'] ?? 0 }} anggota, {{ $counts['users'] ?? 0 }} user</p>
                        </div>
                    </div>
                    <form action="{{ route('import.reset.members') }}" method="POST" 
                          onsubmit="return confirm('Yakin ingin menghapus SEMUA data anggota? Akun Admin akan tetap aman.')">
                        @csrf
                        <input type="hidden" name="confirm" value="HAPUS">
                        <button type="submit" class="w-full py-2 px-4 bg-red-100 hover:bg-red-200 dark:bg-red-900/50 dark:hover:bg-red-900 text-red-700 dark:text-red-300 rounded-lg text-sm font-medium transition-colors">
                            🗑️ Hapus Semua Anggota
                        </button>
                    </form>
                </div>

                {{-- Reset Savings --}}
                <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="text-xl">💰</span>
                        <div>
                            <h4 class="font-semibold text-gray-900 dark:text-white">Reset Simpanan</h4>
                            <p class="text-xs text-gray-500">{{ $counts['savings'] ?? 0 }} transaksi</p>
                        </div>
                    </div>
                    <form action="{{ route('import.reset.savings') }}" method="POST" 
                          onsubmit="return confirm('Yakin ingin menghapus SEMUA data simpanan?')">
                        @csrf
                        <input type="hidden" name="confirm" value="HAPUS">
                        <button type="submit" class="w-full py-2 px-4 bg-red-100 hover:bg-red-200 dark:bg-red-900/50 dark:hover:bg-red-900 text-red-700 dark:text-red-300 rounded-lg text-sm font-medium transition-colors">
                            🗑️ Hapus Semua Simpanan
                        </button>
                    </form>
                </div>

                {{-- Reset Loans --}}
                <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="text-xl">📋</span>
                        <div>
                            <h4 class="font-semibold text-gray-900 dark:text-white">Reset Pinjaman</h4>
                            <p class="text-xs text-gray-500">{{ $counts['loans'] ?? 0 }} pinjaman</p>
                        </div>
                    </div>
                    <form action="{{ route('import.reset.loans') }}" method="POST" 
                          onsubmit="return confirm('Yakin ingin menghapus SEMUA data pinjaman?')">
                        @csrf
                        <input type="hidden" name="confirm" value="HAPUS">
                        <button type="submit" class="w-full py-2 px-4 bg-red-100 hover:bg-red-200 dark:bg-red-900/50 dark:hover:bg-red-900 text-red-700 dark:text-red-300 rounded-lg text-sm font-medium transition-colors">
                            🗑️ Hapus Semua Pinjaman
                        </button>
                    </form>
                </div>

                {{-- Reset Purchases --}}
                <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="text-xl">📦</span>
                        <div>
                            <h4 class="font-semibold text-gray-900 dark:text-white">Reset Pembelian</h4>
                            <p class="text-xs text-gray-500">{{ $counts['purchases'] ?? 0 }} pembelian</p>
                        </div>
                    </div>
                    <form action="{{ route('import.reset.purchases') }}" method="POST" 
                          onsubmit="return confirm('Yakin ingin menghapus SEMUA data pembelian?')">
                        @csrf
                        <input type="hidden" name="confirm" value="HAPUS">
                        <button type="submit" class="w-full py-2 px-4 bg-red-100 hover:bg-red-200 dark:bg-red-900/50 dark:hover:bg-red-900 text-red-700 dark:text-red-300 rounded-lg text-sm font-medium transition-colors">
                            🗑️ Hapus Semua Pembelian
                        </button>
                    </form>
                </div>

                {{-- Reset Products --}}
                <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="text-xl">🏷️</span>
                        <div>
                            <h4 class="font-semibold text-gray-900 dark:text-white">Reset Produk</h4>
                            <p class="text-xs text-gray-500">{{ $counts['products'] ?? 0 }} produk</p>
                        </div>
                    </div>
                    <form action="{{ route('import.reset.products') }}" method="POST" 
                          onsubmit="return confirm('Yakin ingin menghapus SEMUA data produk? File foto juga akan dihapus dari server.')">
                        @csrf
                        <input type="hidden" name="confirm" value="HAPUS">
                        <button type="submit" class="w-full py-2 px-4 bg-red-100 hover:bg-red-200 dark:bg-red-900/50 dark:hover:bg-red-900 text-red-700 dark:text-red-300 rounded-lg text-sm font-medium transition-colors">
                            🗑️ Hapus Semua Produk
                        </button>
                    </form>
                </div>

                {{-- Reset Transactions --}}
                <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="text-xl">🛒</span>
                        <div>
                            <h4 class="font-semibold text-gray-900 dark:text-white">Reset Transaksi</h4>
                            <p class="text-xs text-gray-500">{{ $counts['transactions'] ?? 0 }} transaksi</p>
                        </div>
                    </div>
                    <form action="{{ route('import.reset.transactions') }}" method="POST" 
                          onsubmit="return confirm('Yakin ingin menghapus SEMUA data transaksi (Online & POS)?')">
                        @csrf
                        <input type="hidden" name="confirm" value="HAPUS">
                        <button type="submit" class="w-full py-2 px-4 bg-red-100 hover:bg-red-200 dark:bg-red-900/50 dark:hover:bg-red-900 text-red-700 dark:text-red-300 rounded-lg text-sm font-medium transition-colors">
                            🗑️ Hapus Semua Transaksi
                        </button>
                    </form>
                </div>

                {{-- Reset Audit Logs --}}
                <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="text-xl">📜</span>
                        <div>
                            <h4 class="font-semibold text-gray-900 dark:text-white">Reset Audit Log</h4>
                            <p class="text-xs text-gray-500">{{ $counts['audit_logs'] ?? 0 }} log aktivitas</p>
                        </div>
                    </div>
                    <form action="{{ route('import.reset.audit-logs') }}" method="POST" 
                          onsubmit="return confirm('Yakin ingin menghapus SEMUA log aktivitas?')">
                        @csrf
                        <input type="hidden" name="confirm" value="HAPUS">
                        <button type="submit" class="w-full py-2 px-4 bg-red-100 hover:bg-red-200 dark:bg-red-900/50 dark:hover:bg-red-900 text-red-700 dark:text-red-300 rounded-lg text-sm font-medium transition-colors">
                            🗑️ Hapus Semua Log
                        </button>
                    </form>
                </div>

                {{-- Reset Journals --}}
                <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="text-xl">📊</span>
                        <div>
                            <h4 class="font-semibold text-gray-900 dark:text-white">Reset Jurnal Akun</h4>
                            <p class="text-xs text-gray-500">{{ $counts['journals'] ?? 0 }} entri jurnal</p>
                        </div>
                    </div>
                    <form action="{{ route('import.reset.journals') }}" method="POST" 
                          onsubmit="return confirm('Yakin ingin menghapus SEMUA data jurnal akuntansi?')">
                        @csrf
                        <input type="hidden" name="confirm" value="HAPUS">
                        <button type="submit" class="w-full py-2 px-4 bg-red-100 hover:bg-red-200 dark:bg-red-900/50 dark:hover:bg-red-900 text-red-700 dark:text-red-300 rounded-lg text-sm font-medium transition-colors">
                            🗑️ Hapus Semua Jurnal
                        </button>
                    </form>
                </div>

                {{-- Reset Expenses --}}
                <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="text-xl">💸</span>
                        <div>
                            <h4 class="font-semibold text-gray-900 dark:text-white">Reset Biaya</h4>
                            <p class="text-xs text-gray-500">{{ $counts['expenses'] ?? 0 }} transaksi biaya</p>
                        </div>
                    </div>
                    <form action="{{ route('import.reset.expenses') }}" method="POST" 
                          onsubmit="return confirm('Yakin ingin menghapus SEMUA data biaya/pengeluaran?')">
                        @csrf
                        <input type="hidden" name="confirm" value="HAPUS">
                        <button type="submit" class="w-full py-2 px-4 bg-red-100 hover:bg-red-200 dark:bg-red-900/50 dark:hover:bg-red-900 text-red-700 dark:text-red-300 rounded-lg text-sm font-medium transition-colors">
                            🗑️ Hapus Semua Biaya
                        </button>
                    </form>
                </div>

                {{-- Reset Suppliers --}}
                <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="text-xl">🏗️</span>
                        <div>
                            <h4 class="font-semibold text-gray-900 dark:text-white">Reset Supplier</h4>
                            <p class="text-xs text-gray-500">{{ $counts['suppliers'] ?? 0 }} supplier</p>
                        </div>
                    </div>
                    <form action="{{ route('import.reset.suppliers') }}" method="POST" 
                          onsubmit="return confirm('Yakin ingin menghapus SEMUA data supplier?')">
                        @csrf
                        <input type="hidden" name="confirm" value="HAPUS">
                        <button type="submit" class="w-full py-2 px-4 bg-red-100 hover:bg-red-200 dark:bg-red-900/50 dark:hover:bg-red-900 text-red-700 dark:text-red-300 rounded-lg text-sm font-medium transition-colors">
                            🗑️ Hapus Semua Supplier
                        </button>
                    </form>
                </div>

                {{-- Reset Categories --}}
                <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="text-xl">📁</span>
                        <div>
                            <h4 class="font-semibold text-gray-900 dark:text-white">Reset Kategori</h4>
                            <p class="text-xs text-gray-500">{{ $counts['categories'] ?? 0 }} kategori</p>
                        </div>
                    </div>
                    <form action="{{ route('import.reset.categories') }}" method="POST" 
                          onsubmit="return confirm('Yakin ingin menghapus SEMUA data kategori produk?')">
                        @csrf
                        <input type="hidden" name="confirm" value="HAPUS">
                        <button type="submit" class="w-full py-2 px-4 bg-red-100 hover:bg-red-200 dark:bg-red-900/50 dark:hover:bg-red-900 text-red-700 dark:text-red-300 rounded-lg text-sm font-medium transition-colors">
                            🗑️ Hapus Semua Kategori
                        </button>
                    </form>
                </div>

                {{-- Reset Aspirations --}}
                <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="text-xl">💬</span>
                        <div>
                            <h4 class="font-semibold text-gray-900 dark:text-white">Reset Aspirasi</h4>
                            <p class="text-xs text-gray-500">{{ $counts['aspirations'] ?? 0 }} aspirasi</p>
                        </div>
                    </div>
                    <form action="{{ route('import.reset.aspirations') }}" method="POST" 
                          onsubmit="return confirm('Yakin ingin menghapus SEMUA data aspirasi?')">
                        @csrf
                        <input type="hidden" name="confirm" value="HAPUS">
                        <button type="submit" class="w-full py-2 px-4 bg-red-100 hover:bg-red-200 dark:bg-red-900/50 dark:hover:bg-red-900 text-red-700 dark:text-red-300 rounded-lg text-sm font-medium transition-colors">
                            🗑️ Hapus Semua Aspirasi
                        </button>
                    </form>
                </div>

                {{-- Reset Consignments --}}
                <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="text-xl">🤝</span>
                        <div>
                            <h4 class="font-semibold text-gray-900 dark:text-white">Reset Konsinyasi</h4>
                            <p class="text-xs text-gray-500">{{ $counts['consignment_inbounds'] ?? 0 }} masuk, {{ $counts['consignment_settlements'] ?? 0 }} selesai</p>
                        </div>
                    </div>
                    <form action="{{ route('import.reset.consignments') }}" method="POST" 
                          onsubmit="return confirm('Yakin ingin menghapus SEMUA data konsinyasi?')">
                        @csrf
                        <input type="hidden" name="confirm" value="HAPUS">
                        <button type="submit" class="w-full py-2 px-4 bg-red-100 hover:bg-red-200 dark:bg-red-900/50 dark:hover:bg-red-900 text-red-700 dark:text-red-300 rounded-lg text-sm font-medium transition-colors">
                            🗑️ Hapus Semua Konsinyasi
                        </button>
                    </form>
                </div>

                {{-- Reset Vouchers --}}
                <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="text-xl">🎫</span>
                        <div>
                            <h4 class="font-semibold text-gray-900 dark:text-white">Reset Voucher</h4>
                            <p class="text-xs text-gray-500">{{ $counts['vouchers'] ?? 0 }} voucher</p>
                        </div>
                    </div>
                    <form action="{{ route('import.reset.vouchers') }}" method="POST" 
                          onsubmit="return confirm('Yakin ingin menghapus SEMUA data voucher?')">
                        @csrf
                        <input type="hidden" name="confirm" value="HAPUS">
                        <button type="submit" class="w-full py-2 px-4 bg-red-100 hover:bg-red-200 dark:bg-red-900/50 dark:hover:bg-red-900 text-red-700 dark:text-red-300 rounded-lg text-sm font-medium transition-colors">
                            🗑️ Hapus Semua Voucher
                        </button>
                    </form>
                </div>

                {{-- Reset Polls --}}
                <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="text-xl">🗳️</span>
                        <div>
                            <h4 class="font-semibold text-gray-900 dark:text-white">Reset Polling</h4>
                            <p class="text-xs text-gray-500">{{ $counts['polls'] ?? 0 }} polling</p>
                        </div>
                    </div>
                    <form action="{{ route('import.reset.polls') }}" method="POST" 
                          onsubmit="return confirm('Yakin ingin menghapus SEMUA data polling (termasuk hasil suara)?')">
                        @csrf
                        <input type="hidden" name="confirm" value="HAPUS">
                        <button type="submit" class="w-full py-2 px-4 bg-red-100 hover:bg-red-200 dark:bg-red-900/50 dark:hover:bg-red-900 text-red-700 dark:text-red-300 rounded-lg text-sm font-medium transition-colors">
                            🗑️ Hapus Semua Polling
                        </button>
                    </form>
                </div>
            </div>

            {{-- Danger Zone - Reset All --}}
            <div class="p-4 bg-red-100 dark:bg-red-900/50 rounded-xl border-2 border-red-300 dark:border-red-700">
                <h4 class="font-bold text-red-700 dark:text-red-300 mb-2">🔴 Danger Zone - Reset SEMUA Data</h4>
                <p class="text-sm text-red-600 dark:text-red-400 mb-3">Menghapus semua anggota, simpanan, pinjaman, dan transaksi sekaligus. Akun Admin tetap aman.</p>
                <form action="{{ route('import.reset.all') }}" method="POST" x-data="{ confirmText: '' }">
                    @csrf
                    <div class="flex gap-2">
                        <input type="text" x-model="confirmText" name="confirm" 
                               class="form-input flex-1 text-sm" 
                               placeholder="Ketik 'HAPUS SEMUA' untuk konfirmasi">
                        <button type="submit" 
                                :disabled="confirmText !== 'HAPUS SEMUA'"
                                :class="confirmText === 'HAPUS SEMUA' ? 'bg-red-600 hover:bg-red-700 cursor-pointer' : 'bg-gray-400 cursor-not-allowed'"
                                class="py-2 px-4 text-white rounded-lg text-sm font-medium transition-colors">
                            ⚠️ Reset Semua
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endcan
@endsection

@push('scripts')
<script>
function createUploadHandler(formId, progressId) {
    const form = document.getElementById(formId);
    const progressContainer = document.getElementById(progressId);
    
    if (!form) return;
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const fileInput = form.querySelector('input[type="file"]');
        const submitBtn = form.querySelector('button[type="submit"]');
        
        if (!fileInput.files.length) {
            alert('Pilih file terlebih dahulu');
            return;
        }
        
        const file = fileInput.files[0];
        const fileSize = file.size;
        const formData = new FormData(form);
        
        // Show progress container
        progressContainer.classList.remove('hidden');
        const progressBar = progressContainer.querySelector('.progress-bar');
        const progressText = progressContainer.querySelector('.progress-text');
        const statusText = progressContainer.querySelector('.status-text');
        
        // Disable submit button
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Mengupload...';
        
        const xhr = new XMLHttpRequest();
        const startTime = Date.now();
        
        xhr.upload.addEventListener('progress', function(e) {
            if (e.lengthComputable) {
                const percent = Math.round((e.loaded / e.total) * 100);
                progressBar.style.width = percent + '%';
                
                // Calculate speed and ETA
                const elapsed = (Date.now() - startTime) / 1000;
                const speed = e.loaded / elapsed;
                const remaining = (e.total - e.loaded) / speed;
                
                let etaText = '';
                if (remaining > 60) {
                    etaText = Math.ceil(remaining / 60) + ' menit';
                } else if (remaining > 0) {
                    etaText = Math.ceil(remaining) + ' detik';
                }
                
                progressText.textContent = percent + '%';
                if (percent < 100) {
                    statusText.textContent = 'Mengupload... ' + (etaText ? '(~' + etaText + ' lagi)' : '');
                } else {
                    statusText.textContent = '⏳ Memproses data, mohon tunggu...';
                    progressBar.classList.add('animate-pulse');
                }
            }
        });
        
        xhr.addEventListener('load', function() {
            // Redirect with response (form submission complete)
            document.open();
            document.write(xhr.responseText);
            document.close();
            // window.history.pushState({}, '', form.action);
        });
        
        xhr.addEventListener('error', function() {
            progressContainer.classList.add('hidden');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg> Upload';
            alert('Gagal upload. Silakan coba lagi.');
        });
        
        xhr.open('POST', form.action, true);
        xhr.send(formData);
    });
}

document.addEventListener('DOMContentLoaded', function() {
    createUploadHandler('form-import-members', 'progress-members');
    createUploadHandler('form-import-savings', 'progress-savings');
    createUploadHandler('form-import-loans', 'progress-loans');
    createUploadHandler('form-import-credits', 'progress-credits');
});
</script>
@endpush
