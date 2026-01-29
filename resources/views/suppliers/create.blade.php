@extends('layouts.app')

@section('title', 'Tambah Supplier')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('suppliers.index') }}" class="btn-secondary-sm">
            ← Kembali
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tambah Supplier Baru</h1>
    </div>

    <form action="{{ route('suppliers.store') }}" method="POST" class="glass-card p-6 space-y-6">
        @csrf
        
        <div>
            <label class="form-label">Nama Supplier <span class="text-red-500">*</span></label>
            <input type="text" name="name" class="form-input @error('name') border-red-500 @enderror" value="{{ old('name') }}" required placeholder="Contoh: PT. Sumber Makmur">
            @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="form-label">Kontak Person</label>
                <input type="text" name="contact_person" class="form-input" value="{{ old('contact_person') }}" placeholder="Nama PIC">
            </div>
            <div>
                <label class="form-label">Nomor Telepon</label>
                <input type="text" name="phone" class="form-input" value="{{ old('phone') }}" placeholder="0812...">
            </div>
        </div>

        <div>
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-input" value="{{ old('email') }}" placeholder="email@supplier.com">
        </div>

        <div>
            <label class="form-label">Alamat Lengkap</label>
            <textarea name="address" rows="3" class="form-input">{{ old('address') }}</textarea>
        </div>

        <div>
            <label class="form-label">Deskripsi / Catatan</label>
            <textarea name="description" rows="2" class="form-input" placeholder="Misal: Supplier khusus sembako">{{ old('description') }}</textarea>
        </div>

        <div class="flex justify-end pt-4">
            <button type="submit" class="btn-primary w-full md:w-auto">Simpan Supplier</button>
        </div>
    </form>
</div>
@endsection
