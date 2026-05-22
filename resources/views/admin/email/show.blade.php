@extends('layouts.app')

@section('title', $message->getSubject() ?: '(Tanpa Subjek)')

@section('content')
<div class="max-w-5xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="mb-4 px-4 sm:px-0">
        <a href="{{ route('admin.email.index') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
            <svg class="mr-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Kotak Masuk
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow-sm overflow-hidden sm:rounded-xl border border-gray-100 dark:border-gray-700">
        <!-- Email Header -->
        <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                {{ $message->getSubject() ?: '(Tanpa Subjek)' }}
            </h1>
            
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-4">
                    <div class="h-10 w-10 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-primary-600 dark:text-primary-400 font-bold text-lg flex-shrink-0">
                        {{ strtoupper(substr($message->getFrom()[0]->personal ?? $message->getFrom()[0]->mail ?? 'U', 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ $message->getFrom()[0]->personal ?? 'Unknown Sender' }} 
                            <span class="text-gray-500 dark:text-gray-400 font-normal">&lt;{{ $message->getFrom()[0]->mail }}&gt;</span>
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            Kepada: {{ $message->getTo()[0]->mail ?? 'admin@kopkarskf.com' }}
                        </p>
                    </div>
                </div>
                <div class="text-right flex-shrink-0">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $message->getDate()[0]->format('d M Y, H:i') }}
                    </p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                        {{ $message->getDate()[0]->diffForHumans() }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Attachments -->
        @if($message->getAttachments()->count() > 0)
        <div class="px-6 py-3 bg-gray-50 dark:bg-gray-800/50 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Lampiran ({{ $message->getAttachments()->count() }})</h3>
            <ul class="flex flex-wrap gap-3">
                @foreach($message->getAttachments() as $attachment)
                <li class="flex items-center p-2 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg shadow-sm">
                    <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                    <span class="text-sm text-gray-700 dark:text-gray-200 truncate max-w-xs">{{ $attachment->name }}</span>
                    <span class="text-xs text-gray-500 ml-2">({{ number_format($attachment->size / 1024, 2) }} KB)</span>
                    <!-- Note: Unduh lampiran membutuhkan controller khusus untuk stream file. Untuk saat ini hanya menampilkan -->
                </li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Email Body -->
        <div class="px-6 py-8 text-gray-800 dark:text-gray-200">
            @if($message->hasHTMLBody())
                <div class="prose dark:prose-invert max-w-none">
                    {!! $message->getHTMLBody() !!}
                </div>
            @else
                <div class="whitespace-pre-wrap font-sans">
                    {{ $message->getTextBody() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
