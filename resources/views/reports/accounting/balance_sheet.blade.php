@extends('layouts.app')

@section('title', 'Neraca')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Neraca (Balance Sheet)</h1>
            <p class="page-subtitle">Posisi Keuangan per {{ $date->translatedFormat('d F Y') }}</p>
        </div>
        <div>
            <a href="{{ route('reports.index') }}" class="btn btn-outline">Kembali ke Laporan</a>
        </div>
    </div>

    <!-- Filter Date -->
    <div class="glass-card p-6 mb-6">
        <form action="{{ route('reports.balance-sheet') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
            <div class="w-full md:w-1/3">
                <label class="form-label">Per Tanggal</label>
                <input type="date" name="date" class="form-input" value="{{ $date->format('Y-m-d') }}">
            </div>
            <div class="w-full md:w-auto">
                <button type="submit" class="btn btn-primary w-full">Tampilkan</button>
            </div>
            
            <div class="ml-auto text-right w-full md:w-auto mt-4 md:mt-0">
                @php
                    $isBalanced = abs($totalAssets - ($totalLiabilities + $totalEquity)) < 1;
                @endphp
                @if($isBalanced)
                    <div class="inline-flex items-center px-4 py-2 rounded-lg bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="font-bold">SEIMBANG (Balanced)</span>
                    </div>
                @else
                    <div class="inline-flex items-center px-4 py-2 rounded-lg bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300 animate-pulse">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="font-bold">TIDAK SEIMBANG</span>
                    </div>
                @endif
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- AKTIVA (ASSETS) -->
        <div class="glass-card p-6">
            <div class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-4">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">AKTIVA (Assets)</h2>
            </div>
            
            <div class="space-y-4">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-gray-500 border-b border-gray-100 dark:border-gray-800">
                            <th class="text-left py-2">Akun</th>
                            <th class="text-right py-2">Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($assets as $account)
                        <tr class="group hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="py-2 group-hover:pl-2 transition-all">
                                <span class="font-mono text-gray-500 mr-2">{{ $account->code }}</span>
                                {{ $account->name }}
                            </td>
                            <td class="text-right font-mono text-gray-700 dark:text-gray-300">
                                {{ number_format($account->current_balance, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-8 pt-4 border-t-2 border-gray-200 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
                <span class="font-bold text-lg">TOTAL AKTIVA</span>
                <span class="font-bold text-xl font-mono text-primary-600">Rp {{ number_format($totalAssets, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- PASIVA (LIABILITIES + EQUITY) -->
        <div class="glass-card p-6 flex flex-col h-full">
            <div class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-4">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">PASIVA (Liabilities & Equity)</h2>
            </div>

            <!-- KEWAJIBAN -->
            <div class="mb-8">
                <h3 class="font-bold text-gray-500 uppercase text-xs mb-2 tracking-wider">Kewajiban (Liabilities)</h3>
                <table class="w-full text-sm">
                    <tbody>
                        @foreach($liabilities as $account)
                        <tr class="group hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="py-2 group-hover:pl-2 transition-all">
                                <span class="font-mono text-gray-500 mr-2">{{ $account->code }}</span>
                                {{ $account->name }}
                            </td>
                            <td class="text-right font-mono text-gray-700 dark:text-gray-300">
                                {{ number_format($account->current_balance, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                        <tr class="bg-gray-50 dark:bg-gray-800/30 font-bold">
                            <td class="py-2 pl-2">Total Kewajiban</td>
                            <td class="text-right pr-2 py-2 font-mono">Rp {{ number_format($totalLiabilities, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- MODAL -->
            <div class="mb-4">
                <h3 class="font-bold text-gray-500 uppercase text-xs mb-2 tracking-wider">Modal (Equity)</h3>
                <table class="w-full text-sm">
                    <tbody>
                        @foreach($equities as $account)
                        <tr class="group hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="py-2 group-hover:pl-2 transition-all">
                                <span class="font-mono text-gray-500 mr-2">{{ $account->code }}</span>
                                {{ $account->name }}
                            </td>
                            <td class="text-right font-mono text-gray-700 dark:text-gray-300">
                                {{ number_format($account->current_balance, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                        
                        <!-- Laba Rugi Berjalan (SHU) -->
                        <tr class="bg-yellow-50 dark:bg-yellow-900/10">
                            <td class="py-2 pl-2 font-medium">Laba Rugi Tahun Berjalan (SHU)</td>
                            <td class="text-right pr-2 py-2 font-mono font-medium {{ $currentEarnings >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ number_format($currentEarnings, 0, ',', '.') }}
                            </td>
                        </tr>

                        <tr class="bg-gray-50 dark:bg-gray-800/30 font-bold">
                            <td class="py-2 pl-2">Total Modal</td>
                            <td class="text-right pr-2 py-2 font-mono">Rp {{ number_format($totalEquity, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-auto pt-4 border-t-2 border-gray-200 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
                <span class="font-bold text-lg">TOTAL PASIVA</span>
                <span class="font-bold text-xl font-mono text-primary-600">Rp {{ number_format($totalLiabilities + $totalEquity, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>
@endsection
