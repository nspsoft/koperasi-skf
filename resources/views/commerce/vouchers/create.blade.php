@extends('layouts.app')

@section('title', 'Buat Voucher Baru')

@section('content')
    <div class="max-w-3xl mx-auto">
        <a href="{{ route('vouchers.index') }}" class="flex items-center text-gray-500 hover:text-primary-600 mb-6 transition-colors font-medium">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Daftar
        </a>

        <div class="glass-card-solid p-8">
            <h1 class="text-2xl font-black text-gray-900 dark:text-white mb-8 tracking-tight">Buat Voucher Baru</h1>

            <form action="{{ route('vouchers.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="form-group">
                        <label class="form-label">Kode Voucher</label>
                        <input type="text" name="code" class="form-input font-mono uppercase tracking-widest !bg-primary-50/30 border-primary-100" placeholder="CONTOH: PROMO2024" required>
                        <p class="text-[10px] text-gray-400 mt-1 uppercase font-bold tracking-tighter">Gunakan huruf besar dan angka tanpa spasi</p>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tipe Potongan</label>
                        <select name="type" class="form-input" required>
                            <option value="fixed">Potongan Tetap (Rp)</option>
                            <option value="percentage">Persentase (%)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Nilai Potongan</label>
                        <div class="relative">
                            <input type="number" step="0.01" name="value" class="form-input pl-10" placeholder="0" required>
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-bold">
                                #
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Min. Pembelian</label>
                        <div class="relative">
                            <input type="number" step="0.01" name="min_purchase" class="form-input pl-10" value="0" required>
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-bold">
                                Rp
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Batas Penggunaan (Total)</label>
                        <input type="number" name="usage_limit" class="form-input" placeholder="Kosongkan jika tak terbatas">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <div class="flex items-center mt-3">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" class="sr-only peer" checked>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary-600"></div>
                                <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300 uppercase tracking-widest text-[10px] font-bold">Aktifkan Voucher</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-100 dark:border-gray-700 pt-6 mt-6">
                    <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest mb-4">Masa Berlaku</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="form-group">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" name="start_date" class="form-input">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Tanggal Berakhir</label>
                            <input type="date" name="end_date" class="form-input">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-8">
                    <button type="submit" class="btn-primary !px-10 !py-3 text-base shadow-xl shadow-primary-500/20">
                        Simpan Voucher
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
