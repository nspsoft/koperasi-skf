@extends('layouts.app')

@section('title', isset($selectedMessage) ? $selectedMessage->getSubject() : 'Kotak Masuk')

@push('styles')
<style>
    /* Prevent body scrolling, allow inner panes to scroll */
    body { overflow: hidden; }
    .email-list-row.active {
        background-color: #eff6ff; /* blue-50 */
        border-left: 3px solid #3b82f6; /* blue-500 */
    }
    .dark .email-list-row.active {
        background-color: rgba(59, 130, 246, 0.1); /* blue-500/10 */
        border-left: 3px solid #3b82f6;
    }
</style>
@endpush

@section('content')
<!-- Full height layout container minus header -->
<div class="flex h-[calc(100vh-64px)] w-full overflow-hidden bg-white dark:bg-gray-900">
    
    <!-- Left Column: Folders / Navigation (250px) -->
    <div class="hidden md:flex flex-col w-[250px] flex-shrink-0 border-r border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
        <div class="p-4">
            <button class="w-full bg-gray-800 hover:bg-gray-900 dark:bg-gray-100 dark:hover:bg-white text-white dark:text-gray-900 rounded-xl py-3 px-4 font-semibold shadow-sm transition-colors flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                Tulis Email
            </button>
            <a href="{{ route('admin.email.index') }}" class="w-full mt-2 bg-primary-100 hover:bg-primary-200 dark:bg-primary-900/30 dark:hover:bg-primary-900/50 text-primary-700 dark:text-primary-400 rounded-xl py-2.5 px-4 font-medium transition-colors flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                Sinkronisasi
            </a>
        </div>
        
        <div class="flex-1 overflow-y-auto px-3 py-2">
            <h3 class="px-3 text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Folder Utama</h3>
            <nav class="space-y-1">
                <a href="{{ route('admin.email.index') }}" class="flex items-center justify-between px-3 py-2 rounded-lg bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400 font-medium">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                        Kotak Masuk
                    </div>
                </a>
                <a href="#" class="flex items-center justify-between px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700/50 transition-colors">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                        Terkirim
                    </div>
                </a>
                <a href="#" class="flex items-center justify-between px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700/50 transition-colors">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        Sampah
                    </div>
                </a>
            </nav>
        </div>
        
        <div class="p-4 border-t border-gray-200 dark:border-gray-700">
            <a href="https://webmail.kopkarskf.com" target="_blank" class="flex items-center justify-center gap-2 text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                Buka Webmail cPanel <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
            </a>
        </div>
    </div>

    <!-- Middle Column: Email List (350px) -->
    <div class="flex flex-col w-full md:w-[350px] flex-shrink-0 border-r border-gray-200 dark:border-gray-700 {{ isset($selectedMessage) ? 'hidden md:flex' : 'flex' }}">
        <!-- Toolbar -->
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 flex-shrink-0">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" placeholder="Cari email..." class="block w-full pl-10 pr-3 py-2 border border-gray-200 dark:border-gray-700 rounded-lg leading-5 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-primary-500 focus:border-primary-500 sm:text-sm transition-colors">
            </div>
        </div>

        <!-- Error Handling -->
        @if(isset($imapError))
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-red-50 dark:bg-red-900/20">
                <p class="text-sm text-red-600 dark:text-red-400 font-medium">Error: {{ $imapError }}</p>
            </div>
        @endif

        <!-- Scrollable List -->
        <div class="flex-1 overflow-y-auto bg-white dark:bg-gray-900">
            @if(isset($messages) && $messages->count() > 0)
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach($messages as $message)
                        @php
                            $isUnread = !$message->hasFlag('seen');
                            $isActive = isset($selectedMessage) && $selectedMessage->getUid() == $message->getUid();
                            $sender = $message->getFrom()[0]->personal ?? $message->getFrom()[0]->mail ?? 'Unknown';
                            $subject = $message->getSubject() ?: '(Tanpa Subjek)';
                            
                            $bodyStr = '';
                            try {
                                $textBody = $message->getTextBody() ?? '';
                                if ($textBody) {
                                    $bodyStr = \Illuminate\Support\Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags($textBody))), 60);
                                }
                            } catch(\Exception $e) {}
                            
                            // Keep pagination query strings when clicking
                            $url = route('admin.email.show', ['uid' => $message->getUid()] + request()->query());
                        @endphp
                        
                        <a href="{{ $url }}" class="email-list-row block p-4 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors {{ $isActive ? 'active' : '' }} {{ $isUnread && !$isActive ? 'bg-gray-50/50 dark:bg-gray-800/30' : '' }}">
                            <div class="flex justify-between items-baseline mb-1">
                                <span class="text-sm truncate pr-2 {{ $isUnread ? 'font-bold text-gray-900 dark:text-white' : 'font-semibold text-gray-700 dark:text-gray-300' }}">
                                    {{ Str::limit($sender, 25) }}
                                </span>
                                <span class="text-xs flex-shrink-0 {{ $isUnread ? 'font-bold text-primary-600 dark:text-primary-400' : 'text-gray-500 dark:text-gray-400' }}">
                                    @php
                                        $dateObj = $message->getDate()[0] ?? null;
                                        if($dateObj) {
                                            if($dateObj->isToday()) echo $dateObj->format('H:i');
                                            elseif($dateObj->isCurrentYear()) echo $dateObj->format('d M');
                                            else echo $dateObj->format('d/m/Y');
                                        }
                                    @endphp
                                </span>
                            </div>
                            <div class="text-sm mb-1 truncate {{ $isUnread ? 'font-bold text-gray-800 dark:text-gray-200' : 'text-gray-700 dark:text-gray-300' }}">
                                {{ $subject }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2 leading-relaxed">
                                {{ $bodyStr }}
                            </div>
                        </a>
                    @endforeach
                </div>
                
                <!-- Pagination Mini -->
                @if($messages->hasPages())
                <div class="p-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 flex justify-center">
                    {{ $messages->links('vendor.pagination.simple-tailwind') }}
                </div>
                @endif
                
            @elseif(!isset($imapError))
                <div class="p-8 text-center">
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Kotak masuk kosong.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Right Column: Reading Pane (Flex-1) -->
    <div class="flex-1 flex flex-col min-w-0 bg-white dark:bg-gray-900 {{ !isset($selectedMessage) ? 'hidden md:flex' : 'flex' }}">
        
        @if(isset($selectedMessage))
            <!-- Mobile Back Button -->
            <div class="md:hidden p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 flex items-center">
                <a href="{{ route('admin.email.index') }}" class="flex items-center text-sm font-medium text-gray-600 dark:text-gray-300">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    Kembali
                </a>
            </div>

            <!-- Email Actions Toolbar -->
            <div class="px-6 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between bg-white dark:bg-gray-900 flex-shrink-0">
                <div class="flex items-center gap-2">
                    <button class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-full" title="Balas"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg></button>
                    <button class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-full" title="Hapus"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                </div>
                <div class="flex items-center gap-2">
                    <button class="p-2 text-gray-400 hover:text-yellow-400 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-full" title="Beri Bintang"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg></button>
                </div>
            </div>

            <!-- Email Content Scrollable -->
            <div class="flex-1 overflow-y-auto">
                <!-- Subject -->
                <div class="px-8 pt-8 pb-4">
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">
                        {{ $selectedMessage->getSubject() ?: '(Tanpa Subjek)' }}
                    </h1>
                </div>
                
                <!-- Sender Info -->
                <div class="px-8 pb-6 flex items-start justify-between">
                    <div class="flex items-center gap-4">
                        <div class="h-12 w-12 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-bold text-xl flex-shrink-0">
                            {{ strtoupper(substr($selectedMessage->getFrom()[0]->personal ?? $selectedMessage->getFrom()[0]->mail ?? 'U', 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-base font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                {{ $selectedMessage->getFrom()[0]->personal ?? 'Unknown Sender' }} 
                                <span class="text-xs font-normal text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-800 px-2 py-0.5 rounded-full">&lt;{{ $selectedMessage->getFrom()[0]->mail }}&gt;</span>
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Kepada: {{ $selectedMessage->getTo()[0]->mail ?? 'admin@kopkarskf.com' }}
                            </p>
                        </div>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">
                            @php
                                $selDate = $selectedMessage->getDate()[0] ?? null;
                                if($selDate) echo $selDate->format('d M Y, H:i');
                            @endphp
                        </p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                            {{ $selDate ? $selDate->diffForHumans() : '' }}
                        </p>
                    </div>
                </div>

                <!-- Attachments -->
                @if($selectedMessage->getAttachments()->count() > 0)
                <div class="px-8 py-4 mx-8 mb-6 bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl">
                    <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                        Lampiran ({{ $selectedMessage->getAttachments()->count() }})
                    </h3>
                    <ul class="flex flex-wrap gap-3">
                        @foreach($selectedMessage->getAttachments() as $attachment)
                        <li class="flex items-center p-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg shadow-sm hover:border-primary-400 transition-colors cursor-pointer">
                            <div class="p-2 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded mr-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-200 truncate w-48">{{ $attachment->name }}</p>
                                <p class="text-xs text-gray-500">{{ number_format($attachment->size / 1024, 2) }} KB</p>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <!-- Email Body Render -->
                <div class="px-8 pb-12 text-gray-800 dark:text-gray-200 text-base leading-relaxed">
                    @if($selectedMessage->hasHTMLBody())
                        <!-- Using iframe approach or prose. Prose is safer for dark mode compatibility -->
                        <div class="prose dark:prose-invert max-w-none email-html-content">
                            {!! $selectedMessage->getHTMLBody() !!}
                        </div>
                    @else
                        <div class="whitespace-pre-wrap font-sans">
                            {{ $selectedMessage->getTextBody() }}
                        </div>
                    @endif
                </div>
            </div>
            
        @else
            <!-- Empty State (No Email Selected) -->
            <div class="flex-1 flex flex-col items-center justify-center text-center p-8 bg-gray-50/50 dark:bg-gray-900/50">
                <div class="w-24 h-24 mb-6 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                    <svg class="w-12 h-12 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Pilih pesan untuk dibaca</h3>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 max-w-sm">Klik salah satu email di daftar sebelah kiri untuk melihat isi pesan secara lengkap.</p>
            </div>
        @endif
    </div>
</div>

<style>
    /* Add some styles to fix HTML email tables breaking layout */
    .email-html-content table { max-width: 100% !important; }
    .email-html-content img { max-width: 100% !important; height: auto !important; }
</style>
@endsection
