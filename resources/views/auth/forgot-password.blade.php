<!DOCTYPE html>
<html lang="id" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))" :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lupa Password - Koperasi SKF</title>
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body { font-family: ui-sans-serif, system-ui, sans-serif; }
        .text-spindo { color: #0054a6; }
        .dark .text-spindo { color: #60a5fa; }
        .bg-pattern {
            background-image: radial-gradient(circle at 1px 1px, rgba(0,0,0,0.05) 1px, transparent 0);
            background-size: 40px 40px;
        }
        .dark .bg-pattern {
            background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,0.03) 1px, transparent 0);
        }
        .glow-green {
            box-shadow: 0 0 30px rgba(0, 150, 64, 0.15);
        }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-center items-center bg-gradient-to-br from-gray-50 via-white to-gray-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 bg-pattern transition-colors duration-500">
    
    <!-- Dark Mode Toggle -->
    <div class="fixed top-4 right-4 z-50">
        <button @click="darkMode = !darkMode" class="p-3 rounded-xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300">
            <svg x-show="!darkMode" class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
            </svg>
            <svg x-show="darkMode" class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
        </button>
    </div>

    <!-- Logo Section -->
    <div class="mb-8 text-center">
        @if(isset($globalSettings['coop_logo']) && $globalSettings['coop_logo'])
            <img src="{{ Storage::url($globalSettings['coop_logo']) }}" alt="Logo" class="h-20 w-auto mx-auto mb-4 object-contain shadow-lg rounded-full bg-white dark:bg-gray-800 p-2 ring-4 ring-white/50 dark:ring-gray-700/50">
        @endif
        <h2 class="text-xl font-bold text-gray-700 dark:text-gray-200">Koperasi Karyawan</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400">PT. SPINDO TBK</p>
    </div>

    <div class="w-full max-w-md px-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl overflow-hidden glow-green">
            <!-- Header -->
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-8 py-6 text-white relative">
                <a href="{{ route('login') }}" class="absolute left-4 top-1/2 -translate-y-1/2 p-2 rounded-lg hover:bg-white/20 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </a>
                <div class="text-center">
                    <h3 class="text-xl font-bold uppercase tracking-tight">Lupa Password</h3>
                    <p class="text-green-100 text-xs mt-1">Reset akses akun Anda</p>
                </div>
            </div>

            <div class="p-8">
                <div class="mb-6 text-sm text-gray-600 dark:text-gray-400 text-center leading-relaxed">
                    {{ __('Masukkan email Anda dan kami akan mengirimkan link untuk mengatur ulang password Anda.') }}
                </div>

                <!-- Session Status -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <!-- Email Address -->
                    <div class="mb-6">
                        <label for="email" class="block font-semibold text-sm text-gray-700 dark:text-gray-300 mb-2">Email Terdaftar</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path></svg>
                            </span>
                            <input id="email" class="block w-full pl-12 pr-4 py-3 rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 transition-all font-medium" 
                                   type="email" name="email" :value="old('email')" required autofocus placeholder="user@email.com" />
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <button type="submit" class="w-full py-3.5 px-6 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl font-bold uppercase tracking-wider shadow-lg shadow-green-500/30 hover:shadow-xl hover:shadow-green-500/40 hover:-translate-y-0.5 transition-all duration-300">
                        {{ __('Kirim Link Reset') }}
                    </button>
                    
                    <div class="mt-6 text-center">
                        <a href="{{ route('login') }}" class="text-sm text-gray-500 hover:text-green-600 dark:text-gray-400 dark:hover:text-green-400 transition-colors font-medium">
                            Kembali ke Login
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
