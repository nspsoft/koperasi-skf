@extends('layouts.app')

@section('title', __('messages.titles.withdrawals'))

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Permintaan Penarikan Simpanan</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Kelola permintaan penarikan simpanan anggota.</p>
        </div>
        @if(!auth()->user()->hasAdminAccess())
        <a href="{{ route('withdrawals.create') }}" class="btn-primary flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            Ajukan Penarikan
        </a>
        @endif
    </div>

    <!-- Filter (Admin only) -->
    @if(auth()->user()->hasAdminAccess())
    <div class="glass-card-solid p-4">
        <form method="GET" action="{{ route('withdrawals.index') }}" class="flex gap-4 items-end">
            <div class="flex-1">
                <label class="form-label">Filter Status</label>
                <select name="status" class="form-input">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>
            <button type="submit" class="btn-primary">Filter</button>
        </form>
    </div>
    @endif

    <!-- Table -->
    <div class="glass-card-solid overflow-hidden">
        <div class="table-scroll-container">
            <table class="table-modern">
                <thead class="sticky top-0 z-10">
                    <tr>
                        <th>Tanggal</th>
                        @if(auth()->user()->hasAdminAccess())
                        <th>Anggota</th>
                        @endif
                        <th>Jenis Simpanan</th>
                        <th>Jumlah</th>
                        <th>Bank Tujuan</th>
                        <th>Status</th>
                        @if(auth()->user()->hasAdminAccess())
                        <th>Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                    <tr>
                        <td class="whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-white">{{ $req->created_at->format('d/m/Y') }}</div>
                            <div class="text-xs text-gray-500">{{ $req->created_at->format('H:i') }}</div>
                        </td>
                        @if(auth()->user()->hasAdminAccess())
                        <td>
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-primary-600 dark:text-primary-400 font-semibold text-sm">
                                    {{ strtoupper(substr($req->member->user->name ?? '?', 0, 1)) }}
                                </div>
                                <div>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $req->member->user->name ?? '-' }}</span>
                                    <div class="text-xs text-gray-500">{{ $req->member->member_id ?? '' }}</div>
                                </div>
                            </div>
                        </td>
                        @endif
                        <td>
                            <span class="badge badge-info">{{ $req->saving_type_label }}</span>
                        </td>
                        <td class="font-semibold text-gray-900 dark:text-white">
                            Rp {{ number_format($req->amount, 0, ',', '.') }}
                        </td>
                        <td>
                            @if($req->bank_name)
                            <div class="text-sm">{{ $req->bank_name }}</div>
                            <div class="text-xs text-gray-500">{{ $req->bank_account_number }} - {{ $req->bank_account_name }}</div>
                            @else
                            <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-{{ $req->status_color }}">{{ $req->status_label }}</span>
                        </td>
                        @if(auth()->user()->hasAdminAccess())
                        <td>
                            <div class="flex gap-2">
                                @if($req->status === 'pending')
                                <form action="{{ route('withdrawals.approve', $req) }}" method="POST" onsubmit="return confirm('Setujui permintaan ini?');">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn-icon text-green-600 hover:bg-green-50 dark:hover:bg-green-900/30" title="Setujui">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </button>
                                </form>
                                <form action="{{ route('withdrawals.reject', $req) }}" method="POST" onsubmit="return confirm('Tolak permintaan ini?');">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn-icon text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30" title="Tolak">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </form>
                                @elseif($req->status === 'approved')
                                <form action="{{ route('withdrawals.complete', $req) }}" method="POST" onsubmit="return confirm('Tandai sebagai selesai? Dana akan dicatatkan sebagai penarikan.');">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn-primary-sm">Cairkan</button>
                                </form>
                                @endif
                            </div>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ auth()->user()->hasAdminAccess() ? 7 : 5 }}" class="text-center py-12">
                            <svg class="w-12 h-12 mx-auto text-gray-400 dark:text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <p class="text-gray-500 dark:text-gray-400 font-medium">Belum ada permintaan penarikan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($requests->hasPages())
        <div class="p-4 border-t border-gray-100 dark:border-gray-700">
            {{ $requests->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
