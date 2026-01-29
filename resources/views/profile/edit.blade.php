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
