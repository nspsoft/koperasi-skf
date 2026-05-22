@extends('layouts.app')

@section('title', 'Kotak Masuk Email Admin')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 px-4 sm:px-0">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <svg class="w-7 h-7 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                Kotak Masuk
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                admin@kopkarskf.com
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.email.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                Segarkan
            </a>
            <a href="https://webmail.kopkarskf.com" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                Buka Webmail
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
            </a>
        </div>
    </div>

    @if(isset($imapError))
        <div class="px-4 sm:px-0 mb-6">
            <div class="bg-red-50 dark:bg-red-900/30 border-l-4 border-red-500 p-4 rounded-r-lg">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800 dark:text-red-200">Gagal terhubung ke Server Email</h3>
                        <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                            <p>{{ $imapError }}</p>
                        </div>
                        <div class="mt-4">
                            <p class="text-xs text-red-600 dark:text-red-400 font-semibold mb-2">Solusi yang mungkin:</p>
                            <ul class="list-disc pl-5 text-xs text-red-600 dark:text-red-400 space-y-1">
                                <li>Pastikan ekstensi PHP `imap` telah diaktifkan di cPanel (Select PHP Version -> Extensions).</li>
                                <li>Pastikan kredensial (password) IMAP di file .env sudah benar.</li>
                                <li>Pastikan port 993 tidak diblokir oleh firewall server.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 shadow-sm overflow-hidden sm:rounded-xl border border-gray-100 dark:border-gray-700">
        @if(isset($messages) && $messages->count() > 0)
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($messages as $message)
                    <a href="{{ route('admin.email.show', $message->getUid()) }}" class="block hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150 {{ !$message->hasFlag('seen') ? 'bg-primary-50/30 dark:bg-primary-900/10' : '' }}">
                        <div class="px-4 py-4 sm:px-6 flex items-center justify-between">
                            <div class="flex items-center min-w-0 gap-4 flex-1">
                                <div class="flex-shrink-0">
                                    @if(!$message->hasFlag('seen'))
                                        <div class="h-3 w-3 bg-primary-500 rounded-full"></div>
                                    @else
                                        <div class="h-3 w-3 bg-transparent"></div>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center justify-between mb-1">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate {{ !$message->hasFlag('seen') ? 'font-bold' : '' }}">
                                            {{ $message->getFrom()[0]->personal ?? $message->getFrom()[0]->mail ?? 'Unknown Sender' }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap ml-4 flex-shrink-0">
                                            {{ $message->getDate()[0]->diffForHumans() }}
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <p class="text-sm text-gray-800 dark:text-gray-200 truncate {{ !$message->hasFlag('seen') ? 'font-semibold' : '' }}">
                                            {{ $message->getSubject() ?: '(Tanpa Subjek)' }}
                                        </p>
                                        @if($message->getAttachments()->count() > 0)
                                            <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
            
            <div class="bg-gray-50 dark:bg-gray-800/80 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
                {{ $messages->links() }}
            </div>
        @elseif(!isset($imapError))
            <div class="p-12 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 mb-4 text-gray-400 dark:text-gray-500">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Kotak Masuk Kosong</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Belum ada email yang masuk ke admin@kopkarskf.com.</p>
            </div>
        @endif
    </div>
</div>
@endsection
