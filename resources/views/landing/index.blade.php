<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $globalSettings['coop_name'] ?? 'Koperasi Digital' }} - Masa Depan Sejahtera</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- AOS Animation CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f8fafc; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        html { scroll-padding-top: 100px; scroll-behavior: smooth; }
        
        .hero-bg {
            background: radial-gradient(circle at top right, #dcfce7 0%, transparent 50%),
                        radial-gradient(circle at bottom left, #e0f2fe 0%, transparent 50%),
                        linear-gradient(to bottom, #ffffff, #f8fafc);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.6);
        }

        .shadow-glow {
            box-shadow: 0 0 60px -15px rgba(22, 163, 74, 0.2);
        }
        
        /* Section Spacing - Mobile First */
        .section-padding {
            padding-top: 4rem;
            padding-bottom: 4rem;
        }
        
        @media (min-width: 640px) {
            .section-padding {
                padding-top: 5rem;
                padding-bottom: 5rem;
            }
        }
        
        @media (min-width: 768px) {
            .section-padding {
                padding-top: 6rem;
                padding-bottom: 6rem;
            }
        }
        
        @media (min-width: 1024px) {
            .section-padding {
                padding-top: 8rem;
                padding-bottom: 8rem;
            }
        }
        
        @media (min-width: 1280px) {
            .section-padding {
                padding-top: 10rem;
                padding-bottom: 10rem;
            }
        }
        
        /* Hero Spacing for fixed navbar */
        .hero-spacing { padding-top: 120px !important; }
        @media (min-width: 640px) {
            .hero-spacing { padding-top: 140px !important; }
        }
        @media (min-width: 1024px) {
            .hero-spacing { padding-top: 180px !important; }
        }
        
        /* Card Proportional Sizing */
        .card-feature {
            min-height: 280px;
            display: flex;
            flex-direction: column;
        }
        
        @media (min-width: 768px) {
            .card-feature {
                min-height: 320px;
            }
        }
        
        /* Container Padding */
        .container-padding {
            padding-left: 1rem;
            padding-right: 1rem;
        }
        
        @media (min-width: 640px) {
            .container-padding {
                padding-left: 1.5rem;
                padding-right: 1.5rem;
            }
        }
        
        @media (min-width: 1024px) {
            .container-padding {
                padding-left: 2rem;
                padding-right: 2rem;
            }
        }
        
        /* Smooth image loading */
        img {
            transition: opacity 0.3s ease;
        }
        
        /* Better text rendering */
        h1, h2, h3, h4, h5, h6 {
            text-rendering: optimizeLegibility;
            -webkit-font-smoothing: antialiased;
        }
    </style>
</head>
<body class="antialiased bg-white text-slate-600" x-data="{ scrolled: false, mobileMenu: false }" @scroll.window="scrolled = (window.pageYOffset > 20)">

    <!-- Navbar -->
    <nav class="fixed w-full z-50 transition-all duration-300 bg-white"
         :class="{ 'shadow-md py-4': scrolled, 'py-6 border-b border-transparent': !scrolled }">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <!-- Logo -->
                <div class="flex items-center gap-3">
                    @if(isset($globalSettings['coop_logo']) && $globalSettings['coop_logo'])
                        <img src="{{ Storage::url($globalSettings['coop_logo']) }}" class="h-10 w-auto">
                    @else
                        <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-green-500 to-emerald-700 flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-green-500/30">K</div>
                    @endif
                    <span class="font-bold text-xl text-slate-900 tracking-tight">
                        {{ $globalSettings['coop_name'] ?? 'Koperasi Modern' }}
                    </span>
                </div>
                
                <!-- Desktop Menu -->
                <div class="hidden lg:flex items-center gap-1 bg-slate-50/50 p-1.5 rounded-full border border-slate-100">
                    <a href="#home" class="px-5 py-2 rounded-full font-medium text-slate-600 hover:text-green-700 hover:bg-green-50 transition-all duration-300">Beranda</a>
                    <a href="#features" class="px-5 py-2 rounded-full font-medium text-slate-600 hover:text-green-700 hover:bg-green-50 transition-all duration-300">Fitur</a>
                    <a href="#visi-misi" class="px-5 py-2 rounded-full font-medium text-slate-600 hover:text-green-700 hover:bg-green-50 transition-all duration-300">Visi Misi</a>
                    <a href="#program-kerja" class="px-5 py-2 rounded-full font-medium text-slate-600 hover:text-green-700 hover:bg-green-50 transition-all duration-300">Program</a>
                    <a href="#struktur" class="px-5 py-2 rounded-full font-medium text-slate-600 hover:text-green-700 hover:bg-green-50 transition-all duration-300">Pengurus</a>
                    <a href="#contact" class="px-5 py-2 rounded-full font-medium text-slate-600 hover:text-green-700 hover:bg-green-50 transition-all duration-300">Kontak</a>
                </div>

                <!-- Auth Buttons -->
                <div class="hidden lg:flex items-center gap-4 ml-6">
                    @auth
                        <a href="{{ route('dashboard') }}" class="px-6 py-2.5 rounded-full bg-gradient-to-r from-green-600 to-emerald-600 text-white font-semibold shadow-lg shadow-green-200 hover:shadow-green-500/40 hover:-translate-y-0.5 transition-all duration-300">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-5 py-2.5 font-semibold text-slate-600 hover:text-green-600 transition-colors relative group">
                            <span class="relative z-10">Masuk</span>
                            <span class="absolute bottom-1 left-1/2 w-0 h-0.5 bg-green-500 transition-all duration-300 group-hover:w-full group-hover:left-0"></span>
                        </a>
                        <a href="{{ route('register') }}" class="px-7 py-2.5 rounded-full bg-slate-900 text-white font-semibold hover:bg-green-600 hover:shadow-xl hover:shadow-green-500/30 hover:-translate-y-0.5 transition-all duration-300 transform">
                            Daftar
                        </a>
                    @endauth
                </div>

                <!-- Mobile Toggle -->
                <button @click="mobileMenu = !mobileMenu" class="lg:hidden text-slate-700">
                    <svg x-show="!mobileMenu" class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    <svg x-show="mobileMenu" class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu with Transition -->
        <div x-show="mobileMenu" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="lg:hidden absolute top-full left-0 w-full bg-white/95 backdrop-blur-md shadow-lg py-6 px-6 flex flex-col gap-3 border-t border-slate-100">
            <a href="#home" @click="mobileMenu=false" class="text-lg font-medium text-slate-800">Beranda</a>
            <a href="#features" @click="mobileMenu=false" class="text-lg font-medium text-slate-800">Fitur</a>
            <a href="#visi-misi" @click="mobileMenu=false" class="text-lg font-medium text-slate-800">Visi & Misi</a>
            <a href="#program-kerja" @click="mobileMenu=false" class="text-lg font-medium text-slate-800">Program Kerja</a>
            <a href="#struktur" @click="mobileMenu=false" class="text-lg font-medium text-slate-800">Pengurus</a>
            <a href="#contact" @click="mobileMenu=false" class="text-lg font-medium text-slate-800">Kontak</a>
            <hr class="border-slate-200">
            <a href="{{ route('login') }}" class="text-center py-3 rounded-xl bg-slate-100 font-bold text-slate-700">Masuk</a>
            <a href="{{ route('register') }}" class="text-center py-3 rounded-xl bg-green-600 text-white font-bold">Daftar Anggota</a>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="relative hero-spacing pb-20 lg:pb-32 overflow-hidden hero-bg">
        <!-- Background Blobs -->
        <div class="absolute top-0 right-0 -translate-y-1/4 translate-x-1/4 w-[500px] h-[500px] bg-green-200/30 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 translate-y-1/4 -translate-x-1/4 w-[500px] h-[500px] bg-blue-200/30 rounded-full blur-3xl"></div>

        <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                <!-- Text Content -->
                <div class="text-center lg:text-left" data-aos="fade-up" data-aos-duration="1000">
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-green-100 shadow-sm text-green-700 text-sm font-bold mb-8 animate-bounce" style="animation-duration: 3s;">
                        <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                        Platform Koperasi Digital #1
                    </div>
                    <h1 class="text-5xl lg:text-7xl font-extrabold text-slate-900 leading-[1.1] mb-6 tracking-tight">
                        {{ $globalSettings['landing_hero_title'] ?? 'Solusi Keuangan Modern.' }}
                    </h1>
                    <p class="text-lg lg:text-xl text-slate-600 mb-10 leading-relaxed max-w-2xl mx-auto lg:mx-0">
                        {{ $globalSettings['landing_hero_subtitle'] ?? 'Bergabunglah dengan koperasi kami untuk masa depan finansial yang lebih baik, aman, dan transparan.' }}
                    </p>
                    <div class="flex flex-col sm:flex-row gap-5 justify-center lg:justify-start">
                        <a href="{{ route('register') }}" class="inline-flex justify-center items-center px-8 py-4 rounded-full bg-green-600 text-white font-bold text-lg hover:bg-green-700 transition-all shadow-xl shadow-green-600/30 hover:-translate-y-1">
                            Mulai Sekarang
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                        </a>
                        <a href="#features" class="inline-flex justify-center items-center px-8 py-4 rounded-full bg-white text-slate-700 font-bold text-lg hover:text-green-600 border border-slate-200 transition-all shadow-sm hover:shadow-lg hover:-translate-y-1">
                            Pelajari Dulu
                        </a>
                    </div>
                </div>

                <!-- Hero Image -->
                <div class="relative mx-auto lg:mr-0 max-w-lg lg:max-w-none perspective-1000" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="200">
                     <div class="relative rounded-2xl bg-white p-2 shadow-2xl shadow-slate-200/50 transform rotate-2 hover:rotate-0 transition-all duration-700 hover:scale-[1.02]">
                        @if(isset($globalSettings['landing_hero_image']) && $globalSettings['landing_hero_image'])
                            <img src="{{ Storage::url($globalSettings['landing_hero_image']) }}" class="rounded-xl w-full h-auto object-cover" alt="Hero Image">
                        @else
                            <img src="https://images.unsplash.com/photo-1579621970563-ebec7560ff3e?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" class="rounded-xl w-full h-auto object-cover" alt="App Preview">
                        @endif
                        
                        <!-- Floating Stat Card -->
                        <div class="absolute -bottom-10 -left-6 bg-white p-5 rounded-2xl shadow-xl border border-slate-100 flex items-center gap-4 animate-bounce" style="animation-duration: 4s;">
                            <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center text-green-600">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <p class="text-sm text-slate-500 font-medium">Status Keamanan</p>
                                <p class="text-xl font-bold text-slate-900">Terverifikasi</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Wave Divider -->
        <div class="absolute bottom-0 left-0 right-0 translate-y-1">
            <svg class="w-full h-auto" viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0 64L80 69.3C160 75 320 85 480 80C640 75 800 53 960 48C1120 43 1280 53 1360 58.7L1440 64V320H1360C1280 320 1120 320 960 320C800 320 640 320 480 320C320 320 160 320 80 320H0V64Z" fill="white"/>
            </svg>
        </div>
    </section>

    <!-- Stats Section with Counter -->
    <!-- Stats Section with Counter -->
    <section class="py-12 bg-white -mt-1 relative z-20" 
             x-data="{ 
                 members: 0, 
                 products: 0, 
                 transactions: 0,
                 animate(target, key) {
                     let current = 0;
                     const increment = Math.ceil(target / 50);
                     const timer = setInterval(() => {
                         current += increment;
                         if (current >= target) {
                             current = target;
                             clearInterval(timer);
                         }
                         this[key] = current;
                     }, 20);
                 }
             }" 
             x-init="
                 setTimeout(() => {
                     animate({{ $stats['members'] }}, 'members');
                     animate({{ $stats['products'] }}, 'products');
                     animate({{ $stats['transactions'] }}, 'transactions');
                 }, 500)
             ">
        <div class="max-w-7xl mx-auto container-padding">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 text-center divide-x divide-slate-100 rounded-3xl bg-white shadow-xl shadow-slate-200 p-8 sm:p-10 border border-slate-100" data-aos="fade-up">
                <div class="space-y-2 p-2">
                    <p class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-green-600 to-emerald-600" x-text="members">0</p>
                    <p class="text-slate-500 font-semibold tracking-wide text-xs sm:text-sm">ANGGOTA AKTIF</p>
                </div>
                <div class="space-y-2 p-2">
                    <p class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-green-600 to-emerald-600" x-text="products">0</p>
                    <p class="text-slate-500 font-semibold tracking-wide text-xs sm:text-sm">PRODUK MART</p>
                </div>
                <div class="space-y-2 p-2">
                    <p class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-green-600 to-emerald-600" x-text="transactions">0</p>
                    <p class="text-slate-500 font-semibold tracking-wide text-xs sm:text-sm">TRANSAKSI</p>
                </div>
                <div class="space-y-2 p-2">
                    <p class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-green-600 to-emerald-600">24/7</p>
                    <p class="text-slate-500 font-semibold tracking-wide text-xs sm:text-sm">SUPPORT</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="section-padding bg-white relative">
        <div class="absolute top-0 left-0 w-full h-full bg-[radial-gradient(#e5e7eb_1px,transparent_1px)] [background-size:20px_20px] [mask-image:radial-gradient(ellipse_60%_50%_at_50%_50%,#000_70%,transparent_100%)] opacity-20"></div>
        
        <div class="max-w-7xl mx-auto container-padding relative z-10">
            <div class="text-center max-w-3xl mx-auto mb-12 md:mb-16 lg:mb-20" data-aos="fade-up">
                <span class="inline-block py-1.5 px-4 rounded-full bg-green-50 text-green-600 font-bold text-xs sm:text-sm mb-4 tracking-wider">FITUR UNGGULAN</span>
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 mb-4 sm:mb-6">Semua Kebutuhan dalam Satu Aplikasi</h2>
                <p class="text-slate-600 text-base sm:text-lg lg:text-xl leading-relaxed px-4">Kami menghadirkan teknologi terkini untuk mempermudah setiap aspek kebutuhan finansial dan harian Anda.</p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8 lg:gap-10">
                @php
                    $features = [
                        [
                            'color' => 'green',
                            'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                            'default_title' => 'Simpan Pinjam',
                            'default_desc' => 'Kelola simpanan pokok dan wajib serta ajukan pinjaman dengan proses cepat, bunga kompetitif, dan transparan.',
                        ],
                        [
                            'color' => 'blue',
                            'icon' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z',
                            'default_title' => 'Toko Digital',
                            'default_desc' => 'Marketplace internal untuk anggota. Belanja kebutuhan harian dengan harga spesial dan pembayaran potong saldo otomatis.',
                        ],
                        [
                            'color' => 'orange',
                            'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
                            'default_title' => 'Laporan Real-time',
                            'default_desc' => 'Akses laporan keuangan pribadi, SHU, dan riwayat transaksi Anda secara real-time kapan saja dan di mana saja.',
                        ],
                    ];
                @endphp

                @foreach($features as $index => $feature)
                @php
                    $num = $index + 1;
                    $title = $globalSettings["landing_feature{$num}_title"] ?? $feature['default_title'];
                    $desc = $globalSettings["landing_feature{$num}_desc"] ?? $feature['default_desc'];
                    $image = $globalSettings["landing_feature{$num}_image"] ?? null;
                @endphp
                <div class="card-feature bg-white p-6 sm:p-8 lg:p-10 rounded-2xl sm:rounded-[2rem] border border-slate-100 shadow-lg hover:shadow-2xl hover:shadow-{{ $feature['color'] }}-500/10 transition-all duration-300 transform hover:-translate-y-2 group" data-aos="fade-up" data-aos-delay="{{ ($index + 1) * 100 }}">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 lg:w-16 lg:h-16 rounded-xl sm:rounded-2xl bg-{{ $feature['color'] }}-50 text-{{ $feature['color'] }}-600 flex items-center justify-center mb-5 sm:mb-6 lg:mb-8 group-hover:scale-110 transition-transform duration-300 overflow-hidden flex-shrink-0">
                        @if($image)
                            <img src="{{ Storage::url($image) }}" alt="{{ $title }}" class="w-full h-full object-contain p-2">
                        @else
                            <svg class="w-6 h-6 sm:w-7 sm:h-7 lg:w-8 lg:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $feature['icon'] }}"></path></svg>
                        @endif
                    </div>
                    <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-slate-900 mb-3 sm:mb-4 group-hover:text-{{ $feature['color'] }}-600 transition-colors">{{ $title }}</h3>
                    <p class="text-slate-600 leading-relaxed text-sm sm:text-base lg:text-lg flex-grow">{{ $desc }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="section-padding bg-slate-50 relative overflow-hidden">
        <!-- Decoration -->
        <div class="absolute -right-20 top-40 w-64 sm:w-80 lg:w-96 h-64 sm:h-80 lg:h-96 bg-green-200/20 rounded-full blur-3xl"></div>

        <div class="max-w-7xl mx-auto container-padding relative z-10">
            <div class="flex flex-col lg:flex-row items-center gap-10 md:gap-16 lg:gap-20">
                <div class="lg:w-1/2 relative" data-aos="fade-right">
                    <div class="relative rounded-[2rem] overflow-hidden shadow-2xl ring-8 ring-white transform rotate-3 hover:rotate-0 transition-all duration-700">
                        @if(isset($globalSettings['landing_about_image']) && $globalSettings['landing_about_image'])
                            <img src="{{ Storage::url($globalSettings['landing_about_image']) }}" alt="About Us" class="w-full object-cover h-[600px] hover:scale-110 transition-transform duration-700">
                        @else
                            <img src="https://images.unsplash.com/photo-1556761175-5973dc0f32e7?ixlib=rb-4.0.3&auto=format&fit=crop&w=1632&q=80" alt="Team" class="w-full object-cover h-[600px] hover:scale-110 transition-transform duration-700">
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent flex items-end p-8">
                            <div class="text-white">
                                <p class="font-bold text-2xl">Profesionalitas Tinggi</p>
                                <p class="text-slate-200">Melayani dengan sepenuh hati</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="lg:w-1/2" data-aos="fade-left">
                    <span class="inline-block py-1 px-3 rounded-full bg-blue-50 text-blue-600 font-bold text-sm mb-4 tracking-wider">TENTANG KAMI</span>
                    @php
                        $aboutTitle = $globalSettings['landing_about_title'] ?? 'Bersama Membangun Kesejahteraan.';
                        $titleParts = explode(' ', $aboutTitle);
                        $lastWord = array_pop($titleParts);
                        $firstPart = implode(' ', $titleParts);
                    @endphp
                    <h2 class="text-4xl lg:text-5xl font-extrabold text-slate-900 mb-8 leading-tight">{{ $firstPart }} <span class="text-green-600">{{ $lastWord }}</span></h2>
                    <div class="prose prose-lg text-slate-600 mb-10">
                        <p class="mb-6 leading-relaxed">
                            {{ $globalSettings['landing_about_text'] ?? 'Koperasi ini didirikan dengan tujuan utama meningkatkan kesejahteraan anggota melalui gotong royong dan pengelolaan ekonomi yang profesional, transparan, dan akuntabel.' }}
                        </p>
                        <div class="space-y-4">
                            <div class="flex items-start gap-4 p-4 rounded-xl bg-white shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
                                <div class="w-10 h-10 rounded-full bg-green-100 text-green-600 flex items-center justify-center flex-shrink-0 mt-1">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-900">{{ $globalSettings['landing_about_highlight1_title'] ?? 'Badan Hukum Resmi & Terdaftar' }}</h4>
                                    <p class="text-sm text-slate-500">{{ $globalSettings['landing_about_highlight1_desc'] ?? 'Legalitas terjamin di bawah kementerian koperasi.' }}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4 p-4 rounded-xl bg-white shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
                                <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0 mt-1">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-900">{{ $globalSettings['landing_about_highlight2_title'] ?? 'Sistem Pengelolaan Profesional' }}</h4>
                                    <p class="text-sm text-slate-500">{{ $globalSettings['landing_about_highlight2_desc'] ?? 'Dikelola oleh tim ahli dengan standar audit terpercaya.' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <a href="#" class="inline-flex font-bold text-green-600 hover:text-green-700 items-center gap-2 group">
                        Baca Selengkapnya
                        <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Visi Misi Section -->
    <section id="visi-misi" class="section-padding bg-white relative overflow-hidden">
        <!-- Floating Decor - Simplified -->
        <div class="absolute -left-20 top-40 w-80 h-80 bg-blue-50/50 rounded-full blur-3xl"></div>
        <div class="absolute -right-20 bottom-40 w-80 h-80 bg-green-50/50 rounded-full blur-3xl"></div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-3xl mx-auto mb-16 lg:mb-24" data-aos="fade-up">
                <span class="inline-block py-1.5 px-4 rounded-full bg-blue-50 text-blue-600 font-bold text-xs sm:text-sm mb-4 tracking-wider uppercase">VISI & MISI</span>
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 mb-6 leading-tight">Arah dan Tujuan Kami</h2>
                <div class="w-20 h-1.5 bg-green-500 mx-auto rounded-full"></div>
                <p class="mt-8 text-slate-600 text-base sm:text-lg lg:text-xl leading-relaxed">Komitmen kami untuk mencapai kesejahteraan bersama melalui tata kelola yang profesional dan inovatif.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 max-w-6xl mx-auto items-stretch">
                <!-- Visi -->
                <div class="group bg-white p-10 sm:p-14 rounded-2xl sm:rounded-[2rem] border border-slate-100 shadow-lg hover:shadow-2xl hover:shadow-green-500/10 transition-all duration-500 flex flex-col items-center text-center relative" data-aos="fade-right">
                    <!-- Icon Box - More Solid for better visibility -->
                    <div class="w-24 h-24 rounded-3xl bg-green-600 text-white flex items-center justify-center mb-10 shadow-xl shadow-green-500/30 group-hover:scale-110 transition-transform duration-500">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    </div>
                    
                    <h3 class="text-3xl font-extrabold text-slate-900 mb-8 tracking-tight">Visi</h3>
                    <div class="relative">
                        <svg class="absolute -top-4 -left-6 w-8 h-8 text-slate-100" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21L14.017 18C14.017 16.899 15.116 16 16.217 16H19.217C19.769 16 20.217 15.552 20.217 15V11H14.017V4H21.017V15C21.017 18.314 18.331 21 15.017 21H14.017ZM3 21V18C3 16.899 4.1 16 5.2 16H8.2C8.752 16 9.2 15.552 9.2 15V11H3V4H10V15C10 18.314 7.316 21 4 21H3Z"></path></svg>
                        <p class="text-lg text-slate-600 leading-relaxed font-semibold px-4 italic">
                            "{{ $globalSettings['landing_visi'] ?? 'Menjadi koperasi terdepan dan terpercaya dalam meningkatkan kesejahteraan ekonomi anggota melalui pelayanan prima, inovasi digital, dan pengelolaan yang transparan.' }}"
                        </p>
                    </div>
                </div>

                <!-- Misi -->
                <div class="group bg-white p-10 sm:p-14 rounded-2xl sm:rounded-[2rem] border border-slate-100 shadow-lg hover:shadow-2xl hover:shadow-blue-500/10 transition-all duration-500 flex flex-col relative" data-aos="fade-left">
                    <div class="flex flex-col items-center mb-10">
                        <div class="w-24 h-24 rounded-3xl bg-blue-600 text-white flex items-center justify-center shadow-xl shadow-blue-500/30 group-hover:scale-110 transition-transform duration-500">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        </div>
                    </div>

                    <h3 class="text-3xl font-extrabold text-slate-900 mb-10 text-center text-blue-600 uppercase tracking-widest text-sm font-bold">Langkah Strategis</h3>
                    <h3 class="text-3xl font-extrabold text-slate-900 mb-10 text-center">Misi</h3>
                    
                    <ul class="space-y-6 flex-grow">
                        @php
                            $misiItems = isset($globalSettings['landing_misi']) && $globalSettings['landing_misi'] 
                                ? explode("\n", $globalSettings['landing_misi']) 
                                : ['Memberikan pelayanan prima kepada seluruh anggota.', 'Mengelola keuangan secara transparan dan akuntabel.', 'Meningkatkan kesejahteraan anggota melalui program-program inovatif.', 'Membangun ekosistem digital yang modern dan mudah diakses.'];
                        @endphp
                        @foreach($misiItems as $misi)
                            @if(trim($misi))
                            <li class="flex items-start gap-4 group/item">
                                <div class="w-7 h-7 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0 mt-0.5 group-hover/item:bg-blue-600 group-hover/item:text-white transition-all duration-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <span class="text-lg text-slate-600 leading-relaxed font-semibold transition-colors duration-300 group-hover/item:text-slate-900">{{ trim($misi) }}</span>
                            </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Program Kerja Section -->
    <section id="program-kerja" class="section-padding bg-slate-50 relative overflow-hidden">
        <!-- Floating Decor -->
        <div class="absolute -left-20 top-40 w-80 h-80 bg-orange-50/50 rounded-full blur-3xl"></div>
        <div class="absolute -right-20 bottom-40 w-80 h-80 bg-purple-50/50 rounded-full blur-3xl"></div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-3xl mx-auto mb-16 lg:mb-24" data-aos="fade-up">
                <span class="inline-block py-1.5 px-4 rounded-full bg-orange-50 text-orange-600 font-bold text-xs sm:text-sm mb-4 tracking-wider uppercase">PROGRAM KERJA</span>
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 mb-6 leading-tight">Rencana Strategis Kami</h2>
                <div class="w-20 h-1.5 bg-orange-500 mx-auto rounded-full"></div>
                <p class="mt-8 text-slate-600 text-base sm:text-lg lg:text-xl leading-relaxed">Program-program unggulan untuk kemajuan bersama dan kesejahteraan anggota.</p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8 lg:gap-10 max-w-6xl mx-auto">
                @php
                    $defaultIcons = ['M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z', 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z', 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'];
                    $colorMap = [
                        'green' => ['bg' => 'bg-green-600', 'shadow' => 'shadow-green-500/30'],
                        'blue' => ['bg' => 'bg-blue-600', 'shadow' => 'shadow-blue-500/30'],
                        'purple' => ['bg' => 'bg-purple-600', 'shadow' => 'shadow-purple-500/30'],
                        'orange' => ['bg' => 'bg-orange-600', 'shadow' => 'shadow-orange-500/30'],
                        'teal' => ['bg' => 'bg-teal-600', 'shadow' => 'shadow-teal-500/30'],
                        'pink' => ['bg' => 'bg-pink-600', 'shadow' => 'shadow-pink-500/30'],
                    ];
                @endphp
                
                @if(isset($workPrograms) && $workPrograms->count() > 0)
                    @foreach($workPrograms as $index => $program)
                    @php
                        $color = $program->color ?? 'blue';
                        $bgClass = $colorMap[$color]['bg'] ?? 'bg-blue-600';
                        $shadowClass = $colorMap[$color]['shadow'] ?? 'shadow-blue-500/30';
                    @endphp
                    <div class="group bg-white p-10 sm:p-12 rounded-2xl sm:rounded-[2rem] border border-slate-100 shadow-lg hover:shadow-2xl hover:shadow-{{ $color }}-500/10 transition-all duration-500 flex flex-col items-center text-center" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                        <!-- Icon Box -->
                        <div class="{{ $bgClass }} text-white w-20 h-20 rounded-3xl flex items-center justify-center mb-8 shadow-xl {{ $shadowClass }} group-hover:scale-110 transition-transform duration-500 overflow-hidden">
                            @if($program->icon && Storage::disk('public')->exists($program->icon))
                                <img src="{{ Storage::url($program->icon) }}" alt="{{ $program->title }}" class="w-12 h-12 object-contain">
                            @else
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $defaultIcons[$index % count($defaultIcons)] }}"></path></svg>
                            @endif
                        </div>
                        
                        <h4 class="text-xl font-bold text-slate-900 leading-tight mb-3">{{ $program->title }}</h4>
                        @if($program->description)
                            <p class="text-slate-600 text-sm leading-relaxed">{{ $program->description }}</p>
                        @endif
                    </div>
                    @endforeach
                @else
                    <!-- Fallback jika belum ada data -->
                    <div class="col-span-full text-center py-16">
                        <div class="w-20 h-20 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-6">
                            <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-2">Belum Ada Program Kerja</h3>
                        <p class="text-slate-600">Program kerja akan ditampilkan di sini setelah ditambahkan dari halaman pengaturan.</p>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Struktur Organisasi Section -->
    <section id="struktur" class="section-padding bg-white relative overflow-hidden">
        <div class="absolute -right-20 bottom-20 w-64 sm:w-80 lg:w-96 h-64 sm:h-80 lg:h-96 bg-teal-200/20 rounded-full blur-3xl"></div>
        
        <div class="max-w-7xl mx-auto container-padding relative z-10">
            <div class="text-center max-w-3xl mx-auto mb-10 md:mb-14 lg:mb-16" data-aos="fade-up">
                <span class="inline-block py-1.5 px-4 rounded-full bg-teal-50 text-teal-600 font-bold text-xs sm:text-sm mb-4 tracking-wider">STRUKTUR ORGANISASI</span>
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 mb-4 sm:mb-6">Pengurus Koperasi</h2>
                <p class="text-slate-600 text-base sm:text-lg lg:text-xl leading-relaxed px-4">Tim profesional yang berkomitmen untuk melayani anggota.</p>
            </div>

            <!-- Dynamic Team Grid -->
            @if(isset($teamMembers) && $teamMembers->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    @foreach($teamMembers as $index => $member)
                        <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden group hover:-translate-y-2 transition-all duration-300" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                            <!-- Image Container -->
                            <div class="aspect-[3/3.5] w-full bg-slate-100 relative overflow-hidden">
                                <img src="{{ Storage::url($member->image) }}" alt="{{ $member->name }}" class="w-full h-full object-cover object-top hover:scale-105 transition-transform duration-500">
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            </div>
                            
                            <!-- Content -->
                            <div class="p-6 text-center">
                                <h3 class="text-xl font-bold text-slate-900 group-hover:text-green-600 transition-colors mb-1">{{ $member->name }}</h3>
                                <p class="text-green-600 font-bold text-xs uppercase tracking-wider mb-4">{{ $member->role }}</p>
                                
                                @if($member->bio)
                                    <p class="text-slate-500 text-sm mb-6 leading-relaxed line-clamp-3">
                                        {{ $member->bio }}
                                    </p>
                                @endif
                                
                                <!-- Social Links -->
                                <div class="flex justify-center gap-4">
                                    @if($member->twitter_link)
                                        <a href="{{ $member->twitter_link }}" target="_blank" class="text-slate-400 hover:text-[#1DA1F2] transition-colors"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg></a>
                                    @endif
                                    @if($member->facebook_link)
                                        <a href="{{ $member->facebook_link }}" target="_blank" class="text-slate-400 hover:text-[#1877F2] transition-colors"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/></svg></a>
                                    @endif
                                    @if($member->instagram_link)
                                        <a href="{{ $member->instagram_link }}" target="_blank" class="text-slate-400 hover:text-[#E4405F] transition-colors"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg></a>
                                    @endif
                                    @if($member->linkedin_link)
                                        <a href="{{ $member->linkedin_link }}" target="_blank" class="text-slate-400 hover:text-[#0077B5] transition-colors"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M4.98 3.5c0 1.381-1.11 2.5-2.48 2.5s-2.48-1.119-2.48-2.5c0-1.38 1.11-2.5 2.48-2.5s2.48 1.12 2.48 2.5zm.02 4.5h-5v16h5v-16zm7.982 0h-4.968v16h4.969v-8.399c0-4.67 6.029-5.052 6.029 0v8.399h4.988v-10.131c0-7.88-8.922-7.593-11.018-3.714v-2.155z"/></svg></a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Fallback if no members are added yet (e.g. initial demo state) -->
                <div class="flex justify-center" data-aos="zoom-in">
                    <div class="bg-white p-6 sm:p-10 rounded-2xl sm:rounded-3xl shadow-2xl shadow-slate-200/50 border border-slate-100 w-full max-w-5xl text-center py-20 text-slate-400 bg-slate-50">
                        <svg class="w-20 h-20 mx-auto mb-6 opacity-30 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        <h4 class="text-xl font-bold text-slate-600 mb-2">Belum ada data Pengurus</h4>
                        <p class="text-slate-500">Silakan tambahkan data pengurus melalui Login Admin > Pengaturan Landing Page.</p>
                    </div>
                </div>
            @endif
        </div>
    </section>

    <!-- CTA Section -->
    <section class="section-padding relative overflow-hidden">
        <div class="absolute inset-0 bg-slate-900"></div>
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
        <div class="absolute top-0 right-0 w-64 sm:w-80 lg:w-96 h-64 sm:h-80 lg:h-96 bg-green-500/20 blur-3xl rounded-full"></div>
        
        <div class="max-w-4xl mx-auto container-padding relative z-10 text-center" data-aos="zoom-in">
            <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl xl:text-6xl font-extrabold text-white mb-4 sm:mb-6 lg:mb-8 tracking-tight leading-tight">{{ $globalSettings['landing_cta_title'] ?? 'Siap Memulai Perjalanan Finansial Anda?' }}</h2>
            <p class="text-slate-300 text-base sm:text-lg lg:text-xl mb-8 sm:mb-10 lg:mb-12 max-w-2xl mx-auto px-4">{{ $globalSettings['landing_cta_subtitle'] ?? 'Bergabunglah dengan ribuan anggota lainnya yang telah merasakan manfaat dari ekosistem koperasi modern kami.' }}</p>
            <div class="flex flex-col sm:flex-row gap-4 sm:gap-6 justify-center">
                <a href="{{ route('register') }}" class="px-6 sm:px-8 lg:px-10 py-3 sm:py-4 lg:py-5 rounded-full bg-green-500 text-white font-bold text-base sm:text-lg lg:text-xl hover:bg-green-400 hover:shadow-[0_0_30px_rgba(74,222,128,0.5)] transition-all transform hover:-translate-y-1">
                    Daftar Sekarang Gratis
                </a>
                <a href="#contact" class="px-6 sm:px-8 lg:px-10 py-3 sm:py-4 lg:py-5 rounded-full border border-slate-600 text-white font-bold text-base sm:text-lg lg:text-xl hover:bg-white/10 transition-all">
                    Hubungi Kami
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contact" class="bg-white border-t border-slate-100 pt-12 sm:pt-16 lg:pt-24 pb-8 sm:pb-10 lg:pb-12">
        <div class="max-w-7xl mx-auto container-padding">
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-12 xl:gap-20 mb-10 lg:mb-16">
                <div class="sm:col-span-2">
                    <div class="flex items-center gap-3 mb-8">
                        @if(isset($globalSettings['coop_logo']) && $globalSettings['coop_logo'])
                            <img src="{{ Storage::url($globalSettings['coop_logo']) }}" class="h-10 w-auto">
                        @else
                            <div class="h-10 w-10 rounded-xl bg-green-600 flex items-center justify-center text-white font-bold text-xl">K</div>
                        @endif
                        <span class="text-2xl font-bold text-slate-900">{{ $globalSettings['coop_name'] ?? 'Koperasi' }}</span>
                    </div>
                    <p class="text-slate-500 mb-8 max-w-sm text-lg leading-relaxed">{{ $globalSettings['landing_footer_desc'] ?? 'Platform koperasi digital terpercaya yang mengutamakan kesejahteraan anggota melalui inovasi teknologi dan pelayanan prima.' }}</p>
                    <div class="flex gap-4">
                        @if(isset($globalSettings['landing_social_twitter']) && $globalSettings['landing_social_twitter'])
                        <a href="{{ $globalSettings['landing_social_twitter'] }}" target="_blank" class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 hover:bg-green-500 hover:text-white transition-all">
                           <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                        </a>
                        @endif
                        @if(isset($globalSettings['landing_social_facebook']) && $globalSettings['landing_social_facebook'])
                        <a href="{{ $globalSettings['landing_social_facebook'] }}" target="_blank" class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 hover:bg-blue-600 hover:text-white transition-all">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/></svg>
                        </a>
                        @endif
                        @if(isset($globalSettings['landing_social_instagram']) && $globalSettings['landing_social_instagram'])
                        <a href="{{ $globalSettings['landing_social_instagram'] }}" target="_blank" class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 hover:bg-pink-600 hover:text-white transition-all">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        </a>
                        @endif
                    </div>
                </div>
                
                <div>
                    <h4 class="font-extrabold text-slate-900 mb-8">Tautan Cepat</h4>
                    <ul class="space-y-4 text-slate-600">
                        <li><a href="#home" class="hover:text-green-600 hover:pl-2 transition-all">Beranda</a></li>
                        <li><a href="#features" class="hover:text-green-600 hover:pl-2 transition-all">Fitur Layanan</a></li>
                        <li><a href="#about" class="hover:text-green-600 hover:pl-2 transition-all">Tentang Kami</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-green-600 hover:pl-2 transition-all">Login Anggota</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-extrabold text-slate-900 mb-8">Hubungi Kami</h4>
                    <ul class="space-y-6 text-slate-600">
                        <li class="flex items-start gap-4 group">
                            <div class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center text-green-600 group-hover:bg-green-600 group-hover:text-white transition-colors flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </div>
                            <span class="mt-2">{{ $globalSettings['coop_address'] ?? 'Alamat Belum Diatur' }}</span>
                        </li>
                        <li class="flex items-center gap-4 group">
                             <div class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center text-green-600 group-hover:bg-green-600 group-hover:text-white transition-colors flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            </div>
                            <span>{{ $globalSettings['coop_phone'] ?? '-' }}</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-slate-200 pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-sm text-slate-500">
                <div>&copy; {{ date('Y') }} {{ $globalSettings['coop_name'] ?? 'Koperasi' }}. All rights reserved.</div>
                <div>Created with <span class="text-red-500">♥</span> by Koperasi Digital</div>
            </div>
        </div>
    </footer>

    <!-- AOS Script -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            once: true,
            offset: 50,
            duration: 800,
        });
    </script>
    <!-- WhatsApp Float -->
    <x-whatsapp-float />

</body>
</html>
