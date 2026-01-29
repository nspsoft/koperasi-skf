@extends('layouts.app')

@section('title', 'Edit Template')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Edit Template Dokumen</h1>
            <p class="page-subtitle">Ubah pengaturan default untuk dokumen ini.</p>
        </div>
        <a href="{{ route('document-templates.index') }}" class="btn-secondary">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali
        </a>
    </div>

    <div class="max-w-2xl mx-auto">
        <div class="glass-card-solid p-6">
            <form action="{{ route('document-templates.update', $documentTemplate->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-6">
                    <label class="form-label">Nama Dokumen</label>
                    <input type="text" name="name" value="{{ old('name', $documentTemplate->name) }}" class="form-input" required>
                    <p class="text-xs text-gray-500 mt-1">Nama ini akan muncul saat memilih jenis surat.</p>
                </div>

                <div class="mb-6">
                    <label class="form-label">Kode Singkatan Surat (Default)</label>
                    <div class="flex items-center gap-4">
                        <div class="flex-grow">
                            <input type="text" name="code" value="{{ old('code', $documentTemplate->code) }}" class="form-input font-mono font-bold text-lg" placeholder="Contoh: SK, UND, PEMBERITAHUAN" required>
                        </div>
                        <div class="flex-shrink-0 text-sm text-gray-500">
                            Akan menjadi: <span class="font-mono bg-gray-100 px-2 py-1 rounded">001/<span class="text-primary-600 font-bold">KODE</span>/{{ date('m/Y') }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-6 border-t border-gray-100 dark:border-gray-800">
                    <button type="submit" class="btn-primary">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
