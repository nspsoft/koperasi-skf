@extends('layouts.app')

@section('title', 'Input Pembayaran Pinjaman')

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="flex items-center gap-4">
            <a href="{{ route('loans.index') }}" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                <svg class="w-6 h-6 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h1 class="page-title">Input Pembayaran</h1>
                <p class="page-subtitle">Catat pembayaran angsuran pinjaman anggota</p>
            </div>
        </div>
    </div>

    <div class="max-w-2xl mx-auto">
        <div class="glass-card-solid p-6">
            <form action="{{ route('loan-payments.store') }}" method="POST">
                @csrf
                
                <!-- Loan Selection -->
                <div class="form-group">
                    <label for="loan_id" class="form-label">Pilih Pinjaman Aktif <span class="text-red-500">*</span></label>
                    <select id="loan_id" name="loan_id" class="form-input @error('loan_id') !border-red-500 @enderror" required onchange="updateLoanDetails(this)">
                        <option value="">-- Pilih Pinjaman --</option>
                        @foreach($activeLoans as $l)
                        <option value="{{ $l['id'] }}" {{ (old('loan_id') == $l['id'] || ($loan && $loan->id == $l['id'])) ? 'selected' : '' }}>
                            {{ $l['text'] }}
                        </option>
                        @endforeach
                    </select>
                    @error('loan_id')
                    <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Payment Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="form-group">
                        <label for="amount" class="form-label">Jumlah Pembayaran <span class="text-red-500">*</span></label>
                        <div class="relative">
                             <span class="absolute left-4 top-2.5 text-gray-500 dark:text-gray-400 font-semibold">Rp</span>
                            <input type="number" id="amount" name="amount" value="{{ old('amount', $loan ? $loan->monthly_installment : '') }}" 
                                   class="form-input pl-12 @error('amount') !border-red-500 @enderror" required placeholder="0">
                        </div>
                        @error('amount')
                        <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="payment_date" class="form-label">Tanggal Pembayaran <span class="text-red-500">*</span></label>
                        <input type="date" id="payment_date" name="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" 
                               class="form-input @error('payment_date') !border-red-500 @enderror" required>
                        @error('payment_date')
                        <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Metode Pembayaran <span class="text-red-500">*</span></label>
                    <input type="hidden" name="payment_method" id="payment_method_input" value="{{ old('payment_method', 'cash') }}">
                    
                    <div class="grid grid-cols-3 gap-4">
                        <!-- Tunai -->
                        <div onclick="selectMethod('cash')" id="method_cash" 
                             class="payment-method-card cursor-pointer p-3 text-center rounded-xl border transition-all duration-200 {{ old('payment_method', 'cash') == 'cash' ? 'border-primary-500 bg-primary-50 text-primary-700' : 'border-gray-200 hover:border-gray-300 text-gray-600' }}">
                            <span class="text-sm font-medium">Tunai</span>
                        </div>

                        <!-- Transfer -->
                        <div onclick="selectMethod('transfer')" id="method_transfer" 
                             class="payment-method-card cursor-pointer p-3 text-center rounded-xl border transition-all duration-200 {{ old('payment_method') == 'transfer' ? 'border-primary-500 bg-primary-50 text-primary-700' : 'border-gray-200 hover:border-gray-300 text-gray-600' }}">
                            <span class="text-sm font-medium">Transfer</span>
                        </div>

                        <!-- Potong Gaji -->
                        <div onclick="selectMethod('salary_deduction')" id="method_salary_deduction" 
                             class="payment-method-card cursor-pointer p-3 text-center rounded-xl border transition-all duration-200 {{ old('payment_method') == 'salary_deduction' ? 'border-primary-500 bg-primary-50 text-primary-700' : 'border-gray-200 hover:border-gray-300 text-gray-600' }}">
                            <span class="text-sm font-medium">Potong Gaji</span>
                        </div>
                    </div>
                    @error('payment_method')
                    <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <script>
                    function selectMethod(value) {
                        // Update Hidden Input
                        document.getElementById('payment_method_input').value = value;

                        // Reset visual classes
                        document.querySelectorAll('.payment-method-card').forEach(el => {
                            el.classList.remove('border-primary-500', 'bg-primary-50', 'text-primary-700');
                            el.classList.add('border-gray-200', 'text-gray-600');
                        });

                        // Set active visual class
                        const activeEl = document.getElementById('method_' + value);
                        activeEl.classList.remove('border-gray-200', 'text-gray-600');
                        activeEl.classList.add('border-primary-500', 'bg-primary-50', 'text-primary-700');
                    }
                </script>

                <div class="form-group">
                    <label for="notes" class="form-label">Catatan (Opsional)</label>
                    <textarea id="notes" name="notes" rows="2" class="form-input" placeholder="Keterangan tambahan...">{{ old('notes') }}</textarea>
                </div>

                <div class="flex justify-end gap-4 mt-8">
                     <a href="{{ route('loans.index') }}" class="btn-secondary">Batal</a>
                    <button type="submit" class="btn-primary">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Simpan Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
