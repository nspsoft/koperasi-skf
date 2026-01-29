@extends('layouts.app')

@section('title', 'Laporan Anggota')

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
                <h1 class="page-title">Laporan Anggota</h1>
                <p class="page-subtitle">Periode: {{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}</p>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="glass-card-solid p-6 mb-6">
        <form method="GET" action="{{ route('reports.members') }}" class="flex flex-col md:flex-row items-end gap-4">
            <div class="flex-1 w-full">
                <label class="form-label">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ request('start_date', $startDate->format('Y-m-d')) }}" class="form-input">
            </div>
            <div class="flex-1 w-full">
                <label class="form-label">Tanggal Selesai</label>
                <input type="date" name="end_date" value="{{ request('end_date', $endDate->format('Y-m-d')) }}" class="form-input">
            </div>
            <button type="submit" class="btn-primary w-full md:w-auto">
                Terapkan Filter
            </button>
            <a href="{{ route('reports.members.pdf', request()->query()) }}" class="btn-secondary w-full md:w-auto flex items-center justify-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
                Export PDF
            </a>
            <a href="{{ route('reports.members.excel', request()->query()) }}" class="btn-secondary w-full md:w-auto flex items-center justify-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Export Excel
            </a>
        </form>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="glass-card p-6 border-l-4 border-blue-500">
            <p class="text-sm text-gray-500 mb-1">Total Anggota</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $totalMembers }}</p>
        </div>
        <div class="glass-card p-6 border-l-4 border-green-500">
             <p class="text-sm text-gray-500 mb-1">Anggota Aktif</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $activeMembers }}</p>
        </div>
        <div class="glass-card p-6 border-l-4 border-purple-500">
             <p class="text-sm text-gray-500 mb-1">Anggota Baru (Periode Ini)</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $newMembers }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Gender Distribution -->
        <div class="glass-card-solid p-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Distribusi Gender</h3>
            <div class="space-y-4">
                @foreach($byGender as $stat)
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="capitalize">{{ $stat->gender == 'male' ? 'Laki-laki' : 'Perempuan' }}</span>
                        <span class="font-semibold">{{ $stat->total }} Orang</span>
                    </div>
                    @php $percentage = ($activeMembers > 0) ? ($stat->total / $activeMembers) * 100 : 0; @endphp
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Department Distribution -->
        <div class="glass-card-solid p-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Distribusi Departemen</h3>
            <div class="space-y-4 max-h-60 overflow-y-auto pr-2 scrollbar-thin">
                @foreach($byDepartment as $stat)
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span>{{ $stat->department ?? 'Tidak Diketahui' }}</span>
                        <span class="font-semibold">{{ $stat->total }}</span>
                    </div>
                    @php $percentage = ($activeMembers > 0) ? ($stat->total / $activeMembers) * 100 : 0; @endphp
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div class="bg-indigo-500 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Recent Members Table -->
    <div class="glass-card-solid overflow-hidden">
        <div class="p-4 border-b border-gray-100 dark:border-gray-700">
             <h3 class="text-lg font-bold text-gray-900 dark:text-white">Anggota Baru Bergabung</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>ID Anggota</th>
                        <th>Nama</th>
                        <th>Departemen</th>
                        <th>Tanggal Bergabung</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($members as $member)
                    <tr>
                         <td class="font-mono text-xs">{{ $member->member_id }}</td>
                        <td class="font-medium">{{ $member->user->name }}</td>
                        <td>{{ $member->department }}</td>
                        <td>{{ $member->join_date->format('d/m/Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-4 text-gray-500">Tidak ada anggota baru pada periode ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
