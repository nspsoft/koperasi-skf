@extends('layouts.app')

@section('title', __('messages.shu_tutorial.title_page'))

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('shu.index') }}" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
            <svg class="w-6 h-6 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('messages.shu_tutorial.title') }}</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">{{ __('messages.shu_tutorial.subtitle') }}</p>
        </div>
    </div>

    <!-- Section 1: Dasar Perhitungan SHU -->
    <div class="glass-card-solid p-6 space-y-4">
        <h2 class="text-lg font-bold text-primary-700 dark:text-primary-400 flex items-center gap-2">
            <span class="bg-primary-100 dark:bg-primary-900/40 text-primary-600 w-8 h-8 flex items-center justify-center rounded-full text-sm font-bold">1</span>
            {{ __('messages.shu_tutorial.section1_title') }}
        </h2>
        <div class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed space-y-3">
            <p>{!! __('messages.shu_tutorial.section1_desc') !!}</p>
            
            <div class="bg-blue-50 dark:bg-blue-900/30 border border-blue-100 dark:border-blue-800 rounded-lg p-4">
                <p class="font-semibold text-blue-800 dark:text-blue-300 mb-2">{{ __('messages.shu_tutorial.legal_basis') }}</p>
                <ul class="list-disc list-inside text-xs text-blue-700 dark:text-blue-400 space-y-1">
                    <li><strong>{{ __('messages.shu_tutorial.reserve_fund') }}</strong> {{ __('messages.shu_tutorial.reserve_fund_desc') }}</li>
                    <li><strong>{{ __('messages.shu_tutorial.jasa_modal') }}</strong> {{ __('messages.shu_tutorial.jasa_modal_desc') }}</li>
                    <li><strong>{{ __('messages.shu_tutorial.jasa_usaha') }}</strong> {{ __('messages.shu_tutorial.jasa_usaha_desc') }}</li>
                    <li><strong>{{ __('messages.shu_tutorial.other_funds') }}</strong> {{ __('messages.shu_tutorial.other_funds_desc') }}</li>
                </ul>
            </div>

            <p>{!! __('messages.shu_tutorial.percentage_info') !!}</p>
        </div>
    </div>

    <!-- Section 2: Formula Perhitungan Rincian SHU per Anggota -->
    <div class="glass-card-solid p-6 space-y-4">
        <h2 class="text-lg font-bold text-green-700 dark:text-green-400 flex items-center gap-2">
            <span class="bg-green-100 dark:bg-green-900/40 text-green-600 w-8 h-8 flex items-center justify-center rounded-full text-sm font-bold">2</span>
            {{ __('messages.shu_tutorial.section2_title') }}
        </h2>
        <div class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed space-y-4">
            <p>{!! __('messages.shu_tutorial.section2_desc') !!}</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Jasa Modal -->
                <div class="bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-100 dark:border-emerald-800 rounded-lg p-4">
                    <h4 class="font-bold text-emerald-700 dark:text-emerald-400 mb-2">{{ __('messages.shu_tutorial.shu_jasa_modal_title') }}</h4>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mb-2">{{ __('messages.shu_tutorial.shu_jasa_modal_desc') }}</p>
                    <div class="bg-white dark:bg-gray-800 rounded p-3 font-mono text-xs border border-emerald-200 dark:border-emerald-700">
                        {!! __('messages.shu_tutorial.formula_jasa_modal') !!}
                    </div>
                    <p class="text-[10px] text-gray-500 mt-2">{!! __('messages.shu_tutorial.example_jasa_modal') !!}</p>
                </div>

                <!-- Jasa Usaha -->
                <div class="bg-sky-50 dark:bg-sky-900/30 border border-sky-100 dark:border-sky-800 rounded-lg p-4">
                    <h4 class="font-bold text-sky-700 dark:text-sky-400 mb-2">{{ __('messages.shu_tutorial.shu_jasa_usaha_title') }}</h4>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mb-2">{{ __('messages.shu_tutorial.shu_jasa_usaha_desc') }}</p>
                    <div class="bg-white dark:bg-gray-800 rounded p-3 font-mono text-xs border border-sky-200 dark:border-sky-700">
                        {!! __('messages.shu_tutorial.formula_jasa_usaha') !!}
                    </div>
                    <p class="text-[10px] text-gray-500 mt-2">{!! __('messages.shu_tutorial.example_jasa_usaha') !!}</p>
                </div>
            </div>

            <div class="bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-700 rounded-lg p-4">
                <p class="font-bold text-yellow-800 dark:text-yellow-300 mb-1">{{ __('messages.shu_tutorial.total_shu_member_title') }}</p>
                <div class="font-mono text-sm text-center py-2 bg-white dark:bg-gray-800 rounded border border-yellow-300 dark:border-yellow-600">
                    {!! __('messages.shu_tutorial.total_shu_formula') !!}
                </div>
            </div>
        </div>
    </div>

    <!-- Section 3: Cara Menggunakan Fitur SHU -->
    <div class="glass-card-solid p-6 space-y-4">
        <h2 class="text-lg font-bold text-purple-700 dark:text-purple-400 flex items-center gap-2">
            <span class="bg-purple-100 dark:bg-purple-900/40 text-purple-600 w-8 h-8 flex items-center justify-center rounded-full text-sm font-bold">3</span>
            {{ __('messages.shu_tutorial.section3_title') }}
        </h2>
        <div class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
            <ol class="list-decimal list-inside space-y-3">
                <li>{!! __('messages.shu_tutorial.step1') !!}</li>
                <li>{!! __('messages.shu_tutorial.step2') !!}</li>
                <li>{!! __('messages.shu_tutorial.step3') !!}</li>
                <li>{!! __('messages.shu_tutorial.step4') !!}</li>
                <li>{!! __('messages.shu_tutorial.step5') !!}</li>
                <li>{!! __('messages.shu_tutorial.step6') !!}</li>
                <li>{!! __('messages.shu_tutorial.step7') !!}</li>
                <li>{!! __('messages.shu_tutorial.step8') !!}</li>
            </ol>
        </div>
    </div>

    <!-- Section 4: Cara Memastikan Fitur Sudah Berfungsi -->
    <div class="glass-card-solid p-6 space-y-4">
        <h2 class="text-lg font-bold text-orange-700 dark:text-orange-400 flex items-center gap-2">
            <span class="bg-orange-100 dark:bg-orange-900/40 text-orange-600 w-8 h-8 flex items-center justify-center rounded-full text-sm font-bold">4</span>
            {{ __('messages.shu_tutorial.section4_title') }}
        </h2>
        <div class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed space-y-3">
            <p>{{ __('messages.shu_tutorial.section4_desc') }}</p>

            <div class="overflow-x-auto">
                <table class="w-full text-xs border border-gray-200 dark:border-gray-700 rounded-lg">
                    <thead class="bg-gray-100 dark:bg-gray-800">
                        <tr>
                            <th class="p-3 text-left font-semibold">{{ __('messages.shu_tutorial.table_col1') }}</th>
                            <th class="p-3 text-left font-semibold">{{ __('messages.shu_tutorial.table_col2') }}</th>
                            <th class="p-3 text-center font-semibold">{{ __('messages.shu_tutorial.table_col3') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <tr>
                            <td class="p-3">{!! __('messages.shu_tutorial.verify1') !!}</td>
                            <td class="p-3">{{ __('messages.shu_tutorial.verify1_desc') }}</td>
                            <td class="p-3 text-center"><span class="text-green-500 font-bold">✓</span></td>
                        </tr>
                        <tr>
                            <td class="p-3">{!! __('messages.shu_tutorial.verify2') !!}</td>
                            <td class="p-3">{{ __('messages.shu_tutorial.verify2_desc') }}</td>
                            <td class="p-3 text-center"><span class="text-green-500 font-bold">✓</span></td>
                        </tr>
                        <tr>
                            <td class="p-3">{!! __('messages.shu_tutorial.verify3') !!}</td>
                            <td class="p-3">{{ __('messages.shu_tutorial.verify3_desc') }}</td>
                            <td class="p-3 text-center"><span class="text-green-500 font-bold">✓</span></td>
                        </tr>
                        <tr>
                            <td class="p-3">{!! __('messages.shu_tutorial.verify4') !!}</td>
                            <td class="p-3">{{ __('messages.shu_tutorial.verify4_desc') }}</td>
                            <td class="p-3 text-center"><span class="text-green-500 font-bold">✓</span></td>
                        </tr>
                        <tr>
                            <td class="p-3">{!! __('messages.shu_tutorial.verify5') !!}</td>
                            <td class="p-3">{{ __('messages.shu_tutorial.verify5_desc') }}</td>
                            <td class="p-3 text-center"><span class="text-green-500 font-bold">✓</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 rounded-lg p-4 mt-4">
                <p class="text-green-800 dark:text-green-300 font-semibold">{{ __('messages.shu_tutorial.success_note') }}</p>
            </div>
        </div>
    </div>

    <!-- Back Button -->
    <div class="flex justify-center gap-4 pb-8">
        <a href="{{ route('shu.simulator') }}" class="btn-secondary flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ __('messages.shu_tutorial.try_simulator') }}
        </a>
        <a href="{{ route('shu.calculator') }}" class="btn-primary flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
            {{ __('messages.shu_tutorial.open_calculator') }}
        </a>
    </div>
</div>
@endsection
