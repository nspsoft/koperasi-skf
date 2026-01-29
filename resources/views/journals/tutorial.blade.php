@extends('layouts.app')

@section('title', 'Panduan Jurnal Umum')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Panduan Penggunaan Jurnal Umum</h1>
        <p class="page-subtitle">Pelajari cara mencatat transaksi manual dengan benar secara akuntansi</p>
    </div>
    <div>
        <a href="{{ route('journals.index') }}" class="btn-secondary">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Jurnal
        </a>
    </div>
</div>

<div class="max-w-4xl mx-auto space-y-8 pb-12">
    {{-- Pengantar --}}
    <div class="glass-card-solid p-8 relative overflow-hidden">
        <div class="absolute top-0 right-0 p-4 opacity-10">
            <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
        </div>
        <h2 class="text-2xl font-bold mb-4 flex items-center gap-2">
            <span class="text-primary-600">📖</span> Apa itu Jurnal Umum?
        </h2>
        <p class="text-gray-600 dark:text-gray-400 mb-4 leading-relaxed">
            Jurnal Umum adalah fitur "pintu darurat" untuk mencatat transaksi yang tidak memiliki tombol otomatis di menu aplikasi. 
            Contohnya adalah saat Koperasi membeli aset tetap (seperti Laptop atau Printer) yang tidak dijual kembali, atau saat pengurus menyetorkan modal tambahan.
        </p>
        <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 p-4 rounded-r-lg">
            <p class="text-sm text-blue-700 dark:text-blue-300">
                <strong>Penting:</strong> Gunakan menu ini hanya jika transaksi tersebut tidak ada di menu Simpanan, Pinjaman, atau Pembelian Stok Toko.
            </p>
        </div>
    </div>

    {{-- Langkah Input --}}
    <div class="glass-card-solid p-8">
        <h2 class="text-xl font-bold mb-6">🚀 Langkah-langkah Input Jurnal</h2>
        <div class="space-y-6">
            <div class="flex gap-4">
                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-primary-600 text-white flex items-center justify-center font-bold">1</div>
                <div>
                    <h3 class="font-bold">Buka Menu Jurnal Umum</h3>
                    <p class="text-sm text-gray-500">Klik menu <strong>Laporan > Jurnal Umum</strong> lalu klik tombol <strong>+ Buat Jurnal Manual</strong>.</p>
                </div>
            </div>
            <div class="flex gap-4">
                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-primary-600 text-white flex items-center justify-center font-bold">2</div>
                <div>
                    <h3 class="font-bold">Isi Tanggal & Keterangan</h3>
                    <p class="text-sm text-gray-500">Isi tanggal transaksi dan keterangan singkat (Contoh: "Beli Printer Canon").</p>
                </div>
            </div>
            <div class="flex gap-4">
                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-primary-600 text-white flex items-center justify-center font-bold">3</div>
                <div>
                    <h3 class="font-bold">Pilih Akun & Nominal</h3>
                    <p class="text-sm text-gray-500">Pilih minimal 2 baris akun (Satu di sisi <strong>Debit</strong>, satu di sisi <strong>Kredit</strong>).</p>
                </div>
            </div>
            <div class="flex gap-4">
                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-primary-600 text-white flex items-center justify-center font-bold">4</div>
                <div>
                    <h3 class="font-bold">Simpan Jurnal</h3>
                    <p class="text-sm text-gray-500">Pastikan total Debit dan Kredit seimbang (indikator berubah jadi hijau) lalu klik <strong>Simpan</strong>.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Contoh Transaksi --}}
    <div class="glass-card-solid p-8">
        <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
            <span class="text-primary-600">💡</span> Contoh Kasus Sering Terjadi
        </h2>
        
        <div class="space-y-8">
            {{-- Kasus 1 --}}
            <div>
                <h3 class="font-bold text-lg mb-3">1. Pembelian Aset Tetap (Misal: Laptop Rp 5.000.000)</h3>
                <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-800/50">
                            <tr>
                                <th class="px-4 py-3">Nama Akun</th>
                                <th class="px-4 py-3 text-right">Debit (Rp)</th>
                                <th class="px-4 py-3 text-right">Kredit (Rp)</th>
                                <th class="px-4 py-3">Fungsi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <tr>
                                <td class="px-4 py-3 font-medium">1302 - Peralatan Kantor</td>
                                <td class="px-4 py-3 text-right text-green-600 font-bold">5.000.000</td>
                                <td class="px-4 py-3 text-right">0</td>
                                <td class="px-4 py-3 text-gray-500 italic">Nilai aset laptop bertambah</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 font-medium">1101 - Kas (atau 1102 - Bank)</td>
                                <td class="px-4 py-3 text-right">0</td>
                                <td class="px-4 py-3 text-right text-red-600 font-bold">5.000.000</td>
                                <td class="px-4 py-3 text-gray-500 italic">Uang kas/bank berkurang</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Kasus 2 --}}
            <div>
                <h3 class="font-bold text-lg mb-3">2. Setoran Modal Tambahan (Misal: Tunai Rp 10.000.000)</h3>
                <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-800/50">
                            <tr>
                                <th class="px-4 py-3">Nama Akun</th>
                                <th class="px-4 py-3 text-right">Debit (Rp)</th>
                                <th class="px-4 py-3 text-right">Kredit (Rp)</th>
                                <th class="px-4 py-3">Fungsi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <tr>
                                <td class="px-4 py-3 font-medium">1101 - Kas</td>
                                <td class="px-4 py-3 text-right text-green-600 font-bold">10.000.000</td>
                                <td class="px-4 py-3 text-right">0</td>
                                <td class="px-4 py-3 text-gray-500 italic">Uang tunai koperasi bertambah</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 font-medium">3101 - Modal Pengurus</td>
                                <td class="px-4 py-3 text-right">0</td>
                                <td class="px-4 py-3 text-right text-red-600 font-bold">10.000.000</td>
                                <td class="px-4 py-3 text-gray-500 italic">Kewajiban modal bertambah</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Aturan Dasar --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="glass-card-solid p-6 border-l-4 border-green-500">
            <h3 class="font-bold mb-2 flex items-center gap-2 text-green-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                DEBIT Jika...
            </h3>
            <ul class="text-sm text-gray-600 space-y-1 list-disc pl-5">
                <li>Harta (Aset) bertambah</li>
                <li>Biaya bertambah</li>
                <li>Hutang berkurang</li>
            </ul>
        </div>
        <div class="glass-card-solid p-6 border-l-4 border-red-500">
            <h3 class="font-bold mb-2 flex items-center gap-2 text-red-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                KREDIT Jika...
            </h3>
            <ul class="text-sm text-gray-600 space-y-1 list-disc pl-5">
                <li>Harta (Aset) berkurang</li>
                <li>Pendapatan bertambah</li>
                <li>Hutang bertambah</li>
            </ul>
        </div>
    </div>
</div>
@endsection
