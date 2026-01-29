@extends('layouts.app')

@section('title', 'Keranjang Belanja')

@section('content')
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                <span class="p-2 bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </span>
                Keranjang Belanja
            </h1>
            <a href="{{ route('shop.index') }}" class="text-primary-600 font-semibold text-sm hover:underline flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Lanjut Belanja
            </a>
        </div>

        @if(session('cart') && count(session('cart')) > 0)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Cart Items List (2 cols) -->
            <div class="lg:col-span-2 space-y-4">
                @php $total = 0 @endphp
                @foreach(session('cart') as $id => $details)
                @php $total += $details['price'] * $details['quantity'] @endphp
                <div class="glass-card-solid p-4 sm:p-6 group relative overflow-hidden transition-all hover:border-primary-200 dark:hover:border-primary-800">
                    <div class="flex gap-6 items-start">
                        <!-- Product Image -->
                        <div class="w-24 h-24 sm:w-32 sm:h-32 flex-shrink-0 bg-gray-50 dark:bg-gray-800 rounded-xl overflow-hidden relative">
                            <!-- Corner Accents -->
                            <div class="absolute top-1 left-1 w-3 h-3 border-t-2 border-l-2 border-primary-400/50 z-10"></div>
                            <div class="absolute bottom-1 right-1 w-3 h-3 border-b-2 border-r-2 border-primary-400/50 z-10"></div>
                            
                            @php
                                $imgUrl = isset($details['image']) && $details['image'] 
                                    ? Storage::url($details['image']) 
                                    : 'https://placehold.co/300x300/6366f1/ffffff?text=' . urlencode($details['name']);
                            @endphp
                            <img src="{{ $imgUrl }}" alt="{{ $details['name'] }}" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                 onerror="this.onerror=null; this.src='https://placehold.co/300x300/6366f1/ffffff?text={{ urlencode($details['name']) }}'">
                        </div>

                        <!-- Product Details -->
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-start gap-4">
                                <div>
                                    <h3 class="font-bold text-gray-900 dark:text-white text-lg leading-tight mb-1 truncate pr-4 max-w-[200px] sm:max-w-none">
                                        {{ $details['name'] }}
                                    </h3>
                                    @if(isset($details['is_preorder']) && $details['is_preorder'])
                                    <div class="mb-2">
                                        <span class="text-[10px] uppercase font-bold text-purple-600 bg-purple-100 px-2 py-0.5 rounded">Pre-Order</span>
                                        @if(isset($details['preorder_eta']) && $details['preorder_eta'])
                                            <span class="text-[10px] text-gray-500 ml-1">Est: {{ $details['preorder_eta'] }}</span>
                                        @endif
                                    </div>
                                    @endif
                                    <p class="text-primary-600 font-bold text-sm bg-primary-50 dark:bg-primary-900/30 inline-block px-2 py-0.5 rounded text-xs mb-3">
                                        Rp {{ number_format($details['price'], 0, ',', '.') }}
                                    </p>
                                </div>
                                <div class="font-bold text-gray-900 dark:text-white text-lg text-right hidden sm:block">
                                    Rp {{ number_format($details['price'] * $details['quantity'], 0, ',', '.') }}
                                </div>
                            </div>

                            <div class="flex items-center justify-between flex-wrap gap-4 mt-2">
                                <!-- Quantity Control -->
                                <div class="flex items-center border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden bg-white dark:bg-gray-800">
                                    <button onclick="updateCart({{ $id }}, {{ $details['quantity'] - 1 }})" 
                                            class="w-8 h-8 flex items-center justify-center text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                        -
                                    </button>
                                    <input type="number" value="{{ $details['quantity'] }}" 
                                           class="w-12 h-8 text-center border-0 p-0 text-sm font-semibold bg-transparent focus:ring-0" 
                                           readonly>
                                    <button onclick="updateCart({{ $id }}, {{ $details['quantity'] + 1 }})" 
                                            class="w-8 h-8 flex items-center justify-center text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                        +
                                    </button>
                                </div>

                                <!-- Actions -->
                                <button onclick="removeCart({{ $id }})" 
                                        class="text-gray-400 hover:text-red-500 text-sm flex items-center gap-1.5 transition-colors group/delete">
                                    <span class="p-1.5 rounded-full group-hover/delete:bg-red-50 dark:group-hover/delete:bg-red-900/20 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </span>
                                    Hapus
                                </button>
                                
                                <div class="font-bold text-gray-900 dark:text-white text-lg w-full text-right sm:hidden">
                                    Rp {{ number_format($details['price'] * $details['quantity'], 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Order Summary (1 col - Sticky) -->
            <div class="lg:col-span-1">
                <div class="glass-card-solid p-6 lg:sticky lg:top-24 space-y-6">
                    <h3 class="font-bold text-gray-900 dark:text-white text-lg pb-4 border-b border-gray-100 dark:border-gray-700">
                        Ringkasan Belanja
                    </h3>
                    
                    <!-- Voucher Section -->
                    <div class="space-y-3">
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Voucher Diskon</label>
                        <form action="{{ route('shop.voucher.apply') }}" method="POST" class="relative">
                            @csrf
                            <input type="text" name="code" placeholder="Masukkan kode voucher" 
                                   class="form-input w-full pr-24 uppercase font-mono text-sm py-2.5" 
                                   {{ session('voucher') ? 'disabled' : '' }}
                                   value="{{ session('voucher.code') }}">
                            
                            @if(session('voucher'))
                                <button type="button" 
                                        onclick="window.location.reload();" {{-- Simplifikasi remove voucher --}}
                                        class="absolute right-1 top-1 bottom-1 px-3 bg-red-50 text-red-500 rounded text-xs font-bold hover:bg-red-100 transition-colors border border-red-100 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    HAPUS
                                </button>
                            @else
                                <button type="submit" class="absolute right-1 top-1 bottom-1 px-4 bg-gray-900 dark:bg-gray-700 text-white rounded text-xs font-bold hover:bg-gray-700 dark:hover:bg-gray-600 transition-colors">
                                    PAKAI
                                </button>
                            @endif
                        </form>
                        
                        @if(session('voucher'))
                            <div class="flex items-center gap-2 p-3 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 rounded-lg text-sm border border-green-100 dark:border-green-800">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span class="font-medium">Voucher <strong>{{ session('voucher.code') }}</strong> diterapkan!</span>
                            </div>
                        @elseif(session('error'))
                             <div class="flex items-center gap-2 p-3 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-lg text-sm border border-red-100 dark:border-red-800">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span class="font-medium">{{ session('error') }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Summary Details -->
                    <div class="space-y-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                        <div class="flex justify-between text-gray-600 dark:text-gray-400">
                            <span>Subtotal ({{ count(session('cart')) }} item)</span>
                            <span class="font-medium">Rp {{ number_format($total + ($discount ?? 0), 0, ',', '.') }}</span>
                        </div>
                        
                        @if(isset($discount) && $discount > 0)
                        <div class="flex justify-between text-green-600 font-medium bg-green-50 dark:bg-green-900/10 p-2 rounded -mx-2">
                            <span>Total Diskon</span>
                            <span>-Rp {{ number_format($discount, 0, ',', '.') }}</span>
                        </div>
                        @endif
                        
                        <div class="flex justify-between items-center pt-4 border-t-2 border-dashed border-gray-200 dark:border-gray-700">
                            <span class="font-bold text-gray-900 dark:text-white text-lg">Total</span>
                            <span class="font-black text-2xl text-primary-600">Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Checkout Button -->
                    <a href="{{ route('shop.checkout') }}" class="btn-primary w-full py-3.5 text-center block text-sm font-bold shadow-lg shadow-primary-500/30 group">
                        <span class="flex items-center justify-center gap-2 group-hover:scale-[1.02] transition-transform">
                            Lanjut ke Pembayaran
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        </span>
                    </a>
                    
                    <div class="text-center">
                        <p class="text-xs text-gray-400">Transaksi aman & terenkripsi</p>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="glass-card-solid p-12 text-center max-w-lg mx-auto mt-12">
            <div class="w-24 h-24 bg-gray-50 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
            </div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Keranjang Belanja Kosong</h2>
            <p class="text-gray-500 mb-8">Wah, keranjang belanjaanmu masih kosong nih. Yuk isi dengan barang-barang kebutuhanmu!</p>
            <a href="{{ route('shop.index') }}" class="btn-primary inline-flex items-center gap-2 px-8 py-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Mulai Belanja
            </a>
        </div>
        @endif
    </div>

    @push('scripts')
    <script>
        function updateCart(id, qty) {
            if (qty < 1) return;
            
            fetch('{{ route("shop.update") }}', {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ id: id, quantity: qty })
            }).then(response => {
                window.location.reload();
            });
        }

        function removeCart(id) {
            if(confirm('Apakah Anda yakin ingin menghapus produk ini dari keranjang?')) {
                fetch('{{ route("shop.remove") }}', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ id: id })
                }).then(response => {
                    window.location.reload();
                });
            }
        }
    </script>
    @endpush
@endsection
