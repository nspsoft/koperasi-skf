@extends('layouts.app')

@section('title', 'Surat & Dokumen')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Surat & Dokumen</h1>
            <p class="page-subtitle">Manajemen template dan arsip dokumen resmi koperasi</p>
        </div>
    </div>

    <div x-data="{ activeTab: '{{ request()->query('tab', request()->has('page') ? 'history' : 'templates') }}' }" class="space-y-6">
        <!-- Tabs Implementation -->
        <div class="flex items-center gap-2 border-b border-gray-200 dark:border-gray-700">
            <a href="{{ route('documents.index', ['tab' => 'templates']) }}" 
               @click.prevent="activeTab = 'templates'; window.history.pushState({}, '', $el.href)"
               :class="activeTab === 'templates' ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
               class="px-4 py-2 border-b-2 font-medium text-sm transition-colors">
                Template Surat
            </a>
            <a href="{{ route('documents.index', ['tab' => 'history']) }}" 
               @click.prevent="activeTab = 'history'; window.history.pushState({}, '', $el.href)"
               :class="activeTab === 'history' ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
               class="px-4 py-2 border-b-2 font-medium text-sm transition-colors">
                Arsip Surat Terbit
            </a>
        </div>

        <!-- Tab Content: Templates -->
        <div x-show="activeTab === 'templates'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform -translate-y-2">
            @foreach($templates as $type => $group)
            <div class="mb-10">
                <div class="flex items-center gap-3 mb-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ strtoupper($type) }}</h2>
                    <div class="h-px flex-grow bg-gray-200 dark:bg-gray-800"></div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($group as $template)
                    <div class="glass-card-solid p-6 flex flex-col h-full hover:shadow-lg transition-shadow">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 rounded-lg bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 flex items-center justify-center">
                                @if($template->type === 'membership')
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                @elseif($template->type === 'loan')
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                @else
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                @endif
                            </div>
                            <div class="ml-4">
                                <h3 class="font-bold text-gray-900 dark:text-white">{{ $template->name }}</h3>
                                <span class="text-xs px-2 py-0.5 rounded-full {{ $template->type === 'membership' ? 'bg-green-100 text-green-700' : ($template->type === 'loan' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700') }}">
                                    {{ ucfirst($template->type) }}
                                </span>
                            </div>
                        </div>
                        
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6 flex-grow">
                            Gunakan template ini untuk membuat {{ strtolower($template->name) }} secara resmi.
                        </p>

                        <a href="{{ route('documents.create', $template->id) }}" class="btn-primary w-full justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Buat Dokumen
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>

        <!-- Tab Content: History -->
        <div x-show="activeTab === 'history'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-2">
            <div class="glass-card-solid overflow-hidden">
                <div class="table-scroll-container max-h-[600px] overflow-y-auto mt-0">
                    <table class="table-modern">
                        <thead class="sticky top-0 z-10 bg-primary-600 dark:bg-primary-700">
                            <tr>
                                <th>No. Dokumen</th>
                                <th>Jenis Dokumen</th>
                                <th>Penginput</th>
                                <th>Tanggal Terbit</th>
                                <th>Status Verif</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($history as $doc)
                            <tr>
                                <td class="font-medium text-primary-600 dark:text-primary-400">{{ $doc->document_number }}</td>
                                <td>{{ $doc->document_type }}</td>
                                <td>{{ $doc->user->name ?? '-' }}</td>
                                <td>{{ $doc->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if($doc->verified_at)
                                        <span class="badge badge-success">Terverifikasi</span>
                                    @else
                                        <span class="badge badge-warning">Belum Dicek</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex items-center justify-center gap-1">
                                        {{-- View Public --}}
                                        <a href="{{ route('documents.verify.public', $doc->id) }}" target="_blank" 
                                           class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors" title="Lihat Publik">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </a>

                                        {{-- Print Ulang (Download) --}}
                                        <a href="{{ route('documents.download', $doc->id) }}" target="_blank"
                                           class="p-2 text-purple-600 hover:bg-purple-50 dark:hover:bg-purple-900/20 rounded-lg transition-colors" title="Cetak Ulang">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                        </a>

                                        {{-- Edit (Pre-fill Form) --}}
                                        <a href="{{ route('documents.edit', $doc->id) }}" 
                                           class="p-2 text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 rounded-lg transition-colors" title="Edit Data">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>

                                        {{-- Delete --}}
                                        <form action="{{ route('documents.destroy', $doc->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus arsip dokumen ini?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors" title="Hapus">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-12 text-gray-500">Belum ada dokumen yang diterbitkan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-gray-100 dark:border-gray-800">
                    {{ $history->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
