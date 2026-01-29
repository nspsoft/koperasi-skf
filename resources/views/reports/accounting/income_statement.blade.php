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
        <div class="glass-card p-6">
            <!-- PENDAPATAN -->
            <div class="mb-8">
                <div class="flex justify-between items-center bg-green-50 dark:bg-green-900/20 p-3 rounded-lg border border-green-100 dark:border-green-800 mb-4">
                    <h2 class="text-lg font-bold text-green-800 dark:text-green-300">PENDAPATAN (REVENUE)</h2>
                </div>
                
                <table class="w-full text-sm">
                    <tbody>
                        @forelse($revenues as $account)
                            @if($account->period_balance != 0)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                    <td class="py-2 pl-2">
                                        <span class="font-mono text-gray-500 mr-2">{{ $account->code }}</span>
                                        {{ $account->name }}
                                    </td>
                                    <td class="text-right pr-2 font-mono text-gray-700 dark:text-gray-300">
                                        Rp {{ number_format($account->period_balance, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr><td colspan="2" class="text-center italic text-gray-400">Tidak ada data pendapatan</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot class="border-t-2 border-green-200 dark:border-green-800">
                        <tr>
                            <td class="py-3 pl-2 font-bold text-lg">TOTAL PENDAPATAN</td>
                            <td class="text-right pr-2 py-3 font-bold text-lg font-mono text-green-600">
                                Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- BEBAN -->
            <div class="mb-8">
                <div class="flex justify-between items-center bg-red-50 dark:bg-red-900/20 p-3 rounded-lg border border-red-100 dark:border-red-800 mb-4">
                    <h2 class="text-lg font-bold text-red-800 dark:text-red-300">BEBAN (EXPENSES)</h2>
                </div>
                
                <table class="w-full text-sm">
                    <tbody>
                        @forelse($expenses as $account)
                            @if($account->period_balance != 0)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                    <td class="py-2 pl-2">
                                        <span class="font-mono text-gray-500 mr-2">{{ $account->code }}</span>
                                        {{ $account->name }}
                                    </td>
                                    <td class="text-right pr-2 font-mono text-gray-700 dark:text-gray-300">
                                        Rp {{ number_format($account->period_balance, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr><td colspan="2" class="text-center italic text-gray-400">Tidak ada data beban</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot class="border-t-2 border-red-200 dark:border-red-800">
                        <tr>
                            <td class="py-3 pl-2 font-bold text-lg">TOTAL BEBAN</td>
                            <td class="text-right pr-2 py-3 font-bold text-lg font-mono text-red-600">
                                Rp {{ number_format($totalExpense, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- NET INCOME -->
            <div class="border-t-4 border-double border-gray-300 dark:border-gray-600 pt-6">
                <div class="flex justify-between items-center bg-gray-100 dark:bg-gray-800/50 p-6 rounded-xl">
                    <div>
                        <h2 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-wider">Laba Bersih / SHU</h2>
                        <p class="text-gray-500 text-sm mt-1">Total Pendapatan - Total Beban</p>
                    </div>
                    <div class="text-right">
                        <span class="block text-3xl font-black font-mono {{ $netIncome >= 0 ? 'text-blue-600 dark:text-blue-400' : 'text-red-600 dark:text-red-400' }}">
                            Rp {{ number_format($netIncome, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
