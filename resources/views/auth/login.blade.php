<!DOCTYPE html>
<html lang="id" x-data="authPage()" x-init="init()" :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title x-text="isLogin ? 'Login - Koperasi SKF' : 'Daftar - Koperasi SKF'">Login - Koperasi Karyawan SKF</title>
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#059669">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Koperasi">
    <meta name="application-name" content="Koperasi">
    <meta name="format-detection" content="telephone=no">
    <meta name="description" content="Koperasi Digital - Login & Pendaftaran Anggota">
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/icons/icon-192x192.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/icons/icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/icons/icon-192x192.png">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/favicon.png">

    <!-- Tesseract.js for OCR -->
    <script src='https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js'></script>
    
    <style>
        body { font-family: ui-sans-serif, system-ui, sans-serif; }
        .text-spindo { color: #0054a6; }
        .dark .text-spindo { color: #60a5fa; }
        .bg-spindo { background-color: #0054a6; }
        .bg-green-spindo { background-color: #009640; }
        .bg-green-spindo:hover { background-color: #007a33; }
        
        /* Card Container */
        .auth-container {
            perspective: 1500px;
        }
        
        /* Flip Animation */
        .auth-card {
            transition: transform 0.8s cubic-bezier(0.4, 0, 0.2, 1);
            transform-style: preserve-3d;
        }
        .auth-card.flipped {
            transform: rotateY(180deg);
        }
        .card-face {
            backface-visibility: hidden;
            -webkit-backface-visibility: hidden;
        }
        .card-back {
            transform: rotateY(180deg);
        }
        
        /* Slide Animation for Steps */
        .slide-enter { animation: slideIn 0.4s ease-out; }
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(40px); }
            to { opacity: 1; transform: translateX(0); }
        }
        
        /* Step Indicator */
        .step-dot {
            @apply w-3 h-3 rounded-full transition-all duration-300;
        }
        .step-dot.active { @apply bg-green-500 scale-125; }
        .step-dot.completed { @apply bg-green-500; }
        .step-dot.pending { @apply bg-gray-300 dark:bg-gray-600; }
        
        /* Floating Animation */
        .float-animation {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        /* Glow Effect */
        .glow-green {
            box-shadow: 0 0 30px rgba(0, 150, 64, 0.3);
        }
        
        /* Background Pattern */
        .bg-pattern {
            background-image: radial-gradient(circle at 1px 1px, rgba(0,0,0,0.05) 1px, transparent 0);
            background-size: 40px 40px;
        }
        .dark .bg-pattern {
            background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,0.03) 1px, transparent 0);
        }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-center items-center bg-gradient-to-br from-gray-50 via-white to-gray-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 bg-pattern transition-colors duration-500">
    
    <!-- Dark Mode & Install Toggle -->
    <div class="fixed top-4 right-4 flex gap-2 z-50" x-data="{ canInstall: false }" @pwa-installable.window="canInstall = true">
        <button 
            x-show="canInstall" 
            @click="window.installPWA()" 
            class="p-3 rounded-xl bg-green-600 text-white shadow-lg hover:bg-green-700 hover:scale-105 transition-all duration-300 flex items-center gap-2 px-4"
            style="display: none;"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
            </svg>
            <span class="text-sm font-bold uppercase tracking-tight hidden sm:inline">Install App</span>
        </button>

        <button @click="darkMode = !darkMode" class="p-3 rounded-xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300">
            <svg x-show="!darkMode" class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
            </svg>
            <svg x-show="darkMode" class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
        </button>
    </div>

    <!-- Logo -->
    <div class="mb-6 text-center float-animation">
        @if(isset($globalSettings['coop_logo']) && $globalSettings['coop_logo'])
            <img src="{{ Storage::url($globalSettings['coop_logo']) }}" alt="{{ $globalSettings['coop_name'] ?? 'Logo' }}" class="h-24 w-auto mx-auto mb-4 object-contain shadow-lg rounded-full bg-white dark:bg-gray-800 p-2 ring-4 ring-white/50 dark:ring-gray-700/50">
        @else
            <div class="w-24 h-24 mx-auto mb-4 rounded-full bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center text-white font-bold text-3xl shadow-lg ring-4 ring-white/50 dark:ring-gray-700/50">
                {{ strtoupper(substr($globalSettings['coop_name'] ?? 'K', 0, 1)) }}
            </div>
        @endif
        <div id="text-logo" style="{{ isset($globalSettings['coop_logo']) && $globalSettings['coop_logo'] ? 'display:none;' : '' }}" class="mb-4">
            <h1 class="text-3xl font-bold text-spindo">{{ $globalSettings['coop_name'] ?? 'KOPERASI SKF' }}</h1>
        </div>
        <h2 class="text-xl font-bold text-gray-700 dark:text-gray-200">Koperasi Karyawan</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400">PT. SPINDO TBK</p>
    </div>

    <!-- Auth Container -->
    <div class="auth-container w-full max-w-lg px-4">
        <div class="auth-card relative" :class="{ 'flipped': !isLogin }">
            
            <!-- ==================== LOGIN CARD (FRONT) ==================== -->
            <div class="card-face absolute inset-0 w-full" :class="{ 'pointer-events-none': !isLogin }">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl overflow-hidden glow-green">
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-8 py-6 text-white">
                        <h3 class="text-2xl font-bold">Selamat Datang! 👋</h3>
                        <p class="text-green-100 mt-1">Masuk untuk mengakses akun Anda</p>
                    </div>
                    
                    <div class="p-8">
                        <!-- Session Status -->
                        <x-auth-session-status class="mb-4" :status="session('status')" />

                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            
                            <!-- Email -->
                            <div class="mb-5">
                                <label for="email" class="block font-semibold text-sm text-gray-700 dark:text-gray-300 mb-2">Email</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path></svg>
                                    </span>
                                    <input id="email" class="block w-full pl-12 pr-4 py-3 rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 transition-all" 
                                           type="email" name="email" :value="old('email')" required autofocus placeholder="user@email.com" />
                                </div>
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            <!-- Password -->
                            <div class="mb-5" x-data="{ showPassword: false }">
                                <label for="password" class="block font-semibold text-sm text-gray-700 dark:text-gray-300 mb-2">Password</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                    </span>
                                    <input id="password" class="block w-full pl-12 pr-12 py-3 rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 transition-all"
                                           :type="showPassword ? 'text' : 'password'" name="password" required autocomplete="current-password" placeholder="••••••••" />
                                    
                                    <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none">
                                        <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        <svg x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                                    </button>
                                </div>
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                            <!-- Remember & Forgot -->
                            <div class="flex justify-between items-center mb-6">
                                <label for="remember_me" class="inline-flex items-center cursor-pointer">
                                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-green-600 shadow-sm focus:ring-green-500" name="remember">
                                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Ingat Saya</span>
                                </label>

                                @if (Route::has('password.request'))
                                    <a class="text-sm text-green-600 dark:text-green-400 font-semibold hover:text-green-700 dark:hover:text-green-300 transition-colors" href="{{ route('password.request') }}">
                                        Lupa Password?
                                    </a>
                                @endif
                            </div>

                            <!-- Submit -->
                            <button type="submit" class="w-full py-3.5 px-6 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl font-bold uppercase tracking-wider shadow-lg shadow-green-500/30 hover:shadow-xl hover:shadow-green-500/40 hover:-translate-y-0.5 transition-all duration-300">
                                Masuk
                            </button>
                        </form>

                        <!-- Divider -->
                        <div class="relative my-6">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-200 dark:border-gray-700"></div>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="px-4 bg-white dark:bg-gray-800 text-gray-500">atau</span>
                            </div>
                        </div>

                        <!-- Register Link -->
                        <button type="button" @click="flipToRegister()" class="w-full py-3 px-6 border-2 border-green-500 text-green-600 dark:text-green-400 rounded-xl font-semibold hover:bg-green-50 dark:hover:bg-green-900/20 transition-all flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                            Daftar Anggota Baru
                        </button>
                    </div>
                </div>
            </div>

            <!-- ==================== REGISTER CARD (BACK) ==================== -->
            <div class="card-face card-back w-full" :class="{ 'pointer-events-none': isLogin }">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl overflow-hidden">
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 px-8 py-5 text-white relative">
                        <button type="button" @click="flipToLogin()" class="absolute left-4 top-1/2 -translate-y-1/2 p-2 rounded-lg hover:bg-white/20 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        </button>
                        <div class="text-center">
                            <h3 class="text-xl font-bold">Pendaftaran Anggota</h3>
                            <p class="text-blue-100 text-sm mt-1">Langkah <span x-text="regStep"></span> dari 3</p>
                        </div>
                        
                        <!-- Step Dots -->
                        <div class="flex justify-center gap-2 mt-3">
                            <div class="step-dot" :class="regStep >= 1 ? (regStep > 1 ? 'completed' : 'active') : 'pending'"></div>
                            <div class="step-dot" :class="regStep >= 2 ? (regStep > 2 ? 'completed' : 'active') : 'pending'"></div>
                            <div class="step-dot" :class="regStep >= 3 ? 'active' : 'pending'"></div>
                        </div>
                    </div>
                    
                    <div class="p-6 max-h-[60vh] overflow-y-auto">
                        <form method="POST" action="{{ route('register') }}" id="register-form" enctype="multipart/form-data" @submit="handleRegisterSubmit($event)">
                            @csrf

                            <!-- STEP 1: Data Pribadi -->
                            <div x-show="regStep === 1" x-transition:enter="slide-enter" class="space-y-4">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center gap-2 text-gray-600 dark:text-gray-300">
                                        <span class="w-8 h-8 bg-green-100 dark:bg-green-900/30 text-green-600 rounded-lg flex items-center justify-center text-sm font-bold">1</span>
                                        <span class="font-semibold">Data Pribadi</span>
                                    </div>
                                    
                                    <!-- Mode Toggle -->
                                    <div class="flex items-center bg-gray-100 dark:bg-gray-700 rounded-lg p-1">
                                        <button type="button" @click="regMode = 'manual'" 
                                                class="px-3 py-1 text-xs font-medium rounded-md transition-all"
                                                :class="regMode === 'manual' ? 'bg-white dark:bg-gray-600 text-gray-800 dark:text-white shadow' : 'text-gray-500 dark:text-gray-400'">
                                            ✏️ Manual
                                        </button>
                                        <button type="button" @click="regMode = 'ocr'" 
                                                class="px-3 py-1 text-xs font-medium rounded-md transition-all"
                                                :class="regMode === 'ocr' ? 'bg-white dark:bg-gray-600 text-gray-800 dark:text-white shadow' : 'text-gray-500 dark:text-gray-400'">
                                            📷 Scan KTP
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- OCR Section -->
                                <div x-show="regMode === 'ocr'" x-transition class="mb-4">
                                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 rounded-xl p-3 mb-3">
                                        <p class="text-xs text-blue-700 dark:text-blue-300">
                                            📷 Upload foto KTP untuk mengisi data otomatis (Nama, NIK, Tanggal Lahir, Jenis Kelamin)
                                        </p>
                                    </div>
                                    
                                    <div class="relative border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-4 text-center hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors cursor-pointer"
                                         @click="$refs.ocrInput.click()">
                                        <input type="file" x-ref="ocrInput" accept="image/*" class="hidden" @change="processOCR($event)">
                                        
                                        <div x-show="!ocrPreview">
                                            <svg class="mx-auto h-10 w-10 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                            <p class="mt-1 text-sm text-gray-500">Klik untuk upload KTP</p>
                                        </div>
                                        
                                        <div x-show="ocrPreview" class="relative">
                                            <img :src="ocrPreview" class="max-h-32 mx-auto rounded-lg shadow">
                                            <div x-show="ocrLoading" class="absolute inset-0 bg-black/50 rounded-lg flex items-center justify-center">
                                                <div class="text-white text-sm font-medium" x-text="ocrStatus"></div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <p x-show="ocrStatus && !ocrLoading" class="mt-2 text-center text-xs font-medium" 
                                       :class="ocrError ? 'text-red-500' : 'text-green-600'" x-text="ocrStatus"></p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Lengkap *</label>
                                    <input x-model="regData.name" name="name" type="text" required class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white py-2.5 px-4 focus:border-green-500 focus:ring focus:ring-green-200" placeholder="Sesuai KTP">
                                </div>
                                
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">NIK KTP</label>
                                        <input x-model="regData.id_card_number" name="id_card_number" type="text" maxlength="16" class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white py-2.5 px-4 focus:border-green-500 focus:ring focus:ring-green-200" placeholder="16 digit">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal Lahir *</label>
                                        <input x-model="regData.birth_date" name="birth_date" type="date" required class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white py-2.5 px-4 focus:border-green-500 focus:ring focus:ring-green-200">
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jenis Kelamin *</label>
                                    <select x-model="regData.gender" name="gender" required class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white py-2.5 px-4 focus:border-green-500 focus:ring focus:ring-green-200">
                                        <option value="">Pilih</option>
                                        <option value="male">Laki-laki</option>
                                        <option value="female">Perempuan</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Alamat *</label>
                                    <textarea x-model="regData.address" name="address" rows="2" required class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white py-2.5 px-4 focus:border-green-500 focus:ring focus:ring-green-200" placeholder="Alamat lengkap"></textarea>
                                </div>
                            </div>

                            <!-- STEP 2: Data Karyawan -->
                            <div x-show="regStep === 2" x-transition:enter="slide-enter" class="space-y-4">
                                <div class="flex items-center gap-2 mb-4 text-gray-600 dark:text-gray-300">
                                    <span class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 text-blue-600 rounded-lg flex items-center justify-center text-sm font-bold">2</span>
                                    <span class="font-semibold">Data Karyawan</span>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">NIK Karyawan *</label>
                                    <input x-model="regData.employee_id" name="employee_id" type="text" required class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white py-2.5 px-4 focus:border-green-500 focus:ring focus:ring-green-200" placeholder="Nomor Induk Karyawan">
                                </div>
                                
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Departemen *</label>
                                        <select x-model="regData.department" name="department" required class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white py-2.5 px-4 focus:border-green-500 focus:ring focus:ring-green-200">
                                            <option value="">Pilih</option>
                                            @foreach($departments ?? [] as $dept)
                                                <option value="{{ $dept->name }}">{{ $dept->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jabatan *</label>
                                        <select x-model="regData.position" name="position" required class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white py-2.5 px-4 focus:border-green-500 focus:ring focus:ring-green-200">
                                            <option value="">Pilih</option>
                                            @foreach($positions ?? [] as $pos)
                                                <option value="{{ $pos->name }}">{{ $pos->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Foto Profil</label>
                                    <input type="file" name="photo" accept="image/*" class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-green-50 file:text-green-600 hover:file:bg-green-100">
                                </div>
                            </div>

                            <!-- STEP 3: Akun -->
                            <div x-show="regStep === 3" x-transition:enter="slide-enter" class="space-y-4">
                                <div class="flex items-center gap-2 mb-4 text-gray-600 dark:text-gray-300">
                                    <span class="w-8 h-8 bg-purple-100 dark:bg-purple-900/30 text-purple-600 rounded-lg flex items-center justify-center text-sm font-bold">3</span>
                                    <span class="font-semibold">Buat Akun</span>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email *</label>
                                    <input x-model="regData.email" name="email" type="email" required class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white py-2.5 px-4 focus:border-green-500 focus:ring focus:ring-green-200" placeholder="email@contoh.com">
                                </div>
                                
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password *</label>
                                        <input x-model="regData.password" name="password" type="password" required class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white py-2.5 px-4 focus:border-green-500 focus:ring focus:ring-green-200" placeholder="Min. 8 karakter">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Konfirmasi *</label>
                                        <input x-model="regData.password_confirmation" name="password_confirmation" type="password" required class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white py-2.5 px-4 focus:border-green-500 focus:ring focus:ring-green-200" placeholder="Ulangi password">
                                    </div>
                                </div>
                                
                                <!-- Preview -->
                                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 mt-4">
                                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-2">📋 Ringkasan</p>
                                    <div class="text-sm space-y-1">
                                        <p><span class="text-gray-500">Nama:</span> <span class="font-medium text-gray-800 dark:text-white" x-text="regData.name || '-'"></span></p>
                                        <p><span class="text-gray-500">Dept:</span> <span class="font-medium text-gray-800 dark:text-white" x-text="regData.department || '-'"></span></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Navigation -->
                            <div class="flex justify-between mt-6 pt-4 border-t border-gray-100 dark:border-gray-700">
                                <button x-show="regStep > 1" type="button" @click="regStep--" class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white font-medium flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                                    Kembali
                                </button>
                                <div x-show="regStep === 1"></div>
                                
                                <button x-show="regStep < 3" type="button" @click="nextRegStep()" class="px-6 py-2.5 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl font-semibold shadow-lg shadow-green-500/30 hover:shadow-xl transition-all flex items-center gap-1">
                                    Lanjut
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </button>
                                
                                <button x-show="regStep === 3" type="submit" class="px-6 py-2.5 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-xl font-semibold shadow-lg shadow-blue-500/30 hover:shadow-xl transition-all flex items-center gap-1">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Daftar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <div class="mt-8 text-center space-y-2">
        <button onclick="window.installPWA()" class="text-xs font-semibold text-green-600 dark:text-green-400 hover:text-green-700 underline underline-offset-4 flex items-center justify-center gap-1 mx-auto">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            Install Aplikasi (HP/Desktop)
        </button>
        <div class="text-xs text-gray-400 dark:text-gray-500">
            &copy; {{ date('Y') }} Koperasi Karyawan SKF - PT. SPINDO TBK
        </div>
    </div>

    <script>
        function authPage() {
            return {
                darkMode: localStorage.getItem('darkMode') === 'true',
                isLogin: true,
                regStep: 1,
                regMode: 'manual', // 'manual' or 'ocr'
                ocrPreview: null,
                ocrLoading: false,
                ocrStatus: '',
                ocrError: false,
                regData: {
                    name: '', id_card_number: '', birth_date: '', gender: '', address: '',
                    employee_id: '', department: '', position: '',
                    email: '', password: '', password_confirmation: ''
                },
                
                init() {
                    this.$watch('darkMode', val => localStorage.setItem('darkMode', val));
                    
                    // Check URL for register mode
                    if (window.location.pathname.includes('register')) {
                        this.isLogin = false;
                    }
                },
                
                flipToRegister() {
                    this.isLogin = false;
                    this.regStep = 1;
                    this.regMode = 'manual';
                    history.pushState({}, '', '{{ route("register") }}');
                },
                
                flipToLogin() {
                    this.isLogin = true;
                    this.regStep = 1;
                    history.pushState({}, '', '{{ route("login") }}');
                },
                
                nextRegStep() {
                    if (this.validateRegStep()) {
                        this.regStep++;
                    }
                },
                
                validateRegStep() {
                    if (this.regStep === 1) {
                        if (!this.regData.name) { alert('Nama wajib diisi'); return false; }
                        if (!this.regData.birth_date) { alert('Tanggal lahir wajib diisi'); return false; }
                        if (!this.regData.gender) { alert('Jenis kelamin wajib dipilih'); return false; }
                        if (!this.regData.address) { alert('Alamat wajib diisi'); return false; }
                    }
                    if (this.regStep === 2) {
                        if (!this.regData.employee_id) { alert('NIK Karyawan wajib diisi'); return false; }
                        if (!this.regData.department) { alert('Departemen wajib dipilih'); return false; }
                        if (!this.regData.position) { alert('Jabatan wajib dipilih'); return false; }
                    }
                    return true;
                },
                
                handleRegisterSubmit(e) {
                    if (this.regData.password.length < 8) {
                        e.preventDefault();
                        alert('Password minimal 8 karakter');
                        return false;
                    }
                    if (this.regData.password !== this.regData.password_confirmation) {
                        e.preventDefault();
                        alert('Konfirmasi password tidak cocok');
                        return false;
                    }
                    return true;
                },
                
                async processOCR(event) {
                    const file = event.target.files[0];
                    if (!file) return;
                    
                    // Show preview
                    const reader = new FileReader();
                    reader.onload = (e) => { this.ocrPreview = e.target.result; };
                    reader.readAsDataURL(file);
                    
                    this.ocrLoading = true;
                    this.ocrStatus = 'Memproses...';
                    this.ocrError = false;
                    
                    try {
                        const worker = await Tesseract.createWorker('eng', 1, {
                            logger: m => {
                                if (m.status === 'recognizing text') {
                                    this.ocrStatus = `Membaca ${(m.progress * 100).toFixed(0)}%`;
                                }
                            }
                        });
                        
                        const ret = await worker.recognize(file);
                        this.parseOCR(ret.data.text);
                        await worker.terminate();
                        
                        this.ocrStatus = '✓ Berhasil! Data terisi otomatis';
                        this.ocrLoading = false;
                        
                        // Switch to manual mode to show filled fields
                        setTimeout(() => { this.regMode = 'manual'; }, 1500);
                        
                    } catch (err) {
                        this.ocrStatus = 'Gagal: ' + err.message;
                        this.ocrError = true;
                        this.ocrLoading = false;
                    }
                },
                
                parseOCR(text) {
                    const lines = text.split('\n');
                    
                    lines.forEach(line => {
                        let cl = line.trim().toUpperCase();
                        
                        // NIK (16 digits)
                        if (!this.regData.id_card_number) {
                            let nums = cl.replace(/[^0-9]/g, '');
                            if (nums.length >= 16) {
                                this.regData.id_card_number = nums.substring(0, 16);
                            }
                        }
                        
                        // Name
                        if (!this.regData.name && cl.includes('NAMA')) {
                            let val = cl.replace(/NAMA/i, '').replace(/[:]/g, '').trim();
                            if (val.length > 2) {
                                this.regData.name = this.titleCase(val);
                            }
                        }
                        
                        // Birth Date
                        if (!this.regData.birth_date) {
                            const dm = cl.match(/(\d{2})[-\s\/]+(\d{2})[-\s\/]+(\d{4})/);
                            if (dm && parseInt(dm[1]) <= 31 && parseInt(dm[2]) <= 12) {
                                this.regData.birth_date = `${dm[3]}-${dm[2]}-${dm[1]}`;
                            }
                        }
                        
                        // Gender
                        if (!this.regData.gender) {
                            if (cl.match(/LAKI/i)) this.regData.gender = 'male';
                            else if (cl.match(/PEREM|WANITA/i)) this.regData.gender = 'female';
                        }
                    });
                },
                
                titleCase(str) {
                    return str.toLowerCase().split(' ').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ');
                }
            }
        }
    </script>
</body>
</html>
