@extends('layouts.app')

@section('title', 'Import Pembelian Excel')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Import Transaksi Pembelian</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Upload data pembelian (PO) dari Excel</p>
        </div>
        <a href="{{ route('purchases.index') }}" class="btn-secondary">
            Kembali
        </a>
    </div>

    <!-- Instructions Card -->
    <div class="glass-card-solid p-6">
        <div class="flex items-start gap-4 mb-6">
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Panduan Import</h3>
                <ol class="list-decimal list-inside text-gray-600 dark:text-gray-400 space-y-2 text-sm">
                    <li>Download template Excel yang disediakan.</li>
                    <li>Isi data pembelian. <strong>Satu No PO bisa memiliki banyak baris</strong> (copy No PO ke baris bawahnya).</li>
                    <li>Pastikan <strong>Nama Supplier</strong> dan <strong>Kode Produk</strong> sudah terdaftar di sistem.</li>
                    <li>Upload file Excel yang sudah diisi.</li>
                    <li>Sistem akan membuat PO dengan status <strong>Pending</strong>.</li>
                </ol>
            </div>
        </div>

        <!-- Download Template Button -->
        <a href="{{ route('purchases.import.template') }}" class="w-full flex items-center justify-center px-4 py-3 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 mb-6">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
            </svg>
            Download Template Excel
        </a>

        <!-- Upload Form -->
        <form action="{{ route('purchases.import.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Upload File Excel</label>
                <input type="file" name="file" accept=".xlsx,.xls,.csv" required
                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                <p class="mt-1 text-xs text-gray-500">Mendukung format .xlsx, .xls, .csv (Max 2MB)</p>
            </div>

            <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Import Sekarang
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
