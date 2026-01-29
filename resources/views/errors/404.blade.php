<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Halaman Tidak Ditemukan - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script>
        if (localStorage.getItem('darkMode') === 'true') {
            document.documentElement.classList.add('dark');
        }
    </script>
</head>
<body class="h-full bg-gray-50 dark:bg-gray-900 font-sans antialiased transition-colors duration-300">
    <div class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
            <div class="relative inline-block">
                <div class="text-[120px] font-black text-purple-600/10 dark:text-purple-400/10 leading-none select-none">404</div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-6xl animate-bounce">🔍</span>
                </div>
            </div>
            
            <h1 class="mt-6 text-3xl font-extrabold text-gray-900 dark:text-white sm:text-4xl">
                Halaman Tidak Ditemukan
            </h1>
            <p class="mt-4 text-base text-gray-500 dark:text-gray-400 max-w-xs mx-auto">
                Maaf, kami tidak dapat menemukan halaman yang Anda cari. Mungkin link rusak atau sudah dihapus.
            </p>
            
            <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ url('/dashboard') }}" class="w-full sm:w-auto px-8 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-bold rounded-2xl shadow-xl hover:shadow-purple-500/40 transform hover:-translate-y-1 transition-all">
                    Kembali ke Dashboard
                </a>
                <button onclick="history.back()" class="w-full sm:w-auto px-8 py-3 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 font-bold rounded-2xl border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all">
                    Kembali Sebelumnya
                </button>
            </div>
            
            <p class="mt-12 text-sm text-gray-400">
                Butuh bantuan? <a href="#" class="text-purple-500 font-semibold hover:underline">Hubungi Admin</a>
            </p>
        </div>
    </div>
</body>
</html>
