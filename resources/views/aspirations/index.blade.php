@extends('layouts.app')

@section('title', 'Riwayat Aspirasi')

@section('content')
<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Riwayat Aspirasi</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Daftar masukan dan permintaan barang yang telah Anda kirimkan.</p>
    </div>
    <a href="{{ route('aspirations.create') }}" class="inline-flex items-center px-6 py-3 bg-emerald-600 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest shadow-lg shadow-emerald-500/30 hover:bg-emerald-700 active:bg-emerald-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150 transform hover:-translate-y-0.5">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Kirim Aspirasi Baru
    </a>
</div>

<div class="space-y-4">
    @forelse($aspirations as $aspiration)
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden transform transition hover:scale-[1.01] duration-200">
        <div class="p-6 flex items-start gap-4">
            <div @class([
                'w-12 h-12 rounded-xl flex items-center justify-center shrink-0',
                'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400' => $aspiration->type === 'item_request',
                'bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400' => $aspiration->type === 'system_eval',
            ])>
                @if($aspiration->type === 'item_request')
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                @else
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                @endif
            </div>

            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between mb-1">
                    <h3 class="font-bold text-gray-900 dark:text-white">
                        {{ $aspiration->type === 'item_request' ? 'Permintaan Barang' : 'Evaluasi Sistem' }}
                    </h3>
                    <span class="text-[10px] font-medium text-gray-400 uppercase tracking-wider">{{ $aspiration->created_at->diffForHumans() }}</span>
                </div>

                @if($aspiration->type === 'item_request')
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Mengusulkan <span class="font-bold text-gray-900 dark:text-white">{{ $aspiration->data['item_name'] }}</span> 
                        di kategori <span class="badge badge-outline">{{ $aspiration->data['category'] }}</span> 
                        dengan frekuensi beli <span class="font-semibold text-emerald-600">{{ $aspiration->data['frequency'] }}</span>.
                    </p>
                @else
                    <div class="space-y-2">
                        <div class="flex flex-wrap gap-2">
                            <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded text-[10px] font-bold text-gray-600 dark:text-gray-300">
                                Sistem: {{ ucfirst($aspiration->data['system_choice']) }}
                            </span>
                            <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded text-[10px] font-bold text-gray-600 dark:text-gray-300">
                                Pembayaran: {{ ucfirst($aspiration->data['payment_choice']) }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 italic">"{{ $aspiration->data['reason'] }}"</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="bg-white dark:bg-gray-800 rounded-3xl p-12 text-center border-2 border-dashed border-gray-200 dark:border-gray-700">
        <div class="w-20 h-20 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400 transition-transform duration-500 hover:rotate-12">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
        </div>
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Belum Ada Aspirasi</h3>
        <p class="text-gray-500 mb-6">Suara Anda sangat berharga bagi kami. Ayo mulai berikan aspirasi pertama Anda!</p>
        <a href="{{ route('aspirations.create') }}" class="inline-flex items-center px-6 py-3 bg-emerald-600 text-white font-bold rounded-xl hover:bg-emerald-700 transition">
            Kirim Aspirasi Sekarang
        </a>
    </div>
    @endforelse
</div>
@endsection
