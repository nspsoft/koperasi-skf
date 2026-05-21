@extends('layouts.app')

@section('title', 'Detail Retur Konsinyasi')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <a href="{{ route('consignment.returns.index') }}" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                Detail Retur Konsinyasi
            </h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1 ml-8">{{ $return->transaction_number }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('consignment.returns.print', $return) }}" target="_blank" class="btn-primary flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Bukti Retur
            </a>
        </div>
    </div>

    {{-- Info Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        {{-- Mitra Info --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm">
            <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">Informasi Mitra / Supplier</h3>
            <div class="flex items-center gap-4">
                @php
                    $name = $return->consignor->name ?? 'Unknown';
                    $initials = strtoupper(substr($name, 0, 1));
                    $bgColors = ['bg-red-500', 'bg-blue-500', 'bg-green-500', 'bg-yellow-500', 'bg-purple-500', 'bg-pink-500', 'bg-indigo-500'];
                    $bgColor = $bgColors[crc32($name) % count($bgColors)];
                @endphp
                <div class="w-14 h-14 rounded-xl {{ $bgColor }} flex items-center justify-center text-white font-bold text-xl">
                    {{ $initials }}
                </div>
                <div>
                    <h4 class="text-lg font-bold text-gray-900 dark:text-white">{{ $name }}</h4>
                    <p class="text-gray-500 dark:text-gray-400">
                        @if($return->consignor_type === 'member')
                            Anggota ({{ $return->consignor->member_id ?? '-' }})
                        @else
                            Supplier Eksternal
                        @endif
                    </p>
                    @if($return->consignor->phone)
                        <p class="text-sm text-gray-500 mt-1 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            {{ $return->consignor->phone }}
                        </p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Transaction Info --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm">
            <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">Detail Transaksi</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-500 dark:text-gray-400">No. Transaksi</span>
                    <span class="font-medium text-gray-900 dark:text-white">{{ $return->transaction_number }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 dark:text-gray-400">Tanggal Retur</span>
                    <span class="font-medium text-gray-900 dark:text-white">{{ $return->return_date->format('d/m/Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 dark:text-gray-400">Waktu Dibuat</span>
                    <span class="font-medium text-gray-900 dark:text-white">{{ $return->created_at->format('H:i WIB') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 dark:text-gray-400">Dibuat Oleh</span>
                    <span class="font-medium text-gray-900 dark:text-white">{{ $return->creator->name ?? '-' }}</span>
                </div>
                @if($return->notes)
                <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                    <span class="block text-gray-500 dark:text-gray-400 mb-1">Catatan:</span>
                    <p class="text-sm text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-900/50 p-2 rounded">{{ $return->notes }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Items List --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-800">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white">Daftar Barang Dikembalikan</h2>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 dark:bg-blue-900/50 text-blue-800 dark:text-blue-200">
                Total: {{ $return->total_items }} pcs
            </span>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kode / Produk</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Qty Retur</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($return->items as $index => $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $item->product->name }}</div>
                                <div class="text-xs text-gray-500">{{ $item->product->code ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="font-bold text-red-600 dark:text-red-400">{{ $item->quantity }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                {{ $item->notes ?? '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
