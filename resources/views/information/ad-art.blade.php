@extends('layouts.app')

@section('title', 'AD-ART Koperasi')

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
        .prose {
            max-width: none !important;
        }
    }
</style>
<div class="space-y-6" x-data="{ activeTab: 'ad', activeChapter: 1 }">
    <!-- Header -->
    <div class="page-header">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="page-title">Anggaran Dasar & Anggaran Rumah Tangga</h1>
                <span class="px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-xs font-bold rounded-full border border-green-200 dark:border-green-800">
                    VERSI {{ $settings['ad_art_version'] ?? '3.0' }}
                </span>
            </div>
            <p class="page-subtitle">{{ $settings['coop_name'] ?? 'Koperasi Karyawan PT. SPINDO TBK' }}</p>
        </div>
        <div class="flex items-center gap-2 no-print">
            <button onclick="window.open('{{ route('ad-art.download-pdf') }}?print=1', '_blank')" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center gap-2 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Dokumen Resmi
            </button>
            <a href="{{ route('ad-art.download-pdf') }}" class="btn-primary flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Download PDF
            </a>
        </div>
    </div>

    <!-- Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="glass-card p-4 flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center text-2xl">📜</div>
            <div>
                <p class="text-sm text-gray-500">Disahkan</p>
                <p class="font-bold text-gray-800 dark:text-white">{{ $settings['ad_art_ratification_date'] ?? 'RAT 2024' }}</p>
            </div>
        </div>
        <div class="glass-card p-4 flex items-center gap-4">
            <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center text-2xl">⚖️</div>
            <div>
                <p class="text-sm text-gray-500">Dasar Hukum</p>
                <p class="font-bold text-gray-800 dark:text-white">{{ $settings['coop_legal_principles'] ?? 'UU No. 17/2012' }}</p>
            </div>
        </div>
        <div class="glass-card p-4 flex items-center gap-4">
            <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center text-2xl">🏛️</div>
            <div>
                <p class="text-sm text-gray-500">Badan Hukum</p>
                <p class="font-bold text-gray-800 dark:text-white">{{ $settings['coop_legal_number'] ?? 'No. 123/BH/2020' }}</p>
            </div>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="flex gap-2 border-b border-gray-200 dark:border-gray-700">
        <button @click="activeTab = 'ad'" class="px-6 py-3 font-semibold text-sm transition-colors border-b-2" :class="activeTab === 'ad' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'">
            📋 Anggaran Dasar
        </button>
        <button @click="activeTab = 'art'" class="px-6 py-3 font-semibold text-sm transition-colors border-b-2" :class="activeTab === 'art' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'">
            📖 Anggaran Rumah Tangga
        </button>
    </div>

    <!-- ANGGARAN DASAR -->
    <div x-show="activeTab === 'ad'" class="glass-card p-6">
        <div class="prose dark:prose-invert max-w-none">
            <h2 class="text-xl font-bold text-center mb-6 text-green-600">ANGGARAN DASAR<br>KOPERASI KARYAWAN PT. SPINDO TBK</h2>
            
            <div class="space-y-6">
                <!-- BAB I -->
                <details open class="group">
                    <summary class="cursor-pointer bg-gray-50 dark:bg-gray-700 p-4 rounded-lg font-bold text-gray-800 dark:text-white flex items-center justify-between">
                        BAB I: NAMA, TEMPAT & WILAYAH KERJA
                        <svg class="w-5 h-5 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </summary>
                    <div class="p-4 space-y-3 text-gray-700 dark:text-gray-300">
                        <p><strong>Pasal 1</strong></p>
                        <ol class="list-decimal pl-5 space-y-2">
                            <li>Koperasi ini bernama <strong>{{ $settings['coop_name'] ?? 'KOPERASI KARYAWAN PT. SPINDO TBK' }}</strong>.</li>
                            <li>Koperasi berkedudukan di {{ $settings['coop_address'] ?? 'Karawang, Jawa Barat' }}.</li>
                            <li>Wilayah kerja meliputi seluruh unit kerja {{ $settings['coop_name'] ?? 'PT. SPINDO TBK' }}.</li>
                        </ol>
                    </div>
                </details>

                <!-- BAB II -->
                <details class="group">
                    <summary class="cursor-pointer bg-gray-50 dark:bg-gray-700 p-4 rounded-lg font-bold text-gray-800 dark:text-white flex items-center justify-between">
                        BAB II: LANDASAN, ASAS & PRINSIP
                        <svg class="w-5 h-5 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </summary>
                    <div class="p-4 space-y-3 text-gray-700 dark:text-gray-300">
                        <p><strong>Pasal 2 - Landasan & Asas</strong></p>
                        <p>Koperasi berlandaskan Pancasila dan UUD 1945 serta berasaskan kekeluargaan.</p>
                        <p><strong>Pasal 3 - Prinsip Koperasi</strong></p>
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Keanggotaan bersifat sukarela dan terbuka</li>
                            <li>Pengelolaan dilakukan secara demokratis</li>
                            <li>Pembagian SHU secara adil sesuai jasa anggota</li>
                            <li>Pemberian balas jasa terbatas terhadap modal</li>
                            <li>Kemandirian</li>
                            <li>Pendidikan perkoperasian</li>
                            <li>Kerjasama antar koperasi</li>
                        </ul>
                    </div>
                </details>

                <!-- BAB III -->
                <details class="group">
                    <summary class="cursor-pointer bg-gray-50 dark:bg-gray-700 p-4 rounded-lg font-bold text-gray-800 dark:text-white flex items-center justify-between">
                        BAB III: TUJUAN & KEGIATAN USAHA
                        <svg class="w-5 h-5 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </summary>
                    <div class="p-4 space-y-3 text-gray-700 dark:text-gray-300">
                        <p><strong>Pasal 4 - Tujuan</strong></p>
                        <p>Memajukan kesejahteraan anggota dan masyarakat serta membangun tatanan perekonomian nasional.</p>
                        <p><strong>Pasal 5 - Kegiatan Usaha</strong></p>
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Usaha Simpan Pinjam</li>
                            <li>Usaha Pertokoan (Koperasi Mart)</li>
                            <li>Usaha Jasa Keuangan</li>
                            <li>Usaha lain yang sah dan bermanfaat</li>
                        </ul>
                    </div>
                </details>

                <!-- BAB IV -->
                <details class="group">
                    <summary class="cursor-pointer bg-gray-50 dark:bg-gray-700 p-4 rounded-lg font-bold text-gray-800 dark:text-white flex items-center justify-between">
                        BAB IV: KEANGGOTAAN
                        <svg class="w-5 h-5 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </summary>
                    <div class="p-4 space-y-3 text-gray-700 dark:text-gray-300">
                        <p><strong>Pasal 6 - Syarat Keanggotaan</strong></p>
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Karyawan tetap {{ $settings['coop_name'] ?? 'PT. SPINDO TBK' }}</li>
                            <li>Mengajukan permohonan secara tertulis</li>
                            <li>Menyetujui isi AD-ART</li>
                            <li>Membayar simpanan pokok dan wajib</li>
                        </ul>
                        <p><strong>Pasal 7 - Hak Anggota</strong></p>
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Menghadiri dan bersuara dalam RAT</li>
                            <li>Memilih dan dipilih sebagai pengurus/pengawas</li>
                            <li>Mendapat pelayanan yang sama</li>
                            <li>Mendapat SHU</li>
                        </ul>
                        <p><strong>Pasal 8 - Kewajiban Anggota</strong></p>
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Mematuhi AD-ART dan keputusan RAT</li>
                            <li>Berpartisipasi dalam kegiatan usaha</li>
                            <li>Membayar simpanan tepat waktu</li>
                        </ul>
                    </div>
                </details>

                <!-- BAB V -->
                <details class="group">
                    <summary class="cursor-pointer bg-gray-50 dark:bg-gray-700 p-4 rounded-lg font-bold text-gray-800 dark:text-white flex items-center justify-between">
                        BAB V: MODAL
                        <svg class="w-5 h-5 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </summary>
                    <div class="p-4 space-y-3 text-gray-700 dark:text-gray-300">
                        <p><strong>Pasal 9 - Sumber Modal</strong></p>
                        <ul class="list-disc pl-5 space-y-1">
                            <li><strong>Modal Sendiri:</strong> Simpanan Pokok, Simpanan Wajib, Dana Cadangan, Hibah</li>
                            <li><strong>Modal Pinjaman:</strong> Anggota, Bank, Lembaga Keuangan lain</li>
                        </ul>
                        <div class="bg-blue-50 dark:bg-blue-900/20 p-3 rounded-lg mt-3">
                            <p class="text-sm"><strong>Simpanan Pokok:</strong> Rp {{ number_format($settings['saving_principal'] ?? 100000, 0, ',', '.') }} (dibayar sekali)</p>
                            <p class="text-sm"><strong>Simpanan Wajib:</strong> Rp {{ number_format($settings['saving_mandatory'] ?? 50000, 0, ',', '.') }}/bulan (potong gaji)</p>
                        </div>
                    </div>
                </details>

                <!-- BAB VI -->
                <details class="group">
                    <summary class="cursor-pointer bg-gray-50 dark:bg-gray-700 p-4 rounded-lg font-bold text-gray-800 dark:text-white flex items-center justify-between">
                        BAB VI: ORGANISASI & PENGELOLAAN
                        <svg class="w-5 h-5 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </summary>
                    <div class="p-4 space-y-3 text-gray-700 dark:text-gray-300">
                        <p><strong>Pasal 10 - Perangkat Organisasi</strong></p>
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Rapat Anggota</li>
                            <li>Pengurus</li>
                            <li>Pengawas</li>
                        </ul>
                        <p><strong>Pasal 11 - Rapat Anggota Tahunan (RAT)</strong></p>
                        <p>RAT dilaksanakan paling lambat 3 bulan setelah tutup buku tahun berjalan.</p>
                    </div>
                </details>

                <!-- BAB VII -->
                <details class="group">
                    <summary class="cursor-pointer bg-gray-50 dark:bg-gray-700 p-4 rounded-lg font-bold text-gray-800 dark:text-white flex items-center justify-between">
                        BAB VII: SISA HASIL USAHA (SHU)
                        <svg class="w-5 h-5 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </summary>
                    <div class="p-4 space-y-3 text-gray-700 dark:text-gray-300">
                        <p><strong>Pasal 12 - Pembagian SHU</strong></p>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                                <thead class="bg-gray-100 dark:bg-gray-700/50 text-gray-800 dark:text-gray-200">
                                    <tr><th class="p-3 text-left font-bold">Alokasi</th><th class="p-3 text-right font-bold">%</th></tr>
                                </thead>
                                <tbody class="text-gray-600 dark:text-gray-300">
                                    <tr class="border-t border-gray-200 dark:border-gray-700"><td class="p-3">Dana Cadangan</td><td class="p-3 text-right font-medium">{{ $shuSetting->persen_cadangan ?? 25 }}%</td></tr>
                                    <tr class="border-t border-gray-200 dark:border-gray-700"><td class="p-3">Jasa Modal</td><td class="p-3 text-right font-medium">{{ $shuSetting->persen_jasa_modal ?? 20 }}%</td></tr>
                                    <tr class="border-t border-gray-200 dark:border-gray-700"><td class="p-3">Jasa Usaha Anggota</td><td class="p-3 text-right font-medium">{{ $shuSetting->persen_jasa_usaha ?? 30 }}%</td></tr>
                                    <tr class="border-t border-gray-200 dark:border-gray-700"><td class="p-3">Dana Pengurus</td><td class="p-3 text-right font-medium">{{ $shuSetting->persen_pengurus ?? 10 }}%</td></tr>
                                    <tr class="border-t border-gray-200 dark:border-gray-700"><td class="p-3">Dana Karyawan</td><td class="p-3 text-right font-medium">{{ $shuSetting->persen_karyawan ?? 5 }}%</td></tr>
                                    <tr class="border-t border-gray-200 dark:border-gray-700"><td class="p-3">Dana Pendidikan</td><td class="p-3 text-right font-medium">{{ $shuSetting->persen_pendidikan ?? 5 }}%</td></tr>
                                    <tr class="border-t border-gray-200 dark:border-gray-700"><td class="p-3">Dana Sosial</td><td class="p-3 text-right font-medium">{{ $shuSetting->persen_sosial ?? 3 }}%</td></tr>
                                    <tr class="border-t border-gray-200 dark:border-gray-700"><td class="p-3">Dana Pembangunan</td><td class="p-3 text-right font-medium">{{ $shuSetting->persen_pembangunan ?? 2 }}%</td></tr>
                                    <tr class="border-t border-gray-200 dark:border-gray-700 bg-green-50 dark:bg-green-900/20 font-bold text-green-800 dark:text-green-300">
                                        <td class="p-3">TOTAL</td>
                                        <td class="p-3 text-right">{{ $shuSetting ? $shuSetting->total_persen : 100 }}%</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        @if($shuSetting)
                        <p class="text-xs text-gray-500 mt-2">*Berdasarkan konfigurasi periode {{ $shuSetting->period_year }}</p>
                        @endif
                    </div>
                </details>
            </div>
        </div>
    </div>

    <!-- ANGGARAN RUMAH TANGGA -->
    <div x-show="activeTab === 'art'" class="glass-card p-6">
        <div class="prose dark:prose-invert max-w-none">
            <h2 class="text-xl font-bold text-center mb-6 text-blue-600">ANGGARAN RUMAH TANGGA<br>KOPERASI KARYAWAN PT. SPINDO TBK</h2>
            
            <div class="space-y-6">
                <!-- BAB I ART -->
                <details open class="group">
                    <summary class="cursor-pointer bg-gray-50 dark:bg-gray-700 p-4 rounded-lg font-bold text-gray-800 dark:text-white flex items-center justify-between">
                        BAB I: KEANGGOTAAN
                        <svg class="w-5 h-5 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </summary>
                    <div class="p-4 space-y-3 text-gray-700 dark:text-gray-300">
                        <p><strong>Pasal 1 - Prosedur Pendaftaran</strong></p>
                        <ol class="list-decimal pl-5 space-y-1">
                            <li>Mengisi formulir pendaftaran</li>
                            <li>Menyerahkan fotokopi KTP dan ID Karyawan</li>
                            <li>Membayar simpanan pokok</li>
                            <li>Menandatangani surat pernyataan</li>
                        </ol>
                        <p><strong>Pasal 2 - Berakhirnya Keanggotaan</strong></p>
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Meninggal dunia</li>
                            <li>Mengundurkan diri</li>
                            <li>Diberhentikan karena melanggar AD-ART</li>
                            <li>Berhenti sebagai karyawan {{ $settings['coop_name'] ?? 'PT. SPINDO TBK' }}</li>
                        </ul>
                    </div>
                </details>

                <!-- BAB II ART -->
                <details class="group">
                    <summary class="cursor-pointer bg-gray-50 dark:bg-gray-700 p-4 rounded-lg font-bold text-gray-800 dark:text-white flex items-center justify-between">
                        BAB II: SIMPANAN
                        <svg class="w-5 h-5 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </summary>
                    <div class="p-4 space-y-3 text-gray-700 dark:text-gray-300">
                        <p><strong>Pasal 3 - Jenis Simpanan</strong></p>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div class="bg-green-50 dark:bg-green-900/20 p-3 rounded-lg">
                                <p class="font-bold text-green-700 dark:text-green-400">Simpanan Pokok</p>
                                <p class="text-sm">Rp {{ number_format($settings['saving_principal'] ?? 100000, 0, ',', '.') }} (1x)</p>
                            </div>
                            <div class="bg-blue-50 dark:bg-blue-900/20 p-3 rounded-lg">
                                <p class="font-bold text-blue-700 dark:text-blue-400">Simpanan Wajib</p>
                                <p class="text-sm">Rp {{ number_format($settings['saving_mandatory'] ?? 50000, 0, ',', '.') }}/bulan</p>
                            </div>
                            <div class="bg-purple-50 dark:bg-purple-900/20 p-3 rounded-lg">
                                <p class="font-bold text-purple-700 dark:text-purple-400">Simpanan Sukarela</p>
                                <p class="text-sm">Min. Rp 10.000</p>
                            </div>
                        </div>
                    </div>
                </details>

                <!-- BAB III ART -->
                <details class="group">
                    <summary class="cursor-pointer bg-gray-50 dark:bg-gray-700 p-4 rounded-lg font-bold text-gray-800 dark:text-white flex items-center justify-between">
                        BAB III: PINJAMAN
                        <svg class="w-5 h-5 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </summary>
                    <div class="p-4 space-y-3 text-gray-700 dark:text-gray-300">
                        <p><strong>Pasal 4 - Ketentuan Pinjaman</strong></p>
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Maksimal pinjaman: Rp {{ number_format($settings['loan_limit_max'] ?? 50000000, 0, ',', '.') }}</li>
                            <li>Bunga: {{ $settings['loan_interest_regular'] ?? 1.5 }}% per bulan (flat)</li>
                            <li>Tenor: 3 - {{ $settings['loan_max_duration'] ?? 60 }} bulan</li>
                            <li>Agunan: Gaji bulanan (dipotong langsung)</li>
                        </ul>
                        <p><strong>Pasal 5 - Prosedur Pengajuan</strong></p>
                        <ol class="list-decimal pl-5 space-y-1">
                            <li>Mengisi formulir pinjaman</li>
                            <li>Mendapat persetujuan pengurus</li>
                            <li>Menandatangani perjanjian kredit</li>
                            <li>Dana cair dalam 3 hari kerja</li>
                        </ol>
                    </div>
                </details>

                <!-- BAB IV ART -->
                <details class="group">
                    <summary class="cursor-pointer bg-gray-50 dark:bg-gray-700 p-4 rounded-lg font-bold text-gray-800 dark:text-white flex items-center justify-between">
                        BAB IV: SANKSI
                        <svg class="w-5 h-5 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </summary>
                    <div class="p-4 space-y-3 text-gray-700 dark:text-gray-300">
                        <p><strong>Pasal 6 - Jenis Sanksi</strong></p>
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Teguran lisan</li>
                            <li>Teguran tertulis</li>
                            <li>Pembatasan hak pelayanan</li>
                            <li>Pemberhentian keanggotaan</li>
                        </ul>
                    </div>
                </details>
            </div>
        </div>
    </div>
</div>
@endsection
