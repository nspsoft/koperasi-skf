@extends('layouts.app')

@section('title', __('messages.titles.backup'))

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Backup & Restore Database</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Kelola backup database untuk keamanan data Anda.</p>
        </div>
        <a href="{{ route('settings.index') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali
        </a>
    </div>

    <!-- Warning Alert -->
    <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-xl">
        <div class="flex items-start gap-3">
            <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <div>
                <h3 class="font-semibold text-yellow-800 dark:text-yellow-200">Perhatian!</h3>
                <p class="text-sm text-yellow-700 dark:text-yellow-300 mt-1">Restore database akan mengganti SEMUA data saat ini. Pastikan Anda sudah backup data terbaru sebelum melakukan restore.</p>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="grid md:grid-cols-2 gap-6">
        <!-- Download Backup -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Download Backup</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Export database ke file SQL</p>
                </div>
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Buat backup database saat ini dan download sebagai file .sql yang bisa direstore kapan saja.</p>
            <a href="{{ route('settings.backup.download') }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl font-semibold transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                Download Backup Sekarang
            </a>
        </div>

        <!-- Restore Backup -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Restore Database</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Import dari file SQL backup</p>
                </div>
            </div>
            <form action="{{ route('settings.backup.restore') }}" method="POST" enctype="multipart/form-data" onsubmit="return confirm('PERINGATAN: Restore akan menimpa SEMUA data saat ini. Lanjutkan?');">
                @csrf
                <div class="mb-4">
                    <input type="file" name="backup_file" accept=".sql,.txt" required 
                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>
                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                    </svg>
                    Restore Database
                </button>
            </form>
        </div>
    </div>

    <!-- Existing Backups -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                Backup Tersimpan ({{ $backups->count() }})
            </h2>
        </div>
        <div class="p-6">
            @if($backups->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Nama File</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Ukuran</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Tanggal</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($backups as $backup)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-white font-medium">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path></svg>
                                        {{ $backup['name'] }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                    {{ number_format($backup['size'] / 1024, 2) }} KB
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                    {{ date('d M Y, H:i', $backup['date']) }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <form action="{{ route('settings.backup.destroy', $backup['name']) }}" method="POST" class="inline" onsubmit="return confirm('Hapus backup ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 text-sm font-medium">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="w-12 h-12 mx-auto text-gray-400 dark:text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400 font-medium">Belum ada backup tersimpan.</p>
                    <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Klik "Download Backup Sekarang" untuk membuat backup pertama.</p>
                </div>
            @endif
        </div>
    </div>

    @if(auth()->id() === 1)
    <!-- Danger Zone -->
    <div class="border-t-4 border-red-500 bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden mt-12 mb-8">
        <div class="p-6">
            <div class="flex items-start gap-4">
                <div class="p-3 bg-red-100 dark:bg-red-900/30 rounded-xl">
                    <svg class="w-8 h-8 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-red-600 dark:text-red-400">Danger Zone (Area Berbahaya)</h3>
                    <p class="text-gray-600 dark:text-gray-300 mt-1">
                        Tindakan di bawah ini akan <strong>MENGHAPUS SEMUA DATA</strong> (Transaksi, Anggota, Produk, dll) dan mengembalikan aplikasi ke kondisi awal (Factory Reset). 
                        <br>Akun Super Admin (Anda) dan Pengaturan Dasar tidak akan dihapus.
                    </p>
                    <button onclick="document.getElementById('resetModal').showModal()" class="mt-4 inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">
                        Reset Database ke Awal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Reset Confirmation Modal -->
    <dialog id="resetModal" class="rounded-2xl shadow-2xl backdrop:bg-black/50 p-0 border-0 w-full max-w-md">
        <form action="{{ route('settings.backup.reset') }}" method="POST" class="bg-white dark:bg-gray-800">
            @csrf
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-red-50 dark:bg-red-900/20">
                <h3 class="text-lg font-bold text-red-600 dark:text-red-400 flex items-center gap-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Konfirmasi Reset Database
                </h3>
            </div>
            
            <!-- Body -->
            <div class="p-6 space-y-4">
                <p class="text-gray-700 dark:text-gray-300 text-sm">
                    Apakah Anda yakin ingin menghapus semua data? <br>
                    <strong>Tindakan ini tidak dapat dibatalkan manual!</strong> (Sistem akan membuat backup otomatis sebelum reset).
                </p>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Masukkan Password Admin untuk Konfirmasi
                    </label>
                    <input type="password" name="password" required class="form-input w-full" placeholder="Password Anda...">
                </div>

                <div class="flex items-start gap-2">
                    <input type="checkbox" name="confirm" required id="confirmCheck" class="mt-1 rounded border-gray-300 text-red-600 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50">
                    <label for="confirmCheck" class="text-xs text-gray-600 dark:text-gray-400">
                        Saya mengerti bahwa data akan dihapus permanen.
                    </label>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('resetModal').close()" class="px-4 py-2 bg-white border border-gray-300 rounded-xl font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:text-gray-800 active:bg-gray-50 transition ease-in-out duration-150">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-red-600 border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:border-red-700 focus:shadow-outline-red disabled:opacity-25 transition ease-in-out duration-150">
                    Ya, Reset Sekarang
                </button>
            </div>
        </form>
    </dialog>
    @endif
</div>

@endsection
