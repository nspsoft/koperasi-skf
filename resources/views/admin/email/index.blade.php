@extends('layouts.app')

@section('title', 'Kotak Masuk Email Admin')

@push('styles')
<style>
    /* Custom hover logic for row actions */
    .email-row:hover .email-actions {
        opacity: 1;
        visibility: visible;
    }
    .email-row:hover .email-date {
        opacity: 0;
        visibility: hidden;
    }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto py-4 sm:px-6 lg:px-8 h-[calc(100vh-80px)] flex flex-col">
    <!-- Header Area -->
    <div class="mb-4 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 px-4 sm:px-0">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-primary-100 dark:bg-primary-900/50 rounded-xl">
                <svg class="w-6 h-6 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            </div>
            <div>
                <h1 class="text-xl font-bold text-gray-900 dark:text-white leading-none">Kotak Masuk</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">admin@kopkarskf.com</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.email.index') }}" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-full transition-colors" title="Segarkan">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            </a>
            <a href="https://webmail.kopkarskf.com" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 shadow-sm transition-colors">
                Webmail
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
            </a>
        </div>
    </div>

    <!-- Error Handling -->
    @if(isset($imapError))
        <div class="px-4 sm:px-0 mb-4">
            <div class="bg-red-50 dark:bg-red-900/30 border-l-4 border-red-500 p-4 rounded-r-lg shadow-sm">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800 dark:text-red-200">Gagal terhubung ke Server Email</h3>
                        <div class="mt-2 text-sm text-red-700 dark:text-red-300"><p>{{ $imapError }}</p></div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Email List Container -->
    <div class="bg-white dark:bg-gray-900 shadow-sm sm:rounded-xl border border-gray-200 dark:border-gray-700 flex flex-col flex-1 overflow-hidden">
        
        <!-- Toolbar -->
        <div class="px-4 py-2 border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="flex items-center">
                    <input type="checkbox" class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 cursor-pointer">
                    <svg class="w-4 h-4 text-gray-400 ml-2 cursor-pointer hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
                <div class="h-4 w-px bg-gray-300 dark:bg-gray-600"></div>
                <button class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200" title="Arsip"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg></button>
                <button class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200" title="Hapus"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
            </div>
            <div class="text-xs text-gray-500 dark:text-gray-400 font-medium">
                @if(isset($messages) && $messages->count() > 0)
                    {{ $messages->firstItem() }}-{{ $messages->lastItem() }} dari {{ $messages->total() }}
                @endif
            </div>
        </div>

        <!-- Emails -->
        <div class="overflow-y-auto flex-1">
            @if(isset($messages) && $messages->count() > 0)
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach($messages as $message)
                        @php
                            $isUnread = !$message->hasFlag('seen');
                            $sender = $message->getFrom()[0]->personal ?? $message->getFrom()[0]->mail ?? 'Unknown';
                            $subject = $message->getSubject() ?: '(Tanpa Subjek)';
                            
                            $bodyStr = '';
                            try {
                                // Try to get text body safely
                                $textBody = $message->getTextBody() ?? '';
                                if ($textBody) {
                                    // Remove excess whitespaces and tags, limit to 100 chars
                                    $bodyStr = \Illuminate\Support\Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags($textBody))), 100);
                                }
                            } catch(\Exception $e) {}
                        @endphp
                        
                        <div class="email-row group relative flex items-center px-4 py-2 hover:shadow-[0_4px_12px_rgba(0,0,0,0.05)] hover:bg-white dark:hover:bg-gray-800 hover:z-10 bg-white dark:bg-gray-900 border-b border-transparent transition-all duration-150 {{ $isUnread ? 'bg-blue-50/30 dark:bg-gray-800/50' : '' }}">
                            
                            <a href="{{ route('admin.email.show', $message->getUid()) }}" class="absolute inset-0 z-0"></a>
                            
                            <!-- Left: Controls & Sender -->
                            <div class="flex items-center gap-3 w-48 sm:w-64 flex-shrink-0 z-10">
                                <div class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 cursor-pointer hover:border-gray-400">
                                    <svg class="w-5 h-5 text-gray-300 dark:text-gray-600 hover:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                                </div>
                                <span class="text-sm truncate pr-2 {{ $isUnread ? 'font-bold text-gray-900 dark:text-white' : 'font-medium text-gray-700 dark:text-gray-300' }}" title="{{ $sender }}">
                                    {{ Str::limit($sender, 22) }}
                                </span>
                            </div>

                            <!-- Middle: Subject & Snippet -->
                            <div class="flex-1 min-w-0 flex items-center pr-4">
                                <div class="truncate text-sm w-full">
                                    <span class="mr-1 {{ $isUnread ? 'font-bold text-gray-900 dark:text-white' : 'font-medium text-gray-800 dark:text-gray-200' }}">
                                        {{ $subject }}
                                    </span>
                                    @if($bodyStr)
                                        <span class="text-gray-500 dark:text-gray-400 font-normal">
                                            <span class="hidden sm:inline">-</span> {{ $bodyStr }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Right: Date & Actions -->
                            <div class="flex-shrink-0 flex items-center justify-end w-24 relative">
                                @if($message->getAttachments()->count() > 0)
                                    <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                @endif
                                
                                <span class="email-date text-xs font-medium {{ $isUnread ? 'text-gray-900 dark:text-white font-bold' : 'text-gray-500 dark:text-gray-400' }} transition-opacity duration-150">
                                    @php
                                        $dateObj = $message->getDate()[0] ?? null;
                                        if($dateObj) {
                                            if($dateObj->isToday()) {
                                                echo $dateObj->format('H:i');
                                            } elseif($dateObj->isCurrentYear()) {
                                                echo $dateObj->format('d M');
                                            } else {
                                                echo $dateObj->format('d/m/Y');
                                            }
                                        }
                                    @endphp
                                </span>

                                <!-- Hover Actions -->
                                <div class="email-actions absolute right-0 bg-white dark:bg-gray-800 flex items-center gap-1 opacity-0 invisible transition-all duration-150 z-20 px-1 shadow-sm rounded-lg border border-gray-100 dark:border-gray-700">
                                    <button class="p-1.5 text-gray-500 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors" title="Arsipkan"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg></button>
                                    <button class="p-1.5 text-gray-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-colors" title="Hapus"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                                    <button class="p-1.5 text-gray-500 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors" title="Tandai Belum Dibaca"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg></button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @elseif(!isset($imapError))
                <div class="h-full flex flex-col items-center justify-center text-center p-12">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-50 dark:bg-gray-800 mb-6 border border-gray-100 dark:border-gray-700 shadow-sm">
                        <svg class="w-10 h-10 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Kotak Masuk Kosong</h3>
                    <p class="text-gray-500 dark:text-gray-400 max-w-sm">Anda telah membaca seluruh pesan di kotak masuk admin@kopkarskf.com. Nikmati hari Anda!</p>
                </div>
            @endif
        </div>
        
        <!-- Pagination Footer -->
        @if(isset($messages) && $messages->hasPages())
        <div class="px-4 py-2 border-t border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
            {{ $messages->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
