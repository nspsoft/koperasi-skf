@extends('layouts.app')

@section('title', 'Laporan Pinjaman')

@section('content')
    <!-- Page Header -->
    <div class="page-header">
         <div class="flex items-center gap-4">
            <a href="{{ route('reports.index') }}" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                <svg class="w-6 h-6 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h1 class="page-title">Laporan Pinjaman</h1>
                <p class="page-subtitle">Periode Pengajuan: {{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}</p>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="glass-card-solid p-6 mb-6">
        <form method="GET" action="{{ route('reports.loans') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
             <div>
                <label class="form-label">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ request('start_date', $startDate->format('Y-m-d')) }}" class="form-input">
            </div>
            <div>
                <label class="form-label">Tanggal Selesai</label>
                <input type="date" name="end_date" value="{{ request('end_date', $endDate->format('Y-m-d')) }}" class="form-input">
            </div>
            <div>
                 <label class="form-label">Status Pinjaman</label>
                <select name="status" class="form-input">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Lunas</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    <option value="defaulted" {{ request('status') == 'defaulted' ? 'selected' : '' }}>Macet</option>
                </select>
            </div>
            <button type="submit" class="btn-primary">
                Terapkan Filter
            </button>
            <a href="{{ route('reports.loans.pdf', request()->query()) }}" class="btn-secondary flex items-center justify-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                Export PDF
            </a>
            <a href="{{ route('reports.loans.excel', request()->query()) }}" class="btn-secondary flex items-center justify-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Export Excel
            </a>
        </form>
    </div>

     <!-- Portfolio Summary (All Time Active) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="glass-card p-6 border-l-4 border-primary-500">
            <p class="text-sm text-gray-500 mb-1">Total Pinjaman Aktif</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $totalActiveLoans }}</p>
            <p class="text-xs text-gray-400 mt-1">Kontrak berjalan saat ini</p>
        </div>
        <div class="glass-card p-6 border-l-4 border-amber-500">
             <p class="text-sm text-gray-500 mb-1">Total Outstanding (Sisa Tagihan)</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white">Rp {{ number_format($totalOutstanding, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-1">Uang yang belum kembali</p>
        </div>
        <div class="glass-card p-6 border-l-4 border-indigo-500">
             <p class="text-sm text-gray-500 mb-1">Total Disalurkan (Principal)</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white">Rp {{ number_format($totalDisbursed, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-1">Pada kontrak aktif saat ini</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Status Distribution Chart (Bars) -->
        <div class="lg:col-span-1 glass-card-solid p-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Distribusi Status (Periode Ini)</h3>
            <div class="space-y-4">
                @php $totalInPeriod = $byStatus->sum('total'); @endphp
                @foreach($byStatus as $stat)
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="capitalize">{{ $stat->status }}</span>
                        <span class="font-semibold">{{ $stat->total }}</span>
                    </div>
                    @php $percentage = ($totalInPeriod > 0) ? ($stat->total / $totalInPeriod) * 100 : 0; @endphp
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                         <div class="h-2 rounded-full 
                            @if($stat->status == 'active') bg-green-500 
                            @elseif($stat->status == 'pending') bg-yellow-500
                            @elseif($stat->status == 'rejected') bg-red-500
                            @elseif($stat->status == 'completed') bg-blue-500
                            @else bg-gray-500 @endif" 
                            style="width: {{ $percentage }}%"></div>
                    </div>
                </div>
                @endforeach
                
                @if($byStatus->isEmpty())
                <p class="text-sm text-gray-500 text-center italic py-4">Tidak ada data pengajuan di periode ini.</p>
                @endif
            </div>
        </div>

        <!-- Loans Table -->
         <div class="lg:col-span-2 glass-card-solid overflow-hidden">
            <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                 <h3 class="text-lg font-bold text-gray-900 dark:text-white">Daftar Pinjaman (Periode Ini)</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="table-modern">
                    <thead>
                        <tr>
                            <th>Tgl Pengajuan</th>
                            <th>No. Pinjaman</th>
                            <th>Anggota</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($loans as $loan)
                        <tr>
                            <td>{{ $loan->application_date->format('d/m/Y') }}</td>
                            <td class="font-mono text-xs">{{ $loan->loan_number }}</td>
                            <td>
                                <div class="flex flex-col">
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $loan->member->user->name }}</span>
                                </div>
                            </td>
                            <td class="font-medium">Rp {{ number_format($loan->amount, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge badge-{{ $loan->status_color }}">{{ $loan->status_label }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-gray-500">Tidak ada pengajuan pinjaman pada periode ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
