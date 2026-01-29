@extends('layouts.app')

@section('title', __('messages.titles.loan_simulation'))

@section('content')
    <div class="page-header">
        <h1 class="page-title">Simulasi Pinjaman</h1>
        <p class="page-subtitle">Hitung estimasi angsuran dan bunga pinjaman Anda</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Input Form -->
        <div class="lg:col-span-1">
            <div class="glass-card-solid p-6 sticky top-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Parameter Pinjaman</h3>
                
                <form id="simulationForm" onsubmit="event.preventDefault(); calculateLoan();" class="space-y-4">
                    <!-- Amount -->
                    <div>
                        <label class="form-label mb-1 block">Nominal Pinjaman</label>
                        <div class="flex rounded-md shadow-sm">
                            <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-100 text-gray-500 sm:text-sm font-semibold dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                                Rp
                            </span>
                            <input type="number" id="amount" class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-r-md border border-gray-300 focus:ring-primary-500 focus:border-primary-500 sm:text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white" placeholder="10.000.000" required min="100000">
                        </div>
                    </div>

                    <!-- Interest Rate -->
                    <div>
                        <label class="form-label mb-1 block">Bunga Pertahun</label>
                        <div class="flex rounded-md shadow-sm">
                            <input type="number" id="rate" class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-l-md border border-gray-300 focus:ring-primary-500 focus:border-primary-500 sm:text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white" value="12" step="0.1" required>
                            <span class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-100 text-gray-500 sm:text-sm font-semibold dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                                %
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Default 12% (1% per bulan)</p>
                    </div>

                    <!-- Duration -->
                    <div>
                        <label class="form-label">Tenor (Bulan)</label>
                        <select id="duration" class="form-input w-full">
                            <option value="6">6 Bulan</option>
                            <option value="12" selected>12 Bulan (1 Tahun)</option>
                            <option value="24">24 Bulan (2 Tahun)</option>
                            <option value="36">36 Bulan (3 Tahun)</option>
                            <option value="48">48 Bulan (4 Tahun)</option>
                            <option value="60">60 Bulan (5 Tahun)</option>
                        </select>
                    </div>

                    <button type="submit" class="btn-primary w-full mt-2">
                        Hitung Simulasi
                    </button>
                    
                    <a href="{{ route('loans.create') }}" class="btn-outline w-full text-center block" style="display:none;" id="btnApply">
                        Ajukan Pinjaman Ini
                    </a>
                </form>
            </div>
        </div>

        <!-- Result -->
        <div class="lg:col-span-2">
            <!-- Summary Cards -->
            <div id="resultSummary" class="hidden grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-200 dark:border-blue-800">
                    <p class="text-sm text-blue-800 dark:text-blue-300 font-medium">Angsuran Per Bulan</p>
                    <h3 class="text-xl font-bold text-blue-900 dark:text-white mt-1" id="monthlyInstallment">Rp 0</h3>
                </div>
                <div class="p-4 bg-amber-50 dark:bg-amber-900/20 rounded-xl border border-amber-200 dark:border-amber-800">
                    <p class="text-sm text-amber-800 dark:text-amber-300 font-medium">Total Bunga</p>
                    <h3 class="text-xl font-bold text-amber-900 dark:text-white mt-1" id="totalInterest">Rp 0</h3>
                </div>
                <div class="p-4 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl border border-emerald-200 dark:border-emerald-800">
                    <p class="text-sm text-emerald-800 dark:text-emerald-300 font-medium">Total Pembayaran</p>
                    <h3 class="text-xl font-bold text-emerald-900 dark:text-white mt-1" id="totalPayment">Rp 0</h3>
                </div>
            </div>

            <!-- Schedule Table -->
            <div id="scheduleTableContainer" class="glass-card-solid hidden">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Jadwal Angsuran</h3>
                </div>
                <div class="overflow-x-auto max-h-[500px] overflow-y-auto">
                    <table class="table-modern w-full">
                        <thead class="sticky top-0 bg-gray-50 dark:bg-gray-800 z-10 shadow-sm">
                            <tr>
                                <th class="w-16 text-center">Bulan</th>
                                <th class="text-right">Pokok</th>
                                <th class="text-right">Bunga</th>
                                <th class="text-right">Total Angsuran</th>
                                <th class="text-right">Sisa Pinjaman</th>
                            </tr>
                        </thead>
                        <tbody id="scheduleBody">
                            <!-- JS will populate -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Empty State -->
            <div id="emptyState" class="glass-card-solid p-12 text-center">
                 <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Belum Ada Kalkulasi</h3>
                <p class="text-gray-500 mt-2">Masukkan nominal dan tenor di samping untuk melihat simulasi.</p>
            </div>
        </div>
    </div>

    <script>
        function calculateLoan() {
            // Get Inputs
            const amount = parseFloat(document.getElementById('amount').value);
            const rateYearly = parseFloat(document.getElementById('rate').value);
            const duration = parseInt(document.getElementById('duration').value);

            if (!amount || !rateYearly || !duration) return;

            // Calculations (Flat Rate)
            const principalPerMonth = amount / duration;
            const interestPerMonth = amount * (rateYearly / 100 / 12);
            const installmentPerMonth = principalPerMonth + interestPerMonth;

            const totalInterest = interestPerMonth * duration;
            const totalPayment = amount + totalInterest;

            // Update UI Summary
            document.getElementById('monthlyInstallment').innerText = formatRupiah(installmentPerMonth);
            document.getElementById('totalInterest').innerText = formatRupiah(totalInterest);
            document.getElementById('totalPayment').innerText = formatRupiah(totalPayment);

            // Populate Table
            const tbody = document.getElementById('scheduleBody');
            tbody.innerHTML = '';

            let remainingLoan = amount;

            for (let i = 1; i <= duration; i++) {
                remainingLoan -= principalPerMonth;
                // Avoid negative zero
                if (remainingLoan < 0) remainingLoan = 0;

                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="text-center font-medium">${i}</td>
                    <td class="text-right text-gray-600 dark:text-gray-300">${formatRupiah(principalPerMonth)}</td>
                    <td class="text-right text-gray-600 dark:text-gray-300">${formatRupiah(interestPerMonth)}</td>
                    <td class="text-right font-bold text-gray-900 dark:text-white">${formatRupiah(installmentPerMonth)}</td>
                    <td class="text-right text-gray-500">${formatRupiah(remainingLoan)}</td>
                `;
                tbody.appendChild(tr);
            }

            // Show Results
            document.getElementById('resultSummary').classList.remove('hidden');
            document.getElementById('scheduleTableContainer').classList.remove('hidden');
            document.getElementById('btnApply').style.display = 'block';
            document.getElementById('emptyState').classList.add('hidden');
        }

        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', { 
                style: 'currency', 
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(number);
        }
    </script>
@endsection
