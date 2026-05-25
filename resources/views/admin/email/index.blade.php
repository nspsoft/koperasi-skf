@extends('layouts.app')

@section('title', isset($selectedMessage) ? $selectedMessage->getSubject() : 'Email Organisasi')

@php
    $activeFolder = $activeFolder ?? 'inbox';
    $folders = [
        'inbox'  => ['label' => 'Kotak Masuk', 'icon' => 'M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4', 'color' => '#4f46e5', 'bg' => '#eef2ff'],
        'sent'   => ['label' => 'Terkirim',    'icon' => 'M12 19l9 2-9-18-9 18 9-2zm0 0v-8', 'color' => '#0891b2', 'bg' => '#ecfeff'],
        'drafts' => ['label' => 'Draf',        'icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z', 'color' => '#d97706', 'bg' => '#fffbeb'],
        'trash'  => ['label' => 'Sampah',      'icon' => 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16', 'color' => '#dc2626', 'bg' => '#fef2f2'],
        'spam'   => ['label' => 'Spam',         'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z', 'color' => '#9333ea', 'bg' => '#faf5ff'],
    ];
    $cf = $folders[$activeFolder] ?? $folders['inbox'];
@endphp

@push('styles')
<style>
    /* ===== MAIN LAYOUT ===== */
    .email-page { display: flex; flex-direction: column; height: calc(100vh - 64px); overflow: hidden; }

    /* ===== TOP HEADER BAR ===== */
    .email-topbar {
        display: flex; align-items: center; justify-content: space-between;
        padding: 0 20px; height: 52px; flex-shrink: 0;
        background: #fff; border-bottom: 2px solid #f3f4f6;
    }
    .email-topbar-left { display: flex; align-items: center; gap: 4px; }
    .email-topbar-right { display: flex; align-items: center; gap: 8px; }

    .folder-tab {
        display: flex; align-items: center; gap: 7px;
        padding: 12px 18px; font-size: 13px; font-weight: 500;
        color: #6b7280; text-decoration: none;
        border-bottom: 2px solid transparent;
        margin-bottom: -2px; transition: all 0.2s;
        white-space: nowrap;
    }
    .folder-tab:hover { color: #374151; background: #f9fafb; }
    .folder-tab.active {
        color: {{ $cf['color'] }}; font-weight: 600;
        border-bottom-color: {{ $cf['color'] }};
    }
    .folder-tab svg { width: 16px; height: 16px; }

    .compose-btn {
        display: inline-flex; align-items: center; gap: 7px;
        padding: 8px 18px;
        background: {{ $cf['color'] }}; color: #fff;
        border-radius: 8px; font-size: 13px; font-weight: 600;
        text-decoration: none; transition: all 0.2s;
        box-shadow: 0 1px 4px {{ $cf['color'] }}40;
    }
    .compose-btn:hover { opacity: 0.9; transform: translateY(-1px); box-shadow: 0 3px 10px {{ $cf['color'] }}40; }
    .compose-btn svg { width: 16px; height: 16px; }

    .topbar-icon {
        padding: 8px; color: #9ca3af; border-radius: 8px;
        transition: all 0.15s; display: inline-flex; text-decoration: none;
    }
    .topbar-icon:hover { background: {{ $cf['bg'] }}; color: {{ $cf['color'] }}; }
    .topbar-icon svg { width: 18px; height: 18px; }

    /* ===== SPLIT CONTAINER ===== */
    .email-split { display: flex; flex: 1; overflow: hidden; }

    /* ===== LEFT: EMAIL LIST ===== */
    .email-list-pane {
        width: 400px; min-width: 400px;
        border-right: 1px solid #e5e7eb;
        display: flex; flex-direction: column;
        overflow: hidden; background: #fff;
    }
    .email-search-bar { padding: 12px; flex-shrink: 0; }
    .email-search-input {
        width: 100%; padding: 9px 12px 9px 36px;
        border: 1px solid #e5e7eb; border-radius: 10px;
        font-size: 13px; background: #f9fafb; outline: none; transition: all 0.2s;
    }
    .email-search-input:focus { border-color: {{ $cf['color'] }}; box-shadow: 0 0 0 3px {{ $cf['color'] }}15; }
    .email-list-scroll { flex: 1; overflow-y: auto; }
    .email-list-item {
        display: block; padding: 14px 16px;
        border-bottom: 1px solid #f3f4f6;
        cursor: pointer; transition: all 0.12s;
        text-decoration: none; border-left: 3px solid transparent;
    }
    .email-list-item:hover { background: {{ $cf['bg'] }}; }
    .email-list-item.active { background: {{ $cf['bg'] }}; border-left-color: {{ $cf['color'] }}; }
    .email-list-item.unread { background: #fafbfc; }
    .email-sender { font-size: 14px; color: #111827; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 220px; }
    .email-sender.bold { font-weight: 700; }
    .email-date { font-size: 12px; color: #6b7280; white-space: nowrap; flex-shrink: 0; }
    .email-date.bold { font-weight: 700; color: {{ $cf['color'] }}; }
    .email-subject { font-size: 13px; color: #374151; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-top: 4px; }
    .email-subject.bold { font-weight: 700; color: #111827; }
    .email-snippet {
        font-size: 12px; color: #9ca3af; margin-top: 4px;
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;
        overflow: hidden; line-height: 1.5;
    }

    /* ===== RIGHT: READING PANE ===== */
    .email-reading-pane {
        flex: 1; display: flex; flex-direction: column;
        overflow: hidden; background: #fafbfc; min-width: 0;
    }

    /* Empty State */
    .email-empty-state {
        flex: 1; display: flex; flex-direction: column;
        align-items: center; justify-content: center; padding: 40px;
    }
    .email-empty-icon {
        width: 72px; height: 72px; border-radius: 18px;
        background: {{ $cf['bg'] }};
        display: flex; align-items: center; justify-content: center;
        margin-bottom: 20px;
    }
    .email-empty-icon svg { width: 32px; height: 32px; color: {{ $cf['color'] }}; }
    .email-empty-state h3 { font-size: 17px; font-weight: 600; color: #374151; margin-bottom: 6px; }
    .email-empty-state p { font-size: 13px; color: #9ca3af; max-width: 280px; text-align: center; }

    /* Reading Toolbar */
    .reading-toolbar {
        padding: 10px 24px; border-bottom: 1px solid #e5e7eb;
        display: flex; align-items: center; justify-content: space-between;
        flex-shrink: 0; background: #fff;
    }
    .toolbar-btn {
        padding: 8px; border: none; background: none; color: #9ca3af;
        border-radius: 8px; cursor: pointer; transition: all 0.15s; display: inline-flex;
    }
    .toolbar-btn:hover { background: {{ $cf['bg'] }}; color: {{ $cf['color'] }}; }
    .toolbar-btn svg { width: 20px; height: 20px; }
    .reading-content { flex: 1; overflow-y: auto; background: #fff; }
    .reading-subject { font-size: 22px; font-weight: 700; color: #111827; padding: 28px 32px 16px; line-height: 1.3; }
    .reading-sender { padding: 0 32px 24px; display: flex; align-items: flex-start; justify-content: space-between; }
    .reading-sender-left { display: flex; align-items: center; gap: 14px; }
    .reading-avatar {
        width: 44px; height: 44px; border-radius: 50%;
        background: {{ $cf['bg'] }}; color: {{ $cf['color'] }};
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 18px; flex-shrink: 0;
    }
    .reading-sender-name { font-size: 15px; font-weight: 600; color: #111827; }
    .reading-sender-email { font-size: 12px; color: #9ca3af; margin-top: 2px; }
    .reading-sender-to { font-size: 12px; color: #9ca3af; margin-top: 4px; }
    .reading-date { font-size: 13px; color: #6b7280; text-align: right; flex-shrink: 0; }
    .reading-date-relative { font-size: 11px; color: #9ca3af; margin-top: 2px; }
    .reading-attachments {
        margin: 0 32px 24px; padding: 16px;
        background: {{ $cf['bg'] }}; border: 1px solid {{ $cf['color'] }}22;
        border-radius: 12px;
    }
    .reading-attachments h4 { font-size: 11px; font-weight: 700; text-transform: uppercase; color: {{ $cf['color'] }}; letter-spacing: 0.05em; margin-bottom: 12px; }
    .reading-attachment-item {
        display: inline-flex; align-items: center; padding: 10px 14px;
        background: #fff; border: 1px solid #e5e7eb; border-radius: 8px;
        margin-right: 8px; margin-bottom: 8px; cursor: pointer; transition: border 0.15s;
    }
    .reading-attachment-item:hover { border-color: {{ $cf['color'] }}; }
    .reading-body { padding: 0 32px 48px; font-size: 15px; color: #374151; line-height: 1.7; }
    .reading-body img { max-width: 100% !important; height: auto !important; }
    .reading-body table { max-width: 100% !important; }
    .quick-reply { margin: 0 32px 32px; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; transition: all 0.2s; }
    .quick-reply:hover { border-color: {{ $cf['color'] }}; box-shadow: 0 2px 8px {{ $cf['color'] }}15; }
    .quick-reply a { display: flex; align-items: center; gap: 10px; padding: 16px 20px; text-decoration: none; color: #9ca3af; font-size: 14px; transition: all 0.15s; }
    .quick-reply a:hover { background: {{ $cf['bg'] }}; color: {{ $cf['color'] }}; }

    /* Compose */
    .compose-form { flex: 1; display: flex; flex-direction: column; overflow: hidden; background: #fff; }
    .compose-header { padding: 12px 24px 8px; border-bottom: 1px solid #e5e7eb; flex-shrink: 0; }
    .compose-header h2 { font-size: 17px; font-weight: 700; color: #111827; margin-bottom: 2px; }
    .compose-header p { font-size: 12px; color: #9ca3af; }
    .compose-fields { flex-shrink: 0; }
    .compose-field { display: flex; align-items: center; border-bottom: 1px solid #f3f4f6; padding: 0 24px; }
    .compose-field label { font-size: 13px; font-weight: 500; color: #6b7280; width: 60px; flex-shrink: 0; }
    .compose-field input { flex: 1; border: none; outline: none; padding: 10px 0; font-size: 14px; color: #111827; background: transparent; }
    .compose-body-area { flex: 1; padding: 10px 24px; overflow: hidden; }
    .compose-body-area textarea { width: 100%; height: 100%; min-height: 120px; border: none; outline: none; resize: none; font-size: 14px; color: #374151; line-height: 1.7; font-family: inherit; background: transparent; }
    .compose-footer { padding: 16px 32px; border-top: 1px solid #e5e7eb; display: flex; align-items: center; justify-content: space-between; flex-shrink: 0; background: #fafbfc; }
    .btn-send {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 10px 24px; background: {{ $cf['color'] }}; color: #fff;
        border: none; border-radius: 10px; font-size: 14px; font-weight: 600;
        cursor: pointer; transition: all 0.2s; box-shadow: 0 2px 8px {{ $cf['color'] }}40;
    }
    .btn-send:hover { transform: translateY(-1px); box-shadow: 0 4px 12px {{ $cf['color'] }}50; }
    .btn-discard { padding: 10px 16px; background: none; border: 1px solid #e5e7eb; border-radius: 10px; font-size: 13px; color: #6b7280; cursor: pointer; transition: all 0.2s; text-decoration: none; }
    .btn-discard:hover { background: #fee2e2; color: #dc2626; border-color: #fecaca; }

    /* Alerts */
    .email-alert { padding: 10px 16px; font-size: 13px; font-weight: 500; }
    .email-alert-success { background: #ecfdf5; color: #059669; }
    .email-alert-error { background: #fef2f2; color: #dc2626; }

    /* Mobile */
    @media (max-width: 768px) {
        .email-list-pane { width: 100%; min-width: 100%; }
        .email-list-pane.has-selected, .email-list-pane.has-compose { display: none; }
        .email-reading-pane.no-selected { display: none; }
        .mobile-back { display: flex !important; }
        .folder-tab span { display: none; }
        .folder-tab { padding: 12px 10px; }
    }
    @media (min-width: 769px) { .mobile-back { display: none !important; } }

    /* Dark mode */
    .dark .email-topbar { background: #111827; border-color: #374151; }
    .dark .folder-tab { color: #9ca3af; }
    .dark .folder-tab:hover { background: #1f2937; color: #f3f4f6; }
    .dark .email-list-pane { background: #111827; border-color: #374151; }
    .dark .email-reading-pane { background: #0f172a; }
    .dark .reading-content { background: #111827; }
    .dark .reading-toolbar { border-color: #374151; background: #111827; }
    .dark .email-list-item { border-color: #1f2937; }
    .dark .email-list-item:hover { background: {{ $cf['color'] }}10; }
    .dark .email-list-item.active { background: {{ $cf['color'] }}15; }
    .dark .email-sender { color: #f3f4f6; }
    .dark .email-subject { color: #d1d5db; }
    .dark .email-subject.bold { color: #f3f4f6; }
    .dark .email-search-input { background: #1f2937; border-color: #374151; color: #f3f4f6; }
    .dark .reading-subject { color: #f3f4f6; }
    .dark .reading-sender-name { color: #f3f4f6; }
    .dark .reading-body { color: #d1d5db; }
    .dark .compose-header h2 { color: #f3f4f6; }
    .dark .compose-field { border-color: #374151; }
    .dark .compose-field label { color: #9ca3af; }
    .dark .compose-field input { color: #f3f4f6; }
    .dark .compose-body-area textarea { color: #d1d5db; }
    .dark .compose-footer { background: #1f2937; border-color: #374151; }
</style>
@endpush

@section('content')
<div class="email-page">

    <!-- ===== STATIC TOP HEADER ===== -->
    <div class="email-topbar">
        <div class="email-topbar-left">
            @foreach($folders as $slug => $info)
                <a href="{{ route('admin.email.folder', $slug) }}" 
                   class="folder-tab {{ $activeFolder === $slug ? 'active' : '' }}"
                   style="{{ $activeFolder === $slug ? 'color:' . $info['color'] . ';border-bottom-color:' . $info['color'] . ';' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $info['icon'] }}"></path></svg>
                    <span>{{ $info['label'] }}</span>
                </a>
            @endforeach
        </div>
        <div class="email-topbar-right">
            <a href="{{ route('admin.email.compose') }}" class="compose-btn">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tulis Email
            </a>
            <a href="{{ route('admin.email.folder', $activeFolder) }}" class="topbar-icon" title="Segarkan">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            </a>
            <a href="https://webmail.kopkarskf.com" target="_blank" class="topbar-icon" title="Buka Webmail">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
            </a>
        </div>
    </div>

    <!-- ===== SPLIT PANE BELOW ===== -->
    <div class="email-split">

        <!-- LEFT: Email List Pane -->
        <div class="email-list-pane {{ isset($selectedMessage) ? 'has-selected' : '' }} {{ isset($composeMode) ? 'has-compose' : '' }}">
            
            <!-- Search -->
            <div class="email-search-bar">
                <div style="position:relative;">
                    <svg style="position:absolute;left:12px;top:50%;transform:translateY(-50%);width:15px;height:15px;color:#9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <input type="text" placeholder="Cari di {{ $cf['label'] }}..." class="email-search-input">
                </div>
            </div>

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="email-alert email-alert-success">✓ {{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="email-alert email-alert-error">✕ {{ session('error') }}</div>
            @endif
            @if(isset($imapError))
                <div class="email-alert email-alert-error">{{ $imapError }}</div>
            @endif

            <!-- Scrollable Email List -->
            <div class="email-list-scroll">
                @if(isset($messages) && $messages->count() > 0)
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
                                    $bodyStr = \Illuminate\Support\Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags($textBody))), 80);
                                }
                            } catch(\Exception $e) {}
                            
                            $url = route('admin.email.show', ['folder' => $activeFolder, 'uid' => $message->getUid()] + request()->query());
                        @endphp
                        
                        <a href="{{ $url }}" class="email-list-item {{ $isActive ? 'active' : '' }} {{ $isUnread && !$isActive ? 'unread' : '' }}">
                            <div style="display:flex;justify-content:space-between;align-items:baseline;">
                                <span class="email-sender {{ $isUnread ? 'bold' : '' }}">{{ Str::limit($sender, 28) }}</span>
                                <span class="email-date {{ $isUnread ? 'bold' : '' }}">
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
                            <div class="email-subject {{ $isUnread ? 'bold' : '' }}">
                                {{ $subject }}
                                @if($message->getAttachments()->count() > 0)
                                    <svg style="display:inline;width:14px;height:14px;vertical-align:middle;color:#9ca3af;margin-left:4px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                @endif
                            </div>
                            @if($bodyStr)
                                <div class="email-snippet">{{ $bodyStr }}</div>
                            @endif
                        </a>
                    @endforeach
                    
                    @if($messages->hasPages())
                    <div style="padding:12px 16px;border-top:1px solid #e5e7eb;background:#fafbfc;text-align:center;">
                        {{ $messages->links() }}
                    </div>
                    @endif
                @elseif(!isset($imapError))
                    <div class="email-empty-state">
                        <div class="email-empty-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $cf['icon'] }}"></path></svg>
                        </div>
                        <h3>{{ $cf['label'] }} Kosong</h3>
                        <p>Tidak ada email di folder ini.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- RIGHT: Reading Pane / Compose Pane -->
        <div class="email-reading-pane {{ !isset($selectedMessage) && !isset($composeMode) ? 'no-selected' : '' }}">
            
            @if(isset($composeMode))
                {{-- ==================== COMPOSE / REPLY ==================== --}}
                <a href="{{ route('admin.email.index') }}" class="mobile-back" style="padding:12px 16px;border-bottom:1px solid #e5e7eb;background:#fafbfc;align-items:center;gap:6px;text-decoration:none;color:#374151;font-size:14px;font-weight:500;">
                    <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    Kembali
                </a>

                <form action="{{ route('admin.email.send') }}" method="POST" class="compose-form">
                    @csrf
                    <div class="compose-header">
                        <h2>{{ $composeMode === 'reply' ? '↩ Balas Email' : '✏️ Tulis Email Baru' }}</h2>
                        <p>Dari: {{ config('mail.from.address', 'admin@kopkarskf.com') }}</p>
                    </div>
                    <div class="compose-fields">
                        <div class="compose-field">
                            <label for="compose-to">Kepada</label>
                            <input type="email" name="to" id="compose-to" value="{{ old('to', $composeTo ?? '') }}" placeholder="contoh@email.com" required>
                        </div>
                        <div class="compose-field">
                            <label for="compose-cc">CC</label>
                            <input type="text" name="cc" id="compose-cc" value="{{ old('cc') }}" placeholder="opsional, pisahkan dengan koma">
                        </div>
                        <div class="compose-field">
                            <label for="compose-subject">Subjek</label>
                            <input type="text" name="subject" id="compose-subject" value="{{ old('subject', $composeSubject ?? '') }}" placeholder="Subjek email" required>
                        </div>
                    </div>
                    <div class="compose-body-area">
                        <textarea name="body" id="compose-body" placeholder="Tulis pesan Anda di sini...">{{ old('body', $composeBody ?? '') }}</textarea>
                    </div>
                    @if($errors->any())
                    <div style="padding:12px 32px;">
                        @foreach($errors->all() as $error)
                            <p style="font-size:13px;color:#dc2626;margin-bottom:4px;">• {{ $error }}</p>
                        @endforeach
                    </div>
                    @endif
                    <div class="compose-footer">
                        <button type="submit" class="btn-send">
                            <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                            Kirim
                        </button>
                        <a href="{{ route('admin.email.index') }}" class="btn-discard">Batal</a>
                    </div>
                </form>

            @elseif(isset($selectedMessage))
                {{-- ==================== READING PANE ==================== --}}
                <a href="{{ route('admin.email.folder', $activeFolder) }}" class="mobile-back" style="padding:12px 16px;border-bottom:1px solid #e5e7eb;background:#fafbfc;align-items:center;gap:6px;text-decoration:none;color:#374151;font-size:14px;font-weight:500;">
                    <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    Kembali
                </a>

                <div class="reading-toolbar">
                    <div style="display:flex;align-items:center;gap:4px;">
                        <a href="{{ route('admin.email.reply', $selectedMessage->getUid()) }}" class="toolbar-btn" title="Balas">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                        </a>
                        <button class="toolbar-btn" title="Teruskan"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg></button>
                        <button class="toolbar-btn" title="Hapus"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                    </div>
                    <div style="display:flex;align-items:center;gap:4px;">
                        <button class="toolbar-btn" title="Bintang"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg></button>
                    </div>
                </div>

                <div class="reading-content">
                    <div class="reading-subject">{{ $selectedMessage->getSubject() ?: '(Tanpa Subjek)' }}</div>
                    <div class="reading-sender">
                        <div class="reading-sender-left">
                            <div class="reading-avatar">{{ strtoupper(substr($selectedMessage->getFrom()[0]->personal ?? $selectedMessage->getFrom()[0]->mail ?? 'U', 0, 1)) }}</div>
                            <div>
                                <div class="reading-sender-name">{{ $selectedMessage->getFrom()[0]->personal ?? 'Unknown Sender' }}</div>
                                <div class="reading-sender-email">&lt;{{ $selectedMessage->getFrom()[0]->mail }}&gt;</div>
                                <div class="reading-sender-to">Kepada: {{ $selectedMessage->getTo()[0]->mail ?? 'admin@kopkarskf.com' }}</div>
                            </div>
                        </div>
                        <div style="text-align:right;flex-shrink:0;">
                            @php $selDate = $selectedMessage->getDate()[0] ?? null; @endphp
                            <div class="reading-date">{{ $selDate ? $selDate->format('d M Y, H:i') : '' }}</div>
                            <div class="reading-date-relative">{{ $selDate ? $selDate->diffForHumans() : '' }}</div>
                        </div>
                    </div>

                    @if($selectedMessage->getAttachments()->count() > 0)
                    <div class="reading-attachments">
                        <h4>📎 Lampiran ({{ $selectedMessage->getAttachments()->count() }})</h4>
                        @foreach($selectedMessage->getAttachments() as $attachment)
                        <span class="reading-attachment-item">
                            <svg style="width:18px;height:18px;color:#ef4444;margin-right:10px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                            <span>
                                <span style="font-size:13px;font-weight:500;color:#374151;">{{ $attachment->name }}</span>
                                <span style="font-size:11px;color:#9ca3af;margin-left:6px;">({{ number_format($attachment->size / 1024, 2) }} KB)</span>
                            </span>
                        </span>
                        @endforeach
                    </div>
                    @endif

                    <div class="reading-body">
                        @if($selectedMessage->hasHTMLBody())
                            {!! $selectedMessage->getHTMLBody() !!}
                        @else
                            <div style="white-space:pre-wrap;">{{ $selectedMessage->getTextBody() }}</div>
                        @endif
                    </div>

                    <div class="quick-reply">
                        <a href="{{ route('admin.email.reply', $selectedMessage->getUid()) }}">
                            <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                            Klik di sini untuk membalas email ini...
                        </a>
                    </div>
                </div>

            @else
                {{-- ==================== EMPTY STATE ==================== --}}
                <div class="email-empty-state">
                    <div class="email-empty-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </div>
                    <h3>Pilih pesan untuk dibaca</h3>
                    <p>Klik salah satu email dari daftar di sebelah kiri.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
