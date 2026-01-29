@extends('layouts.app')

@section('title', 'Lacak Pesanan #' . $transaction->invoice_number)

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('shop.history') }}" class="p-2 -ml-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors rounded-full hover:bg-gray-100 dark:hover:bg-gray-800">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Lacak Pesanan</h1>
                <p class="text-sm text-gray-500">Invoice: <span class="font-mono font-bold">{{ $transaction->invoice_number }}</span></p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left: Tracking Timeline -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Status Card -->
                <div class="glass-card-solid p-6 md:p-8">
                    <div class="mb-8">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Status Pesanan</h2>
                        <p class="text-sm text-gray-500">Pantau pergerakan pesanan Anda secara realtime.</p>
                    </div>

                    <!-- Timeline Logic -->
                    @php
                        $steps = [
                            ['title' => 'Pesanan Dibuat', 'desc' => 'Pesanan telah diterima sistem', 'icon' => '📝'],
                            ['title' => 'Pembayaran', 'desc' => 'Menunggu/Verifikasi pembayaran', 'icon' => '💳'],
                            ['title' => 'Diproses', 'desc' => 'Sedang disiapkan oleh petugas', 'icon' => '📦'],
                            ['title' => 'Selesai', 'desc' => 'Pesanan diambil/diantar', 'icon' => '✅'],
                        ];

                        // Logic: 0=Created, 1=Payment, 2=Processing, 3=Ready/Done
                        // We use $activeStep to denote which step is currently "In Progress".
                        // Previous steps are "Completed".
                        
                        $activeStep = 1; // Default: Payment is active (Pending)

                        if ($transaction->status == 'pending') {
                            $activeStep = 1;
                        } elseif ($transaction->status == 'paid' || $transaction->status == 'credit') {
                            $activeStep = 2; // Payment Done, Processing Pending/Start
                        } elseif ($transaction->status == 'processing') {
                            $activeStep = 2; // Processing Active
                        } elseif ($transaction->status == 'ready') {
                            $activeStep = 3; // Processing Done, Ready Active
                        } elseif ($transaction->status == 'completed' || $transaction->status == 'delivered') {
                            $activeStep = 4; // All Done
                        }
                    @endphp

                    <div class="relative">
                        <!-- Vertical Line -->
                        <div class="absolute left-6 top-4 bottom-4 w-0.5 bg-gray-200 dark:bg-gray-700"></div>

                        <div class="space-y-8 relative">
                            @foreach($steps as $index => $step)
                                @php
                                    $isCompleted = $index < $activeStep;
                                    $isCurrent = $index == $activeStep;
                                    
                                    // Status color logic
                                    $bgColor = $isCompleted ? 'bg-green-500' : ($isCurrent ? 'bg-white border-primary-500' : 'bg-gray-200 dark:bg-gray-700 border-gray-200 dark:border-gray-700');
                                    $iconColor = $isCompleted ? 'text-white' : ($isCurrent ? 'text-primary-600' : 'text-gray-400 opacity-0');
                                    // For icons inside
                                    
                                    // Text Logic
                                    $titleClass = $isCompleted ? 'text-gray-900 dark:text-white' : ($isCurrent ? 'text-primary-600 font-bold' : 'text-gray-400');
                                @endphp
                                <div class="flex gap-4">
                                    <!-- Icon -->
                                    <div class="relative z-10 w-12 h-12 rounded-full flex items-center justify-center border-4 {{ $bgColor }} shadow-sm transition-colors duration-500 {{ $isCurrent ? 'ring-4 ring-primary-100 dark:ring-primary-900/30' : '' }}">
                                        @if($isCompleted)
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        @else
                                            <span class="text-sm {{ $isCurrent ? 'text-primary-600 font-bold' : 'text-gray-500' }}">{{ $index + 1 }}</span>
                                        @endif
                                    </div>
                                    
                                    <!-- Content -->
                                    <div class="pt-2 flex-1">
                                        <h3 class="{{ $titleClass }} text-base font-semibold transition-colors duration-300">
                                            {{ $step['title'] }}
                                        </h3>
                                        <p class="text-sm text-gray-500 mt-1">{{ $step['desc'] }}</p>
                                        
                                        @if($index == 0)
                                            <p class="text-xs text-gray-400 mt-1">{{ $transaction->created_at->format('d M Y, H:i') }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                 <!-- Items List -->
                <div class="glass-card-solid p-6 md:p-8">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-6">Rincian Barang</h2>
                    <div class="space-y-4">
                        @foreach($transaction->items as $item)
                        <div class="flex gap-4 pb-4 border-b border-gray-100 dark:border-gray-800 last:border-0 last:pb-0">
                            <div class="w-16 h-16 rounded-xl bg-gray-50 dark:bg-gray-800 flex-shrink-0 overflow-hidden">
                                @php
                                    $imgUrl = $item->product && $item->product->image 
                                        ? Storage::url($item->product->image) 
                                        : 'https://placehold.co/150x150/6366f1/ffffff?text=' . urlencode($item->product ? $item->product->name : 'Item');
                                @endphp
                                <img src="{{ $imgUrl }}" alt="{{ $item->product->name ?? 'Produk Dihapus' }}" class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-bold text-gray-900 dark:text-white text-sm truncate">{{ $item->product->name ?? 'Produk tidak tersedia' }}</h4>
                                <p class="text-xs text-gray-500">{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                            </div>
                            <div class="text-sm font-bold text-gray-900 dark:text-white">
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Right: Info Details -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Payment Info -->
                <div class="glass-card-solid p-6">
                    <h3 class="font-bold text-gray-900 dark:text-white mb-4">Informasi Pembayaran</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Metode</span>
                            <span class="font-semibold uppercase">{{ str_replace('_', ' ', $transaction->payment_method) }}</span>
                        </div>
                         <div class="flex justify-between">
                            <span class="text-gray-500">Status</span>
                            <span class="font-bold {{ $transaction->status == 'completed' || $transaction->status == 'paid' ? 'text-green-600' : ($transaction->status == 'pending' ? 'text-yellow-600' : 'text-red-600') }}">
                                {{ strtoupper($transaction->status) }}
                            </span>
                        </div>
                        <div class="border-t border-gray-100 dark:border-gray-700 pt-3 flex justify-between">
                            <span class="font-bold">Total Bayar</span>
                            <span class="font-black text-primary-600">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Notes / Delivery Info -->
                <div class="glass-card-solid p-6">
                    <h3 class="font-bold text-gray-900 dark:text-white mb-4">Catatan / Pengiriman</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed bg-gray-50 dark:bg-gray-800 p-3 rounded-lg border border-gray-100 dark:border-gray-700">
                        {{ $transaction->notes ?: '-' }}
                    </p>
                </div>
                
                @if($transaction->status == 'pending')
                <div class="p-4 bg-yellow-50 dark:bg-yellow-900/10 border border-yellow-200 dark:border-yellow-800 rounded-xl text-yellow-800 dark:text-yellow-300 text-xs">
                    <p class="font-bold mb-1">⏳ Menunggu Konfirmasi</p>
                    <p>Mohon selesaikan pembayaran atau tunggu admin memverifikasi pesanan Anda.</p>
                </div>
                @endif
                
                <!-- Help Button -->
                <a href="https://wa.me/6281234567890?text=Halo%20Admin,%20saya%20mau%20tanya%20tentang%20pesanan%20{{ $transaction->invoice_number }}" target="_blank" class="btn-secondary w-full flex items-center justify-center gap-2">
                    <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                    Hubungi Admin
                </a>
            </div>
        </div>
    </div>
@endsection
