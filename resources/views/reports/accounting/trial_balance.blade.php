@extends('layouts.app')

@section('title', 'Neraca Saldo')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Neraca Saldo (Trial Balance)</h1>
            <p class="page-subtitle">Posisi saldo seluruh akun per {{ $endDate->translatedFormat('d F Y') }}</p>
        </div>
        <div>
            <a href="{{ route('reports.index') }}" class="btn btn-outline">Kembali ke Laporan</a>
        </div>
    </div>

    <!-- Filter Date -->
    <div class="glass-card p-6 mb-6">
        <form action="{{ route('reports.trial-balance') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
            <div class="w-full md:w-1/3">
                <label class="form-label">Per Tanggal</label>
                <input type="date" name="end_date" class="form-input" value="{{ $endDate->format('Y-m-d') }}">
            </div>
            <div class="w-full md:w-auto">
                <button type="submit" class="btn btn-primary w-full">Tampilkan</button>
            </div>

            <div class="ml-auto text-right w-full md:w-auto mt-4 md:mt-0">
                @php
                    $isBalanced = abs($totalDebit - $totalCredit) < 1;
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

    <div class="glass-card p-6">
        <div class="overflow-x-auto">
            <table class="table w-full">
                <thead>
                    <tr>
                        <th class="w-24">Kode</th>
                        <th>Akun</th>
                        <th class="text-right w-48">Debit</th>
                        <th class="text-right w-48">Kredit</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($accounts as $account)
                        @if($account->debit_balance != 0 || $account->credit_balance != 0)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="font-mono text-gray-500">{{ $account->code }}</td>
                            <td class="font-medium text-gray-900 dark:text-white">{{ $account->name }}</td>
                            <td class="text-right font-mono text-gray-700 dark:text-gray-300">
                                @if($account->debit_balance != 0)
                                    Rp {{ number_format($account->debit_balance, 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-right font-mono text-gray-700 dark:text-gray-300">
                                @if($account->credit_balance != 0)
                                    Rp {{ number_format($account->credit_balance, 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        @endif
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 dark:bg-gray-800 font-bold border-t-2 border-gray-200 dark:border-gray-700">
                    <tr>
                        <td colspan="2" class="text-right py-4 text-lg">TOTAL</td>
                        <td class="text-right py-4 text-lg font-mono text-green-700 dark:text-green-400">
                            Rp {{ number_format($totalDebit, 0, ',', '.') }}
                        </td>
                        <td class="text-right py-4 text-lg font-mono text-blue-700 dark:text-blue-400">
                            Rp {{ number_format($totalCredit, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection
