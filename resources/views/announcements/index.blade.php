@extends('layouts.app')

@section('title', 'Pengumuman')

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Papan Pengumuman</h1>
            <p class="page-subtitle">Informasi dan berita terbaru koperasi</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- List Announcements -->
        <div class="lg:col-span-2 space-y-6">
            @forelse($announcements as $announcement)
            <div class="glass-card-solid p-6 hover:border-primary-500 transition-colors">
                <div class="flex justify-between items-start mb-2">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $announcement->title }}</h3>
                    @if(auth()->user()->hasAdminAccess())
                    <form action="{{ route('announcements.destroy', $announcement) }}" method="POST" 
                          onsubmit="return confirm('Hapus pengumuman ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-gray-400 hover:text-red-500 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </form>
                    @endif
                </div>
                <p class="text-xs text-gray-500 mb-4">{{ $announcement->created_at->format('d F Y, H:i') }}</p>
                <div class="prose dark:prose-invert max-w-none text-gray-700 dark:text-gray-300">
                    {!! nl2br(e($announcement->content)) !!}
                </div>
            </div>
            @empty
            <div class="glass-card-solid p-12 text-center">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Belum Ada Pengumuman</h3>
                <p class="text-gray-500 mt-1">Belum ada informasi yang dipublikasikan saat ini.</p>
            </div>
            @endforelse

            {{ $announcements->links() }}
        </div>

        <!-- Create Form (Admin Only) -->
        @if(auth()->user()->hasAdminAccess())
        <div class="lg:col-span-1">
            <div class="glass-card-solid p-6 sticky top-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Buat Pengumuman</h3>
                <form action="{{ route('announcements.store') }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div class="form-group">
                            <label class="form-label">Judul</label>
                            <input type="text" name="title" class="form-input" placeholder="Judul pengumuman..." required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Isi Pengumuman</label>
                            <textarea name="content" rows="6" class="form-input" placeholder="Tulis informasi di sini..." required></textarea>
                        </div>
                        <button type="submit" class="btn-primary w-full">
                            Publikasikan
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif
    </div>
@endsection
