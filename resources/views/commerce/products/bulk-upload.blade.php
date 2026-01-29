@extends('layouts.app')

@section('title', 'Bulk Upload Produk')

@section('content')
<div class="page-header">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="page-title">Bulk Upload Produk</h1>
            <p class="page-subtitle">Upload data produk dan gambar secara massal</p>
        </div>
        <a href="{{ route('products.index') }}" class="btn-secondary">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Step 1: Download Template -->
    <div class="glass-card-solid p-6">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center text-white font-black text-xl shadow-lg">1</div>
            <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Download Template Excel</h3>
                <p class="text-sm text-gray-500">Download template dan isi data produk</p>
            </div>
        </div>
        
        <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4 mb-4">
            <h4 class="font-bold text-gray-700 dark:text-gray-300 mb-3">Format Kolom Template:</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                <div class="flex items-center gap-2">
                    <span class="w-32 font-mono text-xs bg-gray-200 dark:bg-gray-700 px-2 py-1 rounded">code</span>
                    <span class="text-gray-600 dark:text-gray-400">Kode produk (unik)</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-32 font-mono text-xs bg-gray-200 dark:bg-gray-700 px-2 py-1 rounded">name</span>
                    <span class="text-gray-600 dark:text-gray-400">Nama produk</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-32 font-mono text-xs bg-gray-200 dark:bg-gray-700 px-2 py-1 rounded">category</span>
                    <span class="text-gray-600 dark:text-gray-400">Nama kategori</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-32 font-mono text-xs bg-blue-100 dark:bg-blue-800 px-2 py-1 rounded text-blue-700 dark:text-blue-300">unit</span>
                    <span class="text-gray-600 dark:text-gray-400">Satuan jual (pcs, kg, dll)</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-32 font-mono text-xs bg-blue-100 dark:bg-blue-800 px-2 py-1 rounded text-blue-700 dark:text-blue-300">purchase_unit</span>
                    <span class="text-gray-600 dark:text-gray-400">Satuan beli (dus, pack, dll)</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-32 font-mono text-xs bg-blue-100 dark:bg-blue-800 px-2 py-1 rounded text-blue-700 dark:text-blue-300">conversion</span>
                    <span class="text-gray-600 dark:text-gray-400">Konversi (1 dus = ? pcs)</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-32 font-mono text-xs bg-gray-200 dark:bg-gray-700 px-2 py-1 rounded">cost</span>
                    <span class="text-gray-600 dark:text-gray-400">Harga modal (per satuan beli)</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-32 font-mono text-xs bg-blue-100 dark:bg-blue-800 px-2 py-1 rounded text-blue-700 dark:text-blue-300">margin</span>
                    <span class="text-gray-600 dark:text-gray-400">Margin % (10, 15, 20...)</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-32 font-mono text-xs bg-gray-200 dark:bg-gray-700 px-2 py-1 rounded">price</span>
                    <span class="text-gray-600 dark:text-gray-400">Harga jual (per satuan jual)</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-32 font-mono text-xs bg-gray-200 dark:bg-gray-700 px-2 py-1 rounded">stock</span>
                    <span class="text-gray-600 dark:text-gray-400">Stok (dalam satuan jual)</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-32 font-mono text-xs bg-gray-200 dark:bg-gray-700 px-2 py-1 rounded">image_file</span>
                    <span class="text-gray-600 dark:text-gray-400">Nama file gambar (opsional)</span>
                </div>
            </div>
            <p class="text-xs text-blue-600 dark:text-blue-400 mt-3">💡 Kolom biru adalah fitur baru untuk konversi satuan</p>
        </div>
        
        <a href="{{ route('products.bulk.template') }}" class="btn-primary w-full flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            Download Template Excel
        </a>
    </div>

    <!-- Step 2: Upload Excel -->
    <div class="glass-card-solid p-6">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center text-white font-black text-xl shadow-lg">2</div>
            <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Upload File Excel</h3>
                <p class="text-sm text-gray-500">Upload file Excel yang sudah diisi</p>
            </div>
        </div>
        
        <form action="{{ route('products.bulk.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">File Excel (.xlsx, .csv)</label>
                <input type="file" name="file" accept=".xlsx,.xls,.csv" required
                       class="form-input file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                @error('file')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="update_existing" value="1" class="form-checkbox rounded text-primary-600">
                    <span class="text-sm text-gray-700 dark:text-gray-300">Update produk yang sudah ada (berdasarkan kode)</span>
                </label>
            </div>
            
            <button type="submit" class="btn-success w-full flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                Import Data Produk
            </button>
        </form>
    </div>
</div>

<!-- Step 3: Bulk Upload Images -->
<div class="glass-card-solid p-6 mt-8">
    <div class="flex items-center gap-4 mb-6">
        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center text-white font-black text-xl shadow-lg">3</div>
        <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Bulk Upload Gambar</h3>
            <p class="text-sm text-gray-500">Upload banyak gambar sekaligus, nama file akan di-match dengan kode produk</p>
        </div>
    </div>
    
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4 mb-6">
        <div class="flex gap-3">
            <svg class="w-6 h-6 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <div class="text-sm text-blue-700 dark:text-blue-300">
                <p class="font-bold mb-1">Format Nama File:</p>
                <p>Nama file gambar harus sama dengan <strong>KODE PRODUK</strong>. Contoh:</p>
                <ul class="list-disc list-inside mt-1 space-y-1">
                    <li><code class="bg-blue-100 dark:bg-blue-800 px-1 rounded">PRD001.jpg</code> → akan di-assign ke produk dengan kode PRD001</li>
                    <li><code class="bg-blue-100 dark:bg-blue-800 px-1 rounded">SKU-ABC-123.png</code> → akan di-assign ke produk dengan kode SKU-ABC-123</li>
                </ul>
            </div>
        </div>
    </div>
    
    <form action="{{ route('products.bulk.images') }}" method="POST" enctype="multipart/form-data" x-data="{ files: [], dragover: false }">
        @csrf
        
        <!-- Drag & Drop Area -->
        <div class="relative mb-4"
             @dragover.prevent="dragover = true"
             @dragleave.prevent="dragover = false"
             @drop.prevent="dragover = false; files = [...$event.dataTransfer.files]">
            
            <label class="flex flex-col items-center justify-center w-full h-48 border-2 border-dashed rounded-2xl cursor-pointer transition-all duration-300"
                   :class="dragover ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20' : 'border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800'">
                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                    <svg class="w-12 h-12 mb-4 text-gray-400" :class="dragover && 'text-primary-500 animate-bounce'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <p class="mb-2 text-sm text-gray-500 dark:text-gray-400">
                        <span class="font-semibold">Klik untuk upload</span> atau drag & drop
                    </p>
                    <p class="text-xs text-gray-400">PNG, JPG, JPEG, WEBP (Maks. 2MB per file)</p>
                </div>
                <input type="file" name="images[]" multiple accept="image/*" class="hidden" 
                       @change="files = [...$event.target.files]">
            </label>
        </div>
        
        <!-- Preview Selected Files -->
        <div x-show="files.length > 0" class="mb-4">
            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                <span x-text="files.length"></span> file dipilih
            </p>
            <div class="flex flex-wrap gap-2 max-h-32 overflow-y-auto">
                <template x-for="(file, index) in files" :key="index">
                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-gray-100 dark:bg-gray-700 rounded-full text-sm">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span x-text="file.name" class="truncate max-w-[150px]"></span>
                    </span>
                </template>
            </div>
        </div>
        
        <button type="submit" class="btn-primary w-full flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            Upload Gambar
        </button>
    </form>
</div>

@if(session('import_result'))
<div class="glass-card-solid p-6 mt-8">
    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Hasil Import</h3>
    <div class="grid grid-cols-3 gap-4 mb-4">
        <div class="bg-green-50 dark:bg-green-900/20 rounded-xl p-4 text-center">
            <div class="text-3xl font-black text-green-600">{{ session('import_result.success') ?? 0 }}</div>
            <div class="text-sm text-green-700 dark:text-green-400">Berhasil</div>
        </div>
        <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-xl p-4 text-center">
            <div class="text-3xl font-black text-yellow-600">{{ session('import_result.updated') ?? 0 }}</div>
            <div class="text-sm text-yellow-700 dark:text-yellow-400">Diperbarui</div>
        </div>
        <div class="bg-red-50 dark:bg-red-900/20 rounded-xl p-4 text-center">
            <div class="text-3xl font-black text-red-600">{{ session('import_result.failed') ?? 0 }}</div>
            <div class="text-sm text-red-700 dark:text-red-400">Gagal</div>
        </div>
    </div>
    
    @if(session('import_result.errors'))
    <div class="bg-red-50 dark:bg-red-900/20 rounded-xl p-4">
        <h4 class="font-bold text-red-700 dark:text-red-400 mb-2">Detail Error:</h4>
        <ul class="list-disc list-inside text-sm text-red-600 dark:text-red-400 max-h-40 overflow-y-auto">
            @foreach(session('import_result.errors') as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
</div>
@endif

@endsection
