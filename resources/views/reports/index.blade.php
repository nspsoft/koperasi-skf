@extends('layouts.app')

@section('title', __('messages.titles.reports'))

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ __('messages.reports_page.title') }}</h1>
            <p class="page-subtitle">{{ __('messages.reports_page.subtitle') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Members Report -->
        <a href="{{ route('reports.members') }}" class="group block">
            <div class="glass-card-solid p-6 h-full hover:border-primary-400 dark:hover:border-primary-600 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ __('messages.reports_page.members_title') }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.reports_page.members_desc') }}</p>
            </div>
        </a>

        <!-- Savings Report -->
        <a href="{{ route('reports.savings') }}" class="group block">
            <div class="glass-card-solid p-6 h-full hover:border-green-400 dark:hover:border-green-600 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ __('messages.reports_page.savings_title') }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.reports_page.savings_desc') }}</p>
            </div>
        </a>

        <!-- Loans Report -->
        <a href="{{ route('reports.loans') }}" class="group block">
            <div class="glass-card-solid p-6 h-full hover:border-amber-400 dark:hover:border-amber-600 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                <div class="w-12 h-12 bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 3.666V14h-6v-3.334H5V18h14v-7.334h-2.924M9 7V5a2 2 0 012-2h2a2 2 0 012 2v2m-6 0h6m-10 0h14"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ __('messages.reports_page.loans_title') }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.reports_page.loans_desc') }}</p>
            </div>
        </a>

        <!-- Shopping & Credit Report -->
        @if(auth()->user()->hasAdminAccess())
            <!-- Transactions Report (NEW) -->
            <a href="{{ route('reports.transactions') }}" class="group block">
                <div class="glass-card-solid p-6 h-full hover:border-indigo-400 dark:hover:border-indigo-600 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                    <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ __('messages.reports_page.transactions_title') }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.reports_page.transactions_desc') }}</p>
                </div>
            </a>

            <a href="{{ route('pos.credits') }}" class="group block">
                <div class="glass-card-solid p-6 h-full hover:border-purple-400 dark:hover:border-purple-600 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ __('messages.reports_page.credits_title') }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.reports_page.credits_desc') }}</p>
                </div>
            </a>
        @else
            <a href="{{ route('shop.history') }}" class="group block">
                <div class="glass-card-solid p-6 h-full hover:border-purple-400 dark:hover:border-purple-600 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ __('messages.reports_page.my_history_title') }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.reports_page.my_history_desc') }}</p>
                </div>
            </a>
        @endif
    </div>

    @if(auth()->user()->hasAdminAccess())
    <!-- Accounting Reports Section -->
    <div class="mt-12 mb-8 pt-8 border-t border-gray-200 dark:border-gray-700">
        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200">{{ __('messages.reports_page.accounting_title') }}</h2>
        <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">{{ __('messages.reports_page.accounting_subtitle') }}</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- General Ledger -->
        <a href="{{ route('reports.ledger') }}" class="group block">
            <div class="glass-card-solid p-6 h-full hover:border-teal-400 dark:hover:border-teal-600 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                <div class="w-12 h-12 bg-teal-100 dark:bg-teal-900/30 text-teal-600 dark:text-teal-400 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ __('messages.reports_page.ledger_title') }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.reports_page.ledger_desc') }}</p>
            </div>
        </a>

        <!-- Trial Balance -->
        <a href="{{ route('reports.trial-balance') }}" class="group block">
            <div class="glass-card-solid p-6 h-full hover:border-blue-400 dark:hover:border-blue-600 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ __('messages.reports_page.trial_balance_title') }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.reports_page.trial_balance_desc') }}</p>
            </div>
        </a>

        <!-- Balance Sheet -->
        <a href="{{ route('reports.balance-sheet') }}" class="group block">
            <div class="glass-card-solid p-6 h-full hover:border-cyan-400 dark:hover:border-cyan-600 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                <div class="w-12 h-12 bg-cyan-100 dark:bg-cyan-900/30 text-cyan-600 dark:text-cyan-400 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ __('messages.reports_page.balance_sheet_title') }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.reports_page.balance_sheet_desc') }}</p>
            </div>
        </a>

        <!-- Income Statement -->
        <a href="{{ route('reports.income-statement') }}" class="group block">
            <div class="glass-card-solid p-6 h-full hover:border-emerald-400 dark:hover:border-emerald-600 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ __('messages.reports_page.income_statement_title') }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.reports_page.income_statement_desc') }}</p>
            </div>
        </a>
    </div>
    @endif
@endsection
