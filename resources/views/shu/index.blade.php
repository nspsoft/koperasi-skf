@extends('layouts.app')

@section('title', __('messages.titles.shu'))

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('messages.shu.title') }}</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">{{ __('messages.shu.subtitle') }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('shu.tutorial') }}" class="btn-secondary flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                {{ __('messages.shu.guide') }}
            </a>
            <a href="{{ route('shu.simulator') }}" class="btn-secondary flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ __('messages.shu.simulator') }}
            </a>
            <a href="{{ route('shu.calculator') }}" class="btn-primary flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                {{ __('messages.shu.config') }}
            </a>
        </div>
    </div>

    <!-- Year Filter -->
    <div class="glass-card-solid p-4">
        <div class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="form-label">{{ __('messages.shu.select_year') }}</label>
                <select class="form-input" onchange="window.location.href='{{ route('shu.index') }}?year='+this.value">
                    @if($availableYears->isEmpty())
                    <option value="{{ date('Y') }}">{{ date('Y') }} {{ __('messages.shu.no_data_year') }}</option>
                    @else
                    @foreach($availableYears as $y)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                    @endif
                </select>
            </div>
            @if($setting && $distributions->isNotEmpty())
            <a href="{{ route('shu.print-report', ['year' => $year]) }}" target="_blank" class="btn-secondary flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                PDF
            </a>
            <a href="{{ route('shu.export', ['year' => $year]) }}" class="btn-secondary flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Excel
            </a>
            @endif
            @if($setting && $setting->status === 'calculated')
            <form action="{{ route('shu.distribute') }}" method="POST" class="inline">
                @csrf
                <input type="hidden" name="year" value="{{ $year }}">
                <button type="submit" class="btn-success" onclick="return confirm('{{ __('messages.shu.confirm_distribute', ['year' => $year]) }}')">
                    {{ __('messages.shu.mark_as_distributed') }}
                </button>
            </form>
            @endif
        </div>
    </div>

    @if($setting)
    <!-- Summary Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-3">
        <div class="glass-card p-4 col-span-2">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('messages.shu.total_pool', ['year' => $year]) }}</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">Rp {{ number_format($setting->total_shu_pool, 0, ',', '.') }}</p>
            <span class="badge badge-{{ $setting->status_color }} mt-2">{{ $setting->status_label }}</span>
        </div>
        <div class="glass-card p-4 border-l-4 border-red-500">
            <p class="text-xs text-gray-500 uppercase">{{ __('messages.shu.reserve_fund') }}</p>
            <p class="text-lg font-bold text-red-600 dark:text-red-400">{{ $setting->persen_cadangan }}%</p>
            <p class="text-xs text-gray-500">Rp {{ number_format($setting->pool_cadangan, 0, ',', '.') }}</p>
        </div>
        <div class="glass-card p-4 border-l-4 border-green-500">
            <p class="text-xs text-gray-500 uppercase">{{ __('messages.shu.member_jasa_modal') }}</p>
            <p class="text-lg font-bold text-green-600 dark:text-green-400">{{ $setting->persen_jasa_modal }}%</p>
            <p class="text-xs text-gray-500">Rp {{ number_format($setting->pool_jasa_modal, 0, ',', '.') }}</p>
        </div>
        <div class="glass-card p-4 border-l-4 border-blue-500">
            <p class="text-xs text-gray-500 uppercase">{{ __('messages.shu.member_jasa_usaha') }}</p>
            <p class="text-lg font-bold text-blue-600 dark:text-blue-400">{{ $setting->persen_jasa_usaha }}%</p>
            <p class="text-xs text-gray-500">Rp {{ number_format($setting->pool_jasa_usaha, 0, ',', '.') }}</p>
        </div>
        <div class="glass-card p-4 border-l-4 border-purple-500">
            <p class="text-xs text-gray-500 uppercase">{{ __('messages.shu.management') }}</p>
            <p class="text-lg font-bold text-purple-600 dark:text-purple-400">{{ $setting->persen_pengurus }}%</p>
            <p class="text-xs text-gray-500">Rp {{ number_format($setting->pool_pengurus, 0, ',', '.') }}</p>
        </div>
        <div class="glass-card p-4 border-l-4 border-amber-500">
            <p class="text-xs text-gray-500 uppercase">{{ __('messages.shu.employee') }}</p>
            <p class="text-lg font-bold text-amber-600 dark:text-amber-400">{{ $setting->persen_karyawan }}%</p>
            <p class="text-xs text-gray-500">Rp {{ number_format($setting->pool_karyawan, 0, ',', '.') }}</p>
        </div>
        <div class="glass-card p-4 border-l-4 border-pink-500">
            <p class="text-xs text-gray-500 uppercase">{{ __('messages.shu.others') }}</p>
            <p class="text-lg font-bold text-pink-600 dark:text-pink-400">{{ $setting->persen_pendidikan + $setting->persen_sosial + $setting->persen_pembangunan }}%</p>
            <p class="text-xs text-gray-500">Rp {{ number_format($setting->pool_pendidikan + $setting->pool_sosial + $setting->pool_pembangunan, 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- Member Highlight -->
    <div class="bg-gradient-to-r from-green-50 to-blue-50 dark:from-green-900/30 dark:to-blue-900/30 border border-green-100 dark:border-green-800 rounded-xl p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('messages.shu.member_pool_title') }}</p>
                <p class="text-3xl font-bold text-primary-600 dark:text-primary-400">Rp {{ number_format($setting->pool_anggota, 0, ',', '.') }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('messages.shu.recipient_count') }}</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $distributions->count() }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Distribution Table -->
    <div class="glass-card-solid overflow-hidden">
        <div class="p-4 border-b border-gray-100 dark:border-gray-700">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('messages.shu.detail_title', ['year' => $year]) }}</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('messages.shu.member') }}</th>
                        <th class="text-right">{{ __('messages.shu.saving_balance') }}</th>
                        <th class="text-right">{{ __('messages.shu.total_transactions') }}</th>
                        <th class="text-right">{{ __('messages.shu.shu_jasa_modal') }}</th>
                        <th class="text-right">{{ __('messages.shu.shu_jasa_usaha') }}</th>
                        <th class="text-right">{{ __('messages.shu.total_shu') }}</th>
                        <th>{{ __('messages.shu.status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($distributions as $index => $dist)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-primary-600 dark:text-primary-400 font-semibold text-sm">
                                    {{ strtoupper(substr($dist->member->user->name ?? '?', 0, 1)) }}
                                </div>
                                <div>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $dist->member->user->name ?? '-' }}</span>
                                    <div class="text-xs text-gray-500">{{ $dist->member->member_id ?? '' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="text-right text-gray-600 dark:text-gray-400">Rp {{ number_format($dist->total_savings, 0, ',', '.') }}</td>
                        <td class="text-right text-gray-600 dark:text-gray-400">Rp {{ number_format($dist->total_transactions, 0, ',', '.') }}</td>
                        <td class="text-right text-green-600 dark:text-green-400">Rp {{ number_format($dist->shu_savings, 0, ',', '.') }}</td>
                        <td class="text-right text-blue-600 dark:text-blue-400">Rp {{ number_format($dist->shu_transactions, 0, ',', '.') }}</td>
                        <td class="text-right font-bold text-gray-900 dark:text-white">Rp {{ number_format($dist->total_shu, 0, ',', '.') }}</td>
                        <td>
                            <div class="flex items-center gap-2">
                                <span class="badge badge-{{ $dist->status_color }}">{{ $dist->status_label }}</span>
                                <a href="{{ route('shu.print-slip', $dist->id) }}" target="_blank" class="btn-icon text-gray-500 hover:text-primary-600" title="{{ __('messages.shu.print_slip') }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-12">
                            <svg class="w-12 h-12 mx-auto text-gray-400 dark:text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                            <p class="text-gray-500 dark:text-gray-400 font-medium">{{ __('messages.shu.no_calculation', ['year' => $year]) }}</p>
                            <a href="{{ route('shu.calculator', ['year' => $year]) }}" class="text-primary-600 hover:underline mt-2 inline-block">{{ __('messages.shu.config_calculate') }}</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($distributions->isNotEmpty())
                <tfoot class="bg-gray-50 dark:bg-gray-700/50">
                    <tr class="font-bold">
                        <td colspan="4" class="text-right">TOTAL</td>
                        <td class="text-right text-green-600 dark:text-green-400">Rp {{ number_format($distributions->sum('shu_savings'), 0, ',', '.') }}</td>
                        <td class="text-right text-blue-600 dark:text-blue-400">Rp {{ number_format($distributions->sum('shu_transactions'), 0, ',', '.') }}</td>
                        <td class="text-right text-gray-900 dark:text-white">Rp {{ number_format($distributions->sum('total_shu'), 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection
