@extends('layouts.app')

@section('title', __('messages.titles.master_data'))

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Master Data</h1>
            <p class="page-subtitle">Kelola data departemen dan jabatan</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Departments --}}
        <div class="glass-card-solid">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                <h3 class="text-lg font-bold">Departemen</h3>
                <button onclick="document.getElementById('addDeptModal').classList.remove('hidden')" class="btn-primary btn-sm">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="table-modern">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Kode</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($departments as $dept)
                        <tr>
                            <td class="font-medium">{{ $dept->name }}</td>
                            <td><span class="font-mono text-xs">{{ $dept->code }}</span></td>
                            <td>
                                <span class="badge {{ $dept->is_active ? 'badge-success' : 'badge-secondary' }}">
                                    {{ $dept->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <form action="{{ route('master-data.departments.destroy', $dept) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Positions --}}
        <div class="glass-card-solid">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                <h3 class="text-lg font-bold">Jabatan</h3>
                <button onclick="document.getElementById('addPosModal').classList.remove('hidden')" class="btn-primary btn-sm">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="table-modern">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Kode</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($positions as $pos)
                        <tr>
                            <td class="font-medium">{{ $pos->name }}</td>
                            <td><span class="font-mono text-xs">{{ $pos->code }}</span></td>
                            <td>
                                <span class="badge {{ $pos->is_active ? 'badge-success' : 'badge-secondary' }}">
                                    {{ $pos->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <form action="{{ route('master-data.positions.destroy', $pos) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Add Department Modal --}}
    <div id="addDeptModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md">
            <h3 class="text-lg font-bold mb-4">Tambah Departemen</h3>
            <form action="{{ route('master-data.departments.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="form-label">Nama Departemen</label>
                    <input type="text" name="name" class="form-input" required>
                </div>
                <div class="mb-4">
                    <label class="form-label">Kode</label>
                    <input type="text" name="code" class="form-input">
                </div>
                <div class="mb-4">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" class="form-input" rows="3"></textarea>
                </div>
                <div class="flex gap-2 justify-end">
                    <button type="button" onclick="document.getElementById('addDeptModal').classList.add('hidden')" class="btn-secondary">Batal</button>
                    <button type="submit" class="btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Add Position Modal --}}
    <div id="addPosModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md">
            <h3 class="text-lg font-bold mb-4">Tambah Jabatan</h3>
            <form action="{{ route('master-data.positions.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="form-label">Nama Jabatan</label>
                    <input type="text" name="name" class="form-input" required>
                </div>
                <div class="mb-4">
                    <label class="form-label">Kode</label>
                    <input type="text" name="code" class="form-input">
                </div>
                <div class="mb-4">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" class="form-input" rows="3"></textarea>
                </div>
                <div class="flex gap-2 justify-end">
                    <button type="button" onclick="document.getElementById('addPosModal').classList.add('hidden')" class="btn-secondary">Batal</button>
                    <button type="submit" class="btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection
