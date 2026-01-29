@extends('layouts.app')

@section('title', __('messages.titles.dashboard'))

@section('content')
    <div class="page-header">
        <h1 class="page-title">{{ __('messages.dashboard') }}</h1>
        <p class="page-subtitle">{{ __('messages.dashboard.welcome', ['name' => auth()->user()->name]) }}</p>
    </div>

    <div class="glass-card-solid p-6 border-l-4 border-l-indigo-500">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('messages.dashboard.membership_status') }}</h3>
        <div class="mt-4">
            @if(auth()->user()->member)
                @if(auth()->user()->member->status === 'active')
                    <span class="badge badge-success text-base px-4 py-2">{{ __('messages.dashboard.active_member') }}</span>
                    <p class="mt-2 text-gray-600">{{ __('messages.dashboard.member_id') }}: {{ auth()->user()->member->member_id }}</p>
                @elseif(auth()->user()->member->status === 'inactive')
                    <span class="badge badge-warning text-base px-4 py-2">{{ __('messages.dashboard.pending_approval') }}</span>
                    <p class="mt-2 text-gray-600">{{ __('messages.dashboard.pending_desc') }}</p>
                @else
                    <span class="badge badge-danger text-base px-4 py-2">{{ __('messages.dashboard.inactive_member') }}</span>
                @endif
            @else
                <span class="badge badge-secondary text-base px-4 py-2">{{ __('messages.dashboard.not_a_member') }}</span>
                <p class="mt-2 text-gray-600">{{ __('messages.dashboard.not_a_member_desc') }}</p>
            @endif
        </div>
    </div>
@endsection
