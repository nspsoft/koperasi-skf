<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true', sidebarOpen: true, sidebarMobileOpen: false }" 
      x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))"
      :class="{ 'dark': darkMode }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $globalSettings['coop_name'] ?? config('app.name', 'Koperasi') }} - @yield('title', 'Dashboard')</title>

        <!-- PWA Meta Tags -->
        <meta name="theme-color" content="#059669">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="apple-mobile-web-app-title" content="Koperasi">
        <meta name="application-name" content="Koperasi">
        <meta name="format-detection" content="telephone=no">
        <meta name="description" content="Koperasi Digital - Simpan Pinjam, Belanja Online, dan Layanan Anggota">
        <link rel="manifest" href="/manifest.json">
        <link rel="apple-touch-icon" href="/icons/icon-192x192.png">
        <link rel="apple-touch-icon" sizes="152x152" href="/icons/icon-152x152.png">
        <link rel="apple-touch-icon" sizes="180x180" href="/icons/icon-192x192.png">
        <link rel="apple-touch-icon" sizes="167x167" href="/icons/icon-192x192.png">

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="/favicon.png">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- ApexCharts -->
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        
        <script>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register('/sw.js')
                        .then(reg => {
                            console.log('Service Worker registered', reg);
                            
                            // Check for updates periodically
                            setInterval(() => {
                                reg.update();
                            }, 60000); // Check every minute
                            
                            // Listen for new SW waiting
                            reg.addEventListener('updatefound', () => {
                                const newWorker = reg.installing;
                                newWorker.addEventListener('statechange', () => {
                                    if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                                        // New SW ready, notify user
                                        window.dispatchEvent(new CustomEvent('sw-update'));
                                    }
                                });
                            });
                        })
                        .catch(err => console.log('Service Worker registration failed', err));
                    
                    // Listen for SW messages
                    navigator.serviceWorker.addEventListener('message', event => {
                        if (event.data?.type === 'SW_UPDATED') {
                            window.dispatchEvent(new CustomEvent('sw-update'));
                        }
                    });
                });
            }
        </script>

        @stack('styles')
    </head>
    <body class="font-sans antialiased bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
        <!-- Mobile Sidebar Overlay -->
        <div x-show="sidebarMobileOpen" x-cloak 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="sidebarMobileOpen = false"
             class="fixed inset-0 z-40 bg-gray-900/50 backdrop-blur-sm lg:hidden">
        </div>

        <div class="flex h-screen overflow-hidden">
            <!-- Sidebar -->
            @include('layouts.sidebar')

            <!-- Main Content -->
            <div class="flex-1 flex flex-col overflow-hidden" :class="sidebarOpen ? 'lg:ml-72' : 'lg:ml-20'">
                <!-- Top Navigation -->
                @include('layouts.topnav')

                <!-- Page Content -->
                <main class="flex-1 overflow-y-auto p-4 lg:py-6 lg:px-4 scrollbar-thin">
                    <div class="max-w-[1600px] mx-auto animate-fade-in px-2 sm:px-4 lg:px-6">
                        @yield('content')
                    </div>
                </main>
            </div>
        </div>

        <!-- Enhanced Toast Notifications -->
        <div x-data="{ 
                show: false, 
                message: '', 
                type: 'success',
                timer: null,
                progress: 0,
                duration: 4000,
                startTimer() {
                    const step = 100;
                    const interval = (this.duration / 100);
                    this.progress = 0;
                    if (this.timer) clearInterval(this.timer);
                    this.timer = setInterval(() => {
                        this.progress += 1;
                        if (this.progress >= 100) {
                            clearInterval(this.timer);
                            this.show = false;
                        }
                    }, interval);
                }
             }" 
             x-on:notify.window="show = true; message = $event.detail.message; type = $event.detail.type || 'success'; startTimer()"
             x-show="show"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="translate-y-4 opacity-0 scale-95"
             x-transition:enter-end="translate-y-0 opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="fixed bottom-6 right-6 z-[60] group">
            
            <div :class="{
                    'bg-white dark:bg-gray-800 border-emerald-500 text-emerald-600': type === 'success',
                    'bg-white dark:bg-gray-800 border-red-500 text-red-600': type === 'error',
                    'bg-white dark:bg-gray-800 border-amber-500 text-amber-600': type === 'warning',
                    'bg-white dark:bg-gray-800 border-blue-500 text-blue-600': type === 'info'
                 }" 
                 class="min-w-[320px] max-w-md p-4 rounded-2xl shadow-[0_10px_40px_rgba(0,0,0,0.1)] dark:shadow-[0_20px_50px_rgba(0,0,0,0.3)] border-l-4 flex items-start gap-4 relative overflow-hidden group">
                
                <!-- Icon Box -->
                <div :class="{
                        'bg-emerald-100 text-emerald-600': type === 'success',
                        'bg-red-100 text-red-600': type === 'error',
                        'bg-amber-100 text-amber-600': type === 'warning',
                        'bg-blue-100 text-blue-600': type === 'info'
                     }" class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0">
                    <template x-if="type === 'success'"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></template>
                    <template x-if="type === 'error'"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></template>
                    <template x-if="type === 'warning'"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg></template>
                    <template x-if="type === 'info'"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></template>
                </div>

                <!-- Content -->
                <div class="flex-1 pr-4">
                    <h4 class="font-bold capitalize text-sm mb-1 text-gray-900 dark:text-white" x-text="type"></h4>
                    <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed font-medium" x-text="message"></p>
                </div>

                <!-- Close Button -->
                <button @click="show = false" class="absolute top-2 right-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors p-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>

                <!-- Progress Bar -->
                <div class="absolute bottom-0 left-0 h-1 bg-current opacity-20 transition-all ease-linear" :style="'width: ' + progress + '%'"></div>
            </div>
        </div>

        {{-- Tips/Tutorial AI Assistant (All Users, Dashboard Only) --}}
        @auth
            @if(request()->routeIs('dashboard'))
                @include('components.ai-assistant-v2')
            @endif
        @endauth

        @stack('scripts')
        
        <script>
            // Global Error Handling for Frontend
            window.addEventListener('error', function(event) {
                if (event.message.includes('ResizeObserver')) return; // Ignore harmless ResizeObserver errors
                window.dispatchEvent(new CustomEvent('notify', { 
                    detail: { message: "Terjadi kesalahan sistem: " + event.message, type: 'error' }
                }));
            });

            window.addEventListener('unhandledrejection', function(event) {
                window.dispatchEvent(new CustomEvent('notify', { 
                    detail: { message: "Koneksi terputus atau server gagal merespon.", type: 'error' }
                }));
            });
        </script>
        
        @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                window.dispatchEvent(new CustomEvent('notify', { 
                    detail: { message: "{{ session('success') }}", type: 'success' }
                }));
            });
        </script>
        @endif
        
        @if(session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                window.dispatchEvent(new CustomEvent('notify', { 
                    detail: { message: "{{ session('error') }}", type: 'error' }
                }));
            });
        </script>
        @endif
    {{-- WhatsApp Float (All Users, Dashboard Only) --}}
    @auth
        @if(request()->routeIs('dashboard'))
            <x-whatsapp-float />
        @endif
    @endauth

    {{-- AI Financial Assistant (Admin Role ONLY, Dashboard Only) --}}
    @auth
        @if(auth()->user()->role === 'admin' && request()->routeIs('dashboard'))
            <x-ai-financial-assistant />
        @endif
    @endauth

    {{-- PWA Install Banner & Update Notification --}}
    <x-pwa-install-banner />
    
    {{-- Push Notification Permission Modal --}}
    <x-push-notification />

</body>
</html>
