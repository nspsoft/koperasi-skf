@extends('layouts.app')

@section('title', __('messages.titles.audit_logs'))

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Audit Log</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Riwayat aktivitas dan perubahan di sistem.</p>
        </div>
        <a href="{{ route('settings.index') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali
        </a>
    </div>

    <!-- Filters -->
    <div class="glass-card-solid p-4">
        <form method="GET" action="{{ route('settings.audit-logs') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
            <div>
                <label class="form-label">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-input">
            </div>
            <div>
                <label class="form-label">Tanggal Selesai</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-input">
            </div>
            <div>
                <label class="form-label">Aksi</label>
                <select name="action" class="form-input">
                    <option value="">Semua Aksi</option>
                    @foreach($actions as $action)
                    <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>{{ ucfirst($action) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">User</label>
                <select name="user_id" class="form-input">
                    <option value="">Semua User</option>
                    @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-primary">Filter</button>
        </form>
    </div>

    <!-- Logs Table -->
    <div class="glass-card-solid overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>User</th>
                        <th>Aksi</th>
                        <th>Deskripsi</th>
                        <th>IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td class="whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-white">{{ $log->created_at->format('d/m/Y') }}</div>
                            <div class="text-xs text-gray-500">{{ $log->created_at->format('H:i:s') }}</div>
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-primary-600 dark:text-primary-400 font-semibold text-sm">
                                    {{ $log->user ? strtoupper(substr($log->user->name, 0, 1)) : '?' }}
                                </div>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $log->user->name ?? 'System' }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-{{ $log->action_color }}">{{ ucfirst($log->action) }}</span>
                        </td>
                        <td class="max-w-xs">
                            <p class="text-sm text-gray-700 dark:text-gray-300 truncate">{{ $log->description ?? '-' }}</p>
                            @if($log->model_type)
                            <p class="text-xs text-gray-500 truncate">{{ class_basename($log->model_type) }} #{{ $log->model_id }}</p>
                            @endif
                        </td>
                        <td class="text-sm text-gray-500 font-mono">{{ $log->ip_address ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-12">
                            <svg class="w-12 h-12 mx-auto text-gray-400 dark:text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <p class="text-gray-500 dark:text-gray-400 font-medium">Belum ada log aktivitas.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
        <div class="p-4 border-t border-gray-100 dark:border-gray-700">
            {{ $logs->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
