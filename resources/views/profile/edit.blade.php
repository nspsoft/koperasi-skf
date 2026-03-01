@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="page-title">{{ __('Profile') }}</h1>
                <p class="page-subtitle">Kelola informasi akun dan keamanan Anda</p>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="card">
            <div class="card-body">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Ringkasan Kredit Mart</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Sisa kredit yang belum dibayar</p>
                    </div>
                    @if($member)
                        <a href="{{ route('members.credits') }}" class="text-xs text-primary-600 hover:text-primary-700 font-medium">Lihat Riwayat →</a>
                    @endif
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="p-4 rounded-xl border border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Limit Kredit</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">Rp {{ number_format($creditLimit, 0, ',', '.') }}</p>
                    </div>
                    <div class="p-4 rounded-xl border border-orange-100 dark:border-orange-900/30 bg-orange-50 dark:bg-orange-900/10">
                        <p class="text-xs text-orange-600 dark:text-orange-400 mb-1">Terpakai (Belum Lunas)</p>
                        <p class="text-lg font-bold text-orange-600 dark:text-orange-400">Rp {{ number_format($creditUsed, 0, ',', '.') }}</p>
                    </div>
                    <div class="p-4 rounded-xl border border-green-100 dark:border-green-900/30 bg-green-50 dark:bg-green-900/10">
                        <p class="text-xs text-green-600 dark:text-green-400 mb-1">Sisa Limit</p>
                        <p class="text-lg font-bold text-green-600 dark:text-green-400">Rp {{ number_format($creditAvailable, 0, ',', '.') }}</p>
                    </div>
                </div>
                @if(!$member)
                    <div class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                        Kredit Mart hanya tersedia untuk anggota.
                    </div>
                @endif
            </div>
        </div>

        <!-- Profile Information -->
        <div class="card">
            <div class="card-body">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <!-- Points History -->
        <div class="card">
            <div class="card-body">
                @include('profile.partials.points-history')
            </div>
        </div>

        <!-- Update Password -->
        <div class="card">
            <div class="card-body">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <!-- Delete Account -->
        <div class="card">
            <div class="card-body">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
@endsection
