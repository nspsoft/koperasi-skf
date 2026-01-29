@extends('layouts.app')

@section('title', 'Buku Besar')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Buku Besar (General Ledger)</h1>
            <p class="page-subtitle">Detail mutasi dan saldo per akun</p>
        </div>
        <div>
            <a href="{{ route('reports.index') }}" class="btn btn-outline">Kembali ke Laporan</a>
        </div>
    </div>

    <div class="glass-card p-6 mb-6">
        <form action="{{ route('reports.ledger') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
            <div class="w-full md:w-1/3">
                <label class="form-label">Pilih Akun</label>
                <select name="account_id" class="form-select select2" onchange="this.form.submit()">
                    <option value="">-- Pilih Akun --</option>
                    @foreach($accountsGrouped as $type => $group)
                        <optgroup label="{{ strtoupper($type == 'asset' ? 'Harta (Asset)' : ($type == 'liability' ? 'Kewajiban (Hutang)' : ($type == 'equity' ? 'Modal (Equity)' : ($type == 'revenue' ? 'Pendapatan' : 'Beban (Expense)')))) }}">
                            @foreach($group as $acc)
                                <option value="{{ $acc->id }}" {{ $accountId == $acc->id ? 'selected' : '' }}>
                                    {{ $acc->code }} - {{ $acc->name }}
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>
            <div class="w-full md:w-1/4">
                <label class="form-label">Dari Tanggal</label>
                <input type="date" name="start_date" class="form-input" value="{{ $startDate->format('Y-m-d') }}">
            </div>
            <div class="w-full md:w-1/4">
                <label class="form-label">Sampai Tanggal</label>
                <input type="date" name="end_date" class="form-input" value="{{ $endDate->format('Y-m-d') }}">
            </div>
            <div class="w-full md:w-auto">
                <button type="submit" class="btn btn-primary w-full">Filter</button>
            </div>
        </form>
    </div>

    @if($accountId)
        @php
            $selectedAccount = $accounts->find($accountId);
            $runningBalance = $openingBalance;
            $totalDebit = 0;
            $totalCredit = 0;
        @endphp

        <div class="glass-card p-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-lg font-bold">{{ $selectedAccount->code }} - {{ $selectedAccount->name }}</h3>
                    <p class="text-sm text-gray-500">Saldo Awal: <span class="font-mono font-medium {{ $openingBalance >= 0 ? 'text-green-600' : 'text-red-600' }}">Rp {{ number_format($openingBalance, 0, ',', '.') }}</span></p>
                </div>
                <div class="text-right">
                    <span class="badge {{ $selectedAccount->normal_balance == 'debit' ? 'badge-primary' : 'badge-warning' }}">
                        Normal: {{ ucfirst($selectedAccount->normal_balance) }}
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>No. Jurnal</th>
                            <th>Deskripsi</th>
                            <th>Debit</th>
                            <th>Kredit</th>
                            <th>Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Opening Balance Row -->
                        <tr class="bg-gray-50 dark:bg-gray-800/50 font-medium">
                            <td colspan="5">Saldo Awal</td>
                            <td class="font-mono">Rp {{ number_format($openingBalance, 0, ',', '.') }}</td>
                        </tr>

                        @forelse($lines as $line)
                            @php
                                $totalDebit += $line->debit;
                                $totalCredit += $line->credit;
                                
                                // Calculate Running Balance based on Normal Balance
                                if ($selectedAccount->normal_balance == 'debit') {
                                    $runningBalance += ($line->debit - $line->credit);
                                } else {
                                    $runningBalance += ($line->credit - $line->debit);
                                }
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                <td class="whitespace-nowrap">{{ \Carbon\Carbon::parse($line->journalEntry->transaction_date)->format('d/m/Y') }}</td>
                                <td class="whitespace-nowrap">
                                    <span class="text-xs font-mono text-gray-500">{{ $line->journalEntry->journal_number }}</span>
                                </td>
                                <td>
                                    <div class="font-medium text-gray-900 dark:text-gray-100">{{ $line->journalEntry->description }}</div>
                                    @if($line->description)
                                        <div class="text-xs text-gray-500">{{ $line->description }}</div>
                                    @endif
                                </td>
                                <td class="font-mono text-right text-green-600 dark:text-green-400">
                                    {{ $line->debit > 0 ? number_format($line->debit, 0, ',', '.') : '-' }}
                                </td>
                                <td class="font-mono text-right text-red-600 dark:text-red-400">
                                    {{ $line->credit > 0 ? number_format($line->credit, 0, ',', '.') : '-' }}
                                </td>
                                <td class="font-mono text-right font-bold text-gray-900 dark:text-white">
                                    {{ number_format($runningBalance, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-8 text-gray-500">Tidak ada mutasi pada periode ini</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-gray-50 dark:bg-gray-800 font-bold border-t-2 border-gray-200 dark:border-gray-700">
                        <tr>
                            <td colspan="3" class="text-right">Total Pergerakan Periode Ini</td>
                            <td class="font-mono text-right text-green-700 dark:text-green-400">Rp {{ number_format($totalDebit, 0, ',', '.') }}</td>
                            <td class="font-mono text-right text-red-700 dark:text-red-400">Rp {{ number_format($totalCredit, 0, ',', '.') }}</td>
                            <td class="font-mono text-right">Rp {{ number_format($runningBalance, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @else
        <div class="glass-card flex flex-col items-center justify-center p-12 text-center text-gray-500">
            <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">Silakan Pilih Akun</h3>
            <p>Pilih akun Buku Besar di atas untuk melihat detail mutasi.</p>
        </div>
    @endif
@endsection
