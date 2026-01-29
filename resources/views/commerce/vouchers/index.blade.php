@extends('layouts.app')

@section('title', 'Manajemen Voucher')

@section('content')
    <div class="page-header flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="page-title">Manajemen Voucher</h1>
            <p class="page-subtitle">Kelola kode promo dan diskon belanja</p>
        </div>
        <a href="{{ route('vouchers.create') }}" class="btn-primary inline-flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            Buat Voucher
        </a>
    </div>

    <div class="glass-card-solid overflow-hidden mt-8">
        <div class="table-scroll-container">
            <table class="table-modern w-full text-sm">
                <thead class="sticky top-0 z-10">
                    <tr class="bg-gray-50/50 dark:bg-gray-800/50">
                        <th class="px-6 py-4 text-left font-bold uppercase tracking-wider text-[10px]">Kode</th>
                        <th class="px-6 py-4 text-left font-bold uppercase tracking-wider text-[10px]">Potongan</th>
                        <th class="px-6 py-4 text-left font-bold uppercase tracking-wider text-[10px]">Min. Belanja</th>
                        <th class="px-6 py-4 text-left font-bold uppercase tracking-wider text-[10px]">Penggunaan</th>
                        <th class="px-6 py-4 text-left font-bold uppercase tracking-wider text-[10px]">Masa Berlaku</th>
                        <th class="px-6 py-4 text-left font-bold uppercase tracking-wider text-[10px]">Status</th>
                        <th class="px-6 py-4 text-center font-bold uppercase tracking-wider text-[10px]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($vouchers as $voucher)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                        <td class="px-6 py-4">
                            <span class="font-mono font-bold text-primary-600 bg-primary-50 dark:bg-primary-900/20 px-2 py-1 rounded border border-primary-100 dark:border-primary-800">
                                {{ $voucher->code }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-black text-gray-900 dark:text-white">
                                @if($voucher->type == 'fixed')
                                    Rp {{ number_format($voucher->value, 0, ',', '.') }}
                                @else
                                    {{ rtrim(rtrim($voucher->value, '0'), '.') }}%
                                @endif
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-500">
                            Rp {{ number_format($voucher->min_purchase, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="font-bold text-gray-700 dark:text-gray-300">{{ $voucher->used_count }} / {{ $voucher->usage_limit ?: '∞' }}</span>
                                <div class="w-20 h-1 bg-gray-100 dark:bg-gray-700 rounded-full mt-1 overflow-hidden">
                                    <div class="h-full bg-primary-500" style="width: {{ $voucher->usage_limit ? ($voucher->used_count / $voucher->usage_limit * 100) : 0 }}%"></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-[11px] leading-tight text-gray-500">
                            <div>Mulai: {{ $voucher->start_date ? $voucher->start_date->format('d M Y') : '-' }}</div>
                            <div class="mt-1">Akhir: {{ $voucher->end_date ? $voucher->end_date->format('d M Y') : '-' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($voucher->is_active)
                                <span class="badge badge-success !text-[10px]">Aktif</span>
                            @else
                                <span class="badge badge-danger !text-[10px]">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('vouchers.edit', $voucher) }}" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>
                                @can('delete-data')
                                <form action="{{ route('vouchers.destroy', $voucher) }}" method="POST" onsubmit="return confirm('Hapus voucher ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-400 font-medium">
                            <div class="text-4xl mb-3">🎫</div>
                            Belum ada voucher yang dibuat.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($vouchers->hasPages())
        <div class="p-4 border-t border-gray-100 dark:border-gray-700">
            {{ $vouchers->links() }}
        </div>
        @endif
    </div>
@endsection
