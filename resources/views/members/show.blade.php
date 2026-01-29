@extends('layouts.app')

@section('title', __('messages.titles.member_detail'))

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="flex items-center gap-4">
            <a href="{{ route('members.index') }}" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                <svg class="w-6 h-6 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div class="flex-1">
                <h1 class="page-title">Detail Anggota</h1>
                <p class="page-subtitle">{{ $member->user->name }} - {{ $member->member_id }}</p>
            </div>
            <div class="flex gap-2">
                @if(auth()->user()->isAdmin())
                <form action="{{ route('members.toggle-status', $member) }}" method="POST">
                    @csrf
                    <button type="submit" 
                            class="btn-secondary {{ $member->status === 'active' ? 'text-red-600 hover:bg-red-50' : 'text-green-600 hover:bg-green-50' }}">
                        {{ $member->status === 'active' ? 'Nonaktifkan' : 'Aktifkan' }}
                    </button>
                </form>
                <button onclick="window.open('{{ route('members.digital-card', $member) }}', 'MemberCardDigital', 'width=400,height=800')" 
                        class="btn-secondary text-blue-600">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    Digital
                </button>
                <button onclick="window.open('{{ route('members.card', $member) }}', 'MemberCard', 'width=800,height=600')" 
                        class="btn-secondary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0c0 .884-.896 1.763-2.24 1.763-1.344 0-2.24-.88-2.24-1.763 0-.88.9-1.763 2.24-1.763 1.344 0 2.24.88 2.24 1.763z"></path>
                    </svg>
                    Cetak Kartu
                </button>
                <a href="{{ route('members.edit', $member) }}" class="btn-primary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit Profil
                </a>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile Card -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Main Info -->
            <div class="glass-card-solid p-6 text-center">
                <div class="relative inline-block">
                    @if($member->photo)
                    <img src="{{ Storage::url($member->photo) }}" 
                         alt="{{ $member->user->name }}"
                         class="w-32 h-32 rounded-full object-cover border-4 border-white dark:border-gray-700 shadow-lg mx-auto mb-4">
                    @else
                    <div class="w-32 h-32 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white text-4xl font-bold border-4 border-white dark:border-gray-700 shadow-lg mx-auto mb-4">
                        {{ strtoupper(substr($member->user->name, 0, 1)) }}
                    </div>
                    @endif
                    <div class="absolute bottom-4 right-0">
                        @if($member->status === 'active')
                        <span class="w-6 h-6 rounded-full bg-green-500 border-2 border-white dark:border-gray-800 block" title="Aktif"></span>
                        @else
                        <span class="w-6 h-6 rounded-full bg-red-500 border-2 border-white dark:border-gray-800 block" title="Tidak Aktif"></span>
                        @endif
                    </div>
                </div>
                
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-1">{{ $member->user->name }}</h2>
                <p class="text-gray-500 dark:text-gray-400 text-sm mb-4">{{ $member->position ?? 'Anggota' }} - {{ $member->department ?? '-' }}</p>
                
                <div class="grid grid-cols-2 gap-4 border-t border-gray-100 dark:border-gray-700 pt-4">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Bergabung</p>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $member->join_date->format('d M Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Masa Kerja</p>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $member->join_date->diffForHumans(null, true) }}</p>
                    </div>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="glass-card-solid p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0c0 .884-.896 1.763-2.24 1.763-1.344 0-2.24-.88-2.24-1.763 0-.88.9-1.763 2.24-1.763 1.344 0 2.24.88 2.24 1.763z"></path>
                    </svg>
                    Informasi Kontak
                </h3>
                <ul class="space-y-4">
                    <li class="flex items-start gap-3 text-sm">
                        <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <span class="text-gray-600 dark:text-gray-300 break-all">{{ $member->user->email }}</span>
                    </li>
                    <li class="flex items-start gap-3 text-sm">
                        <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        <span class="text-gray-600 dark:text-gray-300">{{ $member->user->phone ?? '-' }}</span>
                    </li>
                    <li class="flex items-start gap-3 text-sm">
                        <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span class="text-gray-600 dark:text-gray-300">{{ $member->address ?? '-' }}</span>
                    </li>
                </ul>
            </div>
            
             <!-- Personal Info -->
             <div class="glass-card-solid p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Data Pribadi
                </h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">NIK Karyawan</p>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $member->employee_id ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Nomor KTP</p>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $member->id_card_number ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">TTL</p>
                        <p class="font-medium text-gray-900 dark:text-white">
                            {{ $member->birth_date ? $member->birth_date->format('d M Y') : '-' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Jenis Kelamin</p>
                        <p class="font-medium text-gray-900 dark:text-white">
                            @if($member->gender == 'male') Laki-laki @elseif($member->gender == 'female') Perempuan @else - @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Overview -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                <div class="glass-card p-4 bg-gradient-to-br from-green-500/10 to-emerald-500/10 border-green-200/20">
                    <p class="text-sm text-gray-600 dark:text-gray-300">Total Simpanan</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">Rp {{ number_format($stats['total_savings'], 0, ',', '.') }}</p>
                </div>
                <div class="glass-card p-4 bg-gradient-to-br from-blue-500/10 to-indigo-500/10 border-blue-200/20">
                    <p class="text-sm text-gray-600 dark:text-gray-300">Pinjaman Aktif</p>
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['active_loans'] }}</p>
                </div>
                <div class="glass-card p-4 bg-gradient-to-br from-amber-500/10 to-orange-500/10 border-amber-200/20">
                    <p class="text-sm text-gray-600 dark:text-gray-300">Sisa Pinjaman</p>
                    <p class="text-2xl font-bold text-amber-600 dark:text-amber-400">Rp {{ number_format($stats['total_loan_amount'], 0, ',', '.') }}</p>
                </div>
                <div class="glass-card p-4 bg-gradient-to-br from-purple-500/10 to-pink-500/10 border-purple-200/20">
                    <p class="text-sm text-gray-600 dark:text-gray-300">Kredit Belanja</p>
                    <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">Rp {{ number_format($stats['unpaid_credit'], 0, ',', '.') }}</p>
                </div>
            </div>

            <!-- Recent Savings -->
            <div class="glass-card-solid p-6">

                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Riwayat Simpanan Terakhir</h3>
                    <div class="flex gap-3">
                         <button onclick="window.open('{{ route('savings.print', $member) }}', 'BukuTabungan', 'width=800,height=600')" 
                                class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200 font-medium flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                            </svg>
                            Cetak Buku
                        </button>
                        <span class="text-sm text-gray-400">|</span>
                        <a href="{{ route('savings.index', ['search' => $member->member_id]) }}" class="text-sm text-primary-600 hover:text-primary-700 font-medium">Lihat Semua</a>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 uppercase tracking-wider text-xs">
                            <tr>
                                <th class="px-4 py-3 rounded-l-lg">Tanggal</th>
                                <th class="px-4 py-3">Jenis</th>
                                <th class="px-4 py-3">Keterangan</th>
                                <th class="px-4 py-3 text-right rounded-r-lg">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($recentSavings as $saving)
                            <tr>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $saving->transaction_date->format('d/m/Y') }}</td>
                                <td class="px-4 py-3">
                                    @if($saving->transaction_type === 'deposit')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        Setoran
                                    </span>
                                    @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                        Penarikan
                                    </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $saving->description }}</td>
                                <td class="px-4 py-3 text-right font-medium {{ $saving->transaction_type === 'deposit' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $saving->transaction_type === 'deposit' ? '+' : '-' }} Rp {{ number_format($saving->amount, 0, ',', '.') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                    Belum ada transaksi simpanan
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Active Loans -->
            <div class="glass-card-solid p-6">
                 <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Pinjaman Aktif</h3>
                    <span class="text-sm text-gray-400">Lihat Semua di Modul Pinjaman</span>
                </div>
                
                @forelse($activeLoans as $loan)
                <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-4 mb-4 hover:border-primary-300 dark:hover:border-primary-700 transition-colors">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-3">
                        <div>
                            <span class="text-xs font-semibold uppercase tracking-wider text-primary-600 dark:text-primary-400">{{ $loan->loan_type_label ?? $loan->loan_type }}</span>
                            <h4 class="text-lg font-bold text-gray-900 dark:text-white">Rp {{ number_format($loan->amount, 0, ',', '.') }}</h4>
                            <p class="text-sm text-gray-500">Tenor: {{ $loan->duration_months }} Bulan</p>
                        </div>
                        <div class="text-right">
                             <span class="badge badge-success mb-1 inline-block">{{ ucfirst($loan->status) }}</span>
                             <p class="text-sm text-gray-500">
                                Mulai: {{ $loan->disbursement_date ? $loan->disbursement_date->format('d M Y') : '-' }}
                             </p>
                        </div>
                    </div>
                    
                    <!-- Progress Bar -->
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5 mb-2">
                        @php
                            $totalBill = $loan->total_amount > 0 ? $loan->total_amount : $loan->amount;
                            $remaining = $loan->remaining_amount;
                            $paidAmount = $totalBill - $remaining;
                            $percentage = ($totalBill > 0) ? ($paidAmount / $totalBill) * 100 : 0;
                        @endphp
                        <div class="bg-primary-600 h-2.5 rounded-full" style="width: {{ $percentage }}%"></div>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400">
                        <span>Terbayar: Rp {{ number_format($paidAmount, 0, ',', '.') }} ({{ round($percentage) }}%)</span>
                        <span>Sisa: Rp {{ number_format($remaining, 0, ',', '.') }}</span>
                    </div>
                </div>
                @empty
                 <div class="text-center py-8 text-gray-500 dark:text-gray-400 border border-gray-100 dark:border-gray-800 rounded-xl">
                    <p>Tidak ada pinjaman aktif saat ini</p>
                </div>
                @endforelse
            </div>

            <!-- Recent Credits -->
            <div class="glass-card-solid p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Riwayat Kredit Mart</h3>
                    <div class="flex gap-3">
                        <button onclick="window.open('{{ route('members.transactions.print', $member) }}', 'HistoryBelanja', 'width=800,height=600')" 
                                class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200 font-medium flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                            </svg>
                            Cetak History
                        </button>
                        <span class="text-sm text-gray-400">|</span>
                        <a href="{{ route('pos.credits') }}" class="text-sm text-primary-600 font-medium">Lihat Semua</a>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-800 text-gray-500 text-xs">
                            <tr>
                                <th class="px-4 py-3 rounded-l-lg">Tanggal</th>
                                <th class="px-4 py-3">Invoice</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3 text-right rounded-r-lg">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($recentCredits as $credit)
                            <tr>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $credit->created_at->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 font-mono text-xs text-gray-600 dark:text-gray-400">{{ $credit->invoice_number }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold uppercase {{ $credit->status === 'completed' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                                        {{ $credit->status === 'completed' ? 'LUNAS' : 'KREDIT' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right font-medium text-gray-900 dark:text-white">
                                    Rp {{ number_format($credit->total_amount, 0, ',', '.') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                    Belum ada transaksi kredit mart
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
