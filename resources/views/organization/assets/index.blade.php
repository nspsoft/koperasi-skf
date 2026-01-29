@extends('layouts.app')

@section('title', 'Inventaris Aset')

@section('content')
<div class="space-y-6" x-data="{ showForm: false }">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                <a href="{{ route('organization.index') }}" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                Inventaris Organisasi
            </h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1 ml-8">Daftar harta kekayaan tetap milik koperasi</p>
        </div>
        <button @click="showForm = !showForm" class="btn-primary flex items-center gap-2">
            <span x-text="showForm ? 'Tutup Form' : 'Tambah Aset Baru'"></span>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="!showForm"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="showForm" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>

    <!-- Create Form -->
    <div x-show="showForm" x-transition class="glass-card p-6 border-l-4 border-blue-500" style="display: none;">
        <h3 class="font-bold text-lg mb-4 text-gray-800 dark:text-white">📝 Input Aset Baru</h3>
        <form action="{{ route('organization.assets.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf
            
            <div class="form-group">
                <label class="form-label">Nama Aset</label>
                <input type="text" name="name" class="form-input" required placeholder="Contoh: Laptop Admin">
            </div>

            <div class="form-group">
                <label class="form-label">Kode Aset</label>
                <input type="text" name="code" class="form-input" required placeholder="Contoh: INV-2023-001">
            </div>

            <div class="form-group">
                <label class="form-label">Kategori</label>
                <select name="category" class="form-select" required>
                    <option value="">Pilih Kategori</option>
                    <option value="Furniture">Furniture & Meubelair</option>
                    <option value="Electronics">Elektronik & Komputasi</option>
                    <option value="Vehicle">Kendaraan Operasional</option>
                    <option value="Property">Tanah & Bangunan</option>
                    <option value="Machine">Mesin & Peralatan Usaha</option>
                    <option value="Other">Lainnya</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Kondisi Saat Ini</label>
                <select name="condition" class="form-select" required>
                    <option value="good">Baik / Layak Pakai</option>
                    <option value="damaged">Rusak Ringan</option>
                    <option value="broken">Rusak Berat</option>
                    <option value="lost">Hilang</option>
                    <option value="disposed">Sudah Dihapus</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Tanggal Perolehan</label>
                <input type="date" name="purchase_date" class="form-input" required>
            </div>

            <div class="form-group">
                <label class="form-label">Nilai Perolehan (Rp)</label>
                <input type="number" name="purchase_price" class="form-input" required min="0">
            </div>

            <div class="md:col-span-2 flex justify-end gap-2 mt-2">
                <button type="button" @click="showForm = false" class="px-4 py-2 text-gray-600 hover:text-gray-800">Batal</button>
                <button type="submit" class="btn-primary">Simpan Aset</button>
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="glass-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 text-left">
                    <tr>
                        <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Kode & Nama</th>
                        <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Tgl Perolehan</th>
                        <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Nilai Aset</th>
                        <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Kondisi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($assets as $asset)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors" x-data="{ showEditModal: false }">
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-800 dark:text-white">{{ $asset->name }}</div>
                            <div class="text-xs text-blue-600 font-mono">{{ $asset->code }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                {{ $asset->category }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                            {{ $asset->purchase_date->format('d/m/Y') }}
                            <div class="text-xs text-gray-400">{{ $asset->purchase_date->diffForHumans() }}</div>
                        </td>
                        <td class="px-6 py-4 text-right font-mono text-gray-800 dark:text-white">
                            Rp {{ number_format($asset->current_value, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-3">
                                @php
                                    $statusClasses = [
                                        'good' => 'bg-green-100 text-green-800 border-green-200',
                                        'damaged' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                        'broken' => 'bg-red-100 text-red-800 border-red-200',
                                        'lost' => 'bg-gray-100 text-gray-800 border-gray-200',
                                        'disposed' => 'bg-gray-100 text-gray-400 border-gray-200 decoration-line-through',
                                    ];
                                    $label = [
                                        'good' => 'Baik', 'damaged' => 'Rusak Ringan', 'broken' => 'Rusak Berat', 
                                        'lost' => 'Hilang', 'disposed' => 'Dihapus'
                                    ];
                                @endphp
                                <span class="px-2 py-1 text-xs rounded-full border {{ $statusClasses[$asset->condition] ?? 'bg-gray-100' }}">
                                    {{ $label[$asset->condition] ?? $asset->condition }}
                                </span>

                                <!-- Action Buttons -->
                                <button @click="showEditModal = true" class="text-blue-500 hover:text-blue-700">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                                </button>
                                <form action="{{ route('organization.assets.destroy', $asset) }}" method="POST" onsubmit="return confirm('Hapus aset ini dari inventaris?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>

                            <!-- Edit Modal -->
                            <div x-show="showEditModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
                                <div class="flex items-center justify-center min-h-screen px-4">
                                    <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="showEditModal = false"></div>
                                    <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-lg w-full p-6 text-left border border-gray-100 dark:border-gray-700">
                                        <h3 class="text-lg font-bold mb-4">Edit Aset: {{ $asset->name }}</h3>
                                        <form action="{{ route('organization.assets.update', $asset) }}" method="POST" class="grid grid-cols-2 gap-4">
                                            @csrf
                                            @method('PUT')
                                            <div class="col-span-2">
                                                <label class="form-label">Nama Aset</label>
                                                <input type="text" name="name" class="form-input" value="{{ $asset->name }}" required>
                                            </div>
                                            <div>
                                                <label class="form-label">Kode</label>
                                                <input type="text" name="code" class="form-input" value="{{ $asset->code }}" required>
                                            </div>
                                            <div>
                                                <label class="form-label">Kategori</label>
                                                <select name="category" class="form-select">
                                                    @foreach(['Furniture','Electronics','Vehicle','Property','Machine','Other'] as $cat)
                                                        <option value="{{ $cat }}" {{ $asset->category == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <label class="form-label">Nilai Saat Ini (Rp)</label>
                                                <input type="number" name="current_value" class="form-input" value="{{ (int)$asset->current_value }}" required>
                                            </div>
                                            <div>
                                                <label class="form-label">Kondisi</label>
                                                <select name="condition" class="form-select">
                                                    @foreach(['good' => 'Baik', 'damaged' => 'Rusak Ringan', 'broken' => 'Rusak Berat', 'lost' => 'Hilang', 'disposed' => 'Dihapus'] as $val => $lab)
                                                        <option value="{{ $val }}" {{ $asset->condition == $val ? 'selected' : '' }}>{{ $lab }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <label class="form-label">Status</label>
                                                <select name="status" class="form-select">
                                                    <option value="active" {{ $asset->status == 'active' ? 'selected' : '' }}>Aktif</option>
                                                    <option value="inactive" {{ $asset->status == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="form-label">Tgl Perolehan</label>
                                                <input type="date" name="purchase_date" class="form-input" value="{{ $asset->purchase_date->format('Y-m-d') }}" required>
                                            </div>
                                            <div class="col-span-2 flex justify-end gap-2 mt-4">
                                                <button type="button" @click="showEditModal = false" class="px-4 py-2 text-gray-500">Batal</button>
                                                <button type="submit" class="btn-primary">Update Aset</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            Belum ada data inventaris aset. Silakan tambahkan aset baru.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $assets->links() }}
        </div>
    </div>
</div>
@endsection
