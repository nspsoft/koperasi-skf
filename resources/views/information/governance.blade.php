@extends('layouts.app')

@section('title', 'Tugas & Wewenang Koperasi')

@section('content')
<style>
    @media print {
        .sidebar, .navbar, .page-header, .glass-card > div:not(.prose), .btn-primary, button, .footer, .no-print {
            display: none !important;
        }
        .main-content {
            padding: 0 !important;
            margin: 0 !important;
        }
        .glass-card {
            box-shadow: none !important;
            border: none !important;
            background: white !important;
        }
        details {
            display: block !important;
        }
        details summary {
            display: none !important;
        }
        details[open] summary {
            display: none !important;
        }
        [x-show] {
            display: block !important;
        }
    }
</style>
<div class="space-y-6" x-data="{ activeTab: 'struktur' }">
    <!-- Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Tugas, Wewenang & Tanggung Jawab</h1>
            <p class="page-subtitle">Berdasarkan UU No. 25 Tahun 1992 tentang Perkoperasian</p>
        </div>
        <div class="flex items-center gap-2 no-print">
            <button onclick="window.open('{{ route('governance.download-pdf') }}?print=1', '_blank')" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center gap-2 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Dokumen Resmi
            </button>
            <a href="{{ route('governance.download-pdf') }}" class="btn-primary flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Download PDF
            </a>
        </div>
    </div>

    <!-- Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="glass-card p-4 flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center text-2xl">🏛️</div>
            <div>
                <p class="text-sm text-gray-500">Dasar Hukum</p>
                <p class="font-bold text-gray-800 dark:text-white">UU No. 25/1992</p>
            </div>
        </div>
        <div class="glass-card p-4 flex items-center gap-4">
            <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center text-2xl">👥</div>
            <div>
                <p class="text-sm text-gray-500">Rapat Anggota</p>
                <p class="font-bold text-gray-800 dark:text-white">Kekuasaan Tertinggi</p>
            </div>
        </div>
        <div class="glass-card p-4 flex items-center gap-4">
            <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center text-2xl">👔</div>
            <div>
                <p class="text-sm text-gray-500">Pengurus</p>
                <p class="font-bold text-gray-800 dark:text-white">Pengelola Koperasi</p>
            </div>
        </div>
        <div class="glass-card p-4 flex items-center gap-4">
            <div class="w-12 h-12 bg-amber-100 dark:bg-amber-900/30 rounded-xl flex items-center justify-center text-2xl">🔍</div>
            <div>
                <p class="text-sm text-gray-500">Pengawas</p>
                <p class="font-bold text-gray-800 dark:text-white">Pemantau Kinerja</p>
            </div>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="flex flex-wrap gap-2 border-b border-gray-200 dark:border-gray-700 overflow-x-auto">
        <button @click="activeTab = 'struktur'" class="px-4 py-3 font-semibold text-sm transition-colors border-b-2 whitespace-nowrap" :class="activeTab === 'struktur' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'">
            🏢 Struktur Organisasi
        </button>
        <button @click="activeTab = 'rapat'" class="px-4 py-3 font-semibold text-sm transition-colors border-b-2 whitespace-nowrap" :class="activeTab === 'rapat' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'">
            👥 Rapat Anggota
        </button>
        <button @click="activeTab = 'pengurus'" class="px-4 py-3 font-semibold text-sm transition-colors border-b-2 whitespace-nowrap" :class="activeTab === 'pengurus' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'">
            👔 Pengurus
        </button>
        <button @click="activeTab = 'pengawas'" class="px-4 py-3 font-semibold text-sm transition-colors border-b-2 whitespace-nowrap" :class="activeTab === 'pengawas' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'">
            🔍 Pengawas
        </button>
        <button @click="activeTab = 'anggota'" class="px-4 py-3 font-semibold text-sm transition-colors border-b-2 whitespace-nowrap" :class="activeTab === 'anggota' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'">
            🙋 Anggota
        </button>
    </div>

    <!-- TAB 0: STRUKTUR ORGANISASI -->
    <div x-show="activeTab === 'struktur'" class="space-y-6">
        <!-- Org Chart Card -->
        <div class="glass-card p-6">
            <h2 class="text-xl font-bold text-center mb-4 text-indigo-600 dark:text-indigo-400">
                🏢 STRUKTUR ORGANISASI KOPERASI
            </h2>
            <p class="text-center text-gray-600 dark:text-gray-400 mb-6">Koperasi Karyawan PT. SPINDO TBK</p>
            
            <!-- Clean Org Chart -->
            <div class="bg-gray-50 dark:bg-gray-800 p-6 rounded-2xl border border-gray-200 dark:border-gray-700">
                <div class="flex flex-col items-center space-y-3">
                    
                    <!-- Level 1: Rapat Anggota -->
                    <div class="px-8 py-3 rounded-lg shadow-lg text-center w-full max-w-xs" style="background-color: #059669;">
                        <p class="text-base font-bold" style="color: #ffffff;">👥 RAPAT ANGGOTA</p>
                        <p class="text-xs" style="color: #ffffff;">Kekuasaan Tertinggi</p>
                    </div>
                    
                    <!-- Connector -->
                    <div class="w-0.5 h-4 bg-gray-400"></div>
                    
                    <!-- Level 2: Pembina -->
                    <div class="px-8 py-3 rounded-lg shadow-lg text-center w-full max-w-xs" style="background-color: #2563eb;">
                        <p class="text-base font-bold" style="color: #ffffff;">🎓 PEMBINA</p>
                        <p class="text-xs" style="color: #ffffff;">Penasihat & Pelindung</p>
                    </div>
                    
                    <!-- Connector -->
                    <div class="w-0.5 h-4 bg-gray-400"></div>
                    
                    <!-- Level 3: Pengawas & Pengurus -->
                    <div class="flex flex-col md:flex-row items-center justify-center gap-4 w-full">
                        <!-- Pengawas -->
                        <div class="px-6 py-3 rounded-lg shadow-lg text-center w-full max-w-[180px]" style="background-color: #ea580c;">
                            <p class="font-bold text-sm" style="color: #ffffff;">🔍 PENGAWAS</p>
                            <p class="text-xs" style="color: #ffffff;">Pemantau Kinerja</p>
                        </div>
                        
                        <!-- Pengurus Container -->
                        <div class="flex flex-col items-center">
                            <div class="px-6 py-3 rounded-lg shadow-lg text-center w-full max-w-[180px]" style="background-color: #9333ea;">
                                <p class="font-bold text-sm" style="color: #ffffff;">👔 PENGURUS</p>
                                <p class="text-xs" style="color: #ffffff;">Pengelola Koperasi</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Connector to Pengurus Details -->
                    <div class="w-0.5 h-4 bg-gray-400"></div>
                    
                    <!-- Level 4: Ketua -->
                    <div class="px-8 py-3 rounded-lg shadow-lg text-center w-full max-w-xs" style="background-color: #4f46e5;">
                        <p class="text-base font-bold" style="color: #ffffff;">👑 KETUA</p>
                        <p class="text-xs" style="color: #ffffff;">Pimpinan Tertinggi Pengurus</p>
                    </div>
                    
                    <!-- Connector -->
                    <div class="w-0.5 h-4 bg-gray-400"></div>
                    
                    <!-- Level 5: Wakil Ketua -->
                    <div class="px-6 py-2 rounded-lg shadow-md text-center w-full max-w-[200px]" style="background-color: #6366f1;">
                        <p class="font-bold text-sm" style="color: #ffffff;">👤 WAKIL KETUA</p>
                        <p class="text-xs" style="color: #ffffff;">Pendamping Ketua</p>
                    </div>
                    
                    <!-- Connector -->
                    <div class="w-0.5 h-4 bg-gray-400"></div>
                    
                    <!-- Horizontal Line -->
                    <div class="w-full max-w-lg h-0.5 bg-gray-300 dark:bg-gray-600"></div>
                    
                    <!-- Level 6: Sekretaris, Bendahara, Operasional -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 w-full max-w-lg">
                        <div class="px-4 py-3 rounded-lg shadow-md text-center" style="background-color: #0891b2;">
                            <p class="font-bold text-sm" style="color: #ffffff;">📝 SEKRETARIS</p>
                            <p class="text-xs" style="color: #ffffff;">Administrasi</p>
                        </div>
                        <div class="px-4 py-3 rounded-lg shadow-md text-center" style="background-color: #16a34a;">
                            <p class="font-bold text-sm" style="color: #ffffff;">💰 BENDAHARA</p>
                            <p class="text-xs" style="color: #ffffff;">Keuangan</p>
                        </div>
                        <div class="px-4 py-3 rounded-lg shadow-md text-center" style="background-color: #e11d48;">
                            <p class="font-bold text-sm" style="color: #ffffff;">⚙️ OPERASIONAL</p>
                            <p class="text-xs" style="color: #ffffff;">Pelaksana Usaha</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Cards for Each Position -->
        <div class="grid md:grid-cols-2 gap-6">
            <!-- PEMBINA -->
            <div class="glass-card p-6">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-sky-500 to-blue-600 rounded-xl flex items-center justify-center text-3xl shadow-lg">🎓</div>
                    <div>
                        <h3 class="font-bold text-lg text-gray-800 dark:text-white">PEMBINA</h3>
                        <p class="text-sm text-gray-500">Penasihat & Pelindung Koperasi</p>
                    </div>
                </div>
                <div class="space-y-4">
                    <div>
                        <h4 class="font-bold text-blue-600 dark:text-blue-400 mb-2 text-sm">📋 Tugas & Wewenang:</h4>
                        <ul class="space-y-1 text-sm text-gray-700 dark:text-gray-300">
                            <li class="flex items-start gap-2"><span class="text-blue-500">•</span> Memberikan bimbingan dan arahan kepada pengurus</li>
                            <li class="flex items-start gap-2"><span class="text-blue-500">•</span> Memberikan nasihat/saran dalam pengambilan keputusan strategis</li>
                            <li class="flex items-start gap-2"><span class="text-blue-500">•</span> Menjadi penengah bila terjadi perselisihan</li>
                            <li class="flex items-start gap-2"><span class="text-blue-500">•</span> Memberikan perlindungan dan dukungan kepada koperasi</li>
                            <li class="flex items-start gap-2"><span class="text-blue-500">•</span> Mewakili kepentingan koperasi di tingkat perusahaan</li>
                        </ul>
                    </div>
                    <div class="bg-blue-50 dark:bg-blue-900/20 p-3 rounded-lg">
                        <p class="text-xs text-blue-700 dark:text-blue-300"><strong>Catatan:</strong> Pembina biasanya dijabat oleh pimpinan perusahaan atau pejabat yang ditunjuk.</p>
                    </div>
                </div>
            </div>

            <!-- PENGAWAS -->
            <div class="glass-card p-6">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-amber-500 to-orange-500 rounded-xl flex items-center justify-center text-3xl shadow-lg">🔍</div>
                    <div>
                        <h3 class="font-bold text-lg text-gray-800 dark:text-white">PENGAWAS</h3>
                        <p class="text-sm text-gray-500">Pemantau & Pengontrol Kinerja</p>
                    </div>
                </div>
                <div class="space-y-4">
                    <div>
                        <h4 class="font-bold text-amber-600 dark:text-amber-400 mb-2 text-sm">📋 Tugas & Wewenang:</h4>
                        <ul class="space-y-1 text-sm text-gray-700 dark:text-gray-300">
                            <li class="flex items-start gap-2"><span class="text-amber-500">•</span> Mengawasi pelaksanaan kebijakan dan pengelolaan koperasi</li>
                            <li class="flex items-start gap-2"><span class="text-amber-500">•</span> Meneliti catatan dan pembukuan koperasi</li>
                            <li class="flex items-start gap-2"><span class="text-amber-500">•</span> Membuat laporan tertulis hasil pengawasan</li>
                            <li class="flex items-start gap-2"><span class="text-amber-500">•</span> Menjaga kepatuhan terhadap AD/ART</li>
                            <li class="flex items-start gap-2"><span class="text-amber-500">•</span> Bertanggung jawab kepada Rapat Anggota</li>
                        </ul>
                    </div>
                    <div class="bg-amber-50 dark:bg-amber-900/20 p-3 rounded-lg">
                        <p class="text-xs text-amber-700 dark:text-amber-300"><strong>Dasar Hukum:</strong> Pasal 38-40 UU No. 25/1992</p>
                    </div>
                </div>
            </div>

            <!-- KETUA -->
            <div class="glass-card p-6">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-purple-600 to-indigo-600 rounded-xl flex items-center justify-center text-3xl shadow-lg">👑</div>
                    <div>
                        <h3 class="font-bold text-lg text-gray-800 dark:text-white">KETUA</h3>
                        <p class="text-sm text-gray-500">Pimpinan Tertinggi Pengurus</p>
                    </div>
                </div>
                <div class="space-y-4">
                    <div>
                        <h4 class="font-bold text-purple-600 dark:text-purple-400 mb-2 text-sm">📋 Tugas & Wewenang:</h4>
                        <ul class="space-y-1 text-sm text-gray-700 dark:text-gray-300">
                            <li class="flex items-start gap-2"><span class="text-purple-500">•</span> Memimpin dan mengkoordinasikan seluruh kegiatan</li>
                            <li class="flex items-start gap-2"><span class="text-purple-500">•</span> Memimpin Rapat Anggota dan rapat pengurus</li>
                            <li class="flex items-start gap-2"><span class="text-purple-500">•</span> Mewakili koperasi di dalam dan di luar pengadilan</li>
                            <li class="flex items-start gap-2"><span class="text-purple-500">•</span> Menandatangani dokumen resmi koperasi</li>
                            <li class="flex items-start gap-2"><span class="text-purple-500">•</span> Menyampaikan laporan pertanggungjawaban ke RAT</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- WAKIL KETUA -->
            <div class="glass-card p-6">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-purple-400 to-indigo-400 rounded-xl flex items-center justify-center text-3xl shadow-lg">👤</div>
                    <div>
                        <h3 class="font-bold text-lg text-gray-800 dark:text-white">WAKIL KETUA</h3>
                        <p class="text-sm text-gray-500">Pendamping Ketua</p>
                    </div>
                </div>
                <div class="space-y-4">
                    <div>
                        <h4 class="font-bold text-purple-600 dark:text-purple-400 mb-2 text-sm">📋 Tugas & Wewenang:</h4>
                        <ul class="space-y-1 text-sm text-gray-700 dark:text-gray-300">
                            <li class="flex items-start gap-2"><span class="text-purple-500">•</span> Membantu Ketua dalam menjalankan tugasnya</li>
                            <li class="flex items-start gap-2"><span class="text-purple-500">•</span> Menggantikan Ketua bila berhalangan</li>
                            <li class="flex items-start gap-2"><span class="text-purple-500">•</span> Mengkoordinasikan kegiatan operasional harian</li>
                            <li class="flex items-start gap-2"><span class="text-purple-500">•</span> Memimpin rapat bila Ketua tidak hadir</li>
                            <li class="flex items-start gap-2"><span class="text-purple-500">•</span> Melaksanakan tugas khusus dari Ketua</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- SEKRETARIS -->
            <div class="glass-card p-6">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-xl flex items-center justify-center text-3xl shadow-lg">📝</div>
                    <div>
                        <h3 class="font-bold text-lg text-gray-800 dark:text-white">SEKRETARIS</h3>
                        <p class="text-sm text-gray-500">Pengelola Administrasi</p>
                    </div>
                </div>
                <div class="space-y-4">
                    <div>
                        <h4 class="font-bold text-blue-600 dark:text-blue-400 mb-2 text-sm">📋 Tugas & Wewenang:</h4>
                        <ul class="space-y-1 text-sm text-gray-700 dark:text-gray-300">
                            <li class="flex items-start gap-2"><span class="text-blue-500">•</span> Menyelenggarakan administrasi umum</li>
                            <li class="flex items-start gap-2"><span class="text-blue-500">•</span> Mengelola Buku Daftar Anggota & Pengurus</li>
                            <li class="flex items-start gap-2"><span class="text-blue-500">•</span> Membuat notulen rapat dan surat-menyurat</li>
                            <li class="flex items-start gap-2"><span class="text-blue-500">•</span> Mengarsipkan dokumen koperasi</li>
                            <li class="flex items-start gap-2"><span class="text-blue-500">•</span> Menyiapkan undangan dan materi rapat</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- BENDAHARA -->
            <div class="glass-card p-6">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-emerald-500 rounded-xl flex items-center justify-center text-3xl shadow-lg">💰</div>
                    <div>
                        <h3 class="font-bold text-lg text-gray-800 dark:text-white">BENDAHARA</h3>
                        <p class="text-sm text-gray-500">Pengelola Keuangan</p>
                    </div>
                </div>
                <div class="space-y-4">
                    <div>
                        <h4 class="font-bold text-green-600 dark:text-green-400 mb-2 text-sm">📋 Tugas & Wewenang:</h4>
                        <ul class="space-y-1 text-sm text-gray-700 dark:text-gray-300">
                            <li class="flex items-start gap-2"><span class="text-green-500">•</span> Mengelola keuangan koperasi secara keseluruhan</li>
                            <li class="flex items-start gap-2"><span class="text-green-500">•</span> Menyelenggarakan pembukuan keuangan</li>
                            <li class="flex items-start gap-2"><span class="text-green-500">•</span> Menyusun laporan keuangan & neraca</li>
                            <li class="flex items-start gap-2"><span class="text-green-500">•</span> Mengelola simpanan dan pinjaman anggota</li>
                            <li class="flex items-start gap-2"><span class="text-green-500">•</span> Membuat perhitungan SHU tahunan</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- OPERASIONAL -->
            <div class="glass-card p-6 md:col-span-2">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-rose-500 to-pink-500 rounded-xl flex items-center justify-center text-3xl shadow-lg">⚙️</div>
                    <div>
                        <h3 class="font-bold text-lg text-gray-800 dark:text-white">BAGIAN OPERASIONAL</h3>
                        <p class="text-sm text-gray-500">Pelaksana Kegiatan Usaha</p>
                    </div>
                </div>
                <div class="grid md:grid-cols-3 gap-4">
                    <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-xl">
                        <h4 class="font-bold text-rose-600 dark:text-rose-400 mb-2 text-sm">🛒 Unit Toko/Usaha</h4>
                        <ul class="space-y-1 text-xs text-gray-700 dark:text-gray-300">
                            <li>• Mengelola operasional toko koperasi</li>
                            <li>• Melayani transaksi penjualan</li>
                            <li>• Mengelola stok dan inventaris</li>
                            <li>• Membuat laporan penjualan harian</li>
                        </ul>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-xl">
                        <h4 class="font-bold text-rose-600 dark:text-rose-400 mb-2 text-sm">💳 Unit Simpan Pinjam</h4>
                        <ul class="space-y-1 text-xs text-gray-700 dark:text-gray-300">
                            <li>• Memproses pengajuan pinjaman</li>
                            <li>• Mengelola pembayaran angsuran</li>
                            <li>• Mencatat transaksi simpanan</li>
                            <li>• Melakukan penagihan</li>
                        </ul>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-xl">
                        <h4 class="font-bold text-rose-600 dark:text-rose-400 mb-2 text-sm">📊 Unit Administrasi</h4>
                        <ul class="space-y-1 text-xs text-gray-700 dark:text-gray-300">
                            <li>• Menginput data transaksi</li>
                            <li>• Menyiapkan laporan berkala</li>
                            <li>• Mengelola arsip dokumen</li>
                            <li>• Melayani kebutuhan anggota</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Info -->
        <div class="glass-card p-4 text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                📌 Struktur organisasi dapat disesuaikan dengan kebutuhan dan ukuran koperasi masing-masing berdasarkan Anggaran Dasar.
            </p>
        </div>
    </div>

    <!-- TAB 1: RAPAT ANGGOTA -->
    <div x-show="activeTab === 'rapat'" class="glass-card p-6">
        <div class="prose dark:prose-invert max-w-none">
            <h2 class="text-xl font-bold text-center mb-6 text-green-600">
                <span class="text-3xl">👥</span><br>
                RAPAT ANGGOTA
            </h2>
            <p class="text-center text-gray-600 dark:text-gray-400 mb-6">Pasal 23-28 UU No. 25 Tahun 1992</p>
            
            <div class="space-y-6">
                <!-- Kedudukan -->
                <details open class="group">
                    <summary class="cursor-pointer bg-gray-50 dark:bg-gray-700 p-4 rounded-lg font-bold text-gray-800 dark:text-white flex items-center justify-between">
                        Kedudukan Rapat Anggota (Pasal 23)
                        <svg class="w-5 h-5 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </summary>
                    <div class="p-4 space-y-3 text-gray-700 dark:text-gray-300">
                        <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg border-l-4 border-green-500">
                            <p class="font-semibold text-green-700 dark:text-green-400">Rapat Anggota merupakan <strong>kekuasaan tertinggi</strong> dalam Koperasi.</p>
                        </div>
                        <p>Rapat Anggota adalah forum di mana anggota berkumpul untuk membahas dan memutuskan berbagai hal yang berkaitan dengan pengelolaan dan pengembangan koperasi.</p>
                    </div>
                </details>

                <!-- Wewenang -->
                <details class="group">
                    <summary class="cursor-pointer bg-gray-50 dark:bg-gray-700 p-4 rounded-lg font-bold text-gray-800 dark:text-white flex items-center justify-between">
                        Wewenang Rapat Anggota (Pasal 24)
                        <svg class="w-5 h-5 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </summary>
                    <div class="p-4 space-y-3 text-gray-700 dark:text-gray-300">
                        <p>Rapat Anggota menetapkan:</p>
                        <ul class="list-disc pl-5 space-y-2">
                            <li><strong>Anggaran Dasar</strong></li>
                            <li><strong>Kebijakan umum</strong> di bidang organisasi, manajemen, dan usaha Koperasi</li>
                            <li><strong>Pemilihan, pengangkatan, pemberhentian</strong> Pengurus dan Pengawas</li>
                            <li><strong>Rencana kerja, rencana anggaran pendapatan dan belanja</strong> Koperasi, serta pengesahan laporan keuangan</li>
                            <li><strong>Pengesahan pertanggungjawaban</strong> Pengurus dalam pelaksanaan tugasnya</li>
                            <li><strong>Pembagian Sisa Hasil Usaha (SHU)</strong></li>
                            <li><strong>Penggabungan, peleburan, pembagian, dan pembubaran</strong> Koperasi</li>
                        </ul>
                    </div>
                </details>

                <!-- Pelaksanaan -->
                <details class="group">
                    <summary class="cursor-pointer bg-gray-50 dark:bg-gray-700 p-4 rounded-lg font-bold text-gray-800 dark:text-white flex items-center justify-between">
                        Pelaksanaan Rapat Anggota (Pasal 25-27)
                        <svg class="w-5 h-5 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </summary>
                    <div class="p-4 space-y-3 text-gray-700 dark:text-gray-300">
                        <div class="grid md:grid-cols-2 gap-4">
                            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                                <p class="font-bold text-blue-700 dark:text-blue-400 mb-2">RAT (Rapat Anggota Tahunan)</p>
                                <ul class="text-sm space-y-1">
                                    <li>• Dilaksanakan paling lambat <strong>3 bulan</strong> setelah tutup buku</li>
                                    <li>• Wajib diadakan setiap tahun</li>
                                </ul>
                            </div>
                            <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg">
                                <p class="font-bold text-purple-700 dark:text-purple-400 mb-2">Rapat Anggota Luar Biasa</p>
                                <ul class="text-sm space-y-1">
                                    <li>• Atas permintaan sejumlah anggota</li>
                                    <li>• Keputusan Pengurus atau Pengawas</li>
                                </ul>
                            </div>
                        </div>
                        <p class="mt-4"><strong>Pasal 26:</strong> Keputusan Rapat Anggota diambil berdasarkan musyawarah untuk mencapai mufakat.</p>
                        <p><strong>Pasal 27:</strong> Apabila tidak dapat dicapai dengan musyawarah, maka pengambilan keputusan dilakukan berdasarkan suara terbanyak (voting).</p>
                    </div>
                </details>

                <!-- Kuorum -->
                <details class="group">
                    <summary class="cursor-pointer bg-gray-50 dark:bg-gray-700 p-4 rounded-lg font-bold text-gray-800 dark:text-white flex items-center justify-between">
                        Kuorum & Keabsahan (Pasal 28)
                        <svg class="w-5 h-5 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </summary>
                    <div class="p-4 space-y-3 text-gray-700 dark:text-gray-300">
                        <p>Quorum Rapat Anggota diatur dalam Anggaran Dasar Koperasi dengan ketentuan:</p>
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Minimal setengah dari jumlah anggota harus hadir (50%+1)</li>
                            <li>Apabila kuorum tidak tercapai, rapat dapat ditunda</li>
                            <li>Rapat yang ditunda tetap sah meskipun tidak memenuhi kuorum awal</li>
                        </ul>
                    </div>
                </details>
            </div>
        </div>
    </div>

    <!-- TAB 2: PENGURUS -->
    <div x-show="activeTab === 'pengurus'" class="glass-card p-6">
        <div class="prose dark:prose-invert max-w-none">
            <h2 class="text-xl font-bold text-center mb-6 text-purple-600">
                <span class="text-3xl">👔</span><br>
                PENGURUS KOPERASI
            </h2>
            <p class="text-center text-gray-600 dark:text-gray-400 mb-6">Pasal 29-37 UU No. 25 Tahun 1992</p>
            
            <div class="space-y-6">
                <!-- Struktur Organisasi -->
                <details open class="group">
                    <summary class="cursor-pointer bg-gradient-to-r from-purple-500 to-indigo-600 p-4 rounded-lg font-bold text-white flex items-center justify-between">
                        📊 Struktur Organisasi Pengurus
                        <svg class="w-5 h-5 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </summary>
                    <div class="p-4 space-y-4 text-gray-700 dark:text-gray-300">
                        <!-- Org Chart Visual -->
                        <div class="bg-gray-50 dark:bg-gray-800 p-6 rounded-xl">
                            <div class="flex flex-col items-center">
                                <!-- Ketua -->
                                <div class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white px-8 py-4 rounded-xl shadow-lg text-center mb-4">
                                    <p class="text-lg font-bold">👑 KETUA</p>
                                    <p class="text-sm opacity-90">Pimpinan Tertinggi Pengurus</p>
                                </div>
                                <!-- Connector -->
                                <div class="w-0.5 h-8 bg-gray-300 dark:bg-gray-600"></div>
                                <!-- Wakil Ketua -->
                                <div class="bg-gradient-to-r from-purple-500 to-indigo-500 text-white px-6 py-3 rounded-xl shadow-md text-center mb-4">
                                    <p class="font-bold">👤 WAKIL KETUA</p>
                                    <p class="text-xs opacity-90">Pendamping Ketua</p>
                                </div>
                                <!-- Connector -->
                                <div class="w-0.5 h-6 bg-gray-300 dark:bg-gray-600"></div>
                                <div class="w-64 h-0.5 bg-gray-300 dark:bg-gray-600"></div>
                                <!-- Bottom Row -->
                                <div class="flex gap-4 mt-4">
                                    <div class="bg-blue-500 text-white px-4 py-3 rounded-xl shadow-md text-center">
                                        <p class="font-bold">📝 SEKRETARIS</p>
                                        <p class="text-xs opacity-90">Administrasi</p>
                                    </div>
                                    <div class="bg-green-500 text-white px-4 py-3 rounded-xl shadow-md text-center">
                                        <p class="font-bold">💰 BENDAHARA</p>
                                        <p class="text-xs opacity-90">Keuangan</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </details>

                <!-- KETUA -->
                <details class="group">
                    <summary class="cursor-pointer bg-gradient-to-r from-purple-100 to-indigo-100 dark:from-purple-900/30 dark:to-indigo-900/30 p-4 rounded-lg font-bold text-purple-800 dark:text-purple-300 flex items-center justify-between border-l-4 border-purple-500">
                        👑 Ketua - Tugas, Wewenang & Tanggung Jawab
                        <svg class="w-5 h-5 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </summary>
                    <div class="p-4 space-y-4 text-gray-700 dark:text-gray-300">
                        <!-- Tugas Ketua -->
                        <div>
                            <h4 class="font-bold text-purple-600 dark:text-purple-400 mb-3 flex items-center gap-2">
                                <span class="w-6 h-6 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center text-sm">📋</span>
                                Tugas Ketua
                            </h4>
                            <div class="grid md:grid-cols-2 gap-2">
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-lg border border-gray-200 dark:border-gray-700 flex items-start gap-2">
                                    <span class="text-purple-500">•</span>
                                    <span>Memimpin dan mengkoordinasikan seluruh kegiatan koperasi</span>
                                </div>
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-lg border border-gray-200 dark:border-gray-700 flex items-start gap-2">
                                    <span class="text-purple-500">•</span>
                                    <span>Memimpin Rapat Anggota dan rapat pengurus</span>
                                </div>
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-lg border border-gray-200 dark:border-gray-700 flex items-start gap-2">
                                    <span class="text-purple-500">•</span>
                                    <span>Menyusun rencana kerja dan RAPB bersama pengurus lain</span>
                                </div>
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-lg border border-gray-200 dark:border-gray-700 flex items-start gap-2">
                                    <span class="text-purple-500">•</span>
                                    <span>Menandatangani surat-surat penting dan dokumen koperasi</span>
                                </div>
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-lg border border-gray-200 dark:border-gray-700 flex items-start gap-2">
                                    <span class="text-purple-500">•</span>
                                    <span>Mengawasi pelaksanaan tugas pengurus lainnya</span>
                                </div>
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-lg border border-gray-200 dark:border-gray-700 flex items-start gap-2">
                                    <span class="text-purple-500">•</span>
                                    <span>Menyampaikan laporan pertanggungjawaban kepada RAT</span>
                                </div>
                            </div>
                        </div>
                        <!-- Wewenang Ketua -->
                        <div>
                            <h4 class="font-bold text-blue-600 dark:text-blue-400 mb-3 flex items-center gap-2">
                                <span class="w-6 h-6 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center text-sm">⚡</span>
                                Wewenang Ketua
                            </h4>
                            <ul class="space-y-2">
                                <li class="flex items-start gap-2"><span class="text-blue-500">✓</span> Mewakili koperasi di dalam dan di luar pengadilan</li>
                                <li class="flex items-start gap-2"><span class="text-blue-500">✓</span> Mengambil keputusan strategis dalam situasi mendesak</li>
                                <li class="flex items-start gap-2"><span class="text-blue-500">✓</span> Menunjuk pihak lain untuk mewakili koperasi dengan surat kuasa</li>
                                <li class="flex items-start gap-2"><span class="text-blue-500">✓</span> Menyetujui atau menolak usulan dari pengurus lain</li>
                                <li class="flex items-start gap-2"><span class="text-blue-500">✓</span> Membina hubungan dengan pihak eksternal (pemerintah, bank, mitra)</li>
                            </ul>
                        </div>
                        <!-- Tanggung Jawab Ketua -->
                        <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded-lg border-l-4 border-red-500">
                            <h4 class="font-bold text-red-600 dark:text-red-400 mb-2">⚠️ Tanggung Jawab Ketua</h4>
                            <ul class="space-y-1 text-sm">
                                <li>• Bertanggung jawab penuh atas jalannya organisasi koperasi</li>
                                <li>• Menanggung kerugian akibat kelalaian atau kesengajaan dalam pengelolaan</li>
                                <li>• Menjaga nama baik dan integritas koperasi</li>
                                <li>• Memastikan kepatuhan terhadap AD/ART dan peraturan perundangan</li>
                            </ul>
                        </div>
                    </div>
                </details>

                <!-- WAKIL KETUA -->
                <details class="group">
                    <summary class="cursor-pointer bg-gradient-to-r from-purple-50 to-indigo-50 dark:from-purple-900/20 dark:to-indigo-900/20 p-4 rounded-lg font-bold text-purple-700 dark:text-purple-300 flex items-center justify-between border-l-4 border-purple-400">
                        👤 Wakil Ketua - Tugas, Wewenang & Tanggung Jawab
                        <svg class="w-5 h-5 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </summary>
                    <div class="p-4 space-y-4 text-gray-700 dark:text-gray-300">
                        <div>
                            <h4 class="font-bold text-purple-600 dark:text-purple-400 mb-3">📋 Tugas Wakil Ketua</h4>
                            <ul class="space-y-2">
                                <li class="flex items-start gap-2"><span class="text-purple-500">•</span> Membantu Ketua dalam menjalankan tugas-tugasnya</li>
                                <li class="flex items-start gap-2"><span class="text-purple-500">•</span> Menggantikan Ketua apabila berhalangan hadir atau tidak dapat melaksanakan tugas</li>
                                <li class="flex items-start gap-2"><span class="text-purple-500">•</span> Mengkoordinasikan kegiatan operasional sehari-hari</li>
                                <li class="flex items-start gap-2"><span class="text-purple-500">•</span> Membantu menyusun rencana kerja dan program koperasi</li>
                                <li class="flex items-start gap-2"><span class="text-purple-500">•</span> Melaksanakan tugas khusus yang didelegasikan oleh Ketua</li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="font-bold text-blue-600 dark:text-blue-400 mb-3">⚡ Wewenang Wakil Ketua</h4>
                            <ul class="space-y-2">
                                <li class="flex items-start gap-2"><span class="text-blue-500">✓</span> Menjalankan wewenang Ketua saat Ketua berhalangan</li>
                                <li class="flex items-start gap-2"><span class="text-blue-500">✓</span> Menandatangani surat-surat bersama Ketua atau atas nama Ketua</li>
                                <li class="flex items-start gap-2"><span class="text-blue-500">✓</span> Memimpin rapat apabila Ketua berhalangan</li>
                            </ul>
                        </div>
                    </div>
                </details>

                <!-- SEKRETARIS -->
                <details class="group">
                    <summary class="cursor-pointer bg-gradient-to-r from-blue-100 to-cyan-100 dark:from-blue-900/30 dark:to-cyan-900/30 p-4 rounded-lg font-bold text-blue-800 dark:text-blue-300 flex items-center justify-between border-l-4 border-blue-500">
                        📝 Sekretaris - Tugas, Wewenang & Tanggung Jawab
                        <svg class="w-5 h-5 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </summary>
                    <div class="p-4 space-y-4 text-gray-700 dark:text-gray-300">
                        <!-- Tugas Sekretaris -->
                        <div>
                            <h4 class="font-bold text-blue-600 dark:text-blue-400 mb-3 flex items-center gap-2">
                                <span class="w-6 h-6 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center text-sm">📋</span>
                                Tugas Sekretaris
                            </h4>
                            <div class="grid md:grid-cols-2 gap-2">
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-lg border border-gray-200 dark:border-gray-700 flex items-start gap-2">
                                    <span class="text-blue-500">•</span>
                                    <span>Menyelenggarakan administrasi umum koperasi</span>
                                </div>
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-lg border border-gray-200 dark:border-gray-700 flex items-start gap-2">
                                    <span class="text-blue-500">•</span>
                                    <span>Membuat dan memelihara <strong>Buku Daftar Anggota</strong></span>
                                </div>
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-lg border border-gray-200 dark:border-gray-700 flex items-start gap-2">
                                    <span class="text-blue-500">•</span>
                                    <span>Membuat dan memelihara <strong>Buku Daftar Pengurus</strong></span>
                                </div>
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-lg border border-gray-200 dark:border-gray-700 flex items-start gap-2">
                                    <span class="text-blue-500">•</span>
                                    <span>Membuat dan memelihara <strong>Buku Notulen Rapat</strong></span>
                                </div>
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-lg border border-gray-200 dark:border-gray-700 flex items-start gap-2">
                                    <span class="text-blue-500">•</span>
                                    <span>Mengelola surat-menyurat (masuk & keluar)</span>
                                </div>
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-lg border border-gray-200 dark:border-gray-700 flex items-start gap-2">
                                    <span class="text-blue-500">•</span>
                                    <span>Menyiapkan undangan dan materi rapat</span>
                                </div>
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-lg border border-gray-200 dark:border-gray-700 flex items-start gap-2">
                                    <span class="text-blue-500">•</span>
                                    <span>Menyusun laporan kegiatan pengurus</span>
                                </div>
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-lg border border-gray-200 dark:border-gray-700 flex items-start gap-2">
                                    <span class="text-blue-500">•</span>
                                    <span>Mengarsipkan dokumen penting koperasi</span>
                                </div>
                            </div>
                        </div>
                        <!-- Wewenang Sekretaris -->
                        <div>
                            <h4 class="font-bold text-cyan-600 dark:text-cyan-400 mb-3">⚡ Wewenang Sekretaris</h4>
                            <ul class="space-y-2">
                                <li class="flex items-start gap-2"><span class="text-cyan-500">✓</span> Menandatangani surat-surat bersama Ketua</li>
                                <li class="flex items-start gap-2"><span class="text-cyan-500">✓</span> Menerbitkan surat keterangan keanggotaan</li>
                                <li class="flex items-start gap-2"><span class="text-cyan-500">✓</span> Mengelola dan mengamankan dokumen koperasi</li>
                                <li class="flex items-start gap-2"><span class="text-cyan-500">✓</span> Memberikan informasi resmi tentang koperasi</li>
                            </ul>
                        </div>
                        <!-- Tanggung Jawab Sekretaris -->
                        <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border-l-4 border-blue-500">
                            <h4 class="font-bold text-blue-600 dark:text-blue-400 mb-2">📂 Tanggung Jawab Sekretaris</h4>
                            <ul class="space-y-1 text-sm">
                                <li>• Bertanggung jawab atas ketertiban dan kelengkapan administrasi</li>
                                <li>• Menjaga kerahasiaan data anggota dan dokumen koperasi</li>
                                <li>• Memastikan kelancaran komunikasi internal dan eksternal</li>
                                <li>• Menyimpan dan mengamankan stempel/cap koperasi</li>
                            </ul>
                        </div>
                    </div>
                </details>

                <!-- BENDAHARA -->
                <details class="group">
                    <summary class="cursor-pointer bg-gradient-to-r from-green-100 to-emerald-100 dark:from-green-900/30 dark:to-emerald-900/30 p-4 rounded-lg font-bold text-green-800 dark:text-green-300 flex items-center justify-between border-l-4 border-green-500">
                        💰 Bendahara - Tugas, Wewenang & Tanggung Jawab
                        <svg class="w-5 h-5 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </summary>
                    <div class="p-4 space-y-4 text-gray-700 dark:text-gray-300">
                        <!-- Tugas Bendahara -->
                        <div>
                            <h4 class="font-bold text-green-600 dark:text-green-400 mb-3 flex items-center gap-2">
                                <span class="w-6 h-6 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center text-sm">📋</span>
                                Tugas Bendahara
                            </h4>
                            <div class="grid md:grid-cols-2 gap-2">
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-lg border border-gray-200 dark:border-gray-700 flex items-start gap-2">
                                    <span class="text-green-500">•</span>
                                    <span>Mengelola keuangan koperasi secara keseluruhan</span>
                                </div>
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-lg border border-gray-200 dark:border-gray-700 flex items-start gap-2">
                                    <span class="text-green-500">•</span>
                                    <span>Menyelenggarakan <strong>pembukuan keuangan</strong> secara tertib</span>
                                </div>
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-lg border border-gray-200 dark:border-gray-700 flex items-start gap-2">
                                    <span class="text-green-500">•</span>
                                    <span>Menerima, menyimpan, dan mengeluarkan uang koperasi</span>
                                </div>
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-lg border border-gray-200 dark:border-gray-700 flex items-start gap-2">
                                    <span class="text-green-500">•</span>
                                    <span>Membuat <strong>laporan keuangan bulanan</strong></span>
                                </div>
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-lg border border-gray-200 dark:border-gray-700 flex items-start gap-2">
                                    <span class="text-green-500">•</span>
                                    <span>Menyusun <strong>neraca dan perhitungan SHU</strong></span>
                                </div>
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-lg border border-gray-200 dark:border-gray-700 flex items-start gap-2">
                                    <span class="text-green-500">•</span>
                                    <span>Mengelola simpanan (pokok, wajib, sukarela) anggota</span>
                                </div>
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-lg border border-gray-200 dark:border-gray-700 flex items-start gap-2">
                                    <span class="text-green-500">•</span>
                                    <span>Memproses pencairan dan angsuran <strong>pinjaman</strong></span>
                                </div>
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-lg border border-gray-200 dark:border-gray-700 flex items-start gap-2">
                                    <span class="text-green-500">•</span>
                                    <span>Membuat <strong>laporan inventaris</strong> koperasi</span>
                                </div>
                            </div>
                        </div>
                        <!-- Wewenang Bendahara -->
                        <div>
                            <h4 class="font-bold text-emerald-600 dark:text-emerald-400 mb-3">⚡ Wewenang Bendahara</h4>
                            <ul class="space-y-2">
                                <li class="flex items-start gap-2"><span class="text-emerald-500">✓</span> Menandatangani bukti penerimaan dan pengeluaran kas</li>
                                <li class="flex items-start gap-2"><span class="text-emerald-500">✓</span> Mengelola rekening bank koperasi bersama Ketua</li>
                                <li class="flex items-start gap-2"><span class="text-emerald-500">✓</span> Melakukan pembayaran sesuai anggaran yang disetujui</li>
                                <li class="flex items-start gap-2"><span class="text-emerald-500">✓</span> Menyetujui atau menolak pengajuan pinjaman sesuai ketentuan</li>
                                <li class="flex items-start gap-2"><span class="text-emerald-500">✓</span> Mengatur penempatan dana (tabungan, deposito, investasi)</li>
                            </ul>
                        </div>
                        <!-- Tanggung Jawab Bendahara -->
                        <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded-lg border-l-4 border-red-500">
                            <h4 class="font-bold text-red-600 dark:text-red-400 mb-2">⚠️ Tanggung Jawab Bendahara</h4>
                            <ul class="space-y-1 text-sm">
                                <li>• <strong>Bertanggung jawab penuh</strong> atas keamanan uang dan harta koperasi</li>
                                <li>• Menanggung kerugian finansial akibat kelalaian atau kesengajaan</li>
                                <li>• Menjaga keakuratan dan kebenaran laporan keuangan</li>
                                <li>• Memastikan kepatuhan terhadap aturan perpajakan</li>
                                <li>• Menyediakan data keuangan untuk keperluan audit</li>
                            </ul>
                        </div>
                        <!-- Buku Wajib -->
                        <div class="bg-amber-50 dark:bg-amber-900/20 p-4 rounded-lg">
                            <h4 class="font-bold text-amber-700 dark:text-amber-400 mb-3">📚 Buku-Buku Wajib yang Dikelola Bendahara</h4>
                            <div class="grid md:grid-cols-3 gap-3">
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-lg text-center">
                                    <span class="text-2xl">📗</span>
                                    <p class="font-medium text-sm mt-1">Buku Kas</p>
                                </div>
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-lg text-center">
                                    <span class="text-2xl">📘</span>
                                    <p class="font-medium text-sm mt-1">Buku Bank</p>
                                </div>
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-lg text-center">
                                    <span class="text-2xl">📙</span>
                                    <p class="font-medium text-sm mt-1">Buku Simpanan Anggota</p>
                                </div>
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-lg text-center">
                                    <span class="text-2xl">📕</span>
                                    <p class="font-medium text-sm mt-1">Buku Pinjaman Anggota</p>
                                </div>
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-lg text-center">
                                    <span class="text-2xl">📓</span>
                                    <p class="font-medium text-sm mt-1">Buku Inventaris</p>
                                </div>
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-lg text-center">
                                    <span class="text-2xl">📔</span>
                                    <p class="font-medium text-sm mt-1">Buku Besar</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </details>

                <!-- Divider -->
                <div class="border-t border-gray-200 dark:border-gray-700 my-6"></div>
                <h3 class="text-lg font-bold text-gray-800 dark:text-white text-center">📜 Dasar Hukum Pengurus</h3>

                <!-- Pengangkatan -->
                <details class="group">
                    <summary class="cursor-pointer bg-gray-50 dark:bg-gray-700 p-4 rounded-lg font-bold text-gray-800 dark:text-white flex items-center justify-between">
                        Pengangkatan Pengurus (Pasal 29-30)
                        <svg class="w-5 h-5 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </summary>
                    <div class="p-4 space-y-3 text-gray-700 dark:text-gray-300">
                        <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg border-l-4 border-purple-500">
                            <p class="font-semibold text-purple-700 dark:text-purple-400">Pengurus dipilih dari dan oleh anggota Koperasi dalam Rapat Anggota.</p>
                        </div>
                        <p><strong>Pasal 29:</strong></p>
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Pengurus dipilih dari dan oleh anggota dalam Rapat Anggota</li>
                            <li>Pengurus merupakan pemegang kuasa Rapat Anggota</li>
                        </ul>
                        <p><strong>Pasal 30:</strong></p>
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Masa jabatan Pengurus paling lama <strong>5 (lima) tahun</strong></li>
                            <li>Dapat dipilih kembali untuk satu periode berikutnya</li>
                            <li>Persyaratan untuk dapat dipilih menjadi Pengurus ditetapkan dalam Anggaran Dasar</li>
                        </ul>
                    </div>
                </details>

                <!-- Tugas Pengurus (Pasal 30) -->
                <details class="group">
                    <summary class="cursor-pointer bg-gray-50 dark:bg-gray-700 p-4 rounded-lg font-bold text-gray-800 dark:text-white flex items-center justify-between">
                        Tugas Umum Pengurus (Pasal 30)
                        <svg class="w-5 h-5 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </summary>
                    <div class="p-4 space-y-3 text-gray-700 dark:text-gray-300">
                        <p>Pengurus bertugas:</p>
                        <div class="grid md:grid-cols-2 gap-3">
                            <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded-lg flex items-start gap-3">
                                <span class="text-green-500 text-xl">✓</span>
                                <p>Mengelola Koperasi dan usahanya</p>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded-lg flex items-start gap-3">
                                <span class="text-green-500 text-xl">✓</span>
                                <p>Mengajukan rancangan rencana kerja serta RAPB Koperasi</p>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded-lg flex items-start gap-3">
                                <span class="text-green-500 text-xl">✓</span>
                                <p>Menyelenggarakan Rapat Anggota</p>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded-lg flex items-start gap-3">
                                <span class="text-green-500 text-xl">✓</span>
                                <p>Mengajukan laporan keuangan dan pertanggungjawaban pelaksanaan tugas</p>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded-lg flex items-start gap-3">
                                <span class="text-green-500 text-xl">✓</span>
                                <p>Memelihara daftar buku anggota dan pengurus</p>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded-lg flex items-start gap-3">
                                <span class="text-green-500 text-xl">✓</span>
                                <p>Menyelenggarakan pembukuan keuangan dan inventaris secara tertib</p>
                            </div>
                        </div>
                    </div>
                </details>

                <!-- Wewenang Pengurus -->
                <details class="group">
                    <summary class="cursor-pointer bg-gray-50 dark:bg-gray-700 p-4 rounded-lg font-bold text-gray-800 dark:text-white flex items-center justify-between">
                        Wewenang Pengurus (Pasal 31-32)
                        <svg class="w-5 h-5 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </summary>
                    <div class="p-4 space-y-3 text-gray-700 dark:text-gray-300">
                        <p><strong>Pasal 31:</strong> Pengurus berwenang:</p>
                        <ul class="list-disc pl-5 space-y-2">
                            <li>Mewakili Koperasi <strong>di dalam dan di luar pengadilan</strong></li>
                            <li>Memutuskan <strong>penerimaan dan penolakan anggota baru</strong> serta pemberhentian anggota sesuai dengan ketentuan dalam Anggaran Dasar</li>
                            <li>Melakukan <strong>tindakan dan upaya</strong> bagi kepentingan dan kemanfaatan Koperasi sesuai dengan tanggung jawabnya dan keputusan Rapat Anggota</li>
                        </ul>
                        <div class="bg-amber-50 dark:bg-amber-900/20 p-4 rounded-lg mt-4">
                            <p class="font-bold text-amber-700 dark:text-amber-400 mb-2">⚠️ Pasal 32 - Pembatasan Wewenang:</p>
                            <p class="text-sm">Pengurus tidak dapat melakukan tindakan yang bertentangan dengan keputusan Rapat Anggota.</p>
                        </div>
                    </div>
                </details>

                <!-- Tanggung Jawab Pengurus -->
                <details class="group">
                    <summary class="cursor-pointer bg-gray-50 dark:bg-gray-700 p-4 rounded-lg font-bold text-gray-800 dark:text-white flex items-center justify-between">
                        Tanggung Jawab Pengurus (Pasal 34-35)
                        <svg class="w-5 h-5 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </summary>
                    <div class="p-4 space-y-3 text-gray-700 dark:text-gray-300">
                        <p><strong>Pasal 34:</strong></p>
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Pengurus, baik bersama-sama maupun sendiri-sendiri, menanggung kerugian yang diderita Koperasi karena tindakan yang dilakukan dengan <strong>kesengajaan atau kelalaiannya</strong>.</li>
                        </ul>
                        <p><strong>Pasal 35:</strong></p>
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Setelah tahun buku Koperasi ditutup, paling lambat <strong>1 (satu) bulan</strong> sebelum diselenggarakan Rapat Anggota Tahunan, Pengurus menyusun <strong>laporan tahunan</strong> yang memuat:</li>
                        </ul>
                        <div class="ml-5 mt-2 space-y-2">
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-6 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center text-xs font-bold text-blue-600">1</span>
                                <span>Neraca akhir dan perhitungan hasil usaha dari tahun buku yang bersangkutan</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-6 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center text-xs font-bold text-blue-600">2</span>
                                <span>Keadaan dan perkembangan usaha Koperasi</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-6 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center text-xs font-bold text-blue-600">3</span>
                                <span>Hal-hal yang perlu mendapat perhatian anggota</span>
                            </div>
                        </div>
                    </div>
                </details>

                <!-- Pengelola -->
                <details class="group">
                    <summary class="cursor-pointer bg-gray-50 dark:bg-gray-700 p-4 rounded-lg font-bold text-gray-800 dark:text-white flex items-center justify-between">
                        Pengelola / Manager (Pasal 33)
                        <svg class="w-5 h-5 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </summary>
                    <div class="p-4 space-y-3 text-gray-700 dark:text-gray-300">
                        <p>Pengurus dapat mengangkat <strong>Pengelola</strong> yang diberi wewenang dan kuasa untuk mengelola usaha.</p>
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Dalam hal Pengurus mengangkat Pengelola, maka Pengurus bertanggung jawab terhadap hasil pengelolaan yang dilaksanakan oleh Pengelola</li>
                            <li>Pengelola bertanggung jawab langsung kepada Pengurus</li>
                            <li>Pengelolaan usaha oleh Pengelola tidak mengurangi tanggung jawab Pengurus</li>
                        </ul>
                    </div>
                </details>
            </div>
        </div>
    </div>

    <!-- TAB 3: PENGAWAS -->
    <div x-show="activeTab === 'pengawas'" class="glass-card p-6">
        <div class="prose dark:prose-invert max-w-none">
            <h2 class="text-xl font-bold text-center mb-6 text-amber-600">
                <span class="text-3xl">🔍</span><br>
                PENGAWAS KOPERASI
            </h2>
            <p class="text-center text-gray-600 dark:text-gray-400 mb-6">Pasal 38-40 UU No. 25 Tahun 1992</p>
            
            <div class="space-y-6">
                <!-- Pengangkatan -->
                <details open class="group">
                    <summary class="cursor-pointer bg-gray-50 dark:bg-gray-700 p-4 rounded-lg font-bold text-gray-800 dark:text-white flex items-center justify-between">
                        Pengangkatan Pengawas (Pasal 38)
                        <svg class="w-5 h-5 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </summary>
                    <div class="p-4 space-y-3 text-gray-700 dark:text-gray-300">
                        <div class="bg-amber-50 dark:bg-amber-900/20 p-4 rounded-lg border-l-4 border-amber-500">
                            <p class="font-semibold text-amber-700 dark:text-amber-400">Pengawas dipilih dari dan oleh anggota Koperasi dalam Rapat Anggota.</p>
                        </div>
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Pengawas bertanggung jawab kepada Rapat Anggota</li>
                            <li>Persyaratan untuk dapat dipilih menjadi Pengawas ditetapkan dalam Anggaran Dasar</li>
                        </ul>
                    </div>
                </details>

                <!-- Tugas Pengawas -->
                <details class="group">
                    <summary class="cursor-pointer bg-gray-50 dark:bg-gray-700 p-4 rounded-lg font-bold text-gray-800 dark:text-white flex items-center justify-between">
                        Tugas Pengawas (Pasal 39)
                        <svg class="w-5 h-5 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </summary>
                    <div class="p-4 space-y-3 text-gray-700 dark:text-gray-300">
                        <p>Pengawas bertugas:</p>
                        <div class="space-y-3">
                            <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg flex items-start gap-4">
                                <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900/30 rounded-full flex items-center justify-center text-amber-600 font-bold">1</div>
                                <div>
                                    <p class="font-bold">Melakukan pengawasan terhadap pelaksanaan kebijaksanaan dan pengelolaan Koperasi</p>
                                    <p class="text-sm text-gray-500">Memantau apakah keputusan RAT dilaksanakan dengan baik</p>
                                </div>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg flex items-start gap-4">
                                <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900/30 rounded-full flex items-center justify-center text-amber-600 font-bold">2</div>
                                <div>
                                    <p class="font-bold">Membuat laporan tertulis tentang hasil pengawasannya</p>
                                    <p class="text-sm text-gray-500">Disampaikan kepada Rapat Anggota</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </details>

                <!-- Wewenang Pengawas -->
                <details class="group">
                    <summary class="cursor-pointer bg-gray-50 dark:bg-gray-700 p-4 rounded-lg font-bold text-gray-800 dark:text-white flex items-center justify-between">
                        Wewenang Pengawas (Pasal 39)
                        <svg class="w-5 h-5 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </summary>
                    <div class="p-4 space-y-3 text-gray-700 dark:text-gray-300">
                        <p>Pengawas berwenang:</p>
                        <div class="space-y-3">
                            <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg flex items-start gap-4">
                                <span class="text-amber-500 text-2xl">📋</span>
                                <div>
                                    <p class="font-bold">Meneliti catatan yang ada pada Koperasi</p>
                                    <p class="text-sm text-gray-500">Termasuk pembukuan keuangan dan dokumen lainnya</p>
                                </div>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg flex items-start gap-4">
                                <span class="text-amber-500 text-2xl">💰</span>
                                <div>
                                    <p class="font-bold">Mendapatkan segala keterangan yang diperlukan</p>
                                    <p class="text-sm text-gray-500">Pengurus wajib memberikan informasi yang diminta</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </details>

                <!-- Pertanggungjawaban -->
                <details class="group">
                    <summary class="cursor-pointer bg-gray-50 dark:bg-gray-700 p-4 rounded-lg font-bold text-gray-800 dark:text-white flex items-center justify-between">
                        Tanggung Jawab Pengawas (Pasal 40)
                        <svg class="w-5 h-5 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </summary>
                    <div class="p-4 space-y-3 text-gray-700 dark:text-gray-300">
                        <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded-lg border-l-4 border-red-500">
                            <p class="font-semibold text-red-700 dark:text-red-400">Pengawas bertanggung jawab kepada Rapat Anggota.</p>
                        </div>
                        <p>Persyaratan dan tata cara pengawasan Koperasi dapat ditentukan dalam Anggaran Dasar dengan memperhatikan keseimbangan antara kepentingan pengelolaan Koperasi dan partisipasi anggota.</p>
                    </div>
                </details>
            </div>
        </div>
    </div>

    <!-- TAB 4: ANGGOTA -->
    <div x-show="activeTab === 'anggota'" class="glass-card p-6">
        <div class="prose dark:prose-invert max-w-none">
            <h2 class="text-xl font-bold text-center mb-6 text-blue-600">
                <span class="text-3xl">🙋</span><br>
                ANGGOTA KOPERASI
            </h2>
            <p class="text-center text-gray-600 dark:text-gray-400 mb-6">Pasal 17-20 UU No. 25 Tahun 1992</p>
            
            <div class="space-y-6">
                <!-- Keanggotaan -->
                <details open class="group">
                    <summary class="cursor-pointer bg-gray-50 dark:bg-gray-700 p-4 rounded-lg font-bold text-gray-800 dark:text-white flex items-center justify-between">
                        Keanggotaan (Pasal 17)
                        <svg class="w-5 h-5 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </summary>
                    <div class="p-4 space-y-3 text-gray-700 dark:text-gray-300">
                        <div class="grid md:grid-cols-2 gap-4">
                            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                                <p class="font-bold text-blue-700 dark:text-blue-400 mb-2">Sifat Keanggotaan</p>
                                <ul class="text-sm space-y-1">
                                    <li>• <strong>Sukarela</strong> - tidak ada paksaan</li>
                                    <li>• <strong>Terbuka</strong> - bagi yang memenuhi syarat</li>
                                </ul>
                            </div>
                            <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                                <p class="font-bold text-green-700 dark:text-green-400 mb-2">Yang Dapat Menjadi Anggota</p>
                                <ul class="text-sm space-y-1">
                                    <li>• Warga Negara Indonesia</li>
                                    <li>• Memiliki kesamaan kepentingan ekonomi</li>
                                    <li>• Bukan badan usaha</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </details>

                <!-- Hak Anggota -->
                <details class="group">
                    <summary class="cursor-pointer bg-gray-50 dark:bg-gray-700 p-4 rounded-lg font-bold text-gray-800 dark:text-white flex items-center justify-between">
                        Hak Anggota (Pasal 20)
                        <svg class="w-5 h-5 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </summary>
                    <div class="p-4 space-y-3 text-gray-700 dark:text-gray-300">
                        <p class="font-semibold text-green-600 mb-4">Setiap anggota mempunyai hak:</p>
                        <div class="grid md:grid-cols-2 gap-3">
                            <div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 p-4 rounded-lg border border-green-200 dark:border-green-800">
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="text-2xl">🗳️</span>
                                    <span class="font-bold">Menghadiri, menyatakan pendapat, dan memberikan suara dalam Rapat Anggota</span>
                                </div>
                            </div>
                            <div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 p-4 rounded-lg border border-green-200 dark:border-green-800">
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="text-2xl">👥</span>
                                    <span class="font-bold">Memilih dan/atau dipilih menjadi anggota Pengurus atau Pengawas</span>
                                </div>
                            </div>
                            <div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 p-4 rounded-lg border border-green-200 dark:border-green-800">
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="text-2xl">❓</span>
                                    <span class="font-bold">Meminta diadakan Rapat Anggota menurut ketentuan dalam Anggaran Dasar</span>
                                </div>
                            </div>
                            <div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 p-4 rounded-lg border border-green-200 dark:border-green-800">
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="text-2xl">💬</span>
                                    <span class="font-bold">Mengemukakan pendapat atau saran kepada Pengurus di luar Rapat Anggota</span>
                                </div>
                            </div>
                            <div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 p-4 rounded-lg border border-green-200 dark:border-green-800">
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="text-2xl">🛒</span>
                                    <span class="font-bold">Memanfaatkan Koperasi dan mendapat pelayanan yang sama antara sesama anggota</span>
                                </div>
                            </div>
                            <div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 p-4 rounded-lg border border-green-200 dark:border-green-800">
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="text-2xl">📊</span>
                                    <span class="font-bold">Mendapatkan keterangan mengenai perkembangan Koperasi</span>
                                </div>
                            </div>
                            <div class="md:col-span-2 bg-gradient-to-r from-amber-50 to-yellow-50 dark:from-amber-900/20 dark:to-yellow-900/20 p-4 rounded-lg border border-amber-200 dark:border-amber-800">
                                <div class="flex items-center gap-3">
                                    <span class="text-2xl">💰</span>
                                    <span class="font-bold">Mendapatkan pembagian Sisa Hasil Usaha (SHU)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </details>

                <!-- Kewajiban Anggota -->
                <details class="group">
                    <summary class="cursor-pointer bg-gray-50 dark:bg-gray-700 p-4 rounded-lg font-bold text-gray-800 dark:text-white flex items-center justify-between">
                        Kewajiban Anggota (Pasal 20)
                        <svg class="w-5 h-5 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </summary>
                    <div class="p-4 space-y-3 text-gray-700 dark:text-gray-300">
                        <p class="font-semibold text-blue-600 mb-4">Setiap anggota mempunyai kewajiban:</p>
                        <div class="space-y-3">
                            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg flex items-start gap-4">
                                <div class="w-10 h-10 bg-blue-200 dark:bg-blue-800 rounded-full flex items-center justify-center text-blue-700 dark:text-blue-300 font-bold">1</div>
                                <div>
                                    <p class="font-bold">Mematuhi Anggaran Dasar dan Anggaran Rumah Tangga serta keputusan yang telah disepakati dalam Rapat Anggota</p>
                                </div>
                            </div>
                            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg flex items-start gap-4">
                                <div class="w-10 h-10 bg-blue-200 dark:bg-blue-800 rounded-full flex items-center justify-center text-blue-700 dark:text-blue-300 font-bold">2</div>
                                <div>
                                    <p class="font-bold">Berpartisipasi dalam kegiatan usaha yang diselenggarakan oleh Koperasi</p>
                                </div>
                            </div>
                            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg flex items-start gap-4">
                                <div class="w-10 h-10 bg-blue-200 dark:bg-blue-800 rounded-full flex items-center justify-center text-blue-700 dark:text-blue-300 font-bold">3</div>
                                <div>
                                    <p class="font-bold">Mengembangkan dan memelihara kebersamaan berdasar atas asas kekeluargaan</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </details>

                <!-- Berakhirnya Keanggotaan -->
                <details class="group">
                    <summary class="cursor-pointer bg-gray-50 dark:bg-gray-700 p-4 rounded-lg font-bold text-gray-800 dark:text-white flex items-center justify-between">
                        Berakhirnya Keanggotaan (Pasal 19)
                        <svg class="w-5 h-5 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </summary>
                    <div class="p-4 space-y-3 text-gray-700 dark:text-gray-300">
                        <p>Keanggotaan Koperasi berakhir apabila:</p>
                        <div class="grid md:grid-cols-3 gap-3">
                            <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded-lg text-center">
                                <span class="text-3xl mb-2 block">⚰️</span>
                                <p class="font-bold">Meninggal Dunia</p>
                            </div>
                            <div class="bg-orange-50 dark:bg-orange-900/20 p-4 rounded-lg text-center">
                                <span class="text-3xl mb-2 block">👋</span>
                                <p class="font-bold">Mengundurkan Diri</p>
                                <p class="text-xs text-gray-500">Atas permintaan sendiri</p>
                            </div>
                            <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg text-center">
                                <span class="text-3xl mb-2 block">🚫</span>
                                <p class="font-bold">Diberhentikan</p>
                                <p class="text-xs text-gray-500">Karena pelanggaran AD/ART</p>
                            </div>
                        </div>
                    </div>
                </details>
            </div>
        </div>
    </div>

    <!-- Footer Info -->
    <div class="glass-card p-4 text-center text-sm text-gray-500 dark:text-gray-400">
        <p>📚 Referensi: <strong>Undang-Undang Republik Indonesia Nomor 25 Tahun 1992 tentang Perkoperasian</strong></p>
        <p class="mt-1">Dokumen ini disusun untuk keperluan edukasi anggota koperasi.</p>
    </div>
</div>
@endsection
