<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kesalahan Server - {{ config('app.name') }}</title>
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
        <div class="sm:mx-auto sm:w-full sm:max-w-md text-center px-4">
            <div class="relative inline-block">
                <div class="text-[120px] font-black text-red-600/10 dark:text-red-400/10 leading-none select-none">500</div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-6xl animate-pulse">🛠️</span>
                </div>
            </div>
            
            <h1 class="mt-6 text-3xl font-extrabold text-gray-900 dark:text-white sm:text-4xl">
                Oopss! Terjadi Kesalahan
            </h1>
            <p class="mt-4 text-base text-gray-500 dark:text-gray-400">
                Server kami sedang mengalami gangguan atau masalah teknis sementara. Kami sudah mencatat error ini untuk segera diperbaiki.
            </p>
            
            <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                <button onclick="window.location.reload()" class="w-full sm:w-auto px-8 py-3 bg-gradient-to-r from-red-600 to-rose-600 text-white font-bold rounded-2xl shadow-xl hover:shadow-red-500/40 transform hover:-translate-y-1 transition-all">
                    Segarkan Halaman
                </button>
                <a href="{{ url('/dashboard') }}" class="w-full sm:w-auto px-8 py-3 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 font-bold rounded-2xl border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all">
                    Kembali ke Beranda
                </a>
            </div>
            
            <div class="mt-12 p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-100 dark:border-amber-900/30 rounded-2xl text-left">
                <h4 class="text-sm font-bold text-amber-800 dark:text-amber-400 mb-1">Apa yang bisa Anda lakukan?</h4>
                <ul class="text-xs text-amber-700 dark:text-amber-500 space-y-1 list-disc list-inside">
                    <li>Segarkan halaman ini setelah beberapa saat.</li>
                    <li>Pastikan koneksi internet Anda stabil.</li>
                    <li>Jika terus berlanjut, hubungi tim IT Koperasi.</li>
                </ul>
            </div>
            
            <p class="mt-8 text-sm text-gray-400 italic">
                ID Error: {{ substr(md5(time()), 0, 8) }}
            </p>
        </div>
    </div>
</body>
</html>
