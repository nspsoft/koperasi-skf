@extends('layouts.app')

@section('title', __('messages.shu_calculator.title'))

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('shu.index') }}" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
            <svg class="w-6 h-6 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('messages.shu_calculator.title') }}</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">{{ __('messages.shu_calculator.subtitle') }}</p>
        </div>
    </div>

    <!-- Regulatory Info Box -->
    <div class="bg-blue-50 dark:bg-blue-900/30 border border-blue-100 dark:border-blue-800 rounded-xl p-4">
        <div class="flex gap-3">
            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div class="text-sm text-blue-800 dark:text-blue-300">
                <p class="font-semibold mb-1">{{ __('messages.shu_calculator.legal_basis_title') }}</p>
                <ul class="list-disc list-inside space-y-0.5 text-xs">
                    <li><strong>{{ __('messages.shu.reserve_fund') }}:</strong> {{ __('messages.shu_calculator.reserve_fund_min') }}</li>
                    <li><strong>{{ __('messages.shu.member_jasa_modal') }}:</strong> {{ __('messages.shu_calculator.jasa_modal_desc') }}</li>
                    <li><strong>{{ __('messages.shu.member_jasa_usaha') }}:</strong> {{ __('messages.shu_calculator.jasa_usaha_desc') }}</li>
                    <li><strong>{{ __('messages.shu.others') }}:</strong> {{ __('messages.shu_calculator.other_funds_list') }}</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="glass-card-solid p-6">
        <form action="{{ route('shu.save-settings') }}" method="POST" class="space-y-6" id="shuForm">
            @csrf

            <!-- Year & Total Pool -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="form-label">{{ __('messages.shu_calculator.period_year') }} <span class="text-red-500">*</span></label>
                    <select name="year" class="form-input" required onchange="window.location.href='{{ route('shu.calculator') }}?year='+this.value">
                        @foreach($availableYears as $y)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">{{ __('messages.shu_calculator.total_shu_label') }} <span class="text-red-500">*</span></label>
                    <div class="flex gap-2">
                        <div class="relative flex-1">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                            <input type="number" name="total_shu_pool" value="{{ old('total_shu_pool', $setting->total_shu_pool ?? 0) }}" 
                                   class="form-input pl-10" placeholder="0" min="0" step="1000" required id="totalPool">
                        </div>
                        <button type="button" onclick="syncWithPL()" class="btn-secondary px-3" title="Sinkronkan dengan Laba Rugi Berjalan">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            Ambil dari Laba Rugi
                        </button>
                    </div>
                    <p class="text-[10px] text-gray-500 mt-1">
                        Saran berdasarkan Laba Rugi: <span class="font-bold text-primary-600">Rp {{ number_format($suggestedShu, 0, ',', '.') }}</span>
                    </p>
                    <p class="text-xs text-gray-500 mt-1">{{ __('messages.shu_calculator.total_shu_help') }}</p>
                </div>
            </div>

            <!-- Allocation Percentages -->
            <div class="border-t border-gray-100 dark:border-gray-700 pt-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
                    <div class="flex items-center gap-4">
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('messages.shu_calculator.allocation_percentage') }}</h3>
                        <div class="relative inline-block text-left" x-data="{ open: false }">
                            <button type="button" @click="open = !open" class="btn-secondary py-1 px-3 text-xs flex items-center gap-2">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                {{ __('messages.shu_calculator.use_standard_preset') }}
                            </button>
                            <div x-show="open" @click.away="open = false" 
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 class="absolute left-0 mt-2 w-56 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 z-20 overflow-hidden">
                                <div class="py-1">
                                    <button type="button" onclick="applyPreset('standard')" class="block w-full text-left px-4 py-2 text-xs text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        <p class="font-bold">{{ __('messages.shu_calculator.preset_standard_title') }}</p>
                                        <p class="text-[10px] text-gray-500">{{ __('messages.shu_calculator.preset_standard_desc') }}</p>
                                    </button>
                                    <button type="button" onclick="applyPreset('flexible')" class="block w-full text-left px-4 py-2 text-xs text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 border-t border-gray-100 dark:border-gray-700">
                                        <p class="font-bold">{{ __('messages.shu_calculator.preset_flexible_title') }}</p>
                                        <p class="text-[10px] text-gray-500">{{ __('messages.shu_calculator.preset_flexible_desc') }}</p>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-sm font-bold" id="totalPersenDisplay">
                        Total: <span id="totalPersen" class="text-primary-600">0</span>%
                    </div>
                </div>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <!-- Dana Cadangan -->
                    <div class="bg-red-50 dark:bg-red-900/20 p-3 rounded-lg">
                        <label class="text-xs font-medium text-red-700 dark:text-red-400 uppercase">{{ __('messages.shu.reserve_fund') }}</label>
                        <div class="relative mt-1">
                            <input type="number" name="persen_cadangan" value="{{ old('persen_cadangan', $setting->persen_cadangan ?? 25) }}" 
                                   class="form-input pr-8 text-sm persen-input" min="20" max="100" step="0.5" required>
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm">%</span>
                        </div>
                        <p class="text-[10px] text-red-600 dark:text-red-400 mt-1">{{ __('messages.shu_calculator.min_20_percent') }}</p>
                    </div>

                    <!-- Jasa Modal (Anggota) -->
                    <div class="bg-green-50 dark:bg-green-900/20 p-3 rounded-lg">
                        <label class="text-xs font-medium text-green-700 dark:text-green-400 uppercase">{{ __('messages.shu.member_jasa_modal') }}</label>
                        <div class="relative mt-1">
                            <input type="number" name="persen_jasa_modal" value="{{ old('persen_jasa_modal', $setting->persen_jasa_modal ?? 30) }}" 
                                   class="form-input pr-8 text-sm persen-input" min="0" max="100" step="0.5" required>
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm">%</span>
                        </div>
                        <p class="text-[10px] text-green-600 dark:text-green-400 mt-1">{{ __('messages.shu_calculator.jasa_modal_help') }}</p>
                    </div>

                    <!-- Jasa Usaha (Anggota) -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 p-3 rounded-lg">
                        <label class="text-xs font-medium text-blue-700 dark:text-blue-400 uppercase">{{ __('messages.shu.member_jasa_usaha') }}</label>
                        <div class="relative mt-1">
                            <input type="number" name="persen_jasa_usaha" value="{{ old('persen_jasa_usaha', $setting->persen_jasa_usaha ?? 25) }}" 
                                   class="form-input pr-8 text-sm persen-input" min="0" max="100" step="0.5" required>
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm">%</span>
                        </div>
                        <p class="text-[10px] text-blue-600 dark:text-blue-400 mt-1">{{ __('messages.shu_calculator.jasa_usaha_help') }}</p>
                    </div>

                    <!-- Dana Pengurus -->
                    <div class="bg-purple-50 dark:bg-purple-900/20 p-3 rounded-lg">
                        <label class="text-xs font-medium text-purple-700 dark:text-purple-400 uppercase">{{ __('messages.shu.management') }}</label>
                        <div class="relative mt-1">
                            <input type="number" name="persen_pengurus" value="{{ old('persen_pengurus', $setting->persen_pengurus ?? 5) }}" 
                                   class="form-input pr-8 text-sm persen-input" min="0" max="100" step="0.5" required>
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm">%</span>
                        </div>
                    </div>

                    <!-- Dana Karyawan -->
                    <div class="bg-amber-50 dark:bg-amber-900/20 p-3 rounded-lg">
                        <label class="text-xs font-medium text-amber-700 dark:text-amber-400 uppercase">{{ __('messages.shu.employee') }}</label>
                        <div class="relative mt-1">
                            <input type="number" name="persen_karyawan" value="{{ old('persen_karyawan', $setting->persen_karyawan ?? 5) }}" 
                                   class="form-input pr-8 text-sm persen-input" min="0" max="100" step="0.5" required>
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm">%</span>
                        </div>
                    </div>

                    <!-- Dana Pendidikan -->
                    <div class="bg-indigo-50 dark:bg-indigo-900/20 p-3 rounded-lg">
                        <label class="text-xs font-medium text-indigo-700 dark:text-indigo-400 uppercase">{{ __('messages.shu.others') }} - {{ __('messages.reports.education') }}</label>
                        <div class="relative mt-1">
                            <input type="number" name="persen_pendidikan" value="{{ old('persen_pendidikan', $setting->persen_pendidikan ?? 5) }}" 
                                   class="form-input pr-8 text-sm persen-input" min="0" max="100" step="0.5" required>
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm">%</span>
                        </div>
                    </div>

                    <!-- Dana Sosial -->
                    <div class="bg-pink-50 dark:bg-pink-900/20 p-3 rounded-lg">
                        <label class="text-xs font-medium text-pink-700 dark:text-pink-400 uppercase">{{ __('messages.shu.others') }} - {{ __('messages.reports.social') }}</label>
                        <div class="relative mt-1">
                            <input type="number" name="persen_sosial" value="{{ old('persen_sosial', $setting->persen_sosial ?? 2.5) }}" 
                                   class="form-input pr-8 text-sm persen-input" min="0" max="100" step="0.5" required>
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm">%</span>
                        </div>
                    </div>

                    <!-- Dana Pembangunan -->
                    <div class="bg-teal-50 dark:bg-teal-900/20 p-3 rounded-lg">
                        <label class="text-xs font-medium text-teal-700 dark:text-teal-400 uppercase">{{ __('messages.shu.others') }} - {{ __('messages.reports.construction') }}</label>
                        <div class="relative mt-1">
                            <input type="number" name="persen_pembangunan" value="{{ old('persen_pembangunan', $setting->persen_pembangunan ?? 2.5) }}" 
                                   class="form-input pr-8 text-sm persen-input" min="0" max="100" step="0.5" required>
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm">%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Preview Breakdown -->
            <div class="border-t border-gray-100 dark:border-gray-700 pt-6">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">{{ __('messages.shu_calculator.realtime_breakdown') }}</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                    <div class="bg-gray-50 dark:bg-gray-700/50 p-3 rounded-lg border border-gray-100 dark:border-gray-600">
                        <p class="text-[10px] text-gray-500 uppercase font-bold mb-1">Dana Cadangan</p>
                        <p class="font-mono font-bold text-gray-900 dark:text-white" id="realtime_pool_cadangan">Rp 0</p>
                    </div>
                    <div class="bg-green-50 dark:bg-green-900/30 p-3 rounded-lg border border-green-100 dark:border-green-800">
                        <p class="text-[10px] text-green-600 uppercase font-bold mb-1">Jasa Modal (Anggota)</p>
                        <p class="font-mono font-bold text-green-700 dark:text-green-400" id="realtime_pool_jasa_modal">Rp 0</p>
                    </div>
                    <div class="bg-blue-50 dark:bg-blue-900/30 p-3 rounded-lg border border-blue-100 dark:border-blue-800">
                        <p class="text-[10px] text-blue-600 uppercase font-bold mb-1">Jasa Usaha (Anggota)</p>
                        <p class="font-mono font-bold text-blue-700 dark:text-blue-400" id="realtime_pool_jasa_usaha">Rp 0</p>
                    </div>
                    <div class="bg-primary-50 dark:bg-primary-900/40 p-3 rounded-lg border border-primary-100 dark:border-primary-800">
                        <p class="text-[10px] text-primary-600 uppercase font-bold mb-1">{{ __('messages.shu_calculator.total_for_members') }}</p>
                        <p class="font-mono font-bold text-primary-700 dark:text-primary-400" id="realtime_pool_anggota">Rp 0</p>
                    </div>
                </div>
            </div>

            <!-- Formula Information -->
            <div class="bg-gray-50 dark:bg-gray-800/80 rounded-xl p-6 border border-gray-200 dark:border-gray-700 space-y-4">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    {{ __('messages.shu_calculator.formula_info_title') }}
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm leading-relaxed">
                    <div>
                        <h3 class="font-bold text-gray-700 dark:text-gray-300 mb-2">{{ __('messages.shu_calculator.formula_jasa_modal_title') }}</h3>
                        <p class="text-gray-600 dark:text-gray-400">{{ __('messages.shu_calculator.formula_jasa_modal_desc') }}</p>
                        <div class="mt-2 p-2 bg-white dark:bg-gray-700 rounded border border-gray-100 dark:border-gray-600 font-mono text-[11px]">
                            {!! __('messages.shu_tutorial.formula_jasa_modal') !!}
                        </div>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-700 dark:text-gray-300 mb-2">{{ __('messages.shu_calculator.formula_jasa_usaha_title') }}</h3>
                        <p class="text-gray-600 dark:text-gray-400">{{ __('messages.shu_calculator.formula_jasa_usaha_desc') }}</p>
                        <div class="mt-2 p-2 bg-white dark:bg-gray-700 rounded border border-gray-100 dark:border-gray-600 font-mono text-[11px]">
                            {!! __('messages.shu_tutorial.formula_jasa_usaha') !!}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                <div class="text-sm text-gray-500">
                    @if($setting && $setting->exists)
                    Status: <span class="badge badge-{{ $setting->status_color }}">{{ $setting->status_label }}</span>
                    @endif
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('shu.index') }}" class="btn-secondary">{{ __('messages.common.cancel') }}</a>
                    <button type="submit" class="btn-primary">
                        {{ __('messages.shu_calculator.save_config') }}
                    </button>
                    @if($setting && $setting->exists && $setting->total_shu_pool > 0)
                        @if($setting->status == 'distributed')
                            <button type="button" disabled class="btn-secondary flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                {{ __('messages.shu_calculator.already_posted') }}
                            </button>
                        @else
                            <button type="button" onclick="if(confirm('{{ __('messages.shu_calculator.post_confirm') }}')) document.getElementById('postAccountingForm').submit();" class="btn-warning flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                {{ __('messages.shu_calculator.post_to_accounting') }}
                            </button>
                        @endif
                        
                        <button type="button" onclick="document.getElementById('calculateForm').submit();" class="btn-success">
                            {{ __('messages.shu_calculator.calculate_shu') }}
                        </button>
                    @endif
                </div>
            </div>
        </form>

        @if($setting && $setting->exists)
        <form id="calculateForm" action="{{ route('shu.calculate') }}" method="POST" class="hidden">
            @csrf
            <input type="hidden" name="year" value="{{ $year }}">
        </form>
        <form id="postAccountingForm" action="{{ route('shu.post-accounting') }}" method="POST" class="hidden">
            @csrf
            <input type="hidden" name="year" value="{{ $year }}">
        </form>
        @endif
    </div>
</div>

@push('scripts')
<script>
    function syncWithPL() {
        const suggested = {{ $suggestedShu }};
        const totalPool = document.getElementById('totalPool');
        totalPool.value = Math.floor(suggested);
        
        // Trigger input event to update breakdown
        const event = new Event('input', { bubbles: true });
        totalPool.dispatchEvent(event);
        
        alert('Nilai SHU disinkronkan dengan Laba Rugi Berjalan: ' + new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(suggested));
    }

    function applyPreset(type) {
        if (!confirm('{{ __('messages.shu_calculator.preset_confirm') }}')) return;

        const presets = {
            'standard': {
                'persen_cadangan': 25,
                'persen_jasa_modal': 20,
                'persen_jasa_usaha': 20,
                'persen_pengurus': 10,
                'persen_karyawan': 5,
                'persen_pendidikan': 5,
                'persen_sosial': 5,
                'persen_pembangunan': 10
            },
            'flexible': {
                'persen_cadangan': 20,
                'persen_jasa_modal': 30,
                'persen_jasa_usaha': 30,
                'persen_pengurus': 7.5,
                'persen_karyawan': 5,
                'persen_pendidikan': 2.5,
                'persen_sosial': 2.5,
                'persen_pembangunan': 2.5
            }
        };

        const preset = presets[type];
        if (preset) {
            for (const [key, value] of Object.entries(preset)) {
                const input = document.querySelector(`input[name="${key}"]`);
                if (input) {
                    input.value = value;
                }
            }
            // Trigger update total manually
            const totalDisplay = document.getElementById('totalPersen');
            const totalContainer = document.getElementById('totalPersenDisplay');
            let total = 0;
            const inputs = document.querySelectorAll('.persen-input');
            inputs.forEach(i => total += parseFloat(i.value) || 0);
            totalDisplay.textContent = total.toFixed(1);
            
            if (Math.abs(total - 100) < 0.1) {
                totalContainer.classList.remove('text-red-600');
                totalContainer.classList.add('text-green-600');
            } else {
                totalContainer.classList.remove('text-green-600');
                totalContainer.classList.add('text-red-600');
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const totalPoolInput = document.getElementById('totalPool');
        const inputs = document.querySelectorAll('.persen-input');
        const totalDisplay = document.getElementById('totalPersen');
        const totalContainer = document.getElementById('totalPersenDisplay');

        // Real-time elements
        const poolCadanganElem = document.getElementById('realtime_pool_cadangan');
        const poolJasaModalElem = document.getElementById('realtime_pool_jasa_modal');
        const poolJasaUsahaElem = document.getElementById('realtime_pool_jasa_usaha');
        const poolAnggotaElem = document.getElementById('realtime_pool_anggota');

        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                maximumFractionDigits: 0
            }).format(number);
        }

        function updateTotal() {
            let total = 0;
            let totalPool = parseFloat(totalPoolInput.value) || 0;
            
            let pCadangan = 0;
            let pJasaModal = 0;
            let pJasaUsaha = 0;

            inputs.forEach(input => {
                let val = parseFloat(input.value) || 0;
                total += val;

                if (input.name === 'persen_cadangan') pCadangan = val;
                if (input.name === 'persen_jasa_modal') pJasaModal = val;
                if (input.name === 'persen_jasa_usaha') pJasaUsaha = val;
            });

            totalDisplay.textContent = total.toFixed(1);
            
            // Update real-time amounts
            poolCadanganElem.textContent = formatRupiah(totalPool * (pCadangan / 100));
            poolJasaModalElem.textContent = formatRupiah(totalPool * (pJasaModal / 100));
            poolJasaUsahaElem.textContent = formatRupiah(totalPool * (pJasaUsaha / 100));
            poolAnggotaElem.textContent = formatRupiah(totalPool * ((pJasaModal + pJasaUsaha) / 100));
            
            if (Math.abs(total - 100) < 0.1) {
                totalContainer.classList.remove('text-red-600');
                totalContainer.classList.add('text-green-600');
            } else {
                totalContainer.classList.remove('text-green-600');
                totalContainer.classList.add('text-red-600');
            }
        }

        inputs.forEach(input => {
            input.addEventListener('input', updateTotal);
        });
        
        totalPoolInput.addEventListener('input', updateTotal);

        updateTotal();
    });
</script>
@endpush
@endsection
