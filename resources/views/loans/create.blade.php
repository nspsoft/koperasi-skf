@extends('layouts.app')

@section('title', __('messages.titles.loan_apply'))

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
                <h1 class="page-title">Ajukan Pinjaman Baru</h1>
                <p class="page-subtitle">Isi formulir pengajuan pinjaman anggota</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6" x-data="loanCalculator()">
        <!-- Loan Form -->
        <div class="lg:col-span-2">
            <form action="{{ route('loans.store') }}" method="POST">
                @csrf
                
                <div class="glass-card-solid p-6 mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 pb-3 border-b border-gray-200 dark:border-gray-700">Detail Pengajuan</h2>
                    
                    <div class="space-y-6">
                        <!-- Anggota -->
                         <div class="form-group">
                            <label for="member_id" class="form-label">Anggota <span class="text-red-500">*</span></label>
                            <select id="member_id" name="member_id" class="form-input @error('member_id') !border-red-500 @enderror" required>
                                <option value="">Pilih Anggota</option>
                                @foreach($members as $member)
                                <option value="{{ $member['id'] }}" {{ old('member_id') == $member['id'] ? 'selected' : '' }}>
                                    {{ $member['text'] }}
                                </option>
                                @endforeach
                            </select>
                            @error('member_id')
                            <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                         <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Loan Type -->
                            <div class="form-group">
                                <label for="loan_type" class="form-label">Jenis Pinjaman <span class="text-red-500">*</span></label>
                                <select id="loan_type" name="loan_type" class="form-input @error('loan_type') !border-red-500 @enderror" required>
                                    <option value="">Pilih Jenis</option>
                                    <option value="regular" {{ old('loan_type') == 'regular' ? 'selected' : '' }}>Reguler (Bunga Standar)</option>
                                    <option value="emergency" {{ old('loan_type') == 'emergency' ? 'selected' : '' }}>Darurat (Proses Cepat)</option>
                                    <option value="education" {{ old('loan_type') == 'education' ? 'selected' : '' }}>Pendidikan</option>
                                    <option value="special" {{ old('loan_type') == 'special' ? 'selected' : '' }}>Khusus</option>
                                </select>
                                @error('loan_type')
                                <p class="form-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Application Date -->
                            <div class="form-group">
                                <label for="application_date" class="form-label">Tanggal Pengajuan <span class="text-red-500">*</span></label>
                                <input type="date" id="application_date" name="application_date" value="{{ old('application_date', date('Y-m-d')) }}" required
                                       class="form-input @error('application_date') !border-red-500 @enderror">
                                @error('application_date')
                                <p class="form-error">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Amount -->
                        <div class="form-group">
                            <label for="amount" class="form-label">Jumlah Pinjaman (Rp) <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute left-4 top-2.5 text-gray-500 dark:text-gray-400 font-semibold">Rp</span>
                                <input type="number" id="amount" name="amount" x-model.number="amount" min="100000" step="1000" required
                                       class="form-input pl-12 @error('amount') !border-red-500 @enderror"
                                       placeholder="0">
                            </div>
                            @error('amount')
                            <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Duration -->
                            <div class="form-group">
                                <label for="duration_months" class="form-label">Tenor (Bulan) <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="number" id="duration_months" name="duration_months" x-model.number="duration" min="1" max="60" required
                                           class="form-input pr-16 @error('duration_months') !border-red-500 @enderror">
                                    <span class="absolute right-4 top-2.5 text-gray-500 dark:text-gray-400 font-medium">Bulan</span>
                                </div>
                                @error('duration_months')
                                <p class="form-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Interest Rate -->
                            <div class="form-group">
                                <label for="interest_rate" class="form-label">Bunga (% per bulan) <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="number" id="interest_rate" name="interest_rate" x-model.number="rate" min="0" max="100" step="0.01" required
                                           class="form-input pr-12 @error('interest_rate') !border-red-500 @enderror">
                                    <span class="absolute right-4 top-2.5 text-gray-500 dark:text-gray-400 font-medium">%</span>
                                </div>
                                @error('interest_rate')
                                <p class="form-error">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Purpose -->
                         <div class="form-group">
                            <label for="purpose" class="form-label">Keperluan Pinjaman <span class="text-red-500">*</span></label>
                            <textarea id="purpose" name="purpose" rows="3" required
                                      class="form-input @error('purpose') !border-red-500 @enderror"
                                      placeholder="Jelaskan tujuan penggunaan dana...">{{ old('purpose') }}</textarea>
                            @error('purpose')
                            <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4">
                    <a href="{{ route('loans.index') }}" class="btn-secondary">Batal</a>
                    <button type="submit" class="btn-primary">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Ajukan Pinjaman
                    </button>
                </div>
            </form>
        </div>

        <!-- Simulation Card -->
        <div class="lg:col-span-1">
            <div class="glass-card-solid p-6 sticky top-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 3.666V14h-6v-3.334H5V18h14v-7.334h-2.924M9 7V5a2 2 0 012-2h2a2 2 0 012 2v2m-6 0h6m-10 0h14"></path>
                    </svg>
                    Simulasi Angsuran
                </h3>
                
                <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4 space-y-4 mb-6">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Pokok Pinjaman</p>
                        <p class="text-xl font-bold text-gray-900 dark:text-white" x-text="formatRupiah(amount)"></p>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                         <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Bunga Total</p>
                            <p class="text-sm font-semibold text-amber-600 dark:text-amber-400" x-text="formatRupiah(calculateTotalInterest())"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Total Kembali</p>
                            <p class="text-sm font-bold text-gray-900 dark:text-white" x-text="formatRupiah(calculateTotalPayment())"></p>
                        </div>
                    </div>
                </div>

                <div class="bg-primary-50 dark:bg-primary-900/20 border border-primary-100 dark:border-primary-800 rounded-xl p-6 text-center">
                    <p class="text-sm text-primary-600 dark:text-primary-300 font-medium mb-1">Angsuran Per Bulan</p>
                    <p class="text-3xl font-extrabold text-primary-700 dark:text-primary-400" x-text="formatRupiah(calculateMonthlyInstallment())"></p>
                    <p class="text-xs text-primary-500 dark:text-primary-400 mt-2">Selama <span x-text="duration"></span> bulan</p>
                </div>
                
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-4 text-center">
                    *Simulasi ini menggunakan perhitungan bunga flat. Nilai aktual dapat berbeda sedikit karena pembulatan.
                </p>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function loanCalculator() {
            return {
                amount: {{ old('amount', 0) }},
                duration: {{ old('duration_months', 12) }}, // Default 12 bulan
                rate: {{ old('interest_rate', 1.5) }}, // Default 1.5%

                calculateTotalInterest() {
                    return (this.amount * (this.rate / 100)) * this.duration;
                },

                calculateTotalPayment() {
                    return this.amount + this.calculateTotalInterest();
                },

                calculateMonthlyInstallment() {
                    if (this.duration <= 0) return 0;
                    return this.calculateTotalPayment() / this.duration;
                },

                formatRupiah(number) {
                    if (isNaN(number)) return 'Rp 0';
                    return new Intl.NumberFormat('id-ID', { 
                        style: 'currency', 
                        currency: 'IDR',
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0
                    }).format(number);
                }
            }
        }
    </script>
    @endpush
@endsection
