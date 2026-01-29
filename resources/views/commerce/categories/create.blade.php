@extends('layouts.app')

@section('title', 'Tambah Kategori')

@section('content')
    <div class="page-header">
        <a href="{{ route('categories.index') }}" class="flex items-center text-gray-500 hover:text-primary-600 mb-4 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Daftar
        </a>
        <h1 class="page-title">Tambah Kategori</h1>
    </div>

    <div class="max-w-xl">
        <div class="glass-card-solid p-6">
            <form action="{{ route('categories.store') }}" method="POST">
                @csrf
                
                <div class="form-group">
                    <label for="name" class="form-label">Nama Kategori</label>
                    <input type="text" name="name" id="name" class="form-input @error('name') border-red-500 @enderror" value="{{ old('name') }}" required onkeyup="generateSlug(this.value)">
                    @error('name')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div class="form-group">
                    <label for="slug" class="form-label">Slug (URL)</label>
                    <input type="text" name="slug" id="slug" class="form-input bg-gray-50 @error('slug') border-red-500 @enderror" value="{{ old('slug') }}" required readonly>
                    @error('slug')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div class="form-group">
                    <label for="icon" class="form-label">Icon (Emoji)</label>
                    <input type="text" name="icon" id="icon" class="form-input" value="{{ old('icon') }}" placeholder="Contoh: 📦">
                </div>

                <div class="form-group">
                    <label for="description" class="form-label">Deskripsi</label>
                    <textarea name="description" id="description" rows="3" class="form-input">{{ old('description') }}</textarea>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="submit" class="btn-primary">Simpan Kategori</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function generateSlug(text) {
            document.getElementById('slug').value = text
                .toLowerCase()
                .replace(/ /g, '-')
                .replace(/[^\w-]+/g, '');
        }
    </script>
    @endpush
@endsection
