@extends('layouts.app')

@section('title', 'Dokumentasi Aplikasi')

@section('content')
<div class="relative min-h-screen" x-data="{ activeSection: 'intro' }">
    
    <!-- Hero Header -->
    <div class="mb-6 rounded-3xl bg-gradient-to-r from-gray-900 to-gray-800 dark:from-gray-800 dark:to-gray-900 text-white p-8 md:p-12 shadow-2xl overflow-hidden relative">
        <div class="absolute top-0 right-0 -mt-10 -mr-10 w-64 h-64 bg-white opacity-5 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 -mb-10 -ml-10 w-40 h-40 bg-primary-500 opacity-20 rounded-full blur-2xl"></div>
        
        <div class="relative z-10 max-w-3xl">
            <h1 class="text-3xl md:text-5xl font-extrabold tracking-tight mb-4">
                Pusat <span class="text-primary-400">Bantuan</span> & Dokumentasi
            </h1>
            <p class="text-lg text-gray-300 leading-relaxed">
                Panduan resmi penggunaan Sistem Koperasi Digital PT. SPINDO TBK untuk anggota dan pengurus.
            </p>
        </div>
    </div>

    <!-- Horizontal Sticky Navigation -->
    <div class="sticky top-0 z-30 bg-gray-50/95 dark:bg-gray-900/95 backdrop-blur-md border-b border-gray-200 dark:border-gray-700 mb-10 -mx-4 px-4 md:-mx-8 md:px-8">
        <div class="flex items-center gap-1 overflow-x-auto no-scrollbar py-3">
            <button @click="activeSection = 'intro'; document.getElementById('intro').scrollIntoView({behavior: 'smooth'})" 
                    :class="activeSection === 'intro' ? 'bg-primary-600 text-white shadow-lg shadow-primary-500/30' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-800'"
                    class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium transition-all duration-200">
                🚀 Mulai
            </button>
            <button @click="activeSection = 'member'; document.getElementById('member').scrollIntoView({behavior: 'smooth'})" 
                    :class="activeSection === 'member' ? 'bg-primary-600 text-white shadow-lg shadow-primary-500/30' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-800'"
                    class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium transition-all duration-200">
                👤 Panduan Anggota
            </button>
            <button @click="activeSection = 'admin'; document.getElementById('admin').scrollIntoView({behavior: 'smooth'})" 
                    :class="activeSection === 'admin' ? 'bg-primary-600 text-white shadow-lg shadow-primary-500/30' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-800'"
                    class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium transition-all duration-200">
                🛡️ Panduan Admin
            </button>
            <button @click="activeSection = 'finance'; document.getElementById('finance').scrollIntoView({behavior: 'smooth'})" 
                    :class="activeSection === 'finance' ? 'bg-primary-600 text-white shadow-lg shadow-primary-500/30' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-800'"
                    class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium transition-all duration-200">
                💹 Keuangan/SHU
            </button>
            <button @click="activeSection = 'settings'; document.getElementById('settings').scrollIntoView({behavior: 'smooth'})" 
                    :class="activeSection === 'settings' ? 'bg-primary-600 text-white shadow-lg shadow-primary-500/30' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-800'"
                    class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium transition-all duration-200">
                ⚙️ Pengaturan
            </button>
            <button @click="activeSection = 'faq'; document.getElementById('faq').scrollIntoView({behavior: 'smooth'})" 
                    :class="activeSection === 'faq' ? 'bg-primary-600 text-white shadow-lg shadow-primary-500/30' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-800'"
                    class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium transition-all duration-200">
                ❓ FAQ
            </button>
            @can('admin')
            <button @click="activeSection = 'deploy'; document.getElementById('deploy').scrollIntoView({behavior: 'smooth'})" 
                    :class="activeSection === 'deploy' ? 'bg-primary-600 text-white shadow-lg shadow-primary-500/30' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-800'"
                    class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium transition-all duration-200">
                🚀 Deployment
            </button>
            @endcan
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-5xl mx-auto space-y-16 pb-32">
        
        <!-- Intro -->
        <section id="intro" class="scroll-mt-40" x-intersect="activeSection = 'intro'">
            <div class="flex items-center gap-3 mb-6">
                <div class="h-8 w-1 bg-primary-500 rounded-full"></div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Selamat Datang</h2>
            </div>
            <div class="prose prose-lg dark:prose-invert text-gray-600 dark:text-gray-400 max-w-none">
                <p>
                    Sistem Manajemen Koperasi Digital (SMKD) adalah platform terpadu untuk memudahkan seluruh kegiatan perkoperasian di PT. SPINDO TBK secara transparan dan efisien.
                </p>
                <div class="grid md:grid-cols-3 gap-6 mt-8">
                    <div class="bg-blue-50 dark:bg-blue-900/20 p-6 rounded-2xl border border-blue-100 dark:border-blue-800">
                        <div class="text-3xl mb-3">📱</div>
                        <h4 class="font-bold text-blue-900 dark:text-blue-100 mb-2">Akses Kapanpun</h4>
                        <p class="text-sm text-blue-700 dark:text-blue-300">Cek saldo simpanan dan pinjaman langsung dari HP Anda melalui dashboard anggota.</p>
                    </div>
                    <div class="bg-green-50 dark:bg-green-900/20 p-6 rounded-2xl border border-green-100 dark:border-green-800">
                        <div class="text-3xl mb-3">🛒</div>
                        <h4 class="font-bold text-green-900 dark:text-green-100 mb-2">Koperasi Mart</h4>
                        <p class="text-sm text-green-700 dark:text-green-300">Belanja kebutuhan harian di toko fisik atau via E-Commerce dengan sistem poin.</p>
                    </div>
                    <div class="bg-purple-50 dark:bg-purple-900/20 p-6 rounded-2xl border border-purple-100 dark:border-purple-800">
                        <div class="text-3xl mb-3">🤖</div>
                        <h4 class="font-bold text-purple-900 dark:text-purple-100 mb-2">AI Assistant</h4>
                        <p class="text-sm text-purple-700 dark:text-purple-300">Butuh bantuan cepat? Tanyakan pada AI Assistant yang tersedia di setiap halaman.</p>
                    </div>
                </div>
            </div>
        </section>

        <hr class="border-gray-100 dark:border-gray-700">

        <!-- Member Guide -->
        <section id="member" class="scroll-mt-40" x-intersect="activeSection = 'member'">
            <div class="flex items-center gap-3 mb-6">
                <div class="h-8 w-1 bg-green-500 rounded-full"></div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Panduan Anggota</h2>
            </div>
            
            <div class="space-y-8">
                <div class="bg-white dark:bg-gray-800 p-8 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm relative overflow-hidden group">
                    <div class="absolute -top-6 -right-6 text-8xl opacity-5 transition-transform group-hover:scale-110">👤</div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">1. Registrasi & Login</h3>
                    <ul class="space-y-4">
                        <li class="flex gap-4">
                            <span class="flex-shrink-0 w-6 h-6 bg-green-500 text-white rounded-full flex items-center justify-center text-xs font-bold">1</span>
                            <p class="text-gray-600 dark:text-gray-400 text-sm">Akses halaman login, klik **"Daftar Sekarang"** jika belum memiliki akun.</p>
                        </li>
                        <li class="flex gap-4">
                            <span class="flex-shrink-0 w-6 h-6 bg-green-500 text-white rounded-full flex items-center justify-center text-xs font-bold">2</span>
                            <p class="text-gray-600 dark:text-gray-400 text-sm">Isi data diri sesuai KTP dan NIK Karyawan. Gunakan email yang aktif.</p>
                        </li>
                        <li class="flex gap-4">
                            <span class="flex-shrink-0 w-6 h-6 bg-green-500 text-white rounded-full flex items-center justify-center text-xs font-bold">3</span>
                            <p class="text-gray-600 dark:text-gray-400 text-sm">Tunggu persetujuan (approval) dari Pengurus Koperasi sebelum Anda dapat login.</p>
                        </li>
                    </ul>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-100 dark:border-gray-700">
                        <h4 class="font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <span class="text-amber-500">💰</span> Simpanan & Saldo
                        </h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                            Cek menu **Simpanan** untuk melihat saldo Simpanan Pokok, Wajib, dan Sukarela. Anda dapat melakukan top-up simpanan sukarela melalui menu tersebut dan mengunggah bukti transfer.
                        </p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-100 dark:border-gray-700">
                        <h4 class="font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <span class="text-red-500">💸</span> Pinjaman
                        </h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                            Pilih menu **Pinjaman** > **Ajukan Pinjaman**. Gunakan simulasi pinjaman untuk menghitung angsuran bulanan. Status pengajuan Anda dapat dipantau di riwayat pinjaman.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <hr class="border-gray-100 dark:border-gray-700">

        <!-- Admin Guide -->
        <section id="admin" class="scroll-mt-40" x-intersect="activeSection = 'admin'">
            <div class="flex items-center gap-3 mb-6">
                <div class="h-8 w-1 bg-blue-500 rounded-full"></div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Panduan Pengurus (Admin)</h2>
            </div>
            
            <div class="grid md:grid-cols-2 gap-6">
                <div class="bg-gray-50 dark:bg-gray-800/50 p-6 rounded-2xl border border-gray-100 dark:border-gray-700">
                    <h4 class="font-bold text-gray-900 dark:text-white mb-3">Manajemen Anggota</h4>
                    <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-2 list-disc pl-5">
                        <li>Verifikasi pendaftaran anggota baru di menu **Member Waiting List**.</li>
                        <li>Cetak Kartu Anggota Digital dengan QR Code untuk setiap anggota.</li>
                        <li>Kelola limit kredit belanja anggota di Koperasi Mart.</li>
                    </ul>
                </div>
                <div class="bg-gray-50 dark:bg-gray-800/50 p-6 rounded-2xl border border-gray-100 dark:border-gray-700">
                    <h4 class="font-bold text-gray-900 dark:text-white mb-3">POS & Inventori</h4>
                    <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-2 list-disc pl-5">
                        <li>Gunakan menu **Point of Sales (POS)** untuk melayani transaksi langsung.</li>
                        <li>Scan barcode produk untuk input cepat harganya.</li>
                        <li>Pantau stok produk dan terima notifikasi saat stok menipis.</li>
                    </ul>
                </div>
            </div>
        </section>

        <hr class="border-gray-100 dark:border-gray-700">

        <!-- Finance/SHU -->
        <section id="finance" class="scroll-mt-40" x-intersect="activeSection = 'finance'">
            <div class="flex items-center gap-3 mb-6">
                <div class="h-8 w-1 bg-amber-500 rounded-full"></div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Keuangan & SHU</h2>
            </div>
            
            <div class="prose prose-lg dark:prose-invert text-gray-600 dark:text-gray-400 max-w-none">
                <p>
                    Sistem dilengkapi modul akuntansi otomatis yang mencatat setiap transaksi ke dalam jurnal umum, buku besar, hingga laporan laba rugi.
                </p>
                <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 p-8 shadow-sm">
                    <h4 class="font-bold text-gray-900 dark:text-white mb-4">Sisa Hasil Usaha (SHU)</h4>
                    <ol class="text-sm space-y-4">
                        <li class="flex gap-4">
                            <span class="font-bold text-amber-500">01</span>
                            <p>Buka **Pengaturan SHU** di menu Settings untuk menetapkan persentase alokasi (Cadangan, Jasa Modal, Jasa Anggota, dll).</p>
                        </li>
                        <li class="flex gap-4">
                            <span class="font-bold text-amber-500">02</span>
                            <p>Setelah periode tahun berakhir, klik **"Hitung SHU"** untuk mengalokasikan total keuntungan secara otomatis kepada seluruh anggota.</p>
                        </li>
                        <li class="flex gap-4">
                            <span class="font-bold text-amber-500">03</span>
                            <p>Anggota dapat melihat rincian SHU yang mereka terima di dashboard masing-masing.</p>
                        </li>
                    </ol>
                </div>
            </div>
        </section>

        <hr class="border-gray-100 dark:border-gray-700">

        <!-- Settings -->
        <section id="settings" class="scroll-mt-40" x-intersect="activeSection = 'settings'">
            <div class="flex items-center gap-3 mb-6">
                <div class="h-8 w-1 bg-gray-500 rounded-full"></div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Pengaturan Khusus</h2>
            </div>
            
            <div class="grid md:grid-cols-2 gap-6">
                <div class="bg-purple-50 dark:bg-purple-900/10 p-6 rounded-2xl border border-purple-100 dark:border-purple-800">
                    <h4 class="font-bold text-purple-900 dark:text-purple-100 mb-2 flex items-center gap-2">
                        🤖 AI Assistant Config
                    </h4>
                    <p class="text-sm text-purple-700 dark:text-purple-300">
                        Admin dapat mengaktifkan AI Assistant melalui menu **Settings > AI Assistant**. Pilih provider (Ollama/OpenAI), masukkan API key, dan tentukan prompt sistem agar AI membantu menjawab pertanyaan seputar koperasi.
                    </p>
                </div>
                <div class="bg-green-50 dark:bg-green-900/10 p-6 rounded-2xl border border-green-100 dark:border-green-800">
                    <h4 class="font-bold text-green-900 dark:text-green-100 mb-2 flex items-center gap-2">
                        💬 WhatsApp Floating
                    </h4>
                    <p class="text-sm text-green-700 dark:text-green-300">
                        Ubah nomor WhatsApp admin di **Settings > Pengaturan Sistem**. Tombol WA akan muncul secara otomatis di pojok layar aplikasi untuk memudahkan komunikasi langsung.
                    </p>
                </div>
            </div>
        </section>

        <hr class="border-gray-100 dark:border-gray-700">

        <!-- FAQ -->
        <section id="faq" class="scroll-mt-40" x-intersect="activeSection = 'faq'">
             <div class="flex items-center gap-3 mb-6">
                <div class="h-8 w-1 bg-pink-500 rounded-full"></div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Pertanyaan Sering Diajukan (FAQ)</h2>
            </div>
            
            <div class="space-y-4">
                <div x-data="{ open: false }" class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
                    <button @click="open = !open" class="flex justify-between items-center w-full p-5 text-left font-bold text-gray-900 dark:text-white focus:outline-none hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <span>❓ Bagaimana cara mencetak kartu anggota?</span>
                        <svg class="w-5 h-5 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open" x-collapse class="p-5 pt-0 text-gray-600 dark:text-gray-300 border-t border-gray-50 dark:border-gray-700/50">
                        Pergi ke menu **Anggota**, pilih salah satu anggota, lalu klik tombol **"Cetak Kartu"**. Sistem akan menghasilkan PDF berisi ID Card dengan QR Code yang siap dicetak.
                    </div>
                </div>

                <div x-data="{ open: false }" class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
                    <button @click="open = !open" class="flex justify-between items-center w-full p-5 text-left font-bold text-gray-900 dark:text-white focus:outline-none hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <span>❓ Lupa password?</span>
                        <svg class="w-5 h-5 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open" x-collapse class="p-5 pt-0 text-gray-600 dark:text-gray-300 border-t border-gray-50 dark:border-gray-700/50">
                        Hubungi Admin/Pengurus Koperasi melalui tombol WhatsApp untuk melakukan reset password akun Anda.
                    </div>
                </div>

                <div x-data="{ open: false }" class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
                    <button @click="open = !open" class="flex justify-between items-center w-full p-5 text-left font-bold text-gray-900 dark:text-white focus:outline-none hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <span>❓ Bagaimana sistem poin belanja bekerja?</span>
                        <svg class="w-5 h-5 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open" x-collapse class="p-5 pt-0 text-gray-600 dark:text-gray-300 border-t border-gray-50 dark:border-gray-700/50">
                        Setiap pembelanjaan dengan nominal tertentu (misal: Rp 10.000) akan mendapatkan 1 Poin Performa. Poin ini dapat ditukarkan kembali menjadi saldo tabungan atau potongan belanja sesuai kebijakan RAT.
                    </div>
                </div>
            </div>
        </section>

        <hr class="border-gray-100 dark:border-gray-700">

        <!-- Deployment Guide (Admin Only) -->
        @can('admin')
        <section id="deploy" class="scroll-mt-40" x-intersect="activeSection = 'deploy'">
            <div class="flex items-center gap-3 mb-6">
                <div class="h-8 w-1 bg-red-500 rounded-full"></div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">🚀 Panduan Deployment & Production</h2>
            </div>
            
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-2xl p-4 mb-6">
                <p class="text-sm text-red-700 dark:text-red-300"><strong>⚠️ Khusus Admin:</strong> Bagian ini berisi panduan teknis untuk deployment ke production.</p>
            </div>

            <div class="space-y-8">
                <!-- Quick Reset Commands -->
                <div class="bg-white dark:bg-gray-800 p-8 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">🔄 Reset Data untuk Production</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">Gunakan perintah berikut untuk mereset database sebelum production:</p>
                    
                    <div class="space-y-4">
                        <div class="bg-gray-900 rounded-xl p-4 overflow-x-auto">
                            <code class="text-green-400 text-sm"># Reset interaktif (dengan konfirmasi)<br>php artisan koperasi:reset-for-production</code>
                        </div>
                        <div class="bg-gray-900 rounded-xl p-4 overflow-x-auto">
                            <code class="text-green-400 text-sm"># Reset + fresh migration (RECOMMENDED)<br>php artisan koperasi:reset-for-production --fresh --force</code>
                        </div>
                        <div class="bg-gray-900 rounded-xl p-4 overflow-x-auto">
                            <code class="text-green-400 text-sm"># Hanya jalankan production seeder<br>php artisan db:seed --class=ProductionSeeder</code>
                        </div>
                    </div>
                </div>

                <!-- Production Checklist -->
                <div class="bg-white dark:bg-gray-800 p-8 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">✅ Checklist Production</h3>
                    <ul class="space-y-3">
                        <li class="flex gap-3 items-start">
                            <span class="flex-shrink-0 w-6 h-6 bg-green-500 text-white rounded flex items-center justify-center text-xs">1</span>
                            <p class="text-gray-600 dark:text-gray-400">Set <code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-xs">APP_ENV=production</code> dan <code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-xs">APP_DEBUG=false</code> di file .env</p>
                        </li>
                        <li class="flex gap-3 items-start">
                            <span class="flex-shrink-0 w-6 h-6 bg-green-500 text-white rounded flex items-center justify-center text-xs">2</span>
                            <p class="text-gray-600 dark:text-gray-400">Jalankan reset database dengan perintah di atas</p>
                        </li>
                        <li class="flex gap-3 items-start">
                            <span class="flex-shrink-0 w-6 h-6 bg-green-500 text-white rounded flex items-center justify-center text-xs">3</span>
                            <p class="text-gray-600 dark:text-gray-400">Login dengan akun admin default: <strong>admin@koperasi.com</strong> / <strong>admin123</strong></p>
                        </li>
                        <li class="flex gap-3 items-start">
                            <span class="flex-shrink-0 w-6 h-6 bg-red-500 text-white rounded flex items-center justify-center text-xs">!</span>
                            <p class="text-gray-600 dark:text-gray-400"><strong class="text-red-600">WAJIB:</strong> Segera ganti password admin setelah login pertama!</p>
                        </li>
                        <li class="flex gap-3 items-start">
                            <span class="flex-shrink-0 w-6 h-6 bg-green-500 text-white rounded flex items-center justify-center text-xs">4</span>
                            <p class="text-gray-600 dark:text-gray-400">Konfigurasi Settings (nama koperasi, logo, WhatsApp, AI, dll)</p>
                        </li>
                        <li class="flex gap-3 items-start">
                            <span class="flex-shrink-0 w-6 h-6 bg-green-500 text-white rounded flex items-center justify-center text-xs">5</span>
                            <p class="text-gray-600 dark:text-gray-400">Import data anggota via menu <strong>Import Data → Import Anggota</strong></p>
                        </li>
                        <li class="flex gap-3 items-start">
                            <span class="flex-shrink-0 w-6 h-6 bg-green-500 text-white rounded flex items-center justify-center text-xs">6</span>
                            <p class="text-gray-600 dark:text-gray-400">Jalankan <code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-xs">php artisan config:cache</code> untuk optimasi</p>
                        </li>
                    </ul>
                </div>

                <!-- Import Guide -->
                <div class="bg-white dark:bg-gray-800 p-8 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">📥 Import Data Anggota</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">Format kolom Excel untuk import anggota:</p>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-3 py-2 text-left font-medium text-gray-700 dark:text-gray-300">Kolom</th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-700 dark:text-gray-300">Wajib</th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-700 dark:text-gray-300">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                <tr><td class="px-3 py-2">nama</td><td class="px-3 py-2">✅</td><td class="px-3 py-2 text-gray-500">Nama lengkap</td></tr>
                                <tr><td class="px-3 py-2">email</td><td class="px-3 py-2">✅</td><td class="px-3 py-2 text-gray-500">Email unik untuk login</td></tr>
                                <tr><td class="px-3 py-2">no_hp</td><td class="px-3 py-2">-</td><td class="px-3 py-2 text-gray-500">Untuk WhatsApp Bot</td></tr>
                                <tr><td class="px-3 py-2">role</td><td class="px-3 py-2">-</td><td class="px-3 py-2 text-gray-500">admin/manager/member (default: member)</td></tr>
                                <tr><td class="px-3 py-2">id_anggota</td><td class="px-3 py-2">✅</td><td class="px-3 py-2 text-gray-500">ID unik anggota</td></tr>
                                <tr><td class="px-3 py-2">tanggal_bergabung</td><td class="px-3 py-2">✅</td><td class="px-3 py-2 text-gray-500">Format: YYYY-MM-DD</td></tr>
                                <tr><td class="px-3 py-2">password</td><td class="px-3 py-2">-</td><td class="px-3 py-2 text-gray-500">Default: password123</td></tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        <a href="{{ route('import.index') }}" class="btn-primary inline-flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                            Buka Halaman Import
                        </a>
                    </div>
                </div>

                <!-- Reset Data -->
                <div class="bg-white dark:bg-gray-800 p-8 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">🗑️ Reset Data</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">Untuk menghapus data tanpa harus satu per satu:</p>
                    
                    <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                        <li>• Buka menu <strong>Import Data</strong></li>
                        <li>• Scroll ke bawah ke bagian <strong>"Reset Data"</strong></li>
                        <li>• Pilih data yang ingin dihapus (Anggota/Simpanan/Pinjaman)</li>
                        <li>• <span class="text-green-600">✅ Akun Admin tidak akan terhapus</span></li>
                    </ul>
                    
                    <div class="mt-4 p-4 bg-amber-50 dark:bg-amber-900/20 rounded-xl border border-amber-200 dark:border-amber-800">
                        <p class="text-sm text-amber-700 dark:text-amber-300"><strong>💡 Tips:</strong> Backup data terlebih dahulu via menu Pengaturan → Backup & Restore</p>
                    </div>
                </div>

                <!-- Security -->
                <div class="bg-white dark:bg-gray-800 p-8 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">🔒 Security Recommendations</h3>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div class="p-4 bg-red-50 dark:bg-red-900/20 rounded-xl border border-red-100 dark:border-red-800">
                            <h4 class="font-bold text-red-800 dark:text-red-200 mb-2">Wajib</h4>
                            <ul class="text-sm text-red-700 dark:text-red-300 space-y-1">
                                <li>• Ganti password admin default</li>
                                <li>• Aktifkan HTTPS</li>
                                <li>• Set APP_DEBUG=false</li>
                            </ul>
                        </div>
                        <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-xl border border-green-100 dark:border-green-800">
                            <h4 class="font-bold text-green-800 dark:text-green-200 mb-2">Disarankan</h4>
                            <ul class="text-sm text-green-700 dark:text-green-300 space-y-1">
                                <li>• Backup database rutin</li>
                                <li>• Rate limiting aktif</li>
                                <li>• Monitor audit logs</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        @endcan

        <!-- Help Footer -->
        <div class="p-8 rounded-3xl bg-gray-100 dark:bg-gray-800 text-center border border-gray-200 dark:border-gray-700 shadow-inner">
             <h4 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Masih butuh bantuan?</h4>
             <p class="text-gray-500 mb-6 font-medium">Tim IT Koperasi siap membantu Anda kapan saja.</p>
             @php
                $waNum = \App\Models\Setting::get('whatsapp_number');
             @endphp
             @if($waNum)
             <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $waNum) }}" class="btn-primary inline-flex items-center gap-2">
                 <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382l-2.03-.967-.67.15-1.164.199c-.15 2.39-1.475.788-2.059.297-.606.134-.52.149-.497.099-.52-.075-2.207-.242-.51-.173-.01-.57.074.372.297 2.479 1.462 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                 Chat Admin via WhatsApp
             </a>
             @endif
        </div>

    </div>
</div>
@endsection
