@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('shop.cart') }}" class="p-2 -ml-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors rounded-full hover:bg-gray-100 dark:hover:bg-gray-800">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Konfirmasi Pembayaran</h1>
        </div>

        <form action="{{ route('shop.process') }}" method="POST" x-data="{ selectedMethod: '', deliveryMethod: 'pickup' }" class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-12">
            @csrf
            
            <!-- LEFT COLUMN: Payment & Delivery Methods -->
            <div class="lg:col-span-2 space-y-8">
                
                <!-- 1. Metode Pengambilan -->
                <div class="glass-card-solid p-6 md:p-8">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                        <span class="w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900/30 text-primary-600 flex items-center justify-center text-sm font-bold">1</span>
                        Metode Pengambilan
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Ambil Sendiri -->
                        <label class="relative flex items-start gap-4 p-4 rounded-xl border-2 cursor-pointer transition-all duration-200"
                               :class="deliveryMethod === 'pickup' ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/10 shadow-sm ring-1 ring-primary-500' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600'">
                            <input type="radio" name="delivery_method" value="pickup" x-model="deliveryMethod" class="hidden">
                            <div class="mt-1 w-5 h-5 rounded-full border border-gray-300 flex items-center justify-center" :class="deliveryMethod === 'pickup' ? 'border-primary-500' : ''">
                                <div class="w-2.5 h-2.5 rounded-full bg-primary-500 transform scale-0 transition-transform" :class="deliveryMethod === 'pickup' ? 'scale-100' : ''"></div>
                            </div>
                            <div class="flex-1">
                                <span class="block font-bold text-gray-900 dark:text-white">🏪 Ambil Sendiri</span>
                                <span class="text-sm text-gray-500 dark:text-gray-400 mt-1 block">Pesanan diambil di Koperasi Mart</span>
                            </div>
                        </label>

                        <!-- Antar ke Meja -->
                        <label class="relative flex items-start gap-4 p-4 rounded-xl border-2 cursor-pointer transition-all duration-200"
                               :class="deliveryMethod === 'delivery' ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/10 shadow-sm ring-1 ring-primary-500' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600'">
                            <input type="radio" name="delivery_method" value="delivery" x-model="deliveryMethod" class="hidden">
                            <div class="mt-1 w-5 h-5 rounded-full border border-gray-300 flex items-center justify-center" :class="deliveryMethod === 'delivery' ? 'border-primary-500' : ''">
                                <div class="w-2.5 h-2.5 rounded-full bg-primary-500 transform scale-0 transition-transform" :class="deliveryMethod === 'delivery' ? 'scale-100' : ''"></div>
                            </div>
                            <div class="flex-1">
                                <span class="block font-bold text-gray-900 dark:text-white">🚚 Antar ke Meja</span>
                                <span class="text-sm text-gray-500 dark:text-gray-400 mt-1 block">Diantar kurir internal pabrik</span>
                            </div>
                        </label>
                    </div>

                    <!-- Input Lokasi Tambahan -->
                    <div x-show="deliveryMethod === 'delivery'" 
                         x-transition:enter="transition ease-out duration-300" 
                         x-transition:enter-start="opacity-0 -translate-y-2" 
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="mt-6 pl-4 border-l-4 border-primary-200 ml-4">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Detail Lokasi Pengantaran <span class="text-red-500">*</span></label>
                        <input type="text" name="delivery_location" class="form-input w-full" placeholder="Contoh: Gedung A, Lantai 2, Ruang HRD, Meja B-12">
                    </div>
                </div>
                
                <!-- 2. Metode Pembayaran -->
                <div class="glass-card-solid p-6 md:p-8">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                        <span class="w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900/30 text-primary-600 flex items-center justify-center text-sm font-bold">2</span>
                        Metode Pembayaran
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Saldo Sukarela -->
                        <label class="group relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition-all duration-200 hover:shadow-md"
                               :class="selectedMethod === 'saldo_sukarela' ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/10 ring-1 ring-primary-500 scale-[1.01]' : 'border-gray-200 dark:border-gray-700 hover:border-primary-200 {{ $member->balance < $total ? 'opacity-50 grayscale' : '' }}'">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-2xl">💳</span>
                                <input type="radio" name="payment_method" value="saldo_sukarela" x-model="selectedMethod" class="hidden" {{ $member->balance < $total ? 'disabled' : '' }}>
                                <div class="w-5 h-5 rounded-full border-2 border-gray-300 flex items-center justify-center transition-colors" 
                                     :class="selectedMethod === 'saldo_sukarela' ? 'border-primary-500' : 'group-hover:border-gray-400'">
                                    <div class="w-2.5 h-2.5 rounded-full bg-primary-500 transform scale-0 transition-transform" 
                                         :class="selectedMethod === 'saldo_sukarela' ? 'scale-100' : ''"></div>
                                </div>
                            </div>
                            <span class="font-bold text-gray-900 dark:text-white mb-1">Saldo Sukarela</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">Saldo: Rp {{ number_format($member->balance, 0, ',', '.') }}</span>
                        </label>

                        <!-- Kredit Mart -->
                        <label class="group relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition-all duration-200 hover:shadow-md"
                               :class="selectedMethod === 'kredit' ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/10 ring-1 ring-primary-500 scale-[1.01]' : 'border-gray-200 dark:border-gray-700 hover:border-primary-200 {{ $member->credit_available < $total ? 'opacity-50 grayscale' : '' }}'">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-2xl">📝</span>
                                <input type="radio" name="payment_method" value="kredit" x-model="selectedMethod" class="hidden" {{ $member->credit_available < $total ? 'disabled' : '' }}>
                                <div class="w-5 h-5 rounded-full border-2 border-gray-300 flex items-center justify-center transition-colors" 
                                     :class="selectedMethod === 'kredit' ? 'border-primary-500' : 'group-hover:border-gray-400'">
                                    <div class="w-2.5 h-2.5 rounded-full bg-primary-500 transform scale-0 transition-transform" 
                                         :class="selectedMethod === 'kredit' ? 'scale-100' : ''"></div>
                                </div>
                            </div>
                            <span class="font-bold text-gray-900 dark:text-white mb-1">Kredit Mart</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">Limit: Rp {{ number_format($member->credit_available, 0, ',', '.') }}</span>
                        </label>

                        <!-- Transfer Bank -->
                        <label class="group relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition-all duration-200 hover:shadow-md"
                               :class="selectedMethod === 'transfer' ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/10 ring-1 ring-primary-500 scale-[1.01]' : 'border-gray-200 dark:border-gray-700 hover:border-primary-200'">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-2xl">🏦</span>
                                <input type="radio" name="payment_method" value="transfer" x-model="selectedMethod" class="hidden">
                                <div class="w-5 h-5 rounded-full border-2 border-gray-300 flex items-center justify-center transition-colors" 
                                     :class="selectedMethod === 'transfer' ? 'border-primary-500' : 'group-hover:border-gray-400'">
                                    <div class="w-2.5 h-2.5 rounded-full bg-primary-500 transform scale-0 transition-transform" 
                                         :class="selectedMethod === 'transfer' ? 'scale-100' : ''"></div>
                                </div>
                            </div>
                            <span class="font-bold text-gray-900 dark:text-white mb-1">Transfer Bank</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">Verifikasi Manual</span>
                        </label>

                        <!-- QRIS -->
                        <label class="group relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition-all duration-200 hover:shadow-md"
                               :class="selectedMethod === 'qris' ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/10 ring-1 ring-primary-500 scale-[1.01]' : 'border-gray-200 dark:border-gray-700 hover:border-primary-200'">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-2xl">📱</span>
                                <input type="radio" name="payment_method" value="qris" x-model="selectedMethod" class="hidden">
                                <div class="w-5 h-5 rounded-full border-2 border-gray-300 flex items-center justify-center transition-colors" 
                                     :class="selectedMethod === 'qris' ? 'border-primary-500' : 'group-hover:border-gray-400'">
                                    <div class="w-2.5 h-2.5 rounded-full bg-primary-500 transform scale-0 transition-transform" 
                                         :class="selectedMethod === 'qris' ? 'scale-100' : ''"></div>
                                </div>
                            </div>
                            <span class="font-bold text-gray-900 dark:text-white mb-1">QRIS / E-Wallet</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">Scan & Upload Bukti</span>
                        </label>

                        <!-- Virtual Account -->
                        <label class="group relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition-all duration-200 hover:shadow-md"
                               :class="selectedMethod === 'va' ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/10 ring-1 ring-primary-500 scale-[1.01]' : 'border-gray-200 dark:border-gray-700 hover:border-primary-200'">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-2xl">🏢</span>
                                <input type="radio" name="payment_method" value="va" x-model="selectedMethod" class="hidden">
                                <div class="w-5 h-5 rounded-full border-2 border-gray-300 flex items-center justify-center transition-colors" 
                                     :class="selectedMethod === 'va' ? 'border-primary-500' : 'group-hover:border-gray-400'">
                                    <div class="w-2.5 h-2.5 rounded-full bg-primary-500 transform scale-0 transition-transform" 
                                         :class="selectedMethod === 'va' ? 'scale-100' : ''"></div>
                                </div>
                            </div>
                            <span class="font-bold text-gray-900 dark:text-white mb-1">Virtual Account</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">Konfirmasi Otomatis</span>
                        </label>

                        <!-- Bayar di Toko -->
                        <label class="group relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition-all duration-200 hover:shadow-md"
                               :class="selectedMethod === 'cash_pickup' ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/10 ring-1 ring-primary-500 scale-[1.01]' : 'border-gray-200 dark:border-gray-700 hover:border-primary-200'">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-2xl">🏪</span>
                                <input type="radio" name="payment_method" value="cash_pickup" x-model="selectedMethod" class="hidden">
                                <div class="w-5 h-5 rounded-full border-2 border-gray-300 flex items-center justify-center transition-colors" 
                                     :class="selectedMethod === 'cash_pickup' ? 'border-primary-500' : 'group-hover:border-gray-400'">
                                    <div class="w-2.5 h-2.5 rounded-full bg-primary-500 transform scale-0 transition-transform" 
                                         :class="selectedMethod === 'cash_pickup' ? 'scale-100' : ''"></div>
                                </div>
                            </div>
                            <span class="font-bold text-gray-900 dark:text-white mb-1">Ambil di Toko</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">Bayar saat pengambilan</span>
                        </label>
                    </div>

                    <!-- Payment Info Details -->
                    <div class="mt-6">
                        <!-- Transfer Info -->
                        <div x-show="selectedMethod === 'transfer'" x-transition class="p-4 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700">
                            <h4 class="font-bold text-gray-900 dark:text-white mb-2 text-sm">Rekening Tujuan</h4>
                            <div class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                                <p>Bank: <span class="font-bold text-gray-900 dark:text-white">{{ $settings['bank_name'] ?? 'BCA / Mandiri' }}</span></p>
                                <p>No. Rekening: <span class="font-bold text-gray-900 dark:text-white font-mono bg-white dark:bg-gray-900 px-2 py-0.5 rounded border border-gray-200 dark:border-gray-700 select-all">{{ $settings['bank_account_number'] ?? '123-456-7890' }}</span></p>
                                <p>Atas Nama: <span class="font-bold text-gray-900 dark:text-white">{{ $settings['bank_account_name'] ?? 'Koperasi Spindo' }}</span></p>
                            </div>
                        </div>

                         <!-- QRIS Info -->
                        <div x-show="selectedMethod === 'qris'" x-transition class="p-6 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 text-center">
                            @if(isset($settings['payment_qris_image']))
                                <div class="bg-white p-2 rounded-lg shadow-sm inline-block mb-3">
                                    <img src="{{ Storage::url($settings['payment_qris_image']) }}" alt="QRIS" class="w-48 h-48 object-contain">
                                </div>
                                <p class="text-xs text-gray-500">Scan QRIS di atas untuk melakukan pembayaran</p>
                            @else
                                <div class="w-48 h-48 mx-auto bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center text-gray-400 text-xs text-center p-4">
                                    QR Code belum diatur dalam sistem
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- 3. Catatan -->
                <div class="glass-card-solid p-6 md:p-8">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                        <span class="w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900/30 text-primary-600 flex items-center justify-center text-sm font-bold">3</span>
                        Catatan Tambahan
                    </h2>
                    <textarea name="notes" rows="3" class="form-input w-full" placeholder="Contoh: Tolong disiapkan siang ini jam 12.00"></textarea>
                </div>

            </div>

            <!-- RIGHT COLUMN: Order Summary -->
            <div class="lg:col-span-1">
                <div class="glass-card-solid p-6 md:p-8 sticky top-24">
                    <h2 class="font-bold text-lg text-gray-900 dark:text-white mb-6 pb-4 border-b border-gray-100 dark:border-gray-700">Ringkasan Pesanan</h2>
                    
                     <!-- Items List -->
                    <div class="max-h-60 overflow-y-auto pr-2 space-y-4 mb-6 custom-scrollbar">
                        @foreach($cart as $details)
                        <div class="flex gap-4">
                            <div class="w-12 h-12 rounded-lg bg-gray-50 dark:bg-gray-800 flex-shrink-0 overflow-hidden">
                                @php
                                    $imgUrl = isset($details['image']) && $details['image'] 
                                        ? Storage::url($details['image']) 
                                        : 'https://placehold.co/100x100/6366f1/ffffff?text=' . urlencode($details['name']);
                                @endphp
                                <img src="{{ $imgUrl }}" alt="{{ $details['name'] }}" class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ $details['name'] }}</p>
                                @if(isset($details['is_preorder']) && $details['is_preorder'])
                                    <span class="text-[9px] uppercase font-bold text-purple-600 bg-purple-100 px-1.5 py-0.5 rounded inline-block mb-1">Pre-Order</span>
                                @endif
                                <p class="text-xs text-gray-500">{{ $details['quantity'] }} x Rp {{ number_format($details['price'], 0, ',', '.') }}</p>
                            </div>
                            <div class="font-bold text-sm text-gray-900 dark:text-white">
                                Rp {{ number_format($details['price'] * $details['quantity'], 0, ',', '.') }}
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Calculation -->
                    <div class="space-y-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                        <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                            <span>Subtotal</span>
                            <span>Rp {{ number_format($originalTotal ?? $total, 0, ',', '.') }}</span>
                        </div>

                        @if(isset($discount) && $discount > 0)
                        <div class="flex justify-between text-sm text-green-600">
                            <span>Diskon Voucher</span>
                            <span>-Rp {{ number_format($discount, 0, ',', '.') }}</span>
                        </div>
                        @endif

                        <!-- Point Redemption Option -->
                        @if($member->points > 0)
                        <div class="p-3 bg-yellow-50 dark:bg-yellow-900/10 rounded-xl border border-yellow-100 dark:border-yellow-900/30 mt-2">
                             <label class="flex items-start gap-3 cursor-pointer">
                                <input type="checkbox" name="use_points" value="1" class="form-checkbox mt-1 text-yellow-500 rounded focus:ring-yellow-500">
                                <div class="flex-1">
                                    <span class="block text-sm font-bold text-gray-900 dark:text-white">Gunakan Poin</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400 block mb-1">Tukar {{ number_format($member->points, 0, ',', '.') }} poin</span>
                                    <span class="text-xs font-bold text-yellow-600 dark:text-yellow-500">Hemat Rp {{ number_format($member->points_value, 0, ',', '.') }}</span>
                                </div>
                            </label>
                        </div>
                        @endif

                        <div class="flex justify-between items-center pt-4 border-t-2 border-dashed border-gray-200 dark:border-gray-700">
                            <span class="font-bold text-lg text-gray-900 dark:text-white">Total Bayar</span>
                            <span class="font-black text-2xl text-primary-600">Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <button type="submit" 
                            :disabled="!selectedMethod"
                            class="w-full mt-6 btn-primary py-4 text-lg font-bold shadow-lg shadow-primary-500/30 flex items-center justify-center gap-2 group disabled:opacity-50 disabled:cursor-not-allowed">
                        <span>Bayar Sekarang</span>
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>
                    
                    <p class="text-center text-xs text-gray-400 mt-4 mx-auto w-3/4">
                        Dengan melanjutkan pembayaran, Anda menyetujui syarat & ketentuan Koperasi Mart.
                    </p>
                </div>
            </div>
        </form>
    </div>
@endsection
