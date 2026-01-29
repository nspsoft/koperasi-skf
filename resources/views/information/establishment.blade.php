@extends('layouts.app')

@section('title', 'Panduan Pendirian Koperasi')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Panduan Pendirian Koperasi</h1>
            <p class="page-subtitle">Syarat, Prosedur, dan Dokumen Legalitas (UU Cipta Kerja)</p>
        </div>
        <div class="flex items-center gap-2 no-print">
            <button onclick="window.print()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center gap-2 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Panduan
            </button>
        </div>
    </div>

    <!-- Alert Cipta Kerja -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border-l-4 border-blue-500 p-4 rounded-r-lg shadow-sm">
        <div class="flex items-start gap-4">
            <div class="text-3xl">⚖️</div>
            <div>
                <h3 class="font-bold text-blue-800 dark:text-blue-300">Update Regulasi Terbaru (UU Cipta Kerja)</h3>
                <p class="text-sm text-blue-700 dark:text-blue-400 mt-1">
                    Berdasarkan <strong>UU No. 11 Tahun 2020 tentang Cipta Kerja</strong>, pendirian Koperasi Primer kini jauh lebih mudah:
                    <br>✅ Minimal pendiri hanya <strong>9 orang</strong> (sebelumnya 20 orang).
                    <br>✅ Proses perizinan terintegrasi lewat <strong>OSS (Online Single Submission)</strong>.
                </p>
            </div>
        </div>
    </div>

    <!-- Steps Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Kolom 1: Persiapan -->
        <div class="lg:col-span-1 space-y-6">
            <div class="glass-card p-6 h-full border-t-4 border-orange-500">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900/30 rounded-full flex items-center justify-center text-xl font-bold text-orange-600">1</div>
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">Tahap Persiapan</h3>
                </div>
                
                <ul class="space-y-4 relative before:absolute before:inset-0 before:ml-5 before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-slate-300 before:to-transparent">
                    <li class="relative flex items-start gap-2">
                         <span class="absolute left-0 top-1 -ml-px h-2 w-2 rounded-full border-2 border-slate-300 bg-white dark:bg-gray-800 dark:border-gray-600"></span>
                        <div class="ml-6">
                            <h4 class="font-bold text-sm text-gray-800 dark:text-gray-200">Bentuk Panitia Kecil</h4>
                            <p class="text-xs text-gray-500">Tunjuk beberapa orang untuk menyiapkan konsep AD/ART dan rencana kerja.</p>
                        </div>
                    </li>
                    <li class="relative flex items-start gap-2">
                        <span class="absolute left-0 top-1 -ml-px h-2 w-2 rounded-full border-2 border-slate-300 bg-white dark:bg-gray-800 dark:border-gray-600"></span>
                        <div class="ml-6">
                            <h4 class="font-bold text-sm text-gray-800 dark:text-gray-200">Penyuluhan (Wajib)</h4>
                            <p class="text-xs text-gray-500">Mengundang pejabat Dinas Koperasi setempat untuk memberikan penyuluhan pra-koperasi.</p>
                        </div>
                    </li>
                    <li class="relative flex items-start gap-2">
                         <span class="absolute left-0 top-1 -ml-px h-2 w-2 rounded-full border-2 border-slate-300 bg-white dark:bg-gray-800 dark:border-gray-600"></span>
                        <div class="ml-6">
                            <h4 class="font-bold text-sm text-gray-800 dark:text-gray-200">Siapkan Nama Koperasi</h4>
                            <p class="text-xs text-gray-500">Minimal <strong>3 suku kata</strong>, tidak boleh bahasa asing. Cek ketersediaan nama di AHU Online (biasanya via Notaris).</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Kolom 2: Rapat Pembentukan -->
        <div class="lg:col-span-1 space-y-6">
            <div class="glass-card p-6 h-full border-t-4 border-green-500">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center text-xl font-bold text-green-600">2</div>
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">Rapat Pembentukan</h3>
                </div>

                <div class="space-y-4">
                    <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded-lg text-sm">
                        <p class="font-bold text-gray-700 dark:text-gray-300 mb-1">📅 Peserta Rapat:</p>
                        <ul class="list-disc pl-4 text-gray-600 dark:text-gray-400 text-xs space-y-1">
                            <li>Minimal 9 orang pendiri</li>
                            <li>Pejabat Dinas Koperasi (Saksi/Penyuluh)</li>
                            <li>Notaris (Opsional tapi disarankan)</li>
                        </ul>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded-lg text-sm">
                        <p class="font-bold text-gray-700 dark:text-gray-300 mb-1">📝 Agenda Wajib:</p>
                        <ul class="list-disc pl-4 text-gray-600 dark:text-gray-400 text-xs space-y-1">
                            <li>Kesepakatan Nama & Tempat Kedudukan</li>
                            <li>Penetapan AD/ART (Anggaran Dasar)</li>
                            <li>Pemilihan Pengurus & Pengawas Pertama</li>
                            <li>Penetapan Modal Awal (Simpanan Pokok)</li>
                            <li>Rencana Kerja & Rencana Anggaran (RAPB)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom 3: Legalitas -->
        <div class="lg:col-span-1 space-y-6">
            <div class="glass-card p-6 h-full border-t-4 border-purple-500">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center text-xl font-bold text-purple-600">3</div>
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">Pengesahan Legalitas</h3>
                </div>

                <ul class="space-y-3">
                    <li class="flex items-start gap-3 bg-purple-50 dark:bg-purple-900/10 p-2 rounded-lg">
                        <span class="mt-0.5">🏛️</span>
                        <div>
                            <p class="text-sm font-bold text-gray-800 dark:text-gray-200">Notaris (NPAK)</p>
                            <p class="text-xs text-gray-500">Membuat Akta Pendirian Koperasi.</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3 bg-purple-50 dark:bg-purple-900/10 p-2 rounded-lg">
                        <span class="mt-0.5">📜</span>
                        <div>
                            <p class="text-sm font-bold text-gray-800 dark:text-gray-200">SK Menkumham</p>
                            <p class="text-xs text-gray-500">Pengesahan Badan Hukum (via SISMINKOP).</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3 bg-purple-50 dark:bg-purple-900/10 p-2 rounded-lg">
                        <span class="mt-0.5">💳</span>
                        <div>
                            <p class="text-sm font-bold text-gray-800 dark:text-gray-200">NPWP & Rekening Bank</p>
                            <p class="text-xs text-gray-500">Urus di KPP Pratama & Bank terdekat.</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3 bg-purple-50 dark:bg-purple-900/10 p-2 rounded-lg">
                        <span class="mt-0.5">🌐</span>
                        <div>
                            <p class="text-sm font-bold text-gray-800 dark:text-gray-200">NIB (OSS RBA)</p>
                            <p class="text-xs text-gray-500">Nomor Induk Berusaha wajib untuk izin usaha.</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Checklist Dokumen Section -->
    <div class="glass-card p-8 print-break-before">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-6 text-center">✅ Checklist Kelengkapan Dokumen</h2>
        <p class="text-center text-gray-500 mb-8">Dokumen fisik yang wajib dibawa ke Notaris (NPAK)</p>

        <div class="grid md:grid-cols-2 gap-8">
            <!-- Dokumen Pribadi -->
            <div>
                <h3 class="text-lg font-bold text-indigo-600 mb-4 border-b pb-2">📂 Dokumen Administratif</h3>
                <div class="space-y-3">
                    <label class="flex items-center gap-3 p-3 border rounded-lg hover:bg-gray-50 cursor-pointer">
                        <input type="checkbox" class="w-5 h-5 text-indigo-600 rounded focus:ring-indigo-500">
                        <span class="text-sm text-gray-700">Fotokopi KTP Pendiri (Min. 9 orang)</span>
                    </label>
                    <label class="flex items-center gap-3 p-3 border rounded-lg hover:bg-gray-50 cursor-pointer">
                        <input type="checkbox" class="w-5 h-5 text-indigo-600 rounded focus:ring-indigo-500">
                        <span class="text-sm text-gray-700">Fotokopi KK & NPWP Pendiri (opsional tapi disarankan)</span>
                    </label>
                    <label class="flex items-center gap-3 p-3 border rounded-lg hover:bg-gray-50 cursor-pointer">
                        <input type="checkbox" class="w-5 h-5 text-indigo-600 rounded focus:ring-indigo-500">
                        <span class="text-sm text-gray-700">Daftar Riwayat Hidup Pengurus & Pengawas Terpilih</span>
                    </label>
                     <label class="flex items-center gap-3 p-3 border rounded-lg hover:bg-gray-50 cursor-pointer">
                        <input type="checkbox" class="w-5 h-5 text-indigo-600 rounded focus:ring-indigo-500">
                        <span class="text-sm text-gray-700">Pas Foto Pengurus (3x4 atau 4x6)</span>
                    </label>
                </div>
            </div>

            <!-- Dokumen Hasil Rapat -->
            <div>
                <h3 class="text-lg font-bold text-indigo-600 mb-4 border-b pb-2">📂 Dokumen Hasil Rapat</h3>
                <div class="space-y-3">
                    <label class="flex items-center gap-3 p-3 border rounded-lg hover:bg-gray-50 cursor-pointer">
                        <input type="checkbox" class="w-5 h-5 text-indigo-600 rounded focus:ring-indigo-500">
                        <span class="text-sm text-gray-700">Daftar Hadir Rapat Pembentukan (Asli)</span>
                    </label>
                    <label class="flex items-center gap-3 p-3 border rounded-lg hover:bg-gray-50 cursor-pointer">
                        <input type="checkbox" class="w-5 h-5 text-indigo-600 rounded focus:ring-indigo-500">
                        <span class="text-sm text-gray-700">Berita Acara Rapat Pembentukan</span>
                    </label>
                    <label class="flex items-center gap-3 p-3 border rounded-lg hover:bg-gray-50 cursor-pointer">
                        <input type="checkbox" class="w-5 h-5 text-indigo-600 rounded focus:ring-indigo-500">
                        <span class="text-sm text-gray-700">Surat Kuasa (Bila dikuasakan ke Notaris)</span>
                    </label>
                    <label class="flex items-center gap-3 p-3 border rounded-lg hover:bg-gray-50 cursor-pointer">
                        <input type="checkbox" class="w-5 h-5 text-indigo-600 rounded focus:ring-indigo-500">
                        <span class="text-sm text-gray-700">Bukti Setor Modal Awal (Slip Bank/Pernyataan)</span>
                    </label>
                    <label class="flex items-center gap-3 p-3 border rounded-lg hover:bg-gray-50 cursor-pointer">
                        <input type="checkbox" class="w-5 h-5 text-indigo-600 rounded focus:ring-indigo-500">
                        <span class="text-sm text-gray-700">Rencana Kerja & Rencana Anggaran (3 Tahun)</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="mt-8 bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-xl border border-yellow-200 dark:border-yellow-800 text-center">
             <p class="text-sm text-yellow-800 dark:text-yellow-200 font-semibold mb-2">
                💡 Tips: Gunakan Aplikasi ini untuk mencetak dokumen-dokumen di atas secara otomatis.
             </p>
             
             @if(\App\Models\DocumentTemplate::where('name', 'Berita Acara Rapat Pembentukan')->count() == 0)
             <form action="{{ route('establishment.install') }}" method="POST">
                @csrf
                <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow-md transition-all flex items-center gap-2 mx-auto">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Install Template Dokumen Pendirian
                </button>
             </form>
             @else
             <div class="flex flex-col md:flex-row items-center justify-center gap-4">
                 <a href="{{ route('documents.index') }}" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg shadow-md transition-all inline-flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Buat Dokumen Sekarang
                 </a>
                 
                 <form action="{{ route('establishment.install') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg shadow-md transition-all flex items-center gap-2 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        Update Template (Fix Layout)
                    </button>
                 </form>
             </div>
             @endif
        </div>
    </div>
</div>
@endsection
