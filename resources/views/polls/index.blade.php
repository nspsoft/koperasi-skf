@extends('layouts.app')

@section('title', 'E-Polling - Pemilihan Pengurus')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">E-Polling</h1>
            <p class="page-subtitle">Pusat pemilihan pengurus dan pemungutan suara koperasi</p>
        </div>
        @if(auth()->user()->hasAdminAccess())
            <a href="{{ route('polls.create') }}" class="btn-primary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Buat Pemilihan Baru
            </a>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($polls as $poll)
            <div class="glass-card-solid p-6 flex flex-col h-full hover:shadow-lg transition-all border border-transparent hover:border-primary-500/30">
                <div class="flex items-start justify-between mb-4">
                    <div class="p-3 rounded-xl {{ $poll->status === 'active' ? 'bg-green-100 text-green-600' : ($poll->status === 'closed' ? 'bg-red-100 text-red-600' : 'bg-gray-100 text-gray-600') }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </div>
                    <span class="text-xs font-bold px-3 py-1 rounded-full uppercase {{ $poll->status === 'active' ? 'bg-green-500 text-white' : ($poll->status === 'closed' ? 'bg-red-500 text-white' : 'bg-gray-500 text-white') }}">
                        {{ $poll->status }}
                    </span>
                </div>

                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ $poll->title }}</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 line-clamp-2">{{ $poll->description }}</p>

                <div class="space-y-2 mb-6 flex-grow">
                    <div class="flex items-center text-xs text-gray-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        {{ $poll->start_date->format('d M Y') }} - {{ $poll->end_date->format('d M Y') }}
                    </div>
                    <div class="flex items-center text-xs text-gray-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        {{ $poll->votes_count }} Suara Terkumpul
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ route('polls.show', $poll->id) }}" class="btn-primary justify-center text-sm">
                        {{ $poll->status === 'active' ? 'Buka Pemilihan' : 'Detail' }}
                    </a>
                    <a href="{{ route('polls.results', $poll->id) }}" class="btn-secondary justify-center text-sm">
                        Hasil
                    </a>
                </div>

                @if(auth()->user()->hasAdminAccess())
                    <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 flex justify-between">
                        <form action="{{ route('polls.update-status', $poll->id) }}" method="POST" class="flex gap-1">
                            @csrf
                            @method('PATCH')
                            <select name="status" onchange="this.form.submit()" class="text-[10px] bg-gray-50 dark:bg-gray-800 border-none rounded p-1">
                                <option value="draft" {{ $poll->status === 'draft' ? 'selected' : '' }}>Set Draf</option>
                                <option value="active" {{ $poll->status === 'active' ? 'selected' : '' }}>Aktifkan</option>
                                <option value="closed" {{ $poll->status === 'closed' ? 'selected' : '' }}>Tutup</option>
                            </select>
                        </form>
                        <form action="{{ route('polls.destroy', $poll->id) }}" method="POST" onsubmit="return confirm('Hapus pemilihan ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 p-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        @empty
            <div class="col-span-full py-12 text-center glass-card-solid">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Tidak ada pemilihan</h3>
                <p class="text-gray-500">Belum ada pemilihan yang dibuat atau sedang berlangsung.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $polls->links() }}
    </div>
@endsection
