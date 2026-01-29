@extends('layouts.app')

@section('title', 'Registrasi Anggota')

@section('content')
    <div class="min-h-[60vh] flex items-center justify-center">
        <div class="glass-card-solid p-12 max-w-2xl w-full text-center">
            <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                Belum Terdaftar Sebagai Anggota
            </h2>
            
            <p class="text-gray-600 dark:text-gray-300 mb-8 leading-relaxed">
                Akun Anda belum terdaftar sebagai anggota koperasi. Silakan hubungi admin untuk mendaftarkan Anda sebagai anggota koperasi atau klik tombol di bawah untuk melengkapi data keanggotaan.
            </p>

            <div class="flex gap-4 justify-center">
                @if(auth()->user()->hasAdminAccess())
                <a href="{{ route('members.create') }}" class="btn-primary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Daftar Sebagai Anggota
                </a>
                @endif
                
                <a href="{{ route('profile.edit') }}" class="btn-secondary">
                    Ke Profil Saya
                </a>
            </div>

            <div class="mt-8 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-200 dark:border-blue-800">
                <p class="text-sm text-blue-800 dark:text-blue-300">
                    <strong>Info:</strong> Setelah terdaftar sebagai anggota, Anda dapat mengakses fitur simpanan dan pinjaman koperasi.
                </p>
            </div>
        </div>
    </div>
@endsection
