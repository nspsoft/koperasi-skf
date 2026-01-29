@extends('layouts.app')

@section('title', 'Preview Perubahan Harga')

@section('content')
<div class="page-header">
    <a href="{{ route('settings.index') }}" class="flex items-center text-gray-500 hover:text-primary-600 mb-4 transition-colors">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Kembali ke Pengaturan
    </a>
    <h1 class="page-title">Preview Perubahan Harga</h1>
    <p class="text-gray-500 dark:text-gray-400 mt-1">
        Berikut adalah daftar produk yang harganya akan berubah berdasarkan aturan pembulatan <strong>Rp {{ number_format($ceiling, 0, ',', '.') }}</strong>.
    </p>
</div>

<div class="space-y-6" x-data="{ selected: [] }">
    @if(count($previews) > 0)
        <!-- Summary Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="glass-card p-4 bg-blue-50/50 dark:bg-blue-900/10 border-blue-100 dark:border-blue-800">
                <div class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider">Total Produk Terdampak</div>
                <div class="text-2xl font-black text-blue-800 dark:text-blue-300">{{ count($previews) }} Item</div>
            </div>
            <div class="glass-card p-4 bg-green-50/50 dark:bg-green-900/10 border-green-100 dark:border-green-800">
                <div class="text-xs font-bold text-green-600 dark:text-green-400 uppercase tracking-wider">Rata-rata Perubahan</div>
                <div class="text-2xl font-black text-green-800 dark:text-green-300">
                    Rp {{ number_format(collect($previews)->avg(fn($p) => abs($p['diff'])), 0, ',', '.') }}
                </div>
            </div>
            <div class="glass-card p-4 bg-amber-50/50 dark:bg-amber-900/10 border-amber-100 dark:border-amber-800">
                <div class="text-xs font-bold text-amber-600 dark:text-amber-400 uppercase tracking-wider">Status Sinkronisasi</div>
                <div class="text-lg font-bold text-amber-800 dark:text-amber-300">Konfigurasi Seleksi</div>
            </div>
        </div>

        <form action="{{ route('settings.sync-prices') }}" method="POST">
            @csrf
            <!-- Desktop Table -->
            <div class="glass-card overflow-hidden mb-6">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 dark:bg-gray-800/50 text-gray-600 dark:text-gray-400 font-bold uppercase text-[10px] tracking-widest border-b border-gray-100 dark:border-gray-700">
                            <tr>
                                <th class="px-6 py-4 w-10">
                                    <input type="checkbox" checked onchange="document.querySelectorAll('.product-check').forEach(c => c.checked = this.checked)" class="w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                </th>
                                <th class="px-6 py-4">Kode & Nama Produk</th>
                                <th class="px-6 py-4 text-right">Harga Saat Ini</th>
                                <th class="px-6 py-4 text-center"></th>
                                <th class="px-6 py-4 text-right">Harga Baru</th>
                                <th class="px-6 py-4 text-right text-blue-600">Selisih</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($previews as $preview)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition-colors">
                                <td class="px-6 py-4 text-center">
                                    <input type="checkbox" name="product_ids[]" value="{{ $preview['id'] }}" checked class="product-check w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900 dark:text-white">{{ $preview['name'] }}</div>
                                    <div class="text-[10px] text-gray-500">{{ $preview['code'] }} • {{ $preview['category'] }}</div>
                                </td>
                                <td class="px-6 py-4 text-right font-medium text-gray-500">
                                    Rp {{ number_format($preview['old_price'], 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-center text-gray-300">
                                    <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 px-2 py-1 rounded font-bold">
                                        Rp {{ number_format($preview['new_price'], 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right font-bold {{ $preview['diff'] > 0 ? 'text-green-600' : 'text-blue-600' }}">
                                    @if($preview['diff'] > 0) +@endif Rp {{ number_format($preview['diff'], 0, ',', '.') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Action Box -->
            <div class="glass-card p-6 border-2 border-amber-200 dark:border-amber-800 bg-amber-50/20 dark:bg-amber-900/5">
                <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                    <div class="flex items-start gap-4">
                        <div class="p-3 bg-amber-100 dark:bg-amber-900/30 rounded-xl text-amber-600">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        <div>
                            <h4 class="font-black text-amber-800 dark:text-amber-400">Konfirmasi Sinkronisasi Selektif</h4>
                            <p class="text-sm text-amber-700/70 dark:text-amber-400/60 max-w-xl">
                                Hanya produk yang dicentang di atas yang akan diperbarui harganya. Item yang tidak dicentang akan tetap menggunakan harga lama.
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex gap-3 shrink-0">
                        <a href="{{ route('settings.index') }}" class="btn-secondary">
                            Batal
                        </a>
                        <button type="submit" class="btn-primary !bg-amber-600 hover:!bg-amber-700 shadow-lg shadow-amber-600/20" onclick="return confirm('Proses ini akan mengubah harga item yang dicentang. Lanjutkan?')">
                            Ya, Update Item Terpilih Sekarang
                        </button>
                    </div>
                </div>
            </div>
        </form>
    @else
        <div class="glass-card p-12 text-center">
            <div class="w-20 h-20 bg-gray-50 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Tidak Ada Perubahan Harga</h3>
            <p class="text-gray-500 mt-2">Seluruh produk Anda sudah sesuai dengan aturan pembulatan Rp {{ number_format($ceiling, 0, ',', '.') }}. Tidak ada sinkronisasi yang diperlukan.</p>
            <a href="{{ route('settings.index') }}" class="btn-primary mt-6">Kembali ke Pengaturan</a>
        </div>
    @endif
</div>
@endsection
