<!-- Top Navigation -->
<header class="sticky top-0 z-30 bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl border-b border-gray-200 dark:border-gray-700">
    <div class="flex items-center justify-between h-16 px-4 lg:px-8">
        <!-- Left Side -->
        <div class="flex items-center gap-4">
            <!-- Mobile Menu Toggle -->
            <button @click="sidebarMobileOpen = true" class="lg:hidden flex items-center justify-center w-10 h-10 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                <svg class="w-6 h-6 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>

            <!-- Page Title (Mobile) -->
            <h1 class="hidden text-lg font-semibold text-gray-900 dark:text-white lg:hidden">
                @yield('title', 'Dashboard')
            </h1>

            <!-- Search Bar (Desktop) -->
            <div class="hidden lg:flex relative">
                <input type="text" placeholder="{{ __('messages.nav.search_placeholder') }}" 
                       class="w-72 pl-10 pr-4 py-2 rounded-xl bg-gray-100 dark:bg-gray-700 border-0 text-sm text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-primary-500 transition-all">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
        </div>

        <!-- Right Side -->
        <div class="flex items-center gap-2 lg:gap-4">
            <!-- View Website -->
            <a href="{{ route('landing') }}" target="_blank" 
               class="flex items-center justify-center w-10 h-10 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
               title="{{ __('messages.nav.view_website') }}">
                <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                </svg>
            </a>

            <!-- Dark Mode Toggle -->
            <button @click="darkMode = !darkMode" 
                    class="flex items-center justify-center w-10 h-10 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                    title="{{ __('messages.settings_page.dark_mode') }}">
                <svg x-show="!darkMode" class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                </svg>
                <svg x-show="darkMode" class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
            </button>

            <!-- Language Toggle -->
            <div x-data="{ langOpen: false }" class="relative">
                <button @click="langOpen = !langOpen" 
                        class="flex items-center justify-center w-10 h-10 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                        title="{{ __('messages.settings_page.language') }}">
                    <span class="text-xs font-bold text-gray-600 dark:text-gray-300 uppercase">{{ app()->getLocale() }}</span>
                </button>
                
                <!-- Language Dropdown -->
                <div x-show="langOpen" @click.away="langOpen = false"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-36 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden z-50">
                    <a href="{{ route('locale.switch', 'id') }}" 
                       class="flex items-center gap-2 px-4 py-2.5 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors {{ app()->getLocale() == 'id' ? 'text-primary-600 font-semibold bg-primary-50 dark:bg-primary-900/20' : 'text-gray-700 dark:text-gray-200' }}">
                        <span class="text-lg">🇮🇩</span> Indonesia
                    </a>
                    <a href="{{ route('locale.switch', 'en') }}" 
                       class="flex items-center gap-2 px-4 py-2.5 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors {{ app()->getLocale() == 'en' ? 'text-primary-600 font-semibold bg-primary-50 dark:bg-primary-900/20' : 'text-gray-700 dark:text-gray-200' }}">
                        <span class="text-lg">🇬🇧</span> English
                    </a>
                </div>
            </div>

            <!-- Cart -->
            <a id="topnav-cart" href="{{ route('shop.cart') }}" class="relative flex items-center justify-center w-10 h-10 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                @if(session('cart') && count(session('cart')) > 0)
                    <span class="absolute top-1 right-1 flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-primary-500"></span>
                    </span>
                @endif
            </a>

            <!-- Notifications -->
            @php
                $unreadNotifications = auth()->user()->unreadNotifications->take(10);
                $unreadCount = auth()->user()->unreadNotifications->count();
            @endphp
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" 
                        class="relative flex items-center justify-center w-10 h-10 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    <!-- Notification Badge -->
                    @if($unreadCount > 0)
                    <span class="absolute -top-0.5 -right-0.5 flex items-center justify-center min-w-[18px] h-[18px] px-1 text-[10px] font-bold text-white bg-red-500 rounded-full ring-2 ring-white dark:ring-gray-800">
                        {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                    </span>
                    @endif
                </button>
                
                <!-- Dropdown -->
                <div x-show="open" @click.away="open = false"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden z-50">
                    <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                        <h3 class="font-semibold text-gray-900 dark:text-white">{{ __('messages.nav.notifications') }}</h3>
                        @if($unreadCount > 0)
                        <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                            @csrf
                            <button type="submit" class="text-xs text-primary-600 hover:text-primary-700 dark:text-primary-400 font-medium">
                                {{ __('messages.nav.mark_all_read') }}
                            </button>
                        </form>
                        @endif
                    </div>
                    <div class="max-h-80 overflow-y-auto divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($unreadNotifications as $notification)
                            @php
                                $data = $notification->data;
                                $type = $data['type'] ?? 'info';
                                $message = $data['message'] ?? 'Notifikasi baru';
                                $url = '#';

                                // Determine URL based on notification type
                                if ($type === 'loan_approved' && isset($data['loan_id'])) {
                                    $url = route('loans.show', $data['loan_id']);
                                } elseif ($type === 'payment_reminder' && isset($data['loan_payment_id'])) {
                                    $loanPayment = \App\Models\LoanPayment::find($data['loan_payment_id']);
                                    $url = $loanPayment ? route('loans.show', $loanPayment->loan_id) : '#';
                                } elseif ($type === 'low_stock' && isset($data['product_id'])) {
                                    $url = route('products.edit', $data['product_id']);
                                } elseif ($type === 'new_online_order' && isset($data['transaction_id'])) {
                                    $url = route('pos.manage', $data['transaction_id']);
                                } elseif ($type === 'order_ready' && isset($data['transaction_id'])) {
                                    $url = route('shop.track', $data['transaction_id']);
                                } elseif ($type === 'new_announcement') {
                                    $url = route('announcements.index');
                                }

                                // Icon based on type
                                $iconColor = match($type) {
                                    'loan_approved' => 'text-green-500 bg-green-100 dark:bg-green-900/30',
                                    'payment_reminder' => 'text-amber-500 bg-amber-100 dark:bg-amber-900/30',
                                    'low_stock' => 'text-red-500 bg-red-100 dark:bg-red-900/30',
                                    'new_online_order' => 'text-blue-500 bg-blue-100 dark:bg-blue-900/30',
                                    'order_ready' => 'text-green-500 bg-green-100 dark:bg-green-900/30',
                                    'new_announcement' => 'text-purple-500 bg-purple-100 dark:bg-purple-900/30',
                                    default => 'text-blue-500 bg-blue-100 dark:bg-blue-900/30',
                                };
                            @endphp
                            <a href="{{ $url }}" 
                               onclick="fetch('{{ route('notifications.mark-read', $notification->id) }}', {method: 'POST', headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}})"
                               class="flex items-start gap-3 p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center {{ $iconColor }}">
                                    @if($type === 'loan_approved' || $type === 'order_ready')
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    @elseif($type === 'payment_reminder')
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    @elseif($type === 'low_stock')
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    @elseif($type === 'new_online_order')
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    @elseif($type === 'new_announcement')
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                                    @else
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-gray-900 dark:text-white line-clamp-2">{{ $message }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                </div>
                            </a>
                        @empty
                            <div class="p-6 text-center text-gray-500 dark:text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-2 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                </svg>
                                <p class="text-sm">{{ __('messages.nav.no_notifications') }}</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- User Menu -->
            <div x-data="{ open: false }" class="relative">
                @auth
                <button @click="open = !open" class="flex items-center gap-3 p-1.5 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white font-semibold text-sm">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <span class="hidden md:block text-sm font-medium text-gray-700 dark:text-gray-200">
                        {{ auth()->user()->name }}
                    </span>
                    <svg class="hidden md:block w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                
                <!-- Dropdown -->
                <div x-show="open" @click.away="open = false"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="p-3 border-b border-gray-200 dark:border-gray-700">
                        <p class="font-medium text-gray-900 dark:text-white">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ auth()->user()->email }}</p>
                    </div>
                    <div class="py-2">
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            {{ __('messages.nav.my_profile') }}
                        </a>
                        @if(auth()->user()->member)
                        <a href="{{ route('members.show', auth()->user()->member) }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            {{ __('messages.nav.membership_data') }}
                        </a>
                        @endif
                    </div>
                    <div class="border-t border-gray-200 dark:border-gray-700 py-2">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                {{ __('messages.logout') }}
                            </button>
                        </form>
                    </div>
                </div>
                @else
                <a href="{{ route('login') }}" class="btn-primary">
                    {{ __('messages.login') }}
                </a>
                @endauth
            </div>
        </div>
    </div>
</header>
