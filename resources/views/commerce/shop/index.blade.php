@extends('layouts.app')

@section('title', __('messages.shop_page.title'))

@section('content')
    <!-- Header -->
    <div id="flying-container" class="fixed inset-0 pointer-events-none z-[100]"></div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">{{ __('messages.shop_page.title') }}</h1>
        <p class="text-gray-500 text-sm">{{ __('messages.shop_page.subtitle') }}</p>
    </div>

    <!-- Search & Filters -->
    <div class="glass-card-solid p-4 mb-6">
        <div class="flex flex-col lg:flex-row gap-4">
            <!-- Search -->
            <form action="{{ route('shop.index') }}" method="GET" class="flex-1 flex gap-2">
                @if(request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
                <div class="relative flex-1">
                    <svg class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <input type="text" name="search" placeholder="{{ __('messages.shop_page.search_placeholder') }}" value="{{ request('search') }}" 
                           class="form-input w-full pl-10 py-2.5">
                </div>
                <button type="submit" class="btn-primary px-6">{{ __('messages.shop_page.search_btn') }}</button>
            </form>
            
            <!-- Category Filter -->
            <div class="flex gap-2 overflow-x-auto scrollbar-hide">
                <a href="{{ route('shop.index') }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap transition-colors {{ !request('category') ? 'bg-primary-600 text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700' }}">
                    {{ __('messages.shop_page.all') }}
                </a>
                @foreach($categories as $category)
                <a href="{{ route('shop.index', ['category' => $category->id]) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap transition-colors {{ request('category') == $category->id ? 'bg-primary-600 text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700' }}">
                    {{ $category->icon }} {{ $category->name }}
                </a>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Products Grid -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
        @forelse($products as $product)
        <div class="glass-card-solid group hover:ring-2 hover:ring-primary-500 transition-all duration-300 flex flex-col">
            <a href="{{ route('shop.show', $product) }}" class="block">
                <!-- Image Container -->
                <div class="aspect-square bg-gray-50 dark:bg-gray-800 m-3 rounded-lg overflow-hidden relative">
                    <!-- Corner Decorations -->
                    <div class="absolute top-1.5 left-1.5 w-3 h-3 border-t-2 border-l-2 border-primary-400 z-10"></div>
                    <div class="absolute top-1.5 right-1.5 w-3 h-3 border-t-2 border-r-2 border-primary-400 z-10"></div>
                    <div class="absolute bottom-1.5 left-1.5 w-3 h-3 border-b-2 border-l-2 border-primary-400 z-10"></div>
                    <div class="absolute bottom-1.5 right-1.5 w-3 h-3 border-b-2 border-r-2 border-primary-400 z-10"></div>
                    
                    <!-- Image -->
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" 
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                         onerror="this.onerror=null; this.src='https://placehold.co/400x400/6366f1/ffffff?text={{ urlencode($product->name) }}'">
                    
                    <!-- Stock Badge -->
                    @if($product->is_preorder)
                        <div class="absolute top-2 right-2 bg-purple-600 text-white text-[9px] font-bold px-2 py-0.5 rounded-full z-20 flex items-center gap-1 shadow-lg">
                            <span class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></span> {{ __('messages.shop_page.preorder') }}
                        </div>
                    @elseif($product->stock <= 0)
                        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-[1px] flex items-center justify-center">
                            <span class="bg-red-600 text-white text-[10px] uppercase font-bold px-3 py-1 rounded-full">{{ __('messages.shop_page.out_of_stock') }}</span>
                        </div>
                    @elseif($product->stock < 5)
                        <div class="absolute top-2 right-2 bg-orange-500 text-white text-[9px] font-bold px-2 py-0.5 rounded-full">
                            {{ __('messages.shop_page.left') }} {{ $product->stock }}
                        </div>
                    @endif
                </div>
                
                <!-- Product Info -->
                <div class="px-3 pb-2">
                    <div class="text-[10px] font-semibold text-primary-500 uppercase tracking-wide mb-0.5">{{ $product->category->name }}</div>
                    <h3 class="font-semibold text-gray-900 dark:text-white text-sm group-hover:text-primary-600 transition-colors line-clamp-2 leading-tight min-h-[2.5rem]">{{ $product->name }}</h3>
                </div>
            </a>
            
            <!-- Price & Add Button -->
            <div class="px-3 pb-3 mt-auto">
                <div class="flex items-center justify-between">
                    <span class="text-primary-600 font-bold text-sm">Rp {{ number_format($product->price, 0, ',', '.') }}<span class="text-gray-400 font-normal text-xs">/{{ $product->unit ?? 'pcs' }}</span></span>
                    <form action="{{ route('shop.add', $product) }}" method="POST">
                        @csrf
                        <button type="button" 
                                class="w-8 h-8 flex items-center justify-center bg-primary-50 dark:bg-primary-900/30 text-primary-600 rounded-lg hover:bg-primary-600 hover:text-white transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed" 
                                {{ (!$product->is_preorder && $product->stock <= 0) ? 'disabled' : '' }} 
                                title="{{ $product->is_preorder ? __('messages.shop_page.preorder') : __('messages.shop_page.add_to_cart_tooltip') }}"
                                onclick="addToCartAnimation(this, event)">
                            @if($product->is_preorder)
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            @else
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            @endif
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full">
            <div class="glass-card-solid p-12 text-center">
                <div class="text-4xl mb-4">🔍</div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ __('messages.shop_page.not_found') }}</h3>
                <p class="text-gray-500 text-sm mb-4">{{ __('messages.shop_page.not_found_desc') }}</p>
                <a href="{{ route('shop.index') }}" class="btn-primary inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                    {{ __('messages.shop_page.view_all_btn') }}
                </a>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-6 flex items-center justify-between">
        <p class="text-sm text-gray-500">
            {{ __('messages.shop_page.showing') }} {{ $products->firstItem() ?? 0 }} {{ __('messages.shop_page.to') }} {{ $products->lastItem() ?? 0 }} {{ __('messages.shop_page.of') }} {{ $products->total() }} {{ __('messages.shop_page.results') }}
        </p>
        {{ $products->links() }}
    </div>

    <!-- Floating Cart Button -->
    @if(session('cart') && count(session('cart')) > 0)
    <a href="{{ route('shop.cart') }}" id="shop-floating-cart"
       class="fixed bottom-6 right-6 bg-primary-600 text-white px-5 py-3 rounded-full shadow-xl shadow-primary-600/30 hover:bg-primary-700 hover:scale-105 transition-all z-50 flex items-center gap-3">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
        <span class="font-semibold">{{ count(session('cart')) }} {{ __('messages.shop_page.items') }}</span>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
    </a>
    @endif


    @push('scripts')
    <script>
        function addToCartAnimation(btn, event) {
            event.preventDefault(); // Prevent double submission just in case
            
            const form = btn.closest('form');
            if (!form) return;

            // 1. Find elements
            const productCard = btn.closest('.group'); // The card has 'group' class
            const imgEl = productCard ? productCard.querySelector('img') : null;
            const container = document.getElementById('flying-container');
            
            // 2. Find target (Always Topnav as requested)
            const cartTarget = document.getElementById('topnav-cart');
            
            if (!imgEl || !container || !cartTarget) {
                form.submit();
                return;
            }

            // 3. Create clone
            const startRect = imgEl.getBoundingClientRect();
            const endRect = cartTarget.getBoundingClientRect();

            const flyingEl = document.createElement('div');
            flyingEl.className = 'absolute rounded-lg shadow-xl border-2 border-primary-500 overflow-hidden z-[100]';
            flyingEl.style.width = startRect.width + 'px';
            flyingEl.style.height = startRect.height + 'px';
            flyingEl.style.left = startRect.left + 'px';
            flyingEl.style.top = startRect.top + 'px';
            flyingEl.style.transition = 'all 0.6s cubic-bezier(0.2, 0.8, 0.2, 1)';
            flyingEl.style.backgroundImage = `url('${imgEl.src}')`;
            flyingEl.style.backgroundSize = 'cover';
            flyingEl.style.backgroundPosition = 'center';
            flyingEl.style.backgroundColor = 'white';
            
            container.appendChild(flyingEl);

            // Force reflow
            void flyingEl.offsetWidth;

            // 4. Animate to target
            flyingEl.style.width = '20px';
            flyingEl.style.height = '20px';
            flyingEl.style.left = (endRect.left + endRect.width/2 - 10) + 'px'; // Center it
            flyingEl.style.top = (endRect.top + endRect.height/2 - 10) + 'px';
            flyingEl.style.opacity = '0.5';
            flyingEl.style.borderRadius = '50%';

            // 5. Submit after animation
            setTimeout(() => {
                flyingEl.remove();
                form.submit();
            }, 600);
        }
    </script>
    @endpush
@endsection
