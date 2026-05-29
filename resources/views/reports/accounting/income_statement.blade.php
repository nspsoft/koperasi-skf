@extends('layouts.app')

@section('title', 'Laporan Laba Rugi')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Laporan Laba Rugi (Income Statement)</h1>
            <p class="page-subtitle">Periode {{ $startDate->translatedFormat('d F Y') }} - {{ $endDate->translatedFormat('d F Y') }}</p>
        </div>
        <div>
            <a href="{{ route('reports.index') }}" class="btn btn-outline">Kembali ke Laporan</a>
        </div>
    </div>

    <!-- Filter Date -->
    <div class="glass-card p-6 mb-6">
        <form action="{{ route('reports.income-statement') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
             <div class="w-full md:w-1/4">
                <label class="form-label">Dari Tanggal</label>
                <input type="date" name="start_date" class="form-input" value="{{ $startDate->format('Y-m-d') }}">
            </div>
            <div class="w-full md:w-1/4">
                <label class="form-label">Sampai Tanggal</label>
                <input type="date" name="end_date" class="form-input" value="{{ $endDate->format('Y-m-d') }}">
            </div>
            <div class="w-full md:w-auto">
                <button type="submit" class="btn btn-primary w-full">Tampilkan</button>
            </div>
        </form>
    </div>

    <div class="max-w-4xl mx-auto">
        <div class="glass-card p-8">
            @php
                // Grouping revenues dynamically
                $salesRevenueAccounts = $revenues->filter(function($acc) {
                    return $acc->code == '4102' || str_contains(strtolower($acc->name), 'penjualan') || str_contains(strtolower($acc->name), 'jual');
                });
                $salesRevenue = $salesRevenueAccounts->sum('period_balance');

                $otherRevenueAccounts = $revenues->filter(function($acc) use ($salesRevenueAccounts) {
                    return !$salesRevenueAccounts->contains('id', $acc->id);
                });
                $otherRevenue = $otherRevenueAccounts->sum('period_balance');

                // Grouping expenses dynamically
                $hppAccounts = $expenses->filter(function($acc) {
                    return $acc->code == '5201' || str_contains(strtolower($acc->name), 'hpp') || str_contains(strtolower($acc->name), 'harga pokok');
                });
                $hpp = $hppAccounts->sum('period_balance');

                $nonOpExpenseAccounts = $expenses->filter(function($acc) {
                    return str_starts_with($acc->code, '59') || str_contains(strtolower($acc->name), 'lain-lain') || str_contains(strtolower($acc->name), 'non-operasional');
                });
                $nonOpExpense = $nonOpExpenseAccounts->sum('period_balance');

                $opExpenseAccounts = $expenses->filter(function($acc) use ($hppAccounts, $nonOpExpenseAccounts) {
                    return !$hppAccounts->contains('id', $acc->id) && !$nonOpExpenseAccounts->contains('id', $acc->id);
                });
                $opExpense = $opExpenseAccounts->sum('period_balance');

                // Multi-Step Calculations
                $grossProfit = $salesRevenue - $hpp;
                $totalGrossAndOther = $grossProfit + $otherRevenue;
                $operatingProfit = $totalGrossAndOther - $opExpense;
                
                // Safety GPM Margin Calculation
                $gpm = $salesRevenue > 0 ? ($grossProfit / $salesRevenue) * 100 : 0;
                $opMargin = ($salesRevenue + $otherRevenue) > 0 ? ($operatingProfit / ($salesRevenue + $otherRevenue)) * 100 : 0;
            @endphp

            <!-- SECTION 1: PENDAPATAN PENJUALAN & HPP (MART) -->
            <div class="mb-6">
                <div class="flex justify-between items-center bg-gray-50 dark:bg-gray-800/40 px-4 py-2.5 rounded-lg border border-gray-100 dark:border-gray-700/80 mb-3">
                    <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 tracking-wider uppercase">I. Unit Usaha Pertokoan (Mart / Dagang)</h3>
                </div>

                <table class="w-full text-sm">
                    <tbody>
                        <!-- Sales Revenues -->
                        <tr class="font-semibold text-gray-800 dark:text-gray-200">
                            <td class="py-1.5 pl-2" colspan="2">Pendapatan Penjualan Mart</td>
                        </tr>
                        @foreach($salesRevenueAccounts as $account)
                            @if($account->period_balance != 0)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30 text-xs text-gray-600 dark:text-gray-400">
                                    <td class="py-1.5 pl-6">
                                        <span class="font-mono text-gray-400 dark:text-gray-600 mr-2">{{ $account->code }}</span>
                                        {{ $account->name }}
                                    </td>
                                    <td class="text-right pr-2 font-mono text-gray-700 dark:text-gray-300">
                                        Rp {{ number_format($account->period_balance, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                        <tr class="border-t border-dashed border-gray-200 dark:border-gray-700 text-xs font-semibold">
                            <td class="py-2 pl-6 text-gray-500">Total Pendapatan Penjualan Mart</td>
                            <td class="text-right pr-2 py-2 font-mono text-gray-700 dark:text-gray-350">
                                Rp {{ number_format($salesRevenue, 0, ',', '.') }}
                            </td>
                        </tr>

                        <!-- Cost of Goods Sold (HPP) -->
                        <tr class="font-semibold text-gray-850 dark:text-gray-200 mt-2">
                            <td class="py-1.5 pl-2 pt-4" colspan="2">Harga Pokok Penjualan (HPP)</td>
                        </tr>
                        @foreach($hppAccounts as $account)
                            @if($account->period_balance != 0)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30 text-xs text-gray-600 dark:text-gray-400">
                                    <td class="py-1.5 pl-6">
                                        <span class="font-mono text-gray-400 dark:text-gray-600 mr-2">{{ $account->code }}</span>
                                        {{ $account->name }}
                                    </td>
                                    <td class="text-right pr-2 font-mono text-red-600 dark:text-red-400">
                                        (Rp {{ number_format($account->period_balance, 0, ',', '.') }})
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                        <tr class="border-t border-dashed border-gray-200 dark:border-gray-700 text-xs font-semibold">
                            <td class="py-2 pl-6 text-gray-500">Total Harga Pokok Penjualan</td>
                            <td class="text-right pr-2 py-2 font-mono text-red-600 dark:text-red-450">
                                (Rp {{ number_format($hpp, 0, ',', '.') }})
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- GROSS PROFIT CARD -->
                <div class="mt-4 p-4 rounded-xl bg-gradient-to-r from-emerald-500/5 to-teal-500/5 dark:from-emerald-500/10 dark:to-teal-500/10 border border-emerald-500/10 dark:border-emerald-500/20 flex justify-between items-center">
                    <div>
                        <span class="text-xs font-bold text-emerald-800 dark:text-emerald-400 uppercase tracking-wider">Laba Kotor Penjualan (Gross Profit)</span>
                        <div class="text-[10px] text-gray-400 dark:text-gray-500 mt-0.5">Pendapatan Penjualan Mart - HPP</div>
                    </div>
                    <div class="text-right flex items-center gap-3">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-350">
                            GPM: {{ number_format($gpm, 2) }}%
                        </span>
                        <span class="font-mono text-base font-extrabold text-emerald-700 dark:text-emerald-400">
                            Rp {{ number_format($grossProfit, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: PENDAPATAN JASA & OPERASIONAL LAIN -->
            <div class="mb-6">
                <div class="flex justify-between items-center bg-gray-50 dark:bg-gray-800/40 px-4 py-2.5 rounded-lg border border-gray-100 dark:border-gray-700/80 mb-3">
                    <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 tracking-wider uppercase">II. Pendapatan Jasa & Lainnya</h3>
                </div>

                <table class="w-full text-sm">
                    <tbody>
                        @forelse($otherRevenueAccounts as $account)
                            @if($account->period_balance != 0)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30 text-xs">
                                    <td class="py-2 pl-2 text-gray-700 dark:text-gray-300">
                                        <span class="font-mono text-gray-400 dark:text-gray-600 mr-2">{{ $account->code }}</span>
                                        {{ $account->name }}
                                    </td>
                                    <td class="text-right pr-2 font-mono text-gray-700 dark:text-gray-300 font-semibold">
                                        Rp {{ number_format($account->period_balance, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr><td colspan="2" class="text-center italic text-xs text-gray-400 py-3">Tidak ada pendapatan jasa tambahan</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot class="border-t border-gray-250/70 dark:border-gray-700">
                        <tr class="font-bold text-xs">
                            <td class="py-3 pl-2 text-gray-800 dark:text-gray-200">TOTAL LABA KOTOR + PENDAPATAN JASA</td>
                            <td class="text-right pr-2 py-3 font-mono text-gray-850 dark:text-gray-200">
                                Rp {{ number_format($totalGrossAndOther, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- SECTION 3: BEBAN OPERASIONAL -->
            <div class="mb-6">
                <div class="flex justify-between items-center bg-gray-50 dark:bg-gray-800/40 px-4 py-2.5 rounded-lg border border-gray-100 dark:border-gray-700/80 mb-3">
                    <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 tracking-wider uppercase">III. Beban Operasional</h3>
                </div>

                <table class="w-full text-sm">
                    <tbody>
                        @forelse($opExpenseAccounts as $account)
                            @if($account->period_balance != 0)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30 text-xs text-gray-600 dark:text-gray-400">
                                    <td class="py-2 pl-2">
                                        <span class="font-mono text-gray-400 dark:text-gray-600 mr-2">{{ $account->code }}</span>
                                        {{ $account->name }}
                                    </td>
                                    <td class="text-right pr-2 font-mono text-gray-700 dark:text-gray-300">
                                        Rp {{ number_format($account->period_balance, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr><td colspan="2" class="text-center italic text-xs text-gray-400 py-3">Tidak ada beban operasional</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot class="border-t border-gray-250/70 dark:border-gray-700">
                        <tr class="text-xs font-semibold">
                            <td class="py-2.5 pl-2 text-gray-500">Total Beban Operasional</td>
                            <td class="text-right pr-2 py-2.5 font-mono text-gray-700 dark:text-gray-300">
                                Rp {{ number_format($opExpense, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>

                <!-- OPERATING INCOME CARD -->
                <div class="mt-4 p-4 rounded-xl bg-gradient-to-r from-blue-500/5 to-indigo-500/5 dark:from-blue-500/10 dark:to-indigo-500/10 border border-blue-500/10 dark:border-blue-500/20 flex justify-between items-center">
                    <div>
                        <span class="text-xs font-bold text-blue-800 dark:text-blue-400 uppercase tracking-wider">Laba Operasional (Operating Income)</span>
                        <div class="text-[10px] text-gray-400 dark:text-gray-500 mt-0.5">Laba Kotor & Jasa - Beban Operasional</div>
                    </div>
                    <div class="text-right flex items-center gap-3">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-100 dark:bg-blue-950/40 text-blue-800 dark:text-blue-350">
                            Op Margin: {{ number_format($opMargin, 2) }}%
                        </span>
                        <span class="font-mono text-base font-extrabold text-blue-700 dark:text-blue-400">
                            Rp {{ number_format($operatingProfit, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- SECTION 4: BEBAN NON-OPERASIONAL -->
            <div class="mb-6">
                <div class="flex justify-between items-center bg-gray-50 dark:bg-gray-800/40 px-4 py-2.5 rounded-lg border border-gray-100 dark:border-gray-700/80 mb-3">
                    <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 tracking-wider uppercase">IV. Beban Non-Operasional (Lain-lain)</h3>
                </div>

                <table class="w-full text-sm">
                    <tbody>
                        @forelse($nonOpExpenseAccounts as $account)
                            @if($account->period_balance != 0)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30 text-xs text-gray-600 dark:text-gray-400">
                                    <td class="py-2 pl-2">
                                        <span class="font-mono text-gray-400 dark:text-gray-600 mr-2">{{ $account->code }}</span>
                                        {{ $account->name }}
                                    </td>
                                    <td class="text-right pr-2 font-mono text-gray-700 dark:text-gray-300">
                                        Rp {{ number_format($account->period_balance, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr><td colspan="2" class="text-center italic text-xs text-gray-400 py-3">Tidak ada beban non-operasional</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot class="border-t border-gray-250/70 dark:border-gray-700">
                        <tr class="text-xs font-semibold">
                            <td class="py-2.5 pl-2 text-gray-500">Total Beban Non-Operasional</td>
                            <td class="text-right pr-2 py-2.5 font-mono text-gray-700 dark:text-gray-300">
                                Rp {{ number_format($nonOpExpense, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- SECTION 5: GRAND TOTAL (LABA BERSIH AKHIR / SHU) -->
            <div class="border-t-2 border-double border-gray-300 dark:border-gray-700 pt-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-6 rounded-2xl shadow-md border relative overflow-hidden"
                     style="background: linear-gradient(135deg, #0f172a, #1e1b4b) !important; color: white !important; border-color: #1e1b4b !important;">
                    <!-- Subtle Glow effect -->
                    <div class="absolute -right-10 -bottom-10 w-24 h-24 bg-indigo-500/10 blur-2xl rounded-full"></div>
                    
                    <div class="relative z-10">
                        <h2 class="text-xl font-black uppercase tracking-wider flex items-center gap-1.5" style="color: white !important;">
                            <span>✨</span> LABA BERSIH AKHIR (SHU BERJALAN)
                        </h2>
                        <p class="text-xs mt-1" style="color: rgba(255, 255, 255, 0.7) !important;">Laba Operasional - Beban Non-Operasional</p>
                    </div>
                    <div class="text-right relative z-10 mt-3 sm:mt-0">
                        <span class="block text-3xl font-black font-mono tracking-tight" style="color: {{ $netIncome >= 0 ? '#f59e0b' : '#ef4444' }} !important;">
                            Rp {{ number_format($netIncome, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- AI Financial Audit Assistant Card -->
        <div class="glass-card p-6 mt-6 border border-amber-500/20 shadow-lg shadow-amber-500/5 relative overflow-hidden">
            <!-- Sparkle Ambient Background -->
            <div class="absolute -right-16 -top-16 w-32 h-32 bg-amber-500/10 blur-3xl rounded-full"></div>
            
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 relative z-10">
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <span class="text-amber-500 animate-pulse">✨</span>
                        Analisa Laporan Laba Rugi dengan AI
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Dapatkan audit finansial otomatis, deteksi kebocoran anggaran, dan rekomendasi bisnis strategis langsung dari AI Koperasi.</p>
                </div>
                <button type="button" id="startAiAnalysisBtn" class="flex-shrink-0 px-5 py-2.5 rounded-xl text-white font-bold bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 active:scale-95 transition-all shadow-md shadow-amber-500/20 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    Mulai Analisa AI
                </button>
            </div>

            <!-- Analysis Dashboard Panel (Hidden by default) -->
            <div id="aiAnalysisPanel" class="hidden mt-6 pt-6 border-t border-gray-200 dark:border-gray-800 transition-all duration-500">
                
                <!-- Skeleton Loader with Orbit Animation & Pulsing Glowing Orbs -->
                <div id="aiAnalysisLoading" class="flex flex-col items-center justify-center py-12 relative">
                    <div class="relative w-28 h-28 mb-6 flex items-center justify-center">
                        <!-- Outer Orbit Ring with glowing border -->
                        <div class="absolute inset-0 border-2 border-dashed border-amber-500/20 rounded-full animate-[spin_15s_linear_infinite]"></div>
                        
                        <!-- Middle Scanning Ring -->
                        <div class="absolute w-20 h-20 border-2 border-t-amber-500 border-r-transparent border-b-amber-500 border-l-transparent rounded-full animate-spin"></div>
                        
                        <!-- Inner Glow Backing -->
                        <div class="absolute w-12 h-12 bg-amber-500/10 dark:bg-amber-500/20 blur-xl rounded-full animate-pulse"></div>
                        
                        <!-- Central Orb with pulsing scale -->
                        <div class="relative w-14 h-14 bg-white dark:bg-gray-800 rounded-full border border-amber-250 dark:border-amber-900/60 shadow-lg shadow-amber-500/10 flex items-center justify-center animate-[pulse_2s_cubic-bezier(0.4,0,0.6,1)_infinite]">
                            <span class="text-2xl animate-bounce">💡</span>
                        </div>
                        
                        <!-- Orbiting Sparkle Element -->
                        <div class="absolute w-4 h-4 rounded-full bg-gradient-to-r from-amber-400 to-orange-500 shadow-md shadow-amber-500/50 animate-[ping_1.5s_cubic-bezier(0,0,0.2,1)_infinite]" style="top: 10px; right: 10px;"></div>
                    </div>
                    
                    <h4 class="text-base font-bold text-gray-800 dark:text-gray-200 tracking-wide flex items-center gap-1.5">
                        <span class="inline-block animate-pulse text-amber-500">✨</span> 
                        Menjalankan Audit Finansial AI...
                    </h4>
                    
                    <!-- Fading Status Box for Alternating Messages -->
                    <div class="mt-2 text-center h-5">
                        <p id="aiAnalysisStatusText" class="text-xs text-gray-400 dark:text-gray-500 font-medium" style="transition: all 0.5s ease; transform: translateY(0); opacity: 1;">
                            🔌 Menghubungkan ke Asisten Finansial AI...
                        </p>
                    </div>
                </div>

                <!-- Actions Bar -->
                <div id="aiAnalysisActions" class="hidden flex justify-between items-center mb-6 bg-amber-500/5 dark:bg-amber-500/10 p-3 rounded-xl border border-amber-500/10">
                    <span class="text-xs font-bold text-amber-800 dark:text-amber-400 flex items-center gap-1.5">
                        <span class="animate-pulse">✨</span> Hasil Audit Finansial AI Selesai
                    </span>
                    <button type="button" id="printAiAnalysisBtn" class="px-3.5 py-1.5 rounded-lg text-white text-xs font-bold bg-amber-500 hover:bg-amber-600 active:scale-95 transition-all flex items-center gap-1.5 shadow-sm shadow-amber-500/10">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Cetak Analisa AI
                    </button>
                </div>

                <!-- Analysis Content Display -->
                <div id="aiAnalysisContent" class="hidden text-gray-700 dark:text-gray-300 leading-relaxed text-sm mb-6">
                    <!-- Loaded analysis markdown will be formatted here -->
                </div>

                <!-- Document Validation Card (On Screen View) -->
                <div id="aiAnalysisValidation" class="hidden mt-6 mb-8 p-5 bg-gradient-to-br from-gray-50 to-gray-100/50 dark:from-gray-800/40 dark:to-gray-900/40 border border-gray-200 dark:border-gray-700/60 rounded-2xl shadow-sm transition-all duration-300">
                    <div class="flex flex-col md:flex-row gap-5 items-center md:items-start">
                        <!-- QR Code Column -->
                        <div class="flex-shrink-0 bg-white p-2.5 rounded-xl border border-gray-200 shadow-sm dark:border-gray-700">
                            <img id="screenQrCodeImg" src="" alt="QR Code Verifikasi" class="w-24 h-24 object-contain">
                        </div>
                        
                        <!-- Metadata Column -->
                        <div class="flex-1 text-left w-full">
                            <div class="flex flex-wrap items-center gap-2 mb-2">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wider uppercase bg-emerald-100 text-emerald-800 dark:bg-emerald-950/30 dark:text-emerald-400 flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span>
                                    <span>Sah & Valid</span>
                                </span>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wider uppercase bg-amber-100 text-amber-800 dark:bg-amber-950/30 dark:text-amber-400">
                                    Dokumen Resmi Digital
                                </span>
                            </div>
                            
                            <h4 class="text-sm font-bold text-gray-800 dark:text-gray-200 mb-1.5">
                                Verifikasi Keaslian Laporan Hasil Audit LLR (AI)
                            </h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed mb-4">
                                Dokumen ini diterbitkan secara otomatis dan sah melalui Asisten Finansial AI terintegrasi Koperasi Karyawan Spindo Karawang Factory. Pindai QR Code di samping untuk melakukan verifikasi keaslian dokumen secara langsung pada Sistem Portal Ledger Koperasi-SKF.
                            </p>
                            
                            <!-- Grid details -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-2 border-t border-gray-200 dark:border-gray-700/50 pt-3 text-[11px] text-gray-500 dark:text-gray-455">
                                <div>
                                    <span class="font-semibold text-gray-600 dark:text-gray-400">ID Dokumen:</span> 
                                    <span class="font-mono text-gray-850 dark:text-gray-300">AI-RPT-LRL-{{ $startDate->format('Ymd') }}-{{ $endDate->format('Ymd') }}</span>
                                </div>
                                <div>
                                    <span class="font-semibold text-gray-600 dark:text-gray-400">Dicetak Oleh:</span> 
                                    <span class="text-gray-850 dark:text-gray-300">{{ auth()->user()->name }}</span>
                                </div>
                                <div>
                                    <span class="font-semibold text-gray-600 dark:text-gray-400">Tanggal Terbit:</span> 
                                    <span class="text-gray-850 dark:text-gray-300">${new Date().toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'})}</span>
                                </div>
                                <div>
                                    <span class="font-semibold text-gray-600 dark:text-gray-400">Status Portal:</span> 
                                    <span class="text-emerald-600 dark:text-emerald-400 font-bold flex items-center gap-1 inline-flex">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Terverifikasi Sistem
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Error Container -->
                <div id="aiAnalysisError" class="hidden p-4 rounded-xl bg-red-50 dark:bg-red-950/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 text-sm flex items-center gap-3">
                    <span class="text-xl">⚠️</span>
                    <span id="aiAnalysisErrorMsg">Terjadi kesalahan saat memuat analisis.</span>
                </div>

                <!-- Interactive Chat Box -->
                <div id="aiChatSection" class="hidden mt-8 pt-6 border-t border-gray-200 dark:border-gray-800">
                    <h4 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2 mb-4">
                        <span>💬</span> Tanya Asisten AI Lebih Detail
                    </h4>
                    
                    <!-- Chat Messages Thread -->
                    <div id="aiChatThread" class="space-y-3 max-h-[250px] overflow-y-auto pr-1 mb-4 text-sm p-4 bg-gray-50/50 dark:bg-gray-800/10 border border-gray-100 dark:border-gray-800/50 rounded-xl">
                        <div class="flex items-start gap-2 bg-amber-50/50 dark:bg-amber-950/10 p-3 rounded-lg border border-amber-500/10 text-gray-700 dark:text-gray-300">
                            <span class="text-base flex-shrink-0">🤖</span>
                            <div>
                                <p class="font-medium text-xs text-amber-800 dark:text-amber-400 uppercase tracking-wider mb-1">Asisten Finansial Koperasi</p>
                                <p class="text-xs">Saya telah merangkum analisis di atas. Apakah ada detail pengeluaran atau strategi keuangan yang ingin Anda tanyakan lebih lanjut? Tuliskan pertanyaan Anda di bawah!</p>
                            </div>
                        </div>
                    </div>

                    <!-- Chat Input form -->
                    <form id="aiChatForm" class="flex gap-2">
                        <input type="text" id="aiChatInput" class="form-input flex-1 !rounded-xl !text-sm placeholder-gray-400" placeholder="Contoh: Bagaimana cara menurunkan HPP di Mart?..." required>
                        <button type="submit" id="aiChatSubmitBtn" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-xl active:scale-95 transition-all text-sm flex items-center gap-1.5 shadow-md shadow-amber-500/10">
                            <span>Kirim</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const startAiAnalysisBtn = document.getElementById('startAiAnalysisBtn');
            const aiAnalysisPanel = document.getElementById('aiAnalysisPanel');
            const aiAnalysisLoading = document.getElementById('aiAnalysisLoading');
            const aiAnalysisContent = document.getElementById('aiAnalysisContent');
            const aiAnalysisError = document.getElementById('aiAnalysisError');
            const aiAnalysisErrorMsg = document.getElementById('aiAnalysisErrorMsg');
            
            const aiChatSection = document.getElementById('aiChatSection');
            const aiChatThread = document.getElementById('aiChatThread');
            const aiChatForm = document.getElementById('aiChatForm');
            const aiChatInput = document.getElementById('aiChatInput');
            const aiChatSubmitBtn = document.getElementById('aiChatSubmitBtn');

            const financialContextString = `LAPORAN LABA RUGI KOPERASI
Periode: {{ $startDate->translatedFormat('d F Y') }} s/d {{ $endDate->translatedFormat('d F Y') }}

POST PENDAPATAN (REVENUE):
@foreach($revenues as $account)
@if($account->period_balance != 0)
- {{ $account->code }} {{ $account->name }}: Rp {{ number_format($account->period_balance, 0, ',', '.') }}
@endif
@endforeach
TOTAL PENDAPATAN: Rp {{ number_format($totalRevenue, 0, ',', '.') }}

POST BEBAN (EXPENSES):
@foreach($expenses as $account)
@if($account->period_balance != 0)
- {{ $account->code }} {{ $account->name }}: Rp {{ number_format($account->period_balance, 0, ',', '.') }}
@endif
@endforeach
TOTAL BEBAN: Rp {{ number_format($totalExpense, 0, ',', '.') }}

LABA BERSIH / SHU BERJALAN: Rp {{ number_format($netIncome, 0, ',', '.') }}`;

            const parseMarkdown = (markdown) => {
                if (!markdown) return '';
                
                const lines = markdown.split('\n');
                let html = [];
                let inList = false;
                let listType = null; // 'ul' or 'ol'
                let inTable = false;
                let tableRows = [];
                
                const flushList = () => {
                    if (inList) {
                        html.push(`</${listType}>`);
                        inList = false;
                        listType = null;
                    }
                };
                
                const flushTable = () => {
                    if (inTable && tableRows.length > 0) {
                        let tableHtml = '<div class="overflow-x-auto my-5 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm"><table class="w-full text-xs md:text-sm text-left">';
                        
                        tableRows.forEach((row, idx) => {
                            let cols = row.split('|').map(c => c.trim());
                            if (cols[0] === '') cols.shift();
                            if (cols[cols.length - 1] === '') cols.pop();
                            
                            if (row.includes('---')) {
                                return; 
                            }
                            
                            if (idx === 0) {
                                tableHtml += '<thead class="bg-gray-50 dark:bg-gray-800/80 text-gray-700 dark:text-gray-300 font-bold uppercase text-[10px] tracking-wider border-b border-gray-200 dark:border-gray-800">';
                                tableHtml += '<tr>';
                                cols.forEach(col => {
                                    tableHtml += `<th class="px-4 py-3">${col}</th>`;
                                });
                                tableHtml += '</tr></thead><tbody>';
                            } else {
                                tableHtml += '<tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-colors">';
                                cols.forEach(col => {
                                    const isNumber = /^(Rp\s*|-?\s*|[\d.,]+%?)+$/.test(col) || col.includes('Rp') || col.includes('%');
                                    const cellClass = isNumber ? 'px-4 py-3 font-mono text-gray-900 dark:text-white font-semibold' : 'px-4 py-3 text-gray-600 dark:text-gray-400';
                                    tableHtml += `<td class="${cellClass}">${col}</td>`;
                                });
                                tableHtml += '</tr>';
                            }
                        });
                        
                        tableHtml += '</tbody></table></div>';
                        html.push(tableHtml);
                        inTable = false;
                        tableRows = [];
                    }
                };
                
                for (let i = 0; i < lines.length; i++) {
                    let line = lines[i];
                    let trimmed = line.trim();
                    
                    if (trimmed.startsWith('|')) {
                        flushList();
                        inTable = true;
                        tableRows.push(line);
                        continue;
                    } else {
                        flushTable();
                    }
                    
                    if (trimmed.startsWith('>')) {
                        flushList();
                        let quoteText = trimmed.replace(/^>\s*/, '');
                        let calloutClass = "my-4 p-4 rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/30 text-gray-700 dark:text-gray-300";
                        let title = "";
                        
                        if (quoteText.startsWith('[!CAUTION]')) {
                            calloutClass = "my-4 p-4 rounded-xl border-l-4 border-red-500 bg-red-50/60 dark:bg-red-950/20 text-red-900 dark:text-red-300 border-t border-r border-b border-red-200 dark:border-red-900/50 shadow-md shadow-red-500/5";
                            title = "<div class='flex items-center gap-2 font-bold text-red-700 dark:text-red-400 mb-1.5 text-xs uppercase tracking-wider'>⚠️ Tindakan Darurat / Bahaya</div>";
                            quoteText = quoteText.replace('[!CAUTION]', '').trim();
                        } else if (quoteText.startsWith('[!WARNING]')) {
                            calloutClass = "my-4 p-4 rounded-xl border-l-4 border-amber-500 bg-amber-50/60 dark:bg-amber-950/20 text-amber-900 dark:text-amber-300 border-t border-r border-b border-amber-200 dark:border-amber-900/50 shadow-md shadow-amber-500/5";
                            title = "<div class='flex items-center gap-2 font-bold text-amber-700 dark:text-amber-400 mb-1.5 text-xs uppercase tracking-wider'>⚠️ Peringatan Penting</div>";
                            quoteText = quoteText.replace('[!WARNING]', '').trim();
                        } else if (quoteText.startsWith('[!IMPORTANT]')) {
                            calloutClass = "my-4 p-4 rounded-xl border-l-4 border-blue-500 bg-blue-50/60 dark:bg-blue-950/20 text-blue-900 dark:text-blue-300 border-t border-r border-b border-blue-200 dark:border-blue-900/50 shadow-md shadow-blue-500/5";
                            title = "<div class='flex items-center gap-2 font-bold text-blue-700 dark:text-blue-400 mb-1.5 text-xs uppercase tracking-wider'>📌 Poin Penting</div>";
                            quoteText = quoteText.replace('[!IMPORTANT]', '').trim();
                        } else if (quoteText.startsWith('[!NOTE]')) {
                            calloutClass = "my-4 p-4 rounded-xl border-l-4 border-emerald-500 bg-emerald-50/60 dark:bg-emerald-950/20 text-emerald-900 dark:text-emerald-300 border-t border-r border-b border-emerald-200 dark:border-emerald-900/50 shadow-md shadow-emerald-500/5";
                            title = "<div class='flex items-center gap-2 font-bold text-emerald-700 dark:text-emerald-400 mb-1.5 text-xs uppercase tracking-wider'>ℹ️ Catatan Analis</div>";
                            quoteText = quoteText.replace('[!NOTE]', '').trim();
                        }
                        
                        while (i + 1 < lines.length && lines[i + 1].trim().startsWith('>')) {
                            i++;
                            quoteText += ' ' + lines[i].trim().replace(/^>\s*/, '');
                        }
                        
                        quoteText = quoteText.replace(/\*\*(.*?)\*\*/g, '<strong class="font-bold text-gray-900 dark:text-white">$1</strong>');
                        quoteText = quoteText.replace(/\*(.*?)\*/g, '<em class="italic">$1</em>');
                        quoteText = quoteText.replace(/`(.*?)`/g, '<code class="font-mono bg-black/5 dark:bg-white/10 px-1 py-0.5 rounded text-[11px] font-semibold">$1</code>');
                        
                        html.push(`<div class="${calloutClass}">${title}<p class="text-xs md:text-sm leading-relaxed">${quoteText}</p></div>`);
                        continue;
                    }
                    
                    if (trimmed.startsWith('#')) {
                        flushList();
                        let depth = 0;
                        let headingContent = trimmed;
                        while (headingContent.startsWith('#')) {
                            depth++;
                            headingContent = headingContent.substring(1);
                        }
                        headingContent = headingContent.trim();
                        
                        let headingClass = "";
                        let headingTag = `h${depth}`;
                        
                        if (depth === 1) {
                            headingClass = "text-xl md:text-2xl font-black text-gray-900 dark:text-white mt-8 mb-4 border-b-2 border-gray-200 dark:border-gray-800 pb-2 flex items-center gap-2 uppercase tracking-wide";
                        } else if (depth === 2) {
                            headingClass = "text-lg md:text-xl font-extrabold text-gray-900 dark:text-white mt-6 mb-3 flex items-center gap-2";
                        } else if (depth === 3) {
                            if (headingContent.includes('🔴')) {
                                headingClass = "text-base md:text-lg font-black text-red-600 dark:text-red-400 mt-6 mb-3 pb-1 border-b border-red-150 dark:border-red-950/50 flex items-center gap-2";
                            } else if (headingContent.includes('🟢')) {
                                headingClass = "text-base md:text-lg font-black text-emerald-600 dark:text-emerald-400 mt-6 mb-3 pb-1 border-b border-emerald-150 dark:border-emerald-950/50 flex items-center gap-2";
                            } else if (headingContent.includes('🔵')) {
                                headingClass = "text-base md:text-lg font-black text-blue-600 dark:text-blue-400 mt-6 mb-3 pb-1 border-b border-blue-150 dark:border-blue-950/50 flex items-center gap-2";
                            } else {
                                headingClass = "text-base font-bold text-gray-850 dark:text-gray-250 mt-5 mb-2.5 flex items-center gap-2";
                            }
                        } else {
                            headingClass = "text-sm font-bold text-gray-800 dark:text-gray-300 mt-4 mb-2";
                        }
                        
                        html.push(`<${headingTag} class="${headingClass}">${headingContent}</${headingTag}>`);
                        continue;
                    }
                    
                    const isUnordered = trimmed.startsWith('- ') || trimmed.startsWith('* ');
                    const isOrdered = /^\d+\.\s/.test(trimmed);
                    const isChecklist = trimmed.startsWith('- [ ] ') || trimmed.startsWith('- [x] ') || trimmed.startsWith('- [X] ');
                    
                    if (isChecklist) {
                        if (!inList || listType !== 'ul') {
                            flushList();
                            inList = true;
                            listType = 'ul';
                            html.push('<ul class="space-y-3.5 my-4">');
                        }
                        
                        const checked = trimmed.startsWith('- [x] ') || trimmed.startsWith('- [X] ');
                        let text = trimmed.substring(6).trim();
                        
                        text = text.replace(/\*\*(.*?)\*\*/g, '<strong class="font-bold text-gray-900 dark:text-white">$1</strong>');
                        text = text.replace(/\*(.*?)\*/g, '<em class="italic">$1</em>');
                        text = text.replace(/`(.*?)`/g, '<code class="font-mono bg-gray-100 dark:bg-gray-800 px-1.5 py-0.5 rounded text-xs">$1</code>');
                        
                        if (checked) {
                            html.push(`
                                <li class="flex items-start gap-3 py-0.5 text-gray-500 dark:text-gray-500">
                                    <div class="flex-shrink-0 mt-0.5 w-5 h-5 rounded-md bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800/80 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                    <span class="line-through text-xs md:text-sm leading-relaxed">${text}</span>
                                </li>
                            `);
                        } else {
                            html.push(`
                                <li class="flex items-start gap-3 py-0.5 text-gray-700 dark:text-gray-300">
                                    <div class="flex-shrink-0 mt-0.5 w-5 h-5 rounded-md border border-gray-300 dark:border-gray-700 flex items-center justify-center text-gray-400">
                                        <div class="w-2.5 h-2.5 rounded-sm bg-transparent"></div>
                                    </div>
                                    <span class="text-xs md:text-sm leading-relaxed font-semibold text-gray-850 dark:text-gray-250">${text}</span>
                                </li>
                            `);
                        }
                        continue;
                    }
                    
                    if (isUnordered) {
                        if (!inList || listType !== 'ul') {
                            flushList();
                            inList = true;
                            listType = 'ul';
                            html.push('<ul class="list-disc pl-5 space-y-2 my-4 text-gray-600 dark:text-gray-400">');
                        }
                        
                        let text = trimmed.substring(2).trim();
                        text = text.replace(/\*\*(.*?)\*\*/g, '<strong class="font-bold text-gray-900 dark:text-white">$1</strong>');
                        text = text.replace(/\*(.*?)\*/g, '<em class="italic">$1</em>');
                        text = text.replace(/`(.*?)`/g, '<code class="font-mono bg-gray-100 dark:bg-gray-800 px-1 py-0.5 rounded text-xs">$1</code>');
                        
                        html.push(`<li class="text-xs md:text-sm leading-relaxed pl-1">${text}</li>`);
                        continue;
                    }
                    
                    if (isOrdered) {
                        if (!inList || listType !== 'ol') {
                            flushList();
                            inList = true;
                            listType = 'ol';
                            html.push('<ol class="list-decimal pl-5 space-y-2 my-4 text-gray-600 dark:text-gray-400">');
                        }
                        
                        let text = trimmed.replace(/^\d+\.\s*/, '').trim();
                        text = text.replace(/\*\*(.*?)\*\*/g, '<strong class="font-bold text-gray-900 dark:text-white">$1</strong>');
                        text = text.replace(/\*(.*?)\*/g, '<em class="italic">$1</em>');
                        text = text.replace(/`(.*?)`/g, '<code class="font-mono bg-gray-100 dark:bg-gray-800 px-1 py-0.5 rounded text-xs">$1</code>');
                        
                        html.push(`<li class="text-xs md:text-sm leading-relaxed pl-1 font-semibold text-gray-700 dark:text-gray-300">${text}</li>`);
                        continue;
                    }
                    
                    if (trimmed === '') {
                        flushList();
                        html.push('<div class="h-3"></div>');
                        continue;
                    }
                    
                    flushList();
                    let text = trimmed;
                    text = text.replace(/\*\*(.*?)\*\*/g, '<strong class="font-bold text-gray-900 dark:text-white">$1</strong>');
                    text = text.replace(/\*(.*?)\*/g, '<em class="italic">$1</em>');
                    text = text.replace(/`(.*?)`/g, '<code class="font-mono bg-gray-100 dark:bg-gray-800 px-1.5 py-0.5 rounded text-xs">$1</code>');
                    
                    html.push(`<p class="text-xs md:text-sm leading-relaxed text-gray-650 dark:text-gray-350 mb-3.5">${text}</p>`);
                }
                
                flushList();
                flushTable();
                
                let finalHtml = html.join('\n');
                finalHtml = finalHtml.replace(/<div class="h-3"><\/div>\s*<div class="h-3"><\/div>/g, '<div class="h-3"></div>');
                
                return finalHtml;
            };

            let isAnalyzing = false;
            let currentConversation = [];

            if (startAiAnalysisBtn) {
                startAiAnalysisBtn.addEventListener('click', function () {
                    if (isAnalyzing) return;

                    isAnalyzing = true;
                    aiAnalysisPanel.classList.remove('hidden');
                    aiAnalysisLoading.classList.remove('hidden');
                    aiAnalysisContent.classList.add('hidden');
                    aiAnalysisError.classList.add('hidden');
                    aiChatSection.classList.add('hidden');
                    document.getElementById('aiAnalysisActions')?.classList.add('hidden');
                    document.getElementById('aiAnalysisValidation')?.classList.add('hidden');
                    
                    // Fading status messages setup
                    const statusMessages = [
                        "🔌 Menghubungkan ke Asisten Finansial AI...",
                        "📊 Mengambil data Buku Besar & Laba Rugi periode terpilih...",
                        "🧮 Menghitung margin Laba Kotor (GPM) & Laba Bersih...",
                        "🔍 Memeriksa anomali Harga Pokok Penjualan (HPP)...",
                        "⚖️ Menganalisis proporsi Beban Operasional terhadap Pendapatan...",
                        "💡 Menyusun rekomendasi efisiensi pengadaan & harga Mart...",
                        "📝 Memformat dokumen laporan audit formal..."
                    ];
                    
                    let currentMsgIdx = 0;
                    const statusTextEl = document.getElementById('aiAnalysisStatusText');
                    
                    if (statusTextEl) {
                        statusTextEl.textContent = statusMessages[0];
                        statusTextEl.style.opacity = '1';
                        statusTextEl.style.transform = 'translateY(0)';
                    }
                    
                    const statusInterval = setInterval(() => {
                        if (!isAnalyzing) {
                            clearInterval(statusInterval);
                            return;
                        }
                        
                        if (statusTextEl) {
                            statusTextEl.style.opacity = '0';
                            statusTextEl.style.transform = 'translateY(-6px)';
                            
                            setTimeout(() => {
                                if (!isAnalyzing) return;
                                currentMsgIdx = (currentMsgIdx + 1) % statusMessages.length;
                                statusTextEl.textContent = statusMessages[currentMsgIdx];
                                statusTextEl.style.opacity = '1';
                                statusTextEl.style.transform = 'translateY(0)';
                            }, 500);
                        }
                    }, 3000);
                    
                    // Smooth scroll to panel
                    aiAnalysisPanel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

                    // CSRF Token
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                    fetch('{{ route("reports.income-statement.analyze") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            start_date: '{{ $startDate->format("Y-m-d") }}',
                            end_date: '{{ $endDate->format("Y-m-d") }}'
                        })
                    })
                    .then(response => response.json().then(data => ({ status: response.status, body: data })))
                    .then(res => {
                        isAnalyzing = false;
                        clearInterval(statusInterval);
                        aiAnalysisLoading.classList.add('hidden');
                        
                        if (res.status === 200 && res.body.success) {
                            const markdownResponse = res.body.response;
                            aiAnalysisContent.innerHTML = parseMarkdown(markdownResponse);
                            aiAnalysisContent.classList.remove('hidden');
                            
                            // Show screen QR verification badge
                            const screenQrCodeImg = document.getElementById('screenQrCodeImg');
                            if (screenQrCodeImg) {
                                screenQrCodeImg.src = `https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=${encodeURIComponent(window.location.href)}`;
                            }
                            document.getElementById('aiAnalysisValidation')?.classList.remove('hidden');
                            
                            // Show actions bar
                            document.getElementById('aiAnalysisActions')?.classList.remove('hidden');
                            
                            // Initialize chat history context with first audit
                            currentConversation = [
                                { role: 'system', content: `Konteks audit finansial koperasi:\n${financialContextString}` },
                                { role: 'assistant', content: markdownResponse }
                            ];

                            // Show chat section
                            aiChatSection.classList.remove('hidden');
                        } else {
                            aiAnalysisErrorMsg.textContent = res.body.error || 'Terjadi kesalahan sistem saat memproses analisa AI.';
                            aiAnalysisError.classList.remove('hidden');
                        }
                    })
                    .catch(err => {
                        isAnalyzing = false;
                        clearInterval(statusInterval);
                        aiAnalysisLoading.classList.add('hidden');
                        aiAnalysisErrorMsg.textContent = 'Gagal terhubung dengan server: ' + err.message;
                        aiAnalysisError.classList.remove('hidden');
                        console.error('AI Audit Error:', err);
                    });
                });
            }

            const printAiAnalysisBtn = document.getElementById('printAiAnalysisBtn');
            if (printAiAnalysisBtn) {
                printAiAnalysisBtn.addEventListener('click', function () {
                    const coopLogoUrl = "{{ isset($globalSettings['coop_logo']) ? Storage::url($globalSettings['coop_logo']) : '/icons/logo-original.png' }}";
                    const contentHtml = aiAnalysisContent.innerHTML;
                    const printWindow = window.open('', '_blank', 'width=900,height=800');
                    
                    const printHtml = `
                        <!DOCTYPE html>
                        <html>
                        <head>
                            <meta charset="utf-8">
                            <title>Cetak Analisa Laporan Laba Rugi AI - Koperasi</title>
                            <style>
                                body {
                                    font-family: 'Segoe UI', Arial, sans-serif;
                                    font-size: 13px;
                                    line-height: 1.6;
                                    color: #333;
                                    margin: 0;
                                    padding: 40px;
                                }
                                .kop-surat {
                                    text-align: center;
                                    border-bottom: 3px double #333;
                                    padding-bottom: 12px;
                                    margin-bottom: 25px;
                                    position: relative;
                                }
                                .kop-surat h1 {
                                    margin: 0;
                                    font-size: 18px;
                                    font-weight: bold;
                                    color: #1e3b8b;
                                    text-transform: uppercase;
                                    letter-spacing: 0.5px;
                                }
                                .kop-surat p.sub {
                                    margin: 5px 0 0 0;
                                    font-size: 12px;
                                    font-weight: bold;
                                    color: #333;
                                    letter-spacing: 0.5px;
                                }
                                .kop-surat p.period {
                                    margin: 3px 0 0 0;
                                    font-size: 11px;
                                    color: #666;
                                }
                                .meta-table {
                                    width: 100%;
                                    margin-bottom: 25px;
                                    font-size: 11px;
                                    color: #444;
                                }
                                .meta-table td {
                                    padding: 2px 0;
                                    border: none !important;
                                }
                                h1, h2, h3, h4 {
                                    color: #111;
                                    margin-top: 20px;
                                    margin-bottom: 10px;
                                    page-break-after: avoid;
                                }
                                h1 { font-size: 18px; border-bottom: 1px solid #ccc; padding-bottom: 5px; }
                                h2 { font-size: 15px; }
                                h3 { 
                                    font-size: 13px; 
                                    border-bottom: 1px solid #eee;
                                    padding-bottom: 4px;
                                    margin-top: 25px;
                                }
                                h3:first-of-type { margin-top: 15px; }
                                
                                h3.text-red-600 { color: #b91c1c !important; border-bottom: 1px solid #fca5a5 !important; }
                                h3.text-emerald-600 { color: #047857 !important; border-bottom: 1px solid #6ee7b7 !important; }
                                h3.text-blue-600 { color: #1d4ed8 !important; border-bottom: 1px solid #93c5fd !important; }
                                
                                p { margin: 0 0 10px 0; }
                                strong { font-weight: bold; color: #000; }
                                
                                table {
                                    width: 100%;
                                    border-collapse: collapse;
                                    margin: 15px 0;
                                    font-size: 11px;
                                }
                                th {
                                    background-color: #f3f4f6 !important;
                                    -webkit-print-color-adjust: exact;
                                    print-color-adjust: exact;
                                    color: #111;
                                    font-weight: bold;
                                    padding: 8px 10px;
                                    border: 1px solid #d1d5db;
                                    text-align: left;
                                }
                                td {
                                    padding: 8px 10px;
                                    border: 1px solid #e5e7eb;
                                }
                                tr:nth-child(even) {
                                    background-color: #fafafa;
                                }
                                .font-mono {
                                    font-family: Courier, monospace;
                                    font-weight: bold;
                                }
                                
                                .my-4, .my-5 {
                                    margin-top: 15px;
                                    margin-bottom: 15px;
                                }
                                .p-4 {
                                    padding: 12px 15px;
                                }
                                .rounded-xl {
                                    border-radius: 6px;
                                }
                                .border-l-4 {
                                    border-left-width: 4px !important;
                                    border-left-style: solid !important;
                                }
                                
                                .border-red-500 {
                                    border: 1px solid #fca5a5 !important;
                                    border-left: 4px solid #ef4444 !important;
                                    background-color: #fef2f2 !important;
                                    -webkit-print-color-adjust: exact;
                                    print-color-adjust: exact;
                                    color: #7f1d1d !important;
                                }
                                .border-amber-500 {
                                    border: 1px solid #fde68a !important;
                                    border-left: 4px solid #f59e0b !important;
                                    background-color: #fffbeb !important;
                                    -webkit-print-color-adjust: exact;
                                    print-color-adjust: exact;
                                    color: #78350f !important;
                                }
                                .border-blue-500 {
                                    border: 1px solid #bfdbfe !important;
                                    border-left: 4px solid #3b82f6 !important;
                                    background-color: #eff6ff !important;
                                    -webkit-print-color-adjust: exact;
                                    print-color-adjust: exact;
                                    color: #1e3a8a !important;
                                }
                                .border-emerald-500 {
                                    border: 1px solid #a7f3d0 !important;
                                    border-left: 4px solid #10b981 !important;
                                    background-color: #ecfdf5 !important;
                                    -webkit-print-color-adjust: exact;
                                    print-color-adjust: exact;
                                    color: #064e3b !important;
                                }
                                
                                .flex { display: flex; }
                                .items-start { align-items: flex-start; }
                                .gap-3 { gap: 10px; }
                                .py-0.5 { padding-top: 2px; padding-bottom: 2px; }
                                .line-through { text-decoration: line-through; color: #888; }
                                .space-y-3.5 > * + * { margin-top: 10px; }
                                .list-disc { padding-left: 20px; list-style-type: disc; }
                                .list-decimal { padding-left: 20px; list-style-type: decimal; }
                                
                                .w-5 { width: 16px; }
                                .h-5 { height: 16px; }
                                
                                .footer-print {
                                    margin-top: 40px;
                                    padding-top: 10px;
                                    border-top: 1px solid #ccc;
                                    font-size: 10px;
                                    color: #666;
                                    display: flex;
                                    justify-content: space-between;
                                }
                                
                                @media print {
                                    body {
                                        padding: 0;
                                    }
                                    .no-print {
                                        display: none;
                                    }
                                    thead {
                                        display: table-header-group;
                                    }
                                }
                            </style>
                        </head>
                        <body>
                            <!-- Print Header (Kop Surat Resmi Koperasi) -->
                            <table style="width: 100%; border-collapse: collapse; border-bottom: 3px double #000; margin-bottom: 20px; background: white !important;">
                                <tr style="background: white !important;">
                                    <!-- Left Logo -->
                                    <td style="width: 90px; text-align: center; vertical-align: middle; padding-bottom: 12px; border: none !important; background: white !important;">
                                        <img src="${window.location.origin}${coopLogoUrl}" style="max-height: 80px; width: auto;">
                                    </td>

                                    <!-- Center Text -->
                                    <td style="text-align: center; vertical-align: middle; padding: 0 10px 12px 10px; border: none !important; background: white !important;">
                                        <h1 style="font-family: 'Times New Roman', Times, serif; font-size: 14pt; margin: 0; padding: 0; line-height: 1.1; text-transform: uppercase; font-weight: bold; color: black !important;">KOPERASI KARYAWAN</h1>
                                        <h1 style="font-family: 'Times New Roman', Times, serif; font-size: 14pt; margin: 0; padding: 0; line-height: 1.1; text-transform: uppercase; font-weight: bold; color: black !important;">SPINDO KARAWANG FACTORY</h1>
                                        <h2 style="font-family: 'Times New Roman', Times, serif; font-size: 11pt; margin: 2px 0; padding: 0; line-height: 1.1; text-transform: uppercase; font-weight: bold; color: black !important;">PT STEEL PIPE INDUSTRY OF INDONESIA TBK</h2>
                                        <p style="font-family: 'Times New Roman', Times, serif; font-size: 8pt; margin: 3px 0 0 0; line-height: 1.2; font-style: italic; color: black !important;">
                                            Jl. Mitra Raya Blok F2 Kawasan Industri Mitra Karawang, Ds. Parungmulya Kec. Ciampel Karawang
                                        </p>
                                    </td>

                                    <!-- Right Logo -->
                                    <td style="width: 90px; text-align: center; vertical-align: middle; padding-bottom: 12px; border: none !important; background: white !important;">
                                        <img src="${window.location.origin}/images/spindo-logo.png" style="max-height: 80px; width: auto;">
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Document Header Text -->
                            <div style="text-align: center; margin-bottom: 25px;">
                                <h3 style="font-family: 'Segoe UI', Arial, sans-serif; font-size: 13pt; font-weight: bold; margin: 0; text-transform: uppercase; letter-spacing: 0.5px; border: none !important; padding: 0;">LAPORAN HASIL AUDIT & ANALISA LABA RUGI (AI ASSISTANT)</h3>
                                <p style="font-family: 'Segoe UI', Arial, sans-serif; font-size: 10pt; color: #555; margin: 5px 0 0 0;">Periode Keuangan: {{ $startDate->translatedFormat('d F Y') }} s/d {{ $endDate->translatedFormat('d F Y') }}</p>
                            </div>
                            
                            <table class="meta-table" style="border: none; margin-bottom: 20px;">
                                <tr style="border: none; background: none;">
                                    <td width="20%" style="border: none; padding: 2px 0;">Tanggal Cetak</td>
                                    <td width="2%" style="border: none; padding: 2px 0;">:</td>
                                    <td style="border: none; padding: 2px 0;">${new Date().toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'})} pukul ${new Date().toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'})} WIB</td>
                                </tr>
                                <tr style="border: none; background: none;">
                                    <td style="border: none; padding: 2px 0;">Dicetak Oleh</td>
                                    <td style="border: none; padding: 2px 0;">:</td>
                                    <td style="border: none; padding: 2px 0;">{{ auth()->user()->name }}</td>
                                </tr>
                                <tr style="border: none; background: none;">
                                    <td style="border: none; padding: 2px 0;">Status Dokumen</td>
                                    <td style="border: none; padding: 2px 0;">:</td>
                                    <td style="border: none; padding: 2px 0;"><strong>Dokumen Resmi Koperasi</strong> (Diolah secara otomatis via Asisten AI Finansial)</td>
                                </tr>
                            </table>
                            
                            <div class="content">
                                ${contentHtml}
                            </div>
                            
                            <!-- Document Validation Footer (QR Code & Official Seal) -->
                            <div class="footer-print" style="margin-top: 50px; padding-top: 15px; border-top: 2px solid #000; background: white !important; page-break-inside: avoid; break-inside: avoid;">
                                <table style="width: 100%; border: none !important; border-collapse: collapse; background: white !important; margin: 0;">
                                    <tr style="border: none !important; background: white !important;">
                                        <!-- QR Code Column -->
                                        <td style="width: 70px; vertical-align: middle; border: none !important; padding: 0; background: white !important;">
                                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=65x65&data=${encodeURIComponent(window.location.href)}" style="width: 65px; height: 65px; display: block; border: 1px solid #ddd; padding: 2px; background: white;">
                                        </td>
                                        
                                        <!-- Verification Text Column -->
                                        <td style="vertical-align: middle; text-align: left; padding-left: 15px; border: none !important; background: white !important; font-family: 'Segoe UI', Arial, sans-serif; font-size: 8.5pt; line-height: 1.45; color: black !important;">
                                            <strong>Dokumen ini sah dan diterbitkan secara digital oleh Koperasi Spindo Karawang Factory.</strong><br>
                                            Pindai QR Code ini untuk melakukan verifikasi keaslian dokumen secara langsung pada Sistem Portal Ledger Koperasi-SKF.<br>
                                            <span style="font-family: monospace; font-size: 7.5pt; color: #555;">Document ID: AI-RPT-LRL-{{ $startDate->format('Ymd') }}-{{ $endDate->format('Ymd') }} | Tanggal Cetak: ${new Date().toLocaleDateString('id-ID')} ${new Date().toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'})} WIB</span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            
                            <script>
                                window.onload = function() {
                                    setTimeout(function() {
                                        window.print();
                                        window.close();
                                    }, 600);
                                };
                            <\/script>
                        </body>
                        </html>
                    `;
                    
                    printWindow.document.write(printHtml);
                    printWindow.document.close();
                });
            }

            if (aiChatForm) {
                aiChatForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    const question = aiChatInput.value.trim();
                    if (!question) return;

                    // Append user message
                    const userMsgHtml = `
                        <div class="flex items-start gap-2 bg-gray-100 dark:bg-gray-800/40 p-3 rounded-lg text-gray-700 dark:text-gray-300">
                            <span class="text-base flex-shrink-0">👤</span>
                            <div>
                                <p class="font-medium text-[10px] text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Anda</p>
                                <p class="text-xs font-medium">${question}</p>
                            </div>
                        </div>
                    `;
                    aiChatThread.insertAdjacentHTML('beforeend', userMsgHtml);
                    aiChatThread.scrollTop = aiChatThread.scrollHeight;

                    // Clear input
                    aiChatInput.value = '';
                    aiChatInput.disabled = true;
                    aiChatSubmitBtn.disabled = true;
                    aiChatSubmitBtn.innerHTML = '<span>Memikirkan...</span>';

                    // Prepare prompt context
                    const fullMessageContext = `Konteks Laporan Keuangan kami:\n${financialContextString}\n\nPengguna bertanya: ${question}`;

                    // CSRF Token
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                    fetch('{{ route("ai.chat.public") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            message: fullMessageContext
                        })
                    })
                    .then(response => response.json())
                    .then(res => {
                        aiChatInput.disabled = false;
                        aiChatSubmitBtn.disabled = false;
                        aiChatSubmitBtn.innerHTML = `
                            <span>Kirim</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        `;

                        if (res.success) {
                            const aiResponse = res.response;
                            const aiMsgHtml = `
                                <div class="flex items-start gap-2 bg-amber-50/50 dark:bg-amber-950/10 p-3 rounded-lg border border-amber-500/10 text-gray-700 dark:text-gray-300">
                                    <span class="text-base flex-shrink-0">🤖</span>
                                    <div>
                                        <p class="font-medium text-[10px] text-amber-800 dark:text-amber-400 uppercase tracking-wider mb-1">Asisten Finansial Koperasi</p>
                                        <div class="text-xs text-gray-700 dark:text-gray-300 leading-relaxed">
                                            ${parseMarkdown(aiResponse)}
                                        </div>
                                    </div>
                                </div>
                            `;
                            aiChatThread.insertAdjacentHTML('beforeend', aiMsgHtml);
                            aiChatThread.scrollTop = aiChatThread.scrollHeight;
                        } else {
                            const errMsgHtml = `
                                <div class="flex items-start gap-2 bg-red-50 dark:bg-red-950/20 p-3 rounded-lg border border-red-200 dark:border-red-800/30 text-red-700 dark:text-red-400 text-xs">
                                    <span>⚠️</span>
                                    <p>Gagal memproses jawaban dari AI: ${res.error || 'Terjadi kesalahan sistem.'}</p>
                                </div>
                            `;
                            aiChatThread.insertAdjacentHTML('beforeend', errMsgHtml);
                            aiChatThread.scrollTop = aiChatThread.scrollHeight;
                        }
                    })
                    .catch(err => {
                        aiChatInput.disabled = false;
                        aiChatSubmitBtn.disabled = false;
                        aiChatSubmitBtn.innerHTML = `
                            <span>Kirim</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        `;
                        const errMsgHtml = `
                            <div class="flex items-start gap-2 bg-red-50 dark:bg-red-950/20 p-3 rounded-lg border border-red-200 dark:border-red-800/30 text-red-700 dark:text-red-400 text-xs">
                                <span>⚠️</span>
                                <p>Gagal terhubung dengan server AI: ${err.message}</p>
                            </div>
                        `;
                        aiChatThread.insertAdjacentHTML('beforeend', errMsgHtml);
                        aiChatThread.scrollTop = aiChatThread.scrollHeight;
                    });
                });
            }
        });
    </script>
    @endpush
@endsection
