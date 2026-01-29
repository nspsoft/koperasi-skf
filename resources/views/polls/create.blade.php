@extends('layouts.app')

@section('title', 'Buat Pemilihan Baru')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Buat Pemilihan Baru</h1>
            <p class="page-subtitle">Siapkan detail pemilihan dan daftar kandidat pengurus</p>
        </div>
        <a href="{{ route('polls.index') }}" class="btn-secondary">Kembali</a>
    </div>

    <form action="{{ route('polls.store') }}" method="POST" enctype="multipart/form-data" x-data="candidateForm()">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Election Details -->
            <div class="lg:col-span-2 space-y-6">
                <div class="glass-card-solid p-6">
                    <h3 class="text-lg font-bold mb-6 text-gray-900 dark:text-white border-b pb-2">Detail Pemilihan</h3>
                    
                    <div class="space-y-4">
                        <div class="form-group">
                            <label class="form-label">Judul Pemilihan</label>
                            <input type="text" name="title" class="form-input" required placeholder="Contoh: Pemilihan Ketua Koperasi 2026-2028">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Deskripsi / Peraturan</label>
                            <textarea name="description" rows="4" class="form-input" placeholder="Jelaskan mengenai pemilihan ini..."></textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="form-group">
                                <label class="form-label">Tanggal Mulai</label>
                                <input type="datetime-local" name="start_date" class="form-input" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Tanggal Selesai</label>
                                <input type="datetime-local" name="end_date" class="form-input" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Candidates List -->
                <div class="glass-card-solid p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b pb-2 flex-grow">Daftar Kandidat</h3>
                        <button type="button" @click="addCandidate" class="btn-primary text-xs py-1 px-3">
                             Tambah Kandidat
                        </button>
                    </div>

                    <div class="space-y-4">
                        <template x-for="(candidate, index) in candidates" :key="index">
                            <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700 relative">
                                <button type="button" @click="removeCandidate(index)" class="absolute top-2 right-2 text-red-500 hover:text-red-700" x-show="candidates.length > 2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="md:col-span-1">
                                        <label class="form-label text-xs">Foto Kandidat</label>
                                        <input type="file" :name="`candidates[${index}][photo]`" accept="image/*" class="text-xs">
                                    </div>
                                    <div class="md:col-span-2">
                                        <div class="form-group mb-2">
                                            <label class="form-label text-xs">Nama Lengkap</label>
                                            <input type="text" :name="`candidates[${index}][name]`" class="form-input text-sm" required placeholder="Nama Kandidat">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label text-xs">Visi & Misi</label>
                                            <textarea :name="`candidates[${index}][vision_mission]`" rows="2" class="form-input text-sm" placeholder="Visi & Misi singkat..."></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Summary / Submit -->
            <div class="lg:col-span-1">
                <div class="glass-card-solid p-6 sticky top-6">
                    <h3 class="font-bold mb-4 text-gray-900 dark:text-white">Ringkasan</h3>
                    <ul class="text-sm space-y-3 text-gray-600 dark:text-gray-400 mb-6">
                        <li class="flex justify-between">
                            <span>Jumlah Kandidat:</span>
                            <span class="font-bold text-primary-600" x-text="candidates.length"></span>
                        </li>
                        <li class="flex justify-between">
                            <span>Estimasi Periode:</span>
                            <span class="font-bold">-</span>
                        </li>
                    </ul>

                    <div class="p-4 bg-primary-50 dark:bg-primary-900/20 rounded-lg mb-6 border border-primary-100 dark:border-primary-800">
                        <p class="text-xs text-primary-700 dark:text-primary-300">
                            <strong>Note:</strong> Pemilihan yang baru dibuat akan berstatus <strong>Draf</strong>. Anda perlu mengaktifkannya secara manual agar anggota bisa mulai memilih.
                        </p>
                    </div>

                    <button type="submit" class="btn-primary w-full justify-center py-3">
                        Simpan Pemilihan
                    </button>
                </div>
            </div>
        </div>
    </form>

    <script>
        function candidateForm() {
            return {
                candidates: [
                    { name: '', vision_mission: '' },
                    { name: '', vision_mission: '' }
                ],
                addCandidate() {
                    this.candidates.push({ name: '', vision_mission: '' });
                },
                removeCandidate(index) {
                    if (this.candidates.length > 2) {
                        this.candidates.splice(index, 1);
                    }
                }
            }
        }
    </script>
@endsection
