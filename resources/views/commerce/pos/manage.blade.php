@extends('layouts.app')

@section('title', 'Kelola Pesanan #' . $transaction->invoice_number)

@section('content')
<div class="max-w-md mx-auto px-4 py-8">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('pos.scan') }}" class="p-2 -ml-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <h1 class="text-xl font-bold text-gray-900 dark:text-white">Kelola Pesanan</h1>
    </div>

    <!-- Status Card -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 mb-6">
        <div class="text-center mb-4">
            <span class="font-mono text-sm text-gray-500 block mb-1">{{ $transaction->invoice_number }}</span>
            <div class="flex justify-center items-center gap-2">
                @if($transaction->status == 'completed' || $transaction->status == 'delivered')
                    <span class="badge badge-success px-3 py-1 text-sm">Selesai</span>
                @elseif($transaction->status == 'processing' || $transaction->status == 'ready' || $transaction->status == 'paid')
                    <span class="badge badge-primary px-3 py-1 text-sm">Diproses</span>
                @elseif($transaction->status == 'credit')
                    <span class="badge badge-warning px-3 py-1 text-sm">Kredit</span>
                @elseif($transaction->status == 'pending')
                    <span class="badge badge-warning px-3 py-1 text-sm">Menunggu</span>
                @else
                    <span class="badge badge-danger px-3 py-1 text-sm">{{ $transaction->status }}</span>
                @endif
            </div>
        </div>

        <!-- Action Buttons -->
        @if($transaction->status != 'completed' && $transaction->status != 'cancelled' && $transaction->status != 'delivered')
        <form action="{{ route('pos.orders.process', $transaction) }}" method="POST" class="space-y-3">
            @csrf
            
            @if($transaction->status == 'pending')
                <input type="hidden" name="status" value="paid">
                <button type="submit" class="btn-primary w-full justify-center bg-blue-600 hover:bg-blue-700 h-12 text-lg">
                    Konfirmasi Bayar
                </button>
            @endif

            @if($transaction->status == 'paid' || $transaction->status == 'credit')
                <input type="hidden" name="status" value="processing">
                <button type="submit" class="btn-primary w-full justify-center bg-orange-500 hover:bg-orange-600 h-12 text-lg">
                    Mulai Persiapan
                </button>
            @endif

            @if($transaction->status == 'processing')
                <input type="hidden" name="status" value="ready">
                <button type="submit" class="btn-primary w-full justify-center bg-purple-500 hover:bg-purple-600 h-12 text-lg">
                    Barang Siap
                </button>
            @endif

            @if($transaction->status == 'ready')
                <input type="hidden" name="status" value="{{ $transaction->payment_method === 'kredit' ? 'delivered' : 'completed' }}">
                <button type="submit" class="btn-primary w-full justify-center bg-green-600 hover:bg-green-700 h-12 text-lg">
                    {{ $transaction->payment_method === 'kredit' ? 'Pesanan Diterima (Kredit)' : 'Selesaikan Pesanan' }}
                </button>
            @endif
        </form>
        @else
            <div class="text-center p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl text-gray-500">
                Pesanan sudah selesai atau dibatalkan.
            </div>
        @endif
    </div>

    <!-- Items List -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 font-bold">
            Detail Barang
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @foreach($transaction->items as $item)
            <div class="p-4 flex gap-4">
                <div class="flex-1">
                    <h4 class="font-bold text-gray-900 dark:text-white">{{ $item->product->name }}</h4>
                    <p class="text-sm text-gray-500">{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                </div>
                <div class="text-right font-medium">
                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                </div>
            </div>
            @endforeach
        </div>
        <div class="p-4 bg-gray-50 dark:bg-gray-700/30 flex justify-between font-bold">
            <span>Total</span>
            <span>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
        </div>
    </div>
</div>
@endsection
