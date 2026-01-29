@extends('layouts.app')

@section('title', __('messages.titles.shu_my'))

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('messages.shu_my.title') }}</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">{{ __('messages.shu_my.subtitle') }}</p>
    </div>

    <!-- Info Box -->
    <div class="bg-gradient-to-r from-primary-50 to-blue-50 dark:from-primary-900/30 dark:to-blue-900/30 border border-primary-100 dark:border-primary-800 rounded-xl p-5">
        <div class="flex gap-4">
            <div class="w-12 h-12 bg-primary-100 dark:bg-primary-900/50 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <h3 class="font-semibold text-gray-900 dark:text-white">{{ __('messages.shu_my.what_is_shu') }}</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    {{ __('messages.shu_my.shu_definition') }}
                </p>
            </div>
        </div>
    </div>

    <!-- SHU Cards -->
    <div class="space-y-4">
        @forelse($distributions as $dist)
        <div class="glass-card-solid p-6 hover:shadow-lg transition-shadow">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <!-- Year & Status -->
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-primary-500 to-blue-600 rounded-xl flex items-center justify-center text-white font-bold text-xl shadow-lg">
                        {{ $dist->period_year }}
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('messages.shu_my.shu_year') }} {{ $dist->period_year }}</h3>
                        <span class="badge badge-{{ $dist->status_color }}">{{ $dist->status_label }}</span>
                    </div>
                </div>

                <!-- Total SHU -->
                <div class="text-right">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">{{ __('messages.shu_my.your_total_shu') }}</p>
                    <p class="text-3xl font-bold text-primary-600 dark:text-primary-400">Rp {{ number_format($dist->total_shu, 0, ',', '.') }}</p>
                </div>
            </div>

            <!-- Breakdown -->
            <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-700">
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <div class="bg-blue-50 dark:bg-blue-900/30 rounded-lg p-3">
                        <p class="text-xs text-blue-600 dark:text-blue-400 uppercase tracking-wide">{{ __('messages.shu_my.from_savings') }}</p>
                        <p class="text-lg font-bold text-blue-700 dark:text-blue-300">Rp {{ number_format($dist->shu_savings, 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ __('messages.shu_my.contribution') }}: Rp {{ number_format($dist->total_savings, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-green-50 dark:bg-green-900/30 rounded-lg p-3">
                        <p class="text-xs text-green-600 dark:text-green-400 uppercase tracking-wide">{{ __('messages.shu_my.from_transactions') }}</p>
                        <p class="text-lg font-bold text-green-700 dark:text-green-300">Rp {{ number_format($dist->shu_transactions, 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ __('messages.shu_my.shopping') }}: Rp {{ number_format($dist->total_transactions, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-amber-50 dark:bg-amber-900/30 rounded-lg p-3">
                        <p class="text-xs text-amber-600 dark:text-amber-400 uppercase tracking-wide">Dari Jasa Pinjaman</p>
                        <p class="text-lg font-bold text-amber-700 dark:text-amber-300">Rp {{ number_format($dist->shu_jasa, 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-500 mt-1">Jasa: Rp {{ number_format($dist->total_loans, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            @if($dist->distributed_at)
            <div class="mt-4 flex items-center justify-between text-xs text-gray-500">
                <span>{{ __('messages.shu_my.distributed_at') }}: {{ $dist->distributed_at->format('d M Y H:i') }}</span>
                <a href="{{ route('shu.print-slip', $dist->id) }}" target="_blank" class="btn-secondary-sm flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    {{ __('messages.shu_my.print_slip') }}
                </a>
            </div>
            @else
            <div class="mt-4 flex justify-end">
                <a href="{{ route('shu.print-slip', $dist->id) }}" target="_blank" class="btn-secondary-sm flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    {{ __('messages.shu_my.print_slip') }}
                </a>
            </div>
            @endif
        </div>
        @empty
        <div class="glass-card-solid p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
            </svg>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ __('messages.shu_my.empty_title') }}</h3>
            <p class="text-gray-500 dark:text-gray-400">{!! __('messages.shu_my.empty_desc') !!}</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
