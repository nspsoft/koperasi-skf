@extends('layouts.app')

@section('title', 'Edit Supplier')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('suppliers.index') }}" class="btn-secondary-sm">
            ← Kembali
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Supplier</h1>
    </div>

    <form action="{{ route('suppliers.update', $supplier) }}" method="POST" class="glass-card p-6 space-y-6">
        @csrf
        @method('PUT')
        
        <div>
            <label class="form-label">Nama Supplier <span class="text-red-500">*</span></label>
            <input type="text" name="name" class="form-input @error('name') border-red-500 @enderror" value="{{ old('name', $supplier->name) }}" required>
            @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="form-label">Kontak Person</label>
                <input type="text" name="contact_person" class="form-input" value="{{ old('contact_person', $supplier->contact_person) }}">
            </div>
            <div>
                <label class="form-label">Nomor Telepon</label>
                <input type="text" name="phone" class="form-input" value="{{ old('phone', $supplier->phone) }}">
            </div>
        </div>

        <div>
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-input" value="{{ old('email', $supplier->email) }}">
        </div>

        <div>
            <label class="form-label">Alamat Lengkap</label>
            <textarea name="address" rows="3" class="form-input">{{ old('address', $supplier->address) }}</textarea>
        </div>

        <div>
            <label class="form-label">Deskripsi / Catatan</label>
            <textarea name="description" rows="2" class="form-input">{{ old('description', $supplier->description) }}</textarea>
        </div>

        <div class="flex justify-end pt-4">
            <button type="submit" class="btn-primary w-full md:w-auto">Update Supplier</button>
        </div>
    </form>
</div>
@endsection
