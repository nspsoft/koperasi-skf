<!DOCTYPE html>
<html lang="id" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))" :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ganti Password Baru - Koperasi SKF</title>
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body { font-family: ui-sans-serif, system-ui, sans-serif; }
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
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-8 py-6 text-white text-center">
                <h3 class="text-xl font-bold uppercase tracking-tight">Atur Password Baru</h3>
                <p class="text-blue-100 text-xs mt-1">Gunakan kata sandi yang kuat dan aman</p>
            </div>

            <div class="p-8">
                <form method="POST" action="{{ route('password.store') }}">
                    @csrf

                    <!-- Password Reset Token -->
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <!-- Email Address -->
                    <div class="mb-5">
                        <label for="email" class="block font-semibold text-sm text-gray-700 dark:text-gray-300 mb-2">Email</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path></svg>
                            </span>
                            <input id="email" class="block w-full pl-12 pr-4 py-3 rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring focus:ring-blue-200 transition-all font-medium" 
                                   type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username" />
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div class="mb-5">
                        <label for="password" class="block font-semibold text-sm text-gray-700 dark:text-gray-300 mb-2">Password Baru</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            </span>
                            <input id="password" class="block w-full pl-12 pr-4 py-3 rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring focus:ring-blue-200 transition-all"
                                   type="password" name="password" required autocomplete="new-password" placeholder="••••••••" />
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-8">
                        <label for="password_confirmation" class="block font-semibold text-sm text-gray-700 dark:text-gray-300 mb-2">Konfirmasi Password</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            </span>
                            <input id="password_confirmation" class="block w-full pl-12 pr-4 py-3 rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring focus:ring-blue-200 transition-all"
                                   type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
                        </div>
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>

                    <button type="submit" class="w-full py-3.5 px-6 bg-gradient-to-r from-blue-600 to-indigo-700 text-white rounded-xl font-bold uppercase tracking-wider shadow-lg shadow-blue-500/30 hover:shadow-xl hover:shadow-blue-500/40 hover:-translate-y-0.5 transition-all duration-300">
                        {{ __('Simpan Password Baru') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
