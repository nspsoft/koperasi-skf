@extends('layouts.app')

@section('title', __('messages.titles.savings_new'))

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="flex items-center gap-4">
            <a href="{{ route('savings.index') }}" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                <svg class="w-6 h-6 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h1 class="page-title">Transaksi Simpanan Baru</h1>
                <p class="page-subtitle">Input setoran atau penarikan simpanan anggota</p>
            </div>
        </div>
    </div>

    <form action="{{ route('savings.store') }}" method="POST" class="max-w-3xl">
        @csrf

        <div class="glass-card-solid p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 pb-3 border-b border-gray-200 dark:border-gray-700">Detail Transaksi</h2>
            
            <div class="grid grid-cols-1 gap-6">
                <!-- Anggota -->
                <div class="form-group">
                    <label for="member_id" class="form-label">
                        Anggota <span class="text-red-500">*</span>
                    </label>
                    <select id="member_id" name="member_id" class="form-input @error('member_id') !border-red-500 @enderror" required>
                        <option value="">Pilih Anggota</option>
                        @foreach($members as $member)
                        <option value="{{ $member['id'] }}" {{ old('member_id', request('member_id')) == $member['id'] ? 'selected' : '' }}>
                            {{ $member['text'] }}
                        </option>
                        @endforeach
                    </select>
                    @error('member_id')
                    <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Transaction Type -->
                    <div class="form-group">
                        <label for="transaction_type" class="form-label">
                            Jenis Transaksi <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="cursor-pointer">
                                <input type="radio" name="transaction_type" value="deposit" class="peer sr-only" {{ old('transaction_type', 'deposit') == 'deposit' ? 'checked' : '' }}>
                                <div class="p-3 text-center rounded-xl border-2 border-gray-200 dark:border-gray-700 peer-checked:border-green-500 peer-checked:bg-green-50 dark:peer-checked:bg-green-900/20 transition-all">
                                    <span class="block text-green-600 font-semibold mb-1">Setoran</span>
                                    <span class="text-xs text-gray-500">Masuk kas</span>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="transaction_type" value="withdrawal" class="peer sr-only" {{ old('transaction_type') == 'withdrawal' ? 'checked' : '' }}>
                                <div class="p-3 text-center rounded-xl border-2 border-gray-200 dark:border-gray-700 peer-checked:border-red-500 peer-checked:bg-red-50 dark:peer-checked:bg-red-900/20 transition-all">
                                    <span class="block text-red-600 font-semibold mb-1">Penarikan</span>
                                    <span class="text-xs text-gray-500">Keluar kas</span>
                                </div>
                            </label>
                        </div>
                        @error('transaction_type')
                        <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Saving Type -->
                    <div class="form-group">
                        <label for="type" class="form-label">
                            Jenis Simpanan <span class="text-red-500">*</span>
                        </label>
                        <select id="type" name="type" class="form-input @error('type') !border-red-500 @enderror" required>
                            <option value="">Pilih Jenis</option>
                            <option value="pokok" {{ old('type') == 'pokok' ? 'selected' : '' }}>Simpanan Pokok</option>
                            <option value="wajib" {{ old('type') == 'wajib' ? 'selected' : '' }}>Simpanan Wajib</option>
                            <option value="sukarela" {{ old('type') == 'sukarela' ? 'selected' : '' }}>Simpanan Sukarela</option>
                        </select>
                        @error('type')
                        <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Amount -->
                    <div class="form-group">
                        <label for="amount" class="form-label">
                            Jumlah (Rp) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-2.5 text-gray-500 dark:text-gray-400 font-semibold">Rp</span>
                            <input type="number" id="amount" name="amount" value="{{ old('amount') }}" required min="1000" step="100"
                                   class="form-input pl-12 @error('amount') !border-red-500 @enderror"
                                   placeholder="0">
                        </div>
                        @error('amount')
                        <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Date -->
                    <div class="form-group">
                        <label for="transaction_date" class="form-label">
                            Tanggal Transaksi <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="transaction_date" name="transaction_date" value="{{ old('transaction_date', date('Y-m-d')) }}" required
                               class="form-input @error('transaction_date') !border-red-500 @enderror">
                        @error('transaction_date')
                        <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Description -->
                <div class="form-group">
                    <label for="description" class="form-label">Keterangan</label>
                    <textarea id="description" name="description" rows="2"
                              class="form-input @error('description') !border-red-500 @enderror"
                              placeholder="Simpanan bulan Desember, dsb...">{{ old('description') }}</textarea>
                    @error('description')
                    <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('savings.index') }}" class="btn-secondary">
                Batal
            </a>
            <button type="submit" class="btn-primary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Simpan Transaksi
            </button>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const settings = @json($settings);
            const typeSelect = document.getElementById('type');
            const amountInput = document.getElementById('amount');
            const transactionTypeRadios = document.getElementsByName('transaction_type');

            function updateAmount() {
                // Hanya auto-fill jika Setoran
                const isDeposit = document.querySelector('input[name="transaction_type"]:checked').value === 'deposit';
                
                if (isDeposit) {
                    const type = typeSelect.value;
                    if (type === 'pokok' && settings.principal) {
                        amountInput.value = settings.principal;
                    } else if (type === 'wajib' && settings.mandatory) {
                        amountInput.value = settings.mandatory;
                    }
                }
            }

            typeSelect.addEventListener('change', updateAmount);
            
            // Update juga saat jenis transaksi berubah (opsional, mungkin user mau kembalikan ke 0 atau keep)
            // Array.from(transactionTypeRadios).forEach(radio => {
            //     radio.addEventListener('change', updateAmount);
            // });
        });
    </script>
@endsection
