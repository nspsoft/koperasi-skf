@extends('layouts.app')

@section('title', __('messages.shu_simulator.title'))

@push('styles')
<style>
    .simulator-card {
        background: linear-gradient(135deg, rgba(255,255,255,0.95) 0%, rgba(249,250,251,0.95) 100%);
        border-radius: 1.5rem;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
    }
    .dark .simulator-card {
        background: linear-gradient(135deg, rgba(31,41,55,0.95) 0%, rgba(17,24,39,0.95) 100%);
    }
    
    /* Animated bars */
    .progress-bar {
        transition: width 0.5s ease-out;
    }
    
    /* Floating animation */
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }
    .float-animation {
        animation: float 3s ease-in-out infinite;
    }
    
    /* Pulse animation */
    @keyframes pulse-glow {
        0%, 100% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.4); }
        50% { box-shadow: 0 0 20px 10px rgba(59, 130, 246, 0.1); }
    }
    .pulse-glow {
        animation: pulse-glow 2s ease-in-out infinite;
    }
    
    /* Range slider styling */
    input[type="range"] {
        -webkit-appearance: none;
        width: 100%;
        height: 8px;
        border-radius: 4px;
        background: #e5e7eb;
        outline: none;
    }
    input[type="range"]::-webkit-slider-thumb {
        -webkit-appearance: none;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        cursor: pointer;
        box-shadow: 0 4px 10px rgba(59, 130, 246, 0.3);
        transition: transform 0.2s;
    }
    input[type="range"]::-webkit-slider-thumb:hover {
        transform: scale(1.2);
    }
    .dark input[type="range"] {
        background: #374151;
    }
    
    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem 0;
        border-bottom: 1px dashed #e5e7eb;
    }
    .info-row:last-child {
        border-bottom: none;
    }
    .dark .info-row {
        border-color: #374151;
    }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto space-y-6" x-data="shuSimulator()">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('shu.tutorial') }}" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
            <svg class="w-6 h-6 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('messages.shu_simulator.title') }}</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">{{ __('messages.shu_simulator.subtitle') }}</p>
        </div>
    </div>

    <!-- Section 1: Input Data Koperasi -->
    <div class="simulator-card p-6">
        <h2 class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2 mb-6">
            <span class="bg-blue-100 dark:bg-blue-900/40 text-blue-600 w-8 h-8 flex items-center justify-center rounded-full text-sm">1</span>
            {{ __('messages.shu_simulator.section1_title') }}
        </h2>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Column 1: Anggota & Simpanan -->
            <div class="space-y-4">
                <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">{{ __('messages.shu_simulator.member_savings_title') }}</h3>
                
                <!-- Jumlah Anggota -->
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.shu_simulator.member_count') }}</label>
                        <span class="text-sm font-bold text-purple-600" x-text="jumlahAnggota + ' {{ __('messages.shu_simulator.member_unit') }}'"></span>
                    </div>
                    <input type="range" x-model="jumlahAnggota" min="10" max="500" step="10" class="w-full">
                    <div class="flex justify-between text-xs text-gray-500">
                        <span>10</span>
                        <span>500</span>
                    </div>
                </div>

                <!-- Rata-rata Simpanan -->
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.shu_simulator.avg_savings') }}</label>
                        <span class="text-sm font-bold text-green-600" x-text="formatRupiah(rataRataSimpanan)"></span>
                    </div>
                    <input type="range" x-model="rataRataSimpanan" min="100000" max="5000000" step="100000" class="w-full">
                    <div class="flex justify-between text-xs text-gray-500">
                        <span>Rp 100rb</span>
                        <span>Rp 5 Juta</span>
                    </div>
                </div>

                <!-- Result: Total Simpanan -->
                <div class="bg-green-50 dark:bg-green-900/30 rounded-lg p-3 border border-green-200 dark:border-green-800">
                    <p class="text-xs text-green-600 font-medium">{{ __('messages.shu_simulator.total_coop_savings') }}</p>
                    <p class="text-lg font-bold text-green-700 dark:text-green-400" x-text="formatRupiah(totalSimpananKoperasi)"></p>
                    <p class="text-[10px] text-gray-500 mt-1" x-text="jumlahAnggota + ' × ' + formatRupiah(rataRataSimpanan)"></p>
                </div>
            </div>

            <!-- Column 2: Belanja & Omset -->
            <div class="space-y-4">
                <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">{{ __('messages.shu_simulator.shopping_turnover_title') }}</h3>
                
                <!-- Rata-rata Belanja -->
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.shu_simulator.avg_shopping') }}</label>
                        <span class="text-sm font-bold text-blue-600" x-text="formatRupiah(rataRataBelanja)"></span>
                    </div>
                    <input type="range" x-model="rataRataBelanja" min="500000" max="25000000" step="500000" class="w-full">
                    <div class="flex justify-between text-xs text-gray-500">
                        <span>Rp 500rb</span>
                        <span>Rp 25 Juta</span>
                    </div>
                </div>

                <!-- Margin Keuntungan -->
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.shu_simulator.profit_margin') }}</label>
                        <span class="text-sm font-bold text-amber-600" x-text="marginKeuntungan + '%'"></span>
                    </div>
                    <input type="range" x-model="marginKeuntungan" min="5" max="30" step="1" class="w-full">
                    <div class="flex justify-between text-xs text-gray-500">
                        <span>5%</span>
                        <span>30%</span>
                    </div>
                </div>

                <!-- Result: Total Omset & Laba Kotor -->
                <div class="bg-blue-50 dark:bg-blue-900/30 rounded-lg p-3 border border-blue-200 dark:border-blue-800">
                    <div class="info-row">
                        <span class="text-xs text-gray-500">{{ __('messages.shu_simulator.total_coop_turnover') }}</span>
                        <span class="font-bold text-blue-600" x-text="formatRupiah(totalOmsetKoperasi)"></span>
                    </div>
                    <div class="info-row">
                        <span class="text-xs text-gray-500">{{ __('messages.shu_simulator.gross_profit') }} ({{ __('messages.shu_simulator.profit_margin') }} <span x-text="marginKeuntungan"></span>%)</span>
                        <span class="font-bold text-amber-600" x-text="formatRupiah(labaKotor)"></span>
                    </div>
                </div>
            </div>

            <!-- Column 3: Biaya & Laba Bersih -->
            <div class="space-y-4">
                <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">{{ __('messages.shu_simulator.costs_net_profit_title') }}</h3>
                
                <!-- Biaya Operasional -->
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.shu_simulator.op_costs') }}</label>
                        <span class="text-sm font-bold text-red-600" x-text="formatRupiah(biayaOperasional)"></span>
                    </div>
                    <input type="range" x-model="biayaOperasional" min="0" max="50000000" step="1000000" class="w-full">
                    <div class="flex justify-between text-xs text-gray-500">
                        <span>Rp 0</span>
                        <span>Rp 50 Juta</span>
                    </div>
                </div>

                <!-- Pendapatan Lain -->
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.shu_simulator.other_income') }}</label>
                        <span class="text-sm font-bold text-teal-600" x-text="formatRupiah(pendapatanLain)"></span>
                    </div>
                    <input type="range" x-model="pendapatanLain" min="0" max="20000000" step="500000" class="w-full">
                    <div class="flex justify-between text-xs text-gray-500">
                        <span>Rp 0</span>
                        <span>Rp 20 Juta</span>
                    </div>
                </div>

                <!-- Result: Laba Bersih (SHU) -->
                <div class="bg-gradient-to-r from-primary-500 to-blue-600 rounded-lg p-4 text-white">
                    <p class="text-xs opacity-80">💰 {{ __('messages.shu_simulator.net_profit_shu') }}</p>
                    <p class="text-2xl font-bold" x-text="formatRupiah(labaBersih)"></p>
                    <p class="text-[10px] opacity-70 mt-1">{{ __('messages.shu_simulator.net_profit_help') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 2: Persentase Pembagian -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="simulator-card p-6">
            <h2 class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2 mb-6">
                <span class="bg-purple-100 dark:bg-purple-900/40 text-purple-600 w-8 h-8 flex items-center justify-center rounded-full text-sm">2</span>
                Persentase Pembagian SHU
            </h2>

            <div class="space-y-4">
                <!-- % Dana Cadangan -->
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">% {{ __('messages.shu.reserve_fund') }}</label>
                        <span class="text-sm font-bold text-red-600" x-text="persenCadangan + '%'"></span>
                    </div>
                    <input type="range" x-model.number="persenCadangan" min="20" max="40" step="1" class="w-full">
                </div>

                <!-- % Jasa Modal -->
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">% {{ __('messages.shu.member_jasa_modal') }}</label>
                        <span class="text-sm font-bold text-green-600" x-text="persenJasaModal + '%'"></span>
                    </div>
                    <input type="range" x-model.number="persenJasaModal" min="10" max="40" step="1" class="w-full">
                </div>

                <!-- % Jasa Usaha -->
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">% {{ __('messages.shu.member_jasa_usaha') }}</label>
                        <span class="text-sm font-bold text-blue-600" x-text="persenJasaUsaha + '%'"></span>
                    </div>
                    <input type="range" x-model.number="persenJasaUsaha" min="10" max="40" step="1" class="w-full">
                </div>

                <!-- % Dana Lainnya -->
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('messages.shu_simulator.other_funds_auto') }}</span>
                        <span class="text-sm font-bold" :class="sisaPersen >= 0 ? 'text-purple-600' : 'text-red-600'" x-text="sisaPersen + '%'"></span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">{{ __('messages.shu_simulator.other_funds_help') }}</p>
                </div>

                <!-- Total Check -->
                <div class="flex items-center gap-2 p-3 rounded-lg" :class="totalPersen == 100 ? 'bg-green-50 dark:bg-green-900/30' : 'bg-red-50 dark:bg-red-900/30'">
                    <template x-if="totalPersen == 100">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </template>
                    <template x-if="totalPersen != 100">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </template>
                    <span class="text-sm font-medium" :class="totalPersen == 100 ? 'text-green-700 dark:text-green-400' : 'text-red-700 dark:text-red-400'">
                        Total: <span x-text="totalPersen"></span>%
                    </span>
                </div>
            </div>
        </div>

        <!-- Visualisasi Pembagian -->
        <div class="simulator-card p-6">
            <h2 class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2 mb-6">
                <span class="bg-amber-100 dark:bg-amber-900/40 text-amber-600 w-8 h-8 flex items-center justify-center rounded-full text-sm">3</span>
                {{ __('messages.shu_simulator.section3_title') }}
            </h2>

            <!-- Animated Bars -->
            <div class="space-y-3">
                <div class="space-y-1">
                    <div class="flex justify-between text-xs">
                        <span class="text-red-600 font-medium">{{ __('messages.shu.reserve_fund') }}</span>
                        <span class="font-bold" x-text="formatRupiah(poolCadangan)"></span>
                    </div>
                    <div class="h-6 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden relative">
                        <div class="h-full bg-red-500 progress-bar rounded-full" :style="'width: ' + persenCadangan + '%'"></div>
                        <span class="absolute inset-0 flex items-center justify-center text-xs font-bold" :class="persenCadangan > 15 ? 'text-white' : 'text-red-600'" x-text="persenCadangan + '%'"></span>
                    </div>
                </div>

                <div class="space-y-1">
                    <div class="flex justify-between text-xs">
                        <span class="text-green-600 font-medium">{{ __('messages.shu.member_jasa_modal') }}</span>
                        <span class="font-bold" x-text="formatRupiah(poolJasaModal)"></span>
                    </div>
                    <div class="h-6 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden relative">
                        <div class="h-full bg-green-500 progress-bar rounded-full" :style="'width: ' + persenJasaModal + '%'"></div>
                        <span class="absolute inset-0 flex items-center justify-center text-xs font-bold" :class="persenJasaModal > 15 ? 'text-white' : 'text-green-600'" x-text="persenJasaModal + '%'"></span>
                    </div>
                </div>

                <div class="space-y-1">
                    <div class="flex justify-between text-xs">
                        <span class="text-blue-600 font-medium">{{ __('messages.shu.member_jasa_usaha') }}</span>
                        <span class="font-bold" x-text="formatRupiah(poolJasaUsaha)"></span>
                    </div>
                    <div class="h-6 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden relative">
                        <div class="h-full bg-blue-500 progress-bar rounded-full" :style="'width: ' + persenJasaUsaha + '%'"></div>
                        <span class="absolute inset-0 flex items-center justify-center text-xs font-bold" :class="persenJasaUsaha > 15 ? 'text-white' : 'text-blue-600'" x-text="persenJasaUsaha + '%'"></span>
                    </div>
                </div>

                <div class="space-y-1">
                    <div class="flex justify-between text-xs">
                        <span class="text-purple-600 font-medium">{{ __('messages.shu.others') }}</span>
                        <span class="font-bold" x-text="formatRupiah(poolLainnya)"></span>
                    </div>
                    <div class="h-6 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden relative">
                        <div class="h-full bg-purple-500 progress-bar rounded-full" :style="'width: ' + sisaPersen + '%'"></div>
                        <span class="absolute inset-0 flex items-center justify-center text-xs font-bold" :class="sisaPersen > 15 ? 'text-white' : 'text-purple-600'" x-text="sisaPersen + '%'"></span>
                    </div>
                </div>
            </div>

            <!-- Total untuk Anggota Highlight -->
            <div class="bg-gradient-to-r from-primary-500 to-blue-600 rounded-xl p-4 text-white mt-4 float-animation">
                <p class="text-sm opacity-80">{{ __('messages.shu_simulator.total_for_members') }}</p>
                <p class="text-2xl font-bold" x-text="formatRupiah(poolAnggota)"></p>
                <p class="text-xs opacity-70 mt-1">{{ __('messages.shu_simulator.total_for_members_help') }}</p>
            </div>
        </div>
    </div>

    <!-- Section 3: Simulasi Anggota -->
    <div class="simulator-card p-6">
        <h2 class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2 mb-6">
            <span class="bg-teal-100 dark:bg-teal-900/40 text-teal-600 w-8 h-8 flex items-center justify-center rounded-full text-sm">4</span>
            {{ __('messages.shu_simulator.section4_title') }}
        </h2>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Inputs -->
            <div class="space-y-4">
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.shu_simulator.your_savings') }}</label>
                        <span class="text-sm font-bold text-green-600" x-text="formatRupiah(simpananAnda)"></span>
                    </div>
                    <input type="range" x-model="simpananAnda" min="100000" max="10000000" step="100000" class="w-full">
                    <div class="flex justify-between text-xs text-gray-500">
                        <span>Rp 100rb</span>
                        <span>Rp 10 Juta</span>
                    </div>
                </div>

                <div class="space-y-2">
                    <div class="flex justify-between">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.shu_simulator.your_shopping') }}</label>
                        <span class="text-sm font-bold text-blue-600" x-text="formatRupiah(belanjaAnda)"></span>
                    </div>
                    <input type="range" x-model="belanjaAnda" min="0" max="25000000" step="100000" class="w-full">
                    <div class="flex justify-between text-xs text-gray-500">
                        <span>Rp 0</span>
                        <span>Rp 25 Juta</span>
                    </div>
                </div>

                <!-- Perbandingan -->
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3 text-xs space-y-2">
                    <div class="info-row">
                        <span class="text-gray-500">{{ __('messages.shu_simulator.your_savings_contrib') }}</span>
                        <span class="font-bold" x-text="((simpananAnda / totalSimpananKoperasi) * 100).toFixed(2) + '%'"></span>
                    </div>
                    <div class="info-row">
                        <span class="text-gray-500">{{ __('messages.shu_simulator.your_shopping_contrib') }}</span>
                        <span class="font-bold" x-text="((belanjaAnda / totalOmsetKoperasi) * 100).toFixed(2) + '%'"></span>
                    </div>
                </div>
            </div>

            <!-- Result -->
            <div class="space-y-3">
                <div class="bg-gradient-to-br from-emerald-50 to-green-100 dark:from-emerald-900/30 dark:to-green-900/30 border border-green-200 dark:border-green-800 rounded-xl p-4">
                    <h4 class="text-sm font-bold text-green-800 dark:text-green-400 mb-2">💰 {{ __('messages.shu_simulator.your_jasa_modal') }}</h4>
                    <div class="bg-white/80 dark:bg-gray-800/80 rounded-lg p-2 font-mono text-[10px] mb-2">
                        (<span x-text="formatRupiah(simpananAnda)"></span> / <span x-text="formatRupiah(totalSimpananKoperasi)"></span>) × <span x-text="formatRupiah(poolJasaModal)"></span>
                    </div>
                    <p class="text-xl font-bold text-green-600" x-text="formatRupiah(shuJasaModalAnda)"></p>
                </div>

                <div class="bg-gradient-to-br from-sky-50 to-blue-100 dark:from-sky-900/30 dark:to-blue-900/30 border border-blue-200 dark:border-blue-800 rounded-xl p-4">
                    <h4 class="text-sm font-bold text-blue-800 dark:text-blue-400 mb-2">🛒 {{ __('messages.shu_simulator.your_jasa_usaha') }}</h4>
                    <div class="bg-white/80 dark:bg-gray-800/80 rounded-lg p-2 font-mono text-[10px] mb-2">
                        (<span x-text="formatRupiah(belanjaAnda)"></span> / <span x-text="formatRupiah(totalOmsetKoperasi)"></span>) × <span x-text="formatRupiah(poolJasaUsaha)"></span>
                    </div>
                    <p class="text-xl font-bold text-blue-600" x-text="formatRupiah(shuJasaUsahaAnda)"></p>
                </div>

                <div class="bg-gradient-to-r from-amber-400 to-orange-500 rounded-xl p-4 text-white pulse-glow">
                    <p class="text-sm opacity-80">🎉 {{ __('messages.shu_simulator.your_total_shu') }}</p>
                    <p class="text-2xl font-bold" x-text="formatRupiah(totalShuAnda)"></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tips -->
    <div class="bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/30 dark:to-purple-900/30 border border-indigo-100 dark:border-indigo-800 rounded-xl p-6">
        <h3 class="font-bold text-indigo-800 dark:text-indigo-400 mb-3">💡 {{ __('messages.shu_simulator.tips_title') }}</h3>
        <ul class="text-sm text-indigo-700 dark:text-indigo-300 space-y-2">
            <li class="flex items-start gap-2">
                <span class="text-green-500">✓</span>
                <span>{!! __('messages.shu_simulator.tip1') !!}</span>
            </li>
            <li class="flex items-start gap-2">
                <span class="text-green-500">✓</span>
                <span>{!! __('messages.shu_simulator.tip2') !!}</span>
            </li>
            <li class="flex items-start gap-2">
                <span class="text-green-500">✓</span>
                <span>{!! __('messages.shu_simulator.tip3') !!}</span>
            </li>
        </ul>
    </div>
</div>

@push('scripts')
<script>
function shuSimulator() {
    return {
        // Data Anggota & Simpanan
        jumlahAnggota: 100,
        rataRataSimpanan: 1200000,
        
        // Data Belanja & Omset
        rataRataBelanja: 10000000,
        marginKeuntungan: 15,
        
        // Biaya & Pendapatan Lain
        biayaOperasional: 10000000,
        pendapatanLain: 5000000,
        
        // Persentase Pembagian
        persenCadangan: 25,
        persenJasaModal: 20,
        persenJasaUsaha: 20,
        
        // Simulasi Anggota
        simpananAnda: 2000000,
        belanjaAnda: 5000000,
        
        // Computed: Totals
        get totalSimpananKoperasi() {
            return this.jumlahAnggota * this.rataRataSimpanan;
        },
        get totalOmsetKoperasi() {
            return this.jumlahAnggota * this.rataRataBelanja;
        },
        get labaKotor() {
            return this.totalOmsetKoperasi * (this.marginKeuntungan / 100);
        },
        get labaBersih() {
            return Math.max(0, this.labaKotor + parseInt(this.pendapatanLain) - parseInt(this.biayaOperasional));
        },
        
        // Computed: Persentase
        // Computed: Persentase as Numbers (for bar width)
        get pCadangan() {
            return Number(this.persenCadangan);
        },
        get pJasaModal() {
            return Number(this.persenJasaModal);
        },
        get pJasaUsaha() {
            return Number(this.persenJasaUsaha);
        },
        get sisaPersen() {
            return 100 - this.pCadangan - this.pJasaModal - this.pJasaUsaha;
        },
        get totalPersen() {
            return this.pCadangan + this.pJasaModal + this.pJasaUsaha + Math.max(0, this.sisaPersen);
        },
        
        // Computed: Pools
        get poolCadangan() {
            return this.labaBersih * (this.persenCadangan / 100);
        },
        get poolJasaModal() {
            return this.labaBersih * (this.persenJasaModal / 100);
        },
        get poolJasaUsaha() {
            return this.labaBersih * (this.persenJasaUsaha / 100);
        },
        get poolLainnya() {
            return this.labaBersih * (Math.max(0, this.sisaPersen) / 100);
        },
        get poolAnggota() {
            return this.poolJasaModal + this.poolJasaUsaha;
        },
        
        // Computed: Bar Widths (for visualization)
        get barWidthCadangan() {
            return Number(this.persenCadangan) + '%';
        },
        get barWidthJasaModal() {
            return Number(this.persenJasaModal) + '%';
        },
        get barWidthJasaUsaha() {
            return Number(this.persenJasaUsaha) + '%';
        },
        get barWidthLainnya() {
            return Math.max(0, this.sisaPersen) + '%';
        },
        
        // Computed: Anggota SHU
        get shuJasaModalAnda() {
            if (this.totalSimpananKoperasi === 0) return 0;
            return (this.simpananAnda / this.totalSimpananKoperasi) * this.poolJasaModal;
        },
        get shuJasaUsahaAnda() {
            if (this.totalOmsetKoperasi === 0) return 0;
            return (this.belanjaAnda / this.totalOmsetKoperasi) * this.poolJasaUsaha;
        },
        get totalShuAnda() {
            return this.shuJasaModalAnda + this.shuJasaUsahaAnda;
        },
        
        formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                maximumFractionDigits: 0
            }).format(number);
        }
    }
}
</script>
@endpush
@endsection
