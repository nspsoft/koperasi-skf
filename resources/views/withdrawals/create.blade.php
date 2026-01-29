@extends('layouts.app')

@section('title', 'Ajukan Penarikan')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('withdrawals.index') }}" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
            <svg class="w-6 h-6 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Ajukan Penarikan Simpanan</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Ajukan permintaan penarikan simpanan sukarela.</p>
        </div>
    </div>

    <!-- Balance Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="glass-card p-4 border-l-4 border-blue-500">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Simpanan Pokok</p>
            <p class="text-xl font-bold text-gray-900 dark:text-white mt-1">Rp {{ number_format($savings['pokok'] ?? 0, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-1">Tidak dapat ditarik</p>
        </div>
        <div class="glass-card p-4 border-l-4 border-amber-500">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Simpanan Wajib</p>
            <p class="text-xl font-bold text-gray-900 dark:text-white mt-1">Rp {{ number_format($savings['wajib'] ?? 0, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-1">Hanya saat pensiun/keluar</p>
        </div>
        <div class="glass-card p-4 border-l-4 border-green-500">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Simpanan Sukarela</p>
            <p class="text-xl font-bold text-gray-900 dark:text-white mt-1">Rp {{ number_format($savings['sukarela'] ?? 0, 0, ',', '.') }}</p>
            <p class="text-xs text-green-600 dark:text-green-400 mt-1">✓ Dapat ditarik</p>
        </div>
    </div>

    <!-- Form -->
    <div class="glass-card-solid p-6">
        <form action="{{ route('withdrawals.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Saving Type -->
            <div>
                <label class="form-label">Jenis Simpanan <span class="text-red-500">*</span></label>
                <select name="saving_type" class="form-input" required>
                    <option value="sukarela" {{ old('saving_type') == 'sukarela' ? 'selected' : '' }}>Simpanan Sukarela (Rp {{ number_format($savings['sukarela'] ?? 0, 0, ',', '.') }})</option>
                    <option value="wajib" {{ old('saving_type') == 'wajib' ? 'selected' : '' }}>Simpanan Wajib (Rp {{ number_format($savings['wajib'] ?? 0, 0, ',', '.') }}) - Khusus resign/pensiun</option>
                </select>
                <p class="text-xs text-gray-500 mt-1">Simpanan pokok tidak dapat ditarik.</p>
            </div>

            <!-- Amount -->
            <div>
                <label class="form-label">Jumlah Penarikan <span class="text-red-500">*</span></label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                    <input type="number" name="amount" value="{{ old('amount') }}" class="form-input pl-10" placeholder="0" min="10000" required>
                </div>
                <p class="text-xs text-gray-500 mt-1">Minimal penarikan Rp 10.000</p>
            </div>

            <!-- Bank Details -->
            <div class="border-t border-gray-100 dark:border-gray-700 pt-6">
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">Rekening Tujuan (Opsional)</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Nama Bank</label>
                        <input type="text" name="bank_name" value="{{ old('bank_name') }}" class="form-input" placeholder="BCA, Mandiri, BRI, dll">
                    </div>
                    <div>
                        <label class="form-label">No. Rekening</label>
                        <input type="text" name="bank_account_number" value="{{ old('bank_account_number') }}" class="form-input" placeholder="1234567890">
                    </div>
                    <div class="md:col-span-2">
                        <label class="form-label">Nama Pemilik Rekening</label>
                        <input type="text" name="bank_account_name" value="{{ old('bank_account_name') }}" class="form-input" placeholder="Sesuai buku tabungan">
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2">Kosongkan jika ingin menerima tunai langsung.</p>
            </div>

            <!-- Reason -->
            <div>
                <label class="form-label">Alasan Penarikan</label>
                <textarea name="reason" class="form-input" rows="3" placeholder="Opsional: Jelaskan keperluan penarikan...">{{ old('reason') }}</textarea>
            </div>

            <!-- Submit -->
            <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                <a href="{{ route('withdrawals.index') }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary">Ajukan Penarikan</button>
            </div>
        </form>
    </div>
</div>
@endsection
