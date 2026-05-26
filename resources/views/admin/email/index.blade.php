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

    /* Category Badges */
    .email-badge {
        display: inline-flex; align-items: center; gap: 3px;
        padding: 2px 8px; border-radius: 20px;
        font-size: 10px; font-weight: 600; letter-spacing: 0.02em;
        white-space: nowrap; vertical-align: middle;
        line-height: 16px;
    }
    .badge-invoice { background: #dbeafe; color: #1d4ed8; }
    .badge-otp { background: #fef3c7; color: #b45309; }
    .badge-notification { background: #ede9fe; color: #7c3aed; }
    .badge-system { background: #f3f4f6; color: #4b5563; }
    .badge-finance { background: #d1fae5; color: #065f46; }
    .badge-promo { background: #fce7f3; color: #be185d; }
    .badge-meeting { background: #ffedd5; color: #c2410c; }
    .badge-urgent { background: #fee2e2; color: #dc2626; }
    .dark .badge-invoice { background: #1e3a5f; color: #93c5fd; }
    .dark .badge-otp { background: #451a03; color: #fcd34d; }
    .dark .badge-notification { background: #2e1065; color: #c4b5fd; }
    .dark .badge-system { background: #1f2937; color: #9ca3af; }
    .dark .badge-finance { background: #064e3b; color: #6ee7b7; }
    .dark .badge-promo { background: #500724; color: #f9a8d4; }
    .dark .badge-meeting { background: #431407; color: #fdba74; }
    .dark .badge-urgent { background: #450a0a; color: #fca5a5; }

    /* Filter Chips */
    .email-filters { padding: 0 12px 8px; display: flex; gap: 4px; flex-wrap: wrap; flex-shrink: 0; }
    .filter-chip {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 500;
        border: 1px solid #e5e7eb; color: #6b7280; cursor: pointer;
        transition: all 0.15s; background: #fff; text-decoration: none;
    }
    .filter-chip:hover { border-color: {{ $cf['color'] }}; color: {{ $cf['color'] }}; }
    .filter-chip.active { background: {{ $cf['color'] }}; color: #fff; border-color: {{ $cf['color'] }}; }
    .filter-chip svg { width: 12px; height: 12px; }
    .search-result-bar {
        padding: 8px 12px; background: {{ $cf['bg'] }}; border-bottom: 1px solid #e5e7eb;
        font-size: 12px; color: {{ $cf['color'] }}; display: flex; align-items: center;
        justify-content: space-between; flex-shrink: 0;
    }
    .search-result-bar a { font-size: 12px; color: #dc2626; text-decoration: none; font-weight: 500; }
    .search-result-bar a:hover { text-decoration: underline; }
    .dark .filter-chip { background: #1f2937; border-color: #374151; color: #9ca3af; }
    .dark .filter-chip:hover { border-color: {{ $cf['color'] }}; color: {{ $cf['color'] }}; }
    .dark .filter-chip.active { background: {{ $cf['color'] }}; color: #fff; }
    .dark .search-result-bar { background: {{ $cf['color'] }}10; border-color: #374151; }

    /* AI Reply */
    .ai-reply-bar {
        padding: 8px 24px; display: flex; align-items: center; gap: 8px;
        border-bottom: 1px solid #f3f4f6; flex-shrink: 0;
        background: linear-gradient(135deg, #faf5ff, #eef2ff);
    }
    .ai-reply-btn {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 7px 14px; border-radius: 8px; font-size: 12px; font-weight: 600;
        border: none; cursor: pointer; transition: all 0.2s;
        background: linear-gradient(135deg, #8b5cf6, #6366f1);
        color: #fff; box-shadow: 0 1px 4px rgba(139,92,246,0.3);
    }
    .ai-reply-btn:hover { transform: translateY(-1px); box-shadow: 0 3px 10px rgba(139,92,246,0.4); }
    .ai-reply-btn:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }
    .ai-reply-btn svg { width: 14px; height: 14px; }
    .ai-tone-select {
        padding: 6px 10px; border: 1px solid #d1d5db; border-radius: 6px;
        font-size: 12px; color: #374151; background: #fff; outline: none;
    }
    .ai-tone-select:focus { border-color: #8b5cf6; }
    .ai-status { font-size: 11px; color: #6b7280; margin-left: 4px; }
    .ai-status.loading { color: #8b5cf6; }
    .ai-status.error { color: #dc2626; }
    @keyframes aiPulse { 0%,100% { opacity: 1; } 50% { opacity: 0.5; } }
    .ai-loading { animation: aiPulse 1.5s infinite; }
    .dark .ai-reply-bar { background: linear-gradient(135deg, #1e1b4b, #172554); }
    .dark .ai-tone-select { background: #1f2937; border-color: #374151; color: #d1d5db; }

    /* Template Selector */
    .template-btn {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 5px 12px; border-radius: 6px; font-size: 11px; font-weight: 600;
        border: 1px solid #d1d5db; color: #6b7280; cursor: pointer;
        transition: all 0.15s; background: #fff;
    }
    .template-btn:hover { border-color: {{ $cf['color'] }}; color: {{ $cf['color'] }}; }
    .template-btn svg { width: 14px; height: 14px; }
    .template-dropdown {
        position: absolute; top: 100%; right: 0; z-index: 50;
        background: #fff; border: 1px solid #e5e7eb; border-radius: 10px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.12); min-width: 260px;
        padding: 6px; display: none; margin-top: 4px;
    }
    .template-dropdown.show { display: block; }
    .template-item {
        display: block; padding: 10px 12px; border-radius: 6px; cursor: pointer;
        font-size: 13px; color: #374151; transition: background 0.1s; border: none;
        background: none; width: 100%; text-align: left;
    }
    .template-item:hover { background: {{ $cf['bg'] }}; }
    .dark .template-btn { background: #1f2937; border-color: #374151; color: #9ca3af; }
    .dark .template-dropdown { background: #1f2937; border-color: #374151; }
    .dark .template-item { color: #d1d5db; }
    .dark .template-item:hover { background: #111827; }

    /* Member Autocomplete */
    .member-autocomplete {
        position: absolute; top: 100%; left: 60px; right: 0; z-index: 50;
        background: #fff; border: 1px solid #e5e7eb; border-radius: 10px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.12); max-height: 200px;
        overflow-y: auto; display: none;
    }
    .member-autocomplete.show { display: block; }
    .member-item {
        display: flex; align-items: center; gap: 10px;
        padding: 10px 14px; cursor: pointer; transition: background 0.1s;
        border: none; background: none; width: 100%; text-align: left;
    }
    .member-item:hover { background: {{ $cf['bg'] }}; }
    .member-item-avatar {
        width: 32px; height: 32px; border-radius: 50%;
        background: {{ $cf['color'] }}20; color: {{ $cf['color'] }};
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 13px; flex-shrink: 0;
    }
    .member-item-info { flex: 1; min-width: 0; }
    .member-item-name { font-size: 13px; font-weight: 600; color: #111827; }
    .member-item-email { font-size: 11px; color: #6b7280; }
    .member-item-dept { font-size: 10px; color: #9ca3af; background: #f3f4f6; padding: 1px 6px; border-radius: 4px; }
    .dark .member-autocomplete { background: #1f2937; border-color: #374151; }
    .dark .member-item:hover { background: #111827; }
    .dark .member-item-name { color: #f3f4f6; }
    .dark .member-item-dept { background: #374151; color: #9ca3af; }

    /* Signature Preview */
    .signature-preview {
        padding: 8px 24px; flex-shrink: 0; border-top: 1px dashed #e5e7eb;
        font-size: 11px; color: #9ca3af; line-height: 1.6;
    }
    .signature-preview strong { color: #6b7280; font-weight: 600; }
    .dark .signature-preview { border-color: #374151; }

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
        margin-right: 8px; margin-bottom: 8px; cursor: pointer; transition: all 0.15s;
        text-decoration: none;
    }
    .reading-attachment-item:hover { border-color: {{ $cf['color'] }}; background: {{ $cf['bg'] }}; }
    .reading-attachment-item .dl-icon { opacity: 0; transition: opacity 0.15s; margin-left: 8px; }
    .reading-attachment-item:hover .dl-icon { opacity: 1; }

    /* File Upload */
    .file-upload-area { padding: 0 24px 10px; flex-shrink: 0; }
    .file-upload-label {
        display: flex; align-items: center; justify-content: center; gap: 8px;
        padding: 12px; border: 2px dashed #d1d5db; border-radius: 10px;
        color: #6b7280; font-size: 13px; cursor: pointer; transition: all 0.2s;
    }
    .file-upload-label:hover { border-color: {{ $cf['color'] }}; color: {{ $cf['color'] }}; background: {{ $cf['bg'] }}; }
    .file-upload-label svg { width: 18px; height: 18px; }
    .file-upload-list { padding: 4px 24px 0; }
    .file-upload-item {
        display: flex; align-items: center; gap: 8px;
        padding: 6px 10px; background: {{ $cf['bg'] }}; border-radius: 6px;
        font-size: 12px; color: #374151; margin-bottom: 4px;
    }
    .file-upload-item .remove-file { color: #dc2626; cursor: pointer; margin-left: auto; font-weight: 700; }
    .dark .file-upload-label { border-color: #374151; color: #9ca3af; }
    .dark .file-upload-label:hover { border-color: {{ $cf['color'] }}; background: {{ $cf['color'] }}10; }
    .dark .file-upload-item { background: #1f2937; color: #d1d5db; }
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
    .dark .reading-attachments { background: #1f2937; border-color: #374151; }
    .dark .reading-attachment-item { background: #111827; border-color: #374151; color: #d1d5db; }
    .dark .reading-date { color: #9ca3af; }
    .dark .reading-sender-email { color: #6b7280; }
    .dark .reading-sender-to { color: #6b7280; }
    .dark .quick-reply { border-color: #374151; }
    .dark .quick-reply a { color: #6b7280; }
    .dark .quick-reply:hover { border-color: {{ $cf['color'] }}; }
    .dark .quick-reply a:hover { background: {{ $cf['color'] }}15; color: {{ $cf['color'] }}; }
    .dark .compose-form { background: #111827; }
    .dark .compose-header { border-color: #374151; }
    .dark .compose-header h2 { color: #f3f4f6; }
    .dark .compose-header p { color: #6b7280; }
    .dark .compose-field { border-color: #1f2937; }
    .dark .compose-field label { color: #9ca3af; }
    .dark .compose-field input { color: #f3f4f6; }
    .dark .compose-field input::placeholder { color: #4b5563; }
    .dark .compose-body-area textarea { color: #d1d5db; }
    .dark .compose-body-area textarea::placeholder { color: #4b5563; }
    .dark .compose-footer { background: #0f172a; border-color: #374151; }
    .dark .btn-discard { border-color: #374151; color: #9ca3af; }
    .dark .btn-discard:hover { background: rgba(220,38,38,0.15); color: #f87171; border-color: #7f1d1d; }
    .dark .email-empty-state h3 { color: #f3f4f6; }
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
            <form action="{{ route('admin.email.send-loan-reminders') }}" method="POST" style="display:inline;" onsubmit="return confirm('Kirim email pengingat cicilan ke semua anggota yang memiliki tagihan jatuh tempo (3 hari ke depan & terlambat)?')">
                @csrf
                <button type="submit" class="topbar-icon" title="Kirim Pengingat Cicilan Otomatis" style="border: none; background: transparent; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; outline: none; padding: 8px;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                </button>
            </form>
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
                <form action="{{ route('admin.email.folder', $activeFolder) }}" method="GET" style="position:relative;">
                    <svg style="position:absolute;left:12px;top:50%;transform:translateY(-50%);width:15px;height:15px;color:#9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <input type="text" name="q" value="{{ $searchQuery ?? '' }}" placeholder="Cari di {{ $cf['label'] }}..." class="email-search-input" autocomplete="off">
                    @if(!empty($searchQuery))
                    <a href="{{ route('admin.email.folder', $activeFolder) }}" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);color:#9ca3af;" title="Hapus pencarian">
                        <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </a>
                    @endif
                </form>
            </div>

            <!-- Category Filters -->
            <div class="email-filters">
                <span class="filter-chip active" data-filter="all" onclick="filterByCategory('all', this)">
                    Semua
                </span>
                <span class="filter-chip" data-filter="badge-invoice" onclick="filterByCategory('badge-invoice', this)">
                    <span style="width:6px;height:6px;border-radius:50%;background:#1d4ed8;display:inline-block;"></span> Invoice
                </span>
                <span class="filter-chip" data-filter="badge-otp" onclick="filterByCategory('badge-otp', this)">
                    <span style="width:6px;height:6px;border-radius:50%;background:#b45309;display:inline-block;"></span> OTP
                </span>
                <span class="filter-chip" data-filter="badge-system" onclick="filterByCategory('badge-system', this)">
                    <span style="width:6px;height:6px;border-radius:50%;background:#4b5563;display:inline-block;"></span> Sistem
                </span>
                <span class="filter-chip" data-filter="badge-meeting" onclick="filterByCategory('badge-meeting', this)">
                    <span style="width:6px;height:6px;border-radius:50%;background:#c2410c;display:inline-block;"></span> Rapat
                </span>
                <span class="filter-chip" data-filter="badge-finance" onclick="filterByCategory('badge-finance', this)">
                    <span style="width:6px;height:6px;border-radius:50%;background:#065f46;display:inline-block;"></span> Keuangan
                </span>
                <span class="filter-chip" data-filter="badge-urgent" onclick="filterByCategory('badge-urgent', this)">
                    <span style="width:6px;height:6px;border-radius:50%;background:#dc2626;display:inline-block;"></span> Urgent
                </span>
                <span class="filter-chip" data-filter="no-badge" onclick="filterByCategory('no-badge', this)">
                    Tanpa Kategori
                </span>
            </div>

            <!-- Search Result Indicator -->
            @if(!empty($searchQuery))
            <div class="search-result-bar">
                <span>🔍 Hasil pencarian: "<strong>{{ $searchQuery }}</strong>"</span>
                <a href="{{ route('admin.email.folder', $activeFolder) }}">✕ Reset</a>
            </div>
            @endif

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

                            // Auto-detect category badge
                            $subjectLower = strtolower($subject);
                            $senderLower = strtolower($sender . ' ' . ($message->getFrom()[0]->mail ?? ''));
                            $badge = null;
                            
                            if (preg_match('/\b(urgent|darurat|segera|penting)\b/i', $subject)) {
                                $badge = ['label' => 'Urgent', 'class' => 'badge-urgent'];
                            } elseif (preg_match('/\b(invoice|inv\b|tagihan|faktur|billing|pembayaran)/i', $subject) || preg_match('/\bnv\s+vm\b/i', $subject)) {
                                $badge = ['label' => 'Invoice', 'class' => 'badge-invoice'];
                            } elseif (preg_match('/\b(otp|verif|verification|kode|code|token|sandi)\b/i', $subject)) {
                                $badge = ['label' => 'OTP', 'class' => 'badge-otp'];
                            } elseif (preg_match('/\b(rapat|meeting|undangan|rat |agenda)\b/i', $subject)) {
                                $badge = ['label' => 'Rapat', 'class' => 'badge-meeting'];
                            } elseif (preg_match('/\b(transfer|mutasi|rekening|bank|saldo|keuangan)\b/i', $subject)) {
                                $badge = ['label' => 'Keuangan', 'class' => 'badge-finance'];
                            } elseif (preg_match('/\b(promo|diskon|penawaran|offer|sale|marketing)\b/i', $subject)) {
                                $badge = ['label' => 'Promo', 'class' => 'badge-promo'];
                            } elseif (preg_match('/\b(notif|alert|peringatan|reminder|pengingat)\b/i', $subject)) {
                                $badge = ['label' => 'Notifikasi', 'class' => 'badge-notification'];
                            } elseif (preg_match('/(cpanel|server|system|cron|backup|config)/i', $senderLower)) {
                                $badge = ['label' => 'Sistem', 'class' => 'badge-system'];
                            }
                        @endphp
                        
                        <a href="{{ $url }}" class="email-list-item {{ $isActive ? 'active' : '' }} {{ $isUnread && !$isActive ? 'unread' : '' }}" data-category="{{ $badge ? $badge['class'] : 'no-badge' }}">
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
                            <div style="display:flex;align-items:center;gap:6px;margin-top:4px;">
                                <span class="email-subject {{ $isUnread ? 'bold' : '' }}" style="margin-top:0;">
                                    {{ $subject }}
                                    @if($message->getAttachments()->count() > 0)
                                        <svg style="display:inline;width:14px;height:14px;vertical-align:middle;color:#9ca3af;margin-left:4px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                    @endif
                                </span>
                                @if($badge)
                                    <span class="email-badge {{ $badge['class'] }}">{{ $badge['label'] }}</span>
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

                <form action="{{ route('admin.email.send') }}" method="POST" class="compose-form" enctype="multipart/form-data">
                    @csrf
                    <div class="compose-header">
                        <div style="display:flex;align-items:center;justify-content:space-between;">
                            <div>
                                <h2>{{ $composeMode === 'reply' ? '↩ Balas Email' : '✏️ Tulis Email Baru' }}</h2>
                                <p>Dari: {{ config('mail.from.address', 'admin@kopkarskf.com') }}</p>
                            </div>
                            @if($composeMode === 'new')
                            <div style="position:relative;">
                                <button type="button" class="template-btn" onclick="toggleTemplates()">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    Template
                                </button>
                                <div class="template-dropdown" id="template-dropdown">
                                    <div style="padding:6px 12px;font-size:11px;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:0.05em;">Pilih Template</div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="compose-fields">
                        <div class="compose-field" style="position:relative;">
                            <label for="compose-to">Kepada</label>
                            <input type="email" name="to" id="compose-to" value="{{ old('to', $composeTo ?? '') }}" placeholder="ketik nama anggota atau email..." required autocomplete="off" oninput="searchMembers(this.value)" onfocus="searchMembers(this.value)">
                            <div class="member-autocomplete" id="member-dropdown"></div>
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

                    @if($composeMode === 'reply')
                    <!-- AI Reply Bar -->
                    <div class="ai-reply-bar">
                        <button type="button" class="ai-reply-btn" id="ai-reply-btn" onclick="generateAiReply()">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            <span id="ai-btn-text">✨ Balas dengan AI</span>
                        </button>
                        <select class="ai-tone-select" id="ai-tone">
                            <option value="formal">🎩 Formal</option>
                            <option value="friendly">😊 Ramah</option>
                            <option value="brief">⚡ Singkat</option>
                        </select>
                        <span class="ai-status" id="ai-status"></span>
                    </div>
                    @endif

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
                    <div class="signature-preview">
                        <strong>✍ Tanda tangan otomatis:</strong><br>
                        Hormat kami, {{ \App\Models\Setting::get('coop_name', 'Koperasi Karyawan SKF') }}
                        @if(\App\Models\Setting::get('coop_address'))
                            · 📍 {{ \App\Models\Setting::get('coop_address') }}
                        @endif
                        @if(\App\Models\Setting::get('coop_phone'))
                            · 📞 {{ \App\Models\Setting::get('coop_phone') }}
                        @endif
                    </div>
                    <div class="file-upload-area">
                        <label class="file-upload-label" for="compose-files">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                            Lampirkan File (maks. 10MB per file)
                        </label>
                        <input type="file" name="attachments[]" id="compose-files" multiple style="display:none;" onchange="showSelectedFiles(this)">
                        <div id="file-list" class="file-upload-list"></div>
                    </div>
                    <div class="compose-footer">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <button type="submit" class="btn-send">
                                <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                                Kirim
                            </button>
                            <span id="file-count" style="font-size:12px;color:#6b7280;"></span>
                        </div>
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
                        <a href="{{ route('admin.email.forward', ['uid' => $selectedMessage->getUid(), 'folder' => $activeFolder]) }}" class="toolbar-btn" title="Teruskan">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                        </a>
                        <form action="{{ route('admin.email.delete', $selectedMessage->getUid()) }}" method="POST" style="display:inline;" onsubmit="return confirm('{{ $activeFolder === 'trash' ? 'Hapus permanen email ini?' : 'Pindahkan email ini ke Sampah?' }}')">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="folder" value="{{ $activeFolder }}">
                            <button type="submit" class="toolbar-btn" title="{{ $activeFolder === 'trash' ? 'Hapus Permanen' : 'Hapus' }}" style="color:{{ $activeFolder === 'trash' ? '#dc2626' : 'inherit' }}">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </form>
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
                        @foreach($selectedMessage->getAttachments() as $index => $attachment)
                        <a href="{{ route('admin.email.attachment', ['uid' => $selectedMessage->getUid(), 'index' => $index, 'folder' => $activeFolder]) }}" class="reading-attachment-item" title="Unduh {{ $attachment->name }}">
                            <svg style="width:18px;height:18px;color:#ef4444;margin-right:10px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                            <span>
                                <span style="font-size:13px;font-weight:500;color:#374151;">{{ $attachment->name }}</span>
                                <span style="font-size:11px;color:#9ca3af;margin-left:6px;">({{ number_format($attachment->size / 1024, 2) }} KB)</span>
                            </span>
                            <svg class="dl-icon" style="width:16px;height:16px;color:{{ $cf['color'] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        </a>
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

@push('scripts')
<script>
function showSelectedFiles(input) {
    var fileList = document.getElementById('file-list');
    var fileCount = document.getElementById('file-count');
    fileList.innerHTML = '';
    
    if (input.files.length > 0) {
        for (var i = 0; i < input.files.length; i++) {
            var file = input.files[i];
            var size = (file.size / 1024).toFixed(1);
            var unit = 'KB';
            if (size > 1024) { size = (size / 1024).toFixed(1); unit = 'MB'; }
            
            var item = document.createElement('div');
            item.className = 'file-upload-item';
            item.innerHTML = '<svg style="width:14px;height:14px;color:#6b7280;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>' +
                '<span>' + file.name + ' (' + size + ' ' + unit + ')</span>';
            fileList.appendChild(item);
        }
        fileCount.textContent = '📎 ' + input.files.length + ' file terlampir';
    } else {
        fileCount.textContent = '';
    }
}

function filterByCategory(category, el) {
    // Update active chip
    document.querySelectorAll('.filter-chip').forEach(function(chip) {
        chip.classList.remove('active');
    });
    el.classList.add('active');

    // Filter email items
    var items = document.querySelectorAll('.email-list-item');
    var visibleCount = 0;

    items.forEach(function(item) {
        var itemCat = item.getAttribute('data-category');
        if (category === 'all') {
            item.style.display = '';
            visibleCount++;
        } else if (category === 'no-badge') {
            if (itemCat === 'no-badge') {
                item.style.display = '';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        } else {
            if (itemCat === category) {
                item.style.display = '';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        }
    });

    // Show/hide empty state for filter
    var existingMsg = document.getElementById('filter-empty-msg');
    if (existingMsg) existingMsg.remove();
    
    if (visibleCount === 0 && category !== 'all') {
        var msg = document.createElement('div');
        msg.id = 'filter-empty-msg';
        msg.style.cssText = 'padding:32px 16px;text-align:center;color:#9ca3af;font-size:13px;';
        msg.innerHTML = 'Tidak ada email dengan kategori ini.';
        document.querySelector('.email-list-scroll').appendChild(msg);
    }
}

function generateAiReply() {
    var btn = document.getElementById('ai-reply-btn');
    var btnText = document.getElementById('ai-btn-text');
    var status = document.getElementById('ai-status');
    var textarea = document.getElementById('compose-body');
    var tone = document.getElementById('ai-tone').value;
    var subject = document.getElementById('compose-subject').value;
    var sender = document.getElementById('compose-to').value;
    
    // Extract original email body from quoted text
    var bodyContent = textarea.value;
    var originalBody = bodyContent;
    var dashIndex = bodyContent.indexOf('---');
    if (dashIndex > -1) {
        originalBody = bodyContent.substring(dashIndex);
    }

    btn.disabled = true;
    btnText.textContent = '⏳ Generating...';
    btn.classList.add('ai-loading');
    status.textContent = 'AI sedang menulis balasan...';
    status.className = 'ai-status loading';

    fetch('{{ route("admin.email.ai-reply") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            subject: subject,
            sender: sender,
            body: originalBody,
            tone: tone
        })
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        btn.disabled = false;
        btnText.textContent = '✨ Balas dengan AI';
        btn.classList.remove('ai-loading');
        
        if (data.success && data.reply) {
            // Put AI reply before the quoted text
            if (dashIndex > -1) {
                textarea.value = data.reply + '\n\n' + bodyContent.substring(dashIndex);
            } else {
                textarea.value = data.reply;
            }
            status.textContent = '✅ Balasan AI berhasil digenerate!';
            status.className = 'ai-status';
            status.style.color = '#059669';
            textarea.focus();
        } else {
            status.textContent = '❌ ' + (data.error || 'Gagal generate');
            status.className = 'ai-status error';
        }
        
        setTimeout(function() { status.textContent = ''; status.style.color = ''; }, 5000);
    })
    .catch(function(err) {
        btn.disabled = false;
        btnText.textContent = '✨ Balas dengan AI';
        btn.classList.remove('ai-loading');
        status.textContent = '❌ Error: ' + err.message;
        status.className = 'ai-status error';
    });
}

// ===== TEMPLATE FUNCTIONS =====
var templateData = [];
var templatesLoaded = false;

function toggleTemplates() {
    var dd = document.getElementById('template-dropdown');
    if (!dd) return;
    
    if (!templatesLoaded) {
        loadTemplates();
    }
    dd.classList.toggle('show');
}

function loadTemplates() {
    fetch('{{ route("admin.email.templates") }}')
        .then(function(res) { return res.json(); })
        .then(function(data) {
            templateData = data;
            templatesLoaded = true;
            var dd = document.getElementById('template-dropdown');
            data.forEach(function(tpl) {
                var btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'template-item';
                btn.textContent = tpl.name;
                btn.onclick = function() { applyTemplate(tpl); };
                dd.appendChild(btn);
            });
        });
}

function applyTemplate(tpl) {
    document.getElementById('compose-subject').value = tpl.subject;
    document.getElementById('compose-body').value = tpl.body;
    document.getElementById('template-dropdown').classList.remove('show');
    document.getElementById('compose-to').focus();
}

// Close template dropdown when clicking outside
document.addEventListener('click', function(e) {
    var dd = document.getElementById('template-dropdown');
    if (dd && !e.target.closest('.template-btn') && !e.target.closest('.template-dropdown')) {
        dd.classList.remove('show');
    }
    // Also close member dropdown
    var md = document.getElementById('member-dropdown');
    if (md && !e.target.closest('.compose-field')) {
        md.classList.remove('show');
    }
});

// ===== MEMBER SEARCH AUTOCOMPLETE =====
var memberSearchTimeout = null;

function searchMembers(query) {
    var dd = document.getElementById('member-dropdown');
    if (!dd) return;
    
    clearTimeout(memberSearchTimeout);
    
    if (query.length < 2) {
        dd.classList.remove('show');
        return;
    }

    // If it looks like a full email, don't search
    if (query.indexOf('@') > 0 && query.indexOf('.') > query.indexOf('@')) {
        dd.classList.remove('show');
        return;
    }

    memberSearchTimeout = setTimeout(function() {
        fetch('{{ route("admin.email.search-members") }}?q=' + encodeURIComponent(query))
            .then(function(res) { return res.json(); })
            .then(function(members) {
                dd.innerHTML = '';
                if (members.length === 0) {
                    dd.classList.remove('show');
                    return;
                }
                
                members.forEach(function(m) {
                    var initials = m.name.split(' ').map(function(w) { return w[0]; }).join('').substring(0, 2).toUpperCase();
                    var item = document.createElement('button');
                    item.type = 'button';
                    item.className = 'member-item';
                    item.innerHTML = '<div class="member-item-avatar">' + initials + '</div>' +
                        '<div class="member-item-info">' +
                            '<div class="member-item-name">' + m.name + '</div>' +
                            '<div class="member-item-email">' + m.email + '</div>' +
                        '</div>' +
                        (m.department ? '<span class="member-item-dept">' + m.department + '</span>' : '');
                    item.onclick = function() { selectMember(m); };
                    dd.appendChild(item);
                });
                dd.classList.add('show');
            });
    }, 300);
}

function selectMember(member) {
    document.getElementById('compose-to').value = member.email;
    document.getElementById('member-dropdown').classList.remove('show');
    document.getElementById('compose-subject').focus();
}
</script>
@endpush
