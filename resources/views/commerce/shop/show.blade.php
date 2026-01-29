@extends('layouts.app')

@section('title', $product->name)

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('shop.index') }}" class="btn-secondary inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold text-gray-600 dark:text-gray-300 hover:text-primary-600 hover:border-primary-600 transition-all group">
                <svg class="w-4 h-4 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Katalog
            </a>
        </div>

        <!-- Breadcrumb -->
        <nav class="flex text-sm text-gray-500 mb-8 overflow-x-auto whitespace-nowrap pb-2">
            <a href="{{ route('shop.index') }}" class="hover:text-primary-600 transition-colors flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                Belanja
            </a>
            <span class="mx-3 text-gray-300">/</span>
            <a href="{{ route('shop.index', ['category' => $product->category_id]) }}" class="hover:text-primary-600 transition-colors">{{ $product->category->name }}</a>
            <span class="mx-3 text-gray-300">/</span>
            <span class="text-gray-900 dark:text-gray-100 font-medium truncate">{{ $product->name }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16">
            <!-- Left: Image Section -->
            <div class="lg:sticky lg:top-24 h-fit">
                <div class="glass-card-solid p-6 md:p-8 rounded-3xl">
                    <div class="aspect-square bg-gradient-to-br from-primary-100 via-gray-100 to-primary-50 dark:from-gray-700 dark:via-gray-800 dark:to-gray-700 rounded-2xl p-6 relative group overflow-hidden">
                        <!-- Decorative Background Pattern -->
                        <div class="absolute inset-0 opacity-10 dark:opacity-5 pointer-events-none">
                            <div class="absolute inset-0" style="background-image: radial-gradient(circle at 2px 2px, currentColor 1px, transparent 0); background-size: 24px 24px;"></div>
                        </div>
                        
                        <!-- Modern Frame Container -->
                        <div class="relative w-full h-full rounded-2xl overflow-hidden shadow-2xl ring-4 ring-white/50 dark:ring-white/10 group-hover:ring-primary-400/30 transition-all duration-500">
                            <!-- Corner Decorations -->
                            <div class="absolute top-0 left-0 w-12 h-12 border-t-4 border-l-4 border-primary-500 rounded-tl-xl z-20"></div>
                            <div class="absolute top-0 right-0 w-12 h-12 border-t-4 border-r-4 border-primary-500 rounded-tr-xl z-20"></div>
                            <div class="absolute bottom-0 left-0 w-12 h-12 border-b-4 border-l-4 border-primary-500 rounded-bl-xl z-20"></div>
                            <div class="absolute bottom-0 right-0 w-12 h-12 border-b-4 border-r-4 border-primary-500 rounded-br-xl z-20"></div>
                            
                            <!-- Accent Lines -->
                            <div class="absolute top-0 left-16 right-16 h-1 bg-gradient-to-r from-transparent via-primary-400/50 to-transparent z-10"></div>
                            <div class="absolute bottom-0 left-16 right-16 h-1 bg-gradient-to-r from-transparent via-primary-400/50 to-transparent z-10"></div>
                            
                            <!-- Image -->
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" 
                                 class="w-full h-full object-cover transform transition-transform duration-700 group-hover:scale-110"
                                 onerror="this.onerror=null; this.src='https://placehold.co/600x600/6366f1/ffffff?text={{ urlencode($product->name) }}'">
                            
                            <!-- Elegant Overlay on Hover -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black/20 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>

                            <!-- Zoom Hint -->
                             <div class="absolute bottom-4 right-4 bg-white/90 dark:bg-gray-800/90 p-2 rounded-full shadow-lg opacity-0 group-hover:opacity-100 transition-all duration-300 transform scale-90 group-hover:scale-100 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-700 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Information Section -->
            <div class="space-y-8 lg:py-4">
                <div class="space-y-6">
                    <!-- Badges -->
                    <div class="flex flex-wrap items-center gap-3">
                        <span class="px-4 py-1.5 bg-primary-100 dark:bg-primary-900/40 text-primary-700 dark:text-primary-300 text-xs font-bold uppercase tracking-widest rounded-lg border border-primary-200 dark:border-primary-800">
                            {{ $product->category->name }}
                        </span>
                        @if($product->is_preorder)
                             <span class="px-3 py-1 bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300 text-xs font-bold uppercase tracking-widest rounded-full flex items-center gap-1">
                                <span class="w-2 h-2 bg-purple-500 rounded-full animate-ping"></span> Pre-Order
                            </span>
                            @if($product->preorder_eta)
                                <span class="px-3 py-1 bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400 text-xs font-bold uppercase tracking-widest rounded-full flex items-center gap-1">
                                    🕒 Estimasi: {{ $product->preorder_eta }}
                                </span>
                            @endif
                        @elseif($product->stock <= 0)
                            <span class="px-3 py-1 bg-red-100 text-red-600 text-xs font-bold uppercase tracking-widest rounded-full flex items-center gap-1"><span class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></span> Habis</span>
                        @elseif($product->stock < 10)
                            <span class="px-3 py-1 bg-orange-100 text-orange-600 text-xs font-bold uppercase tracking-widest rounded-full flex items-center gap-1"><span class="w-2 h-2 bg-orange-500 rounded-full animate-pulse"></span> Sisa {{ $product->stock }}</span>
                        @else
                            <span class="px-3 py-1 bg-green-100 text-green-600 text-xs font-bold uppercase tracking-widest rounded-full flex items-center gap-1"><span class="w-2 h-2 bg-green-500 rounded-full"></span> Tersedia</span>
                        @endif
                    </div>

                    <!-- Title & Rating -->
                    <div>
                        <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900 dark:text-white leading-tight mb-3">
                            {{ $product->name }}
                        </h1>
                        <a href="#reviews" class="inline-flex items-center gap-2 group p-1 -ml-1 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            <div class="flex items-center text-yellow-400">
                                @for($i=1; $i<=5; $i++)
                                    <svg class="w-5 h-5 {{ $i <= $product->average_rating ? 'fill-current' : 'text-gray-300' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                @endfor
                            </div>
                            <span class="text-sm font-bold text-gray-400 group-hover:text-primary-600 transition-colors uppercase tracking-wider">({{ $product->reviews->count() }} Ulasan)</span>
                        </a>
                    </div>

                    <!-- Price -->
                    <div class="p-6 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-800/50 rounded-2xl border border-gray-100 dark:border-gray-700">
                        <div class="flex items-end gap-3 mb-1">
                            <span class="text-4xl sm:text-5xl font-black text-primary-600 tracking-tight">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                            <span class="text-sm text-gray-500 font-medium mb-2">/ unit</span>
                        </div>
                        <p class="text-xs text-gray-500 font-medium">✨ Harga terbaik khusus anggota koperasi</p>
                    </div>

                    <!-- Description -->
                    <div class="prose prose-sm dark:prose-invert text-gray-600 dark:text-gray-400 leading-relaxed max-w-none">
                        <p>{{ $product->description ?: 'Belum ada deskripsi untuk produk ini. Produk berkualitas tinggi yang tersedia di Koperasi Mart untuk mendukung kebutuhan harian anggota.' }}</p>
                    </div>

                    <!-- Actions -->
                    <div class="pt-6 border-t border-gray-100 dark:border-gray-800">
                        <form action="{{ route('shop.add', $product) }}" method="POST" class="flex flex-col sm:flex-row gap-4">
                            @csrf
                            <!-- Quantity -->
                            <div class="flex items-center justify-between sm:justify-start border-2 border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden bg-white dark:bg-gray-800 p-1 w-full sm:w-auto">
                                <button type="button" class="w-12 h-10 flex items-center justify-center text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors font-bold text-lg" onclick="this.nextElementSibling.stepDown()">-</button>
                                <input type="number" name="quantity" value="1" min="1" {{ $product->is_preorder ? '' : 'max='.$product->stock }} 
                                       class="w-16 text-center border-none focus:ring-0 bg-transparent font-bold text-lg p-0">
                                <button type="button" class="w-12 h-10 flex items-center justify-center text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors font-bold text-lg" onclick="this.previousElementSibling.stepUp()">+</button>
                            </div>
                            
                            <!-- Add Button -->
                            <button type="submit" class="flex-1 btn-primary py-4 px-8 text-lg font-bold shadow-xl shadow-primary-500/30 flex items-center justify-center gap-3 active:scale-[0.98] transition-all hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed"
                                    {{ (!$product->is_preorder && $product->stock <= 0) ? 'disabled' : '' }}>
                                <div class="bg-white/20 p-1.5 rounded-lg">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                </div>
                                @if($product->is_preorder)
                                    Pesan (PO)
                                @elseif($product->stock <= 0)
                                    Stok Habis
                                @else
                                    Tambah ke Keranjang
                                @endif
                            </button>
                        </form>
                    </div>


                    <!-- Features Grid -->
                    <div class="flex flex-row items-center justify-between gap-2 pt-6">
                        <div class="flex-1 flex flex-col items-center justify-center text-center p-2 bg-gray-50 dark:bg-gray-800/50 rounded-xl hover:bg-gray-100 transition-colors">
                            <span class="text-xl mb-1">✅</span> 
                            <span class="text-[9px] uppercase font-bold text-gray-500">Original</span>
                        </div>
                        <div class="flex-1 flex flex-col items-center justify-center text-center p-2 bg-gray-50 dark:bg-gray-800/50 rounded-xl hover:bg-gray-100 transition-colors">
                            <span class="text-xl mb-1">🏢</span> 
                            <span class="text-[9px] uppercase font-bold text-gray-500">Koperasi</span>
                        </div>
                        <div class="flex-1 flex flex-col items-center justify-center text-center p-2 bg-gray-50 dark:bg-gray-800/50 rounded-xl hover:bg-gray-100 transition-colors">
                            <span class="text-xl mb-1">🚀</span> 
                            <span class="text-[9px] uppercase font-bold text-gray-500">Cepat</span>
                        </div>
                        <div class="flex-1 flex flex-col items-center justify-center text-center p-2 bg-gray-50 dark:bg-gray-800/50 rounded-xl hover:bg-gray-100 transition-colors">
                            <span class="text-xl mb-1">🛡️</span> 
                            <span class="text-[9px] uppercase font-bold text-gray-500">Aman</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Divider -->
        <hr class="border-gray-200 dark:border-gray-800 my-16">

        <!-- Reviews Section -->
        <div id="reviews" class="max-w-4xl mx-auto">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tight flex items-center gap-3">
                    <span class="text-4xl text-primary-500">💬</span> Ulasan Pelanggan
                </h2>
                <div class="flex flex-col items-end">
                    <span class="text-3xl font-black text-gray-900 dark:text-white">{{ $product->average_rating }}<span class="text-lg text-gray-400 font-medium">/ 5</span></span>
                    <div class="flex text-yellow-400 gap-0.5">
                        @for($i=1; $i<=5; $i++)
                        <svg class="w-4 h-4 {{ $i <= $product->average_rating ? 'fill-current' : 'text-gray-300' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        @endfor
                    </div>
                </div>
            </div>

            <!-- Review Form -->
            @php
                $canReview = \App\Models\Transaction::where('user_id', auth()->id())
                    ->whereHas('items', function($q) use ($product) {
                        $q->where('product_id', $product->id);
                    })
                    ->where('status', 'completed')
                    ->exists();
                
                $existingReview = \App\Models\Review::where('user_id', auth()->id())
                    ->where('product_id', $product->id)
                    ->first();
                
                $currentRating = $existingReview ? $existingReview->rating : 5;
            @endphp

            @if($canReview)
            <div class="glass-card-solid p-6 md:p-8 mb-10 border-2 border-dashed border-primary-200 dark:border-primary-800">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-12 h-12 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-2xl">✍️</div>
                    <div>
                        <h4 class="font-bold text-gray-900 dark:text-white text-lg">
                            {{ $existingReview ? 'Update Ulasan Anda' : 'Bagikan Pengalaman Anda' }}
                        </h4>
                        <p class="text-sm text-gray-500">Beritahu anggota lain tentang kualitas produk ini</p>
                    </div>
                </div>
                
                <form action="{{ route('shop.review', $product) }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Rating Kualitas</label>
                        <div class="flex gap-2" id="star-rating">
                            @for($i = 1; $i <= 5; $i++)
                            <button type="button" onclick="setRating({{ $i }})" class="star-btn focus:outline-none transform transition-transform hover:scale-110" data-value="{{ $i }}">
                                <svg class="w-10 h-10 cursor-pointer transition-colors drop-shadow-sm" style="fill: {{ $i <= $currentRating ? '#facc15' : '#e5e7eb' }};" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                            </button>
                            @endfor
                        </div>
                        <input type="hidden" name="rating" id="rating-input" value="{{ $currentRating }}">
                    </div>
                    <script>
                    function setRating(rating) {
                        document.getElementById('rating-input').value = rating;
                        const stars = document.querySelectorAll('#star-rating .star-btn svg');
                        stars.forEach((svg, index) => {
                            svg.style.fill = index < rating ? '#facc15' : '#e5e7eb';
                        });
                    }
                    </script>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Komentar Anda</label>
                        <textarea name="comment" rows="4" class="form-input w-full rounded-xl" placeholder="Tuliskan detail pengalaman Anda menggunakan produk ini...">{{ $existingReview->comment ?? '' }}</textarea>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="btn-primary py-3 px-8 font-bold">Kirim Ulasan</button>
                    </div>
                </form>
            </div>
            @endif

            <!-- Reviews List -->
            <div class="space-y-6">
                @forelse($product->reviews as $review)
                <div class="glass-card-solid p-6 rounded-2xl flex gap-5 hover:shadow-lg transition-shadow">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 flex items-center justify-center text-lg font-bold text-gray-600 dark:text-gray-300 shadow-inner flex-shrink-0">
                        {{ strtoupper(substr($review->user->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <h5 class="font-bold text-gray-900 dark:text-white">{{ $review->user->name }}</h5>
                                <span class="text-xs text-gray-400">{{ $review->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="px-3 py-1 bg-yellow-50 dark:bg-yellow-900/10 rounded-full border border-yellow-100 dark:border-yellow-900/30 flex items-center gap-1">
                                <span class="text-yellow-500 font-bold">{{ $review->rating }}.0</span>
                                <svg class="w-3 h-3 text-yellow-500 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                            </div>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400 leading-relaxed">{{ $review->comment ?: 'Tidak ada komentar.' }}</p>
                    </div>
                </div>
                @empty
                <div class="glass-card-solid p-12 text-center rounded-3xl border-2 border-dashed border-gray-200 dark:border-gray-800">
                    <div class="w-20 h-20 bg-gray-50 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4 text-4xl grayscale opacity-50">
                        ⭐
                    </div>
                    <h3 class="font-bold text-gray-900 dark:text-white mb-1">Belum ada ulasan</h3>
                    <p class="text-gray-500 text-sm">Jadilah yang pertama mengulas produk ini!</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Related Products (Updated with Modern Frame) -->
        @if($relatedProducts->count() > 0)
        <div class="mt-20">
            <h2 class="text-2xl font-black text-gray-900 dark:text-white mb-8 flex items-center gap-3">
                <span class="p-2 bg-primary-100 dark:bg-primary-900/30 text-primary-600 rounded-lg">✨</span>
                Produk Rekomendasi
            </h2>
            
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-6">
                @foreach($relatedProducts as $related)
                <a href="{{ route('shop.show', $related) }}" class="glass-card-solid group flex flex-col hover:-translate-y-1 transition-transform duration-300 h-full">
                    <div class="p-4 flex-1 flex flex-col">
                        <div class="aspect-square bg-gradient-to-br from-primary-50 to-primary-100 dark:from-gray-700 dark:to-gray-800 rounded-xl overflow-hidden mb-4 relative">
                            <!-- Mini Frame -->
                            <div class="absolute inset-2 border border-primary-500/20 rounded-lg z-10"></div>
                            <div class="absolute top-1.5 left-1.5 w-2 h-2 border-t-2 border-l-2 border-primary-400 z-10"></div>
                            <div class="absolute top-1.5 right-1.5 w-2 h-2 border-t-2 border-r-2 border-primary-400 z-10"></div>
                            <div class="absolute bottom-1.5 left-1.5 w-2 h-2 border-b-2 border-l-2 border-primary-400 z-10"></div>
                            <div class="absolute bottom-1.5 right-1.5 w-2 h-2 border-b-2 border-r-2 border-primary-400 z-10"></div>
                            
                            <img src="{{ $related->image_url }}" alt="{{ $related->name }}" 
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                 onerror="this.onerror=null; this.src='https://placehold.co/300x300/6366f1/ffffff?text={{ urlencode($related->name) }}'">
                        </div>
                        <div class="text-[10px] font-bold text-primary-500 uppercase tracking-widest mb-1">{{ $related->category->name }}</div>
                        <h3 class="font-bold text-gray-900 dark:text-white group-hover:text-primary-600 transition-colors line-clamp-2 mb-2 text-sm leading-snug">{{ $related->name }}</h3>
                        <div class="mt-auto pt-2 flex items-center justify-between">
                            <span class="font-black text-gray-900 dark:text-white">Rp {{ number_format($related->price, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>
@endsection
