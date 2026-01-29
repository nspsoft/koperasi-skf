@extends('layouts.app')

@section('title', __('messages.titles.settings_landing'))

@section('content')
<div x-data="{ showAddTeamModal: false, showAddProgramModal: false }" class="max-w-5xl mx-auto space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Konfigurasi Landing Page</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Sesuaikan tampilan halaman depan website koperasi Anda.</p>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 rounded-xl bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <form action="{{ route('settings.landing.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @method('PUT')

        <div class="grid lg:grid-cols-2 gap-8">
            <!-- Left Column: Text Settings -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-green-500 to-emerald-600">
                    <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
                        Konten Teks
                    </h2>
                </div>
                <div class="p-6 space-y-6">
                    <!-- Hero Title -->
                    <div>
                        <label for="landing_hero_title" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Judul Utama (Hero Title)
                        </label>
                        <input type="text" name="landing_hero_title" id="landing_hero_title"
                               value="{{ old('landing_hero_title', $settings['landing_hero_title'] ?? '') }}"
                               class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-green-500 focus:ring-green-500 transition-colors"
                               placeholder="Solusi Keuangan Modern untuk Anda">
                        @error('landing_hero_title')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Hero Subtitle -->
                    <div>
                        <label for="landing_hero_subtitle" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Subtitle (Deskripsi Singkat)
                        </label>
                        <textarea name="landing_hero_subtitle" id="landing_hero_subtitle" rows="3"
                                  class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-green-500 focus:ring-green-500 transition-colors"
                                  placeholder="Bergabunglah dengan koperasi kami untuk masa depan finansial yang lebih baik...">{{ old('landing_hero_subtitle', $settings['landing_hero_subtitle'] ?? '') }}</textarea>
                        @error('landing_hero_subtitle')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- About Text -->
                    <div>
                        <label for="landing_about_text" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Tentang Koperasi (About Us)
                        </label>
                        <textarea name="landing_about_text" id="landing_about_text" rows="5"
                                  class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-green-500 focus:ring-green-500 transition-colors"
                                  placeholder="Kami adalah koperasi modern yang berkomitmen untuk mensejahterakan anggota melalui inovasi teknologi...">{{ old('landing_about_text', $settings['landing_about_text'] ?? '') }}</textarea>
                        @error('landing_about_text')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Right Column: Image Settings -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-blue-500 to-indigo-600">
                    <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Gambar Utama (Hero Image)
                    </h2>
                </div>
                <div class="p-6 space-y-6">
                    <!-- Current Image Preview -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                            Preview Gambar Saat Ini
                        </label>
                        <div class="relative rounded-xl overflow-hidden border-2 border-dashed border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50">
                            @if(isset($settings['landing_hero_image']) && $settings['landing_hero_image'])
                                <img src="{{ Storage::url($settings['landing_hero_image']) }}" 
                                     alt="Hero Image Preview" 
                                     class="w-full h-64 object-cover">
                                <div class="absolute bottom-2 right-2">
                                    <span class="px-3 py-1 rounded-full bg-green-500 text-white text-xs font-bold shadow-lg">
                                        Gambar Aktif
                                    </span>
                                </div>
                            @else
                                <div class="w-full h-64 flex flex-col items-center justify-center text-gray-400 dark:text-gray-500">
                                    <svg class="w-16 h-16 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <p class="text-sm">Belum ada gambar</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Upload New Image -->
                    <div>
                        <label for="hero_image" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Upload Gambar Baru
                        </label>
                        <div class="relative">
                            <input type="file" name="hero_image" id="hero_image" accept="image/*"
                                   class="block w-full text-sm text-gray-500 dark:text-gray-400
                                          file:mr-4 file:py-3 file:px-5 file:rounded-xl file:border-0
                                          file:text-sm file:font-semibold file:bg-green-50 file:text-green-700
                                          hover:file:bg-green-100 dark:file:bg-green-900/30 dark:file:text-green-300
                                          cursor-pointer transition-all">
                        </div>
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                            Format: JPG, PNG, GIF, WEBP. Maksimal 5MB. Rekomendasi: 1200 x 800 px (Landscape).
                        </p>
                        @error('hero_image')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Fitur Unggulan Section -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-green-500 to-teal-600">
                <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                    Fitur Unggulan (3 Kartu)
                </h2>
            </div>
            <div class="p-6 space-y-8">
                @for($i = 1; $i <= 3; $i++)
                <div class="p-6 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600">
                    <h4 class="font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <span class="w-8 h-8 rounded-full bg-green-100 text-green-600 flex items-center justify-center text-sm font-bold">{{ $i }}</span>
                        Fitur {{ $i }}
                    </h4>
                    <div class="grid md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Judul Fitur</label>
                            <input type="text" name="landing_feature{{ $i }}_title" 
                                   value="{{ old("landing_feature{$i}_title", $settings["landing_feature{$i}_title"] ?? '') }}"
                                   class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-green-500 focus:ring-green-500"
                                   placeholder="{{ $i == 1 ? 'Simpan Pinjam' : ($i == 2 ? 'Toko Digital' : 'Laporan Real-time') }}">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Deskripsi Fitur</label>
                            <input type="text" name="landing_feature{{ $i }}_desc" 
                                   value="{{ old("landing_feature{$i}_desc", $settings["landing_feature{$i}_desc"] ?? '') }}"
                                   class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-green-500 focus:ring-green-500"
                                   placeholder="Deskripsi singkat fitur...">
                        </div>
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Gambar/Icon Fitur</label>
                        <div class="flex items-center gap-4">
                            @if(isset($settings["landing_feature{$i}_image"]) && $settings["landing_feature{$i}_image"])
                                <img src="{{ Storage::url($settings["landing_feature{$i}_image"]) }}" alt="Feature {{ $i }}" class="w-16 h-16 object-contain rounded-lg bg-white border shadow-sm">
                            @else
                                <div class="w-16 h-16 rounded-lg bg-gray-200 dark:bg-gray-600 flex items-center justify-center text-gray-400">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                            @endif
                            <input type="file" name="feature{{ $i }}_image" accept="image/*" class="flex-1 text-sm text-gray-500 dark:text-gray-400
                                file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0
                                file:text-sm file:font-semibold file:bg-green-50 file:text-green-700
                                hover:file:bg-green-100 dark:file:bg-green-900/30 dark:file:text-green-300">
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Opsional. Upload gambar/icon untuk fitur ini.</p>
                    </div>
                </div>
                @endfor
            </div>
        </div>

        <!-- Visi & Misi Section -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-purple-500 to-indigo-600">
                <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                    Visi & Misi
                </h2>
            </div>
            <div class="p-6 grid lg:grid-cols-2 gap-6">
                <!-- Visi -->
                <div>
                    <label for="landing_visi" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Visi Koperasi
                    </label>
                    <textarea name="landing_visi" id="landing_visi" rows="5"
                              class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-purple-500 focus:ring-purple-500 transition-colors"
                              placeholder="Menjadi koperasi terdepan yang mensejahterakan anggota...">{{ old('landing_visi', $settings['landing_visi'] ?? '') }}</textarea>
                    @error('landing_visi')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Misi -->
                <div>
                    <label for="landing_misi" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Misi Koperasi <span class="text-xs text-gray-400">(Pisahkan dengan baris baru untuk setiap poin)</span>
                    </label>
                    <textarea name="landing_misi" id="landing_misi" rows="5"
                              class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-purple-500 focus:ring-purple-500 transition-colors"
                              placeholder="Memberikan pelayanan prima kepada anggota.
Mengelola keuangan secara transparan dan akuntabel.
Meningkatkan kesejahteraan anggota melalui program-program inovatif.">{{ old('landing_misi', $settings['landing_misi'] ?? '') }}</textarea>
                    @error('landing_misi')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Program Kerja Section -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-orange-500 to-amber-600">
                <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    Program Kerja
                </h2>
            </div>
            <div class="p-6">
                <div>
                    <label for="landing_program_kerja" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Daftar Program Kerja <span class="text-xs text-gray-400">(Pisahkan dengan baris baru untuk setiap program)</span>
                    </label>
                    <textarea name="landing_program_kerja" id="landing_program_kerja" rows="8"
                              class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-orange-500 focus:ring-orange-500 transition-colors"
                              placeholder="Digitalisasi layanan simpan pinjam
Pengembangan e-commerce internal
Pelatihan kewirausahaan anggota
Program beasiswa anak anggota
Kerjasama dengan BUMN dan swasta">{{ old('landing_program_kerja', $settings['landing_program_kerja'] ?? '') }}</textarea>
                    @error('landing_program_kerja')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>



        <!-- About Section Details -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-blue-500 to-cyan-600">
                <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Section Tentang Kami (Detail)
                </h2>
            </div>
            <div class="p-6 space-y-6">
                <div class="grid lg:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Judul Section About</label>
                        <input type="text" name="landing_about_title" 
                               value="{{ old('landing_about_title', $settings['landing_about_title'] ?? '') }}"
                               class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                               placeholder="Bersama Membangun Kesejahteraan.">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Gambar Section About</label>
                        <div class="flex items-center gap-4">
                            @if(isset($settings['landing_about_image']) && $settings['landing_about_image'])
                                <img src="{{ Storage::url($settings['landing_about_image']) }}" alt="About" class="w-20 h-20 object-cover rounded-lg border shadow-sm">
                            @else
                                <div class="w-20 h-20 rounded-lg bg-gray-200 dark:bg-gray-600 flex items-center justify-center text-gray-400">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                            @endif
                            <input type="file" name="about_image" accept="image/*" class="flex-1 text-sm text-gray-500
                                file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0
                                file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700
                                hover:file:bg-blue-100">
                        </div>
                    </div>
                </div>

                <h4 class="font-bold text-gray-900 dark:text-white pt-4 border-t">Highlight Cards (2 Kartu)</h4>
                <div class="grid lg:grid-cols-2 gap-6">
                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                        <p class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">Highlight 1</p>
                        <input type="text" name="landing_about_highlight1_title" 
                               value="{{ old('landing_about_highlight1_title', $settings['landing_about_highlight1_title'] ?? '') }}"
                               class="w-full rounded-lg border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white mb-2 text-sm"
                               placeholder="Badan Hukum Resmi & Terdaftar">
                        <input type="text" name="landing_about_highlight1_desc" 
                               value="{{ old('landing_about_highlight1_desc', $settings['landing_about_highlight1_desc'] ?? '') }}"
                               class="w-full rounded-lg border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"
                               placeholder="Legalitas terjamin di bawah kementerian koperasi.">
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                        <p class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">Highlight 2</p>
                        <input type="text" name="landing_about_highlight2_title" 
                               value="{{ old('landing_about_highlight2_title', $settings['landing_about_highlight2_title'] ?? '') }}"
                               class="w-full rounded-lg border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white mb-2 text-sm"
                               placeholder="Sistem Pengelolaan Profesional">
                        <input type="text" name="landing_about_highlight2_desc" 
                               value="{{ old('landing_about_highlight2_desc', $settings['landing_about_highlight2_desc'] ?? '') }}"
                               class="w-full rounded-lg border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"
                               placeholder="Dikelola oleh tim ahli dengan standar audit terpercaya.">
                    </div>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-slate-700 to-slate-900">
                <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
                    Section CTA (Call to Action)
                </h2>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Judul CTA</label>
                    <input type="text" name="landing_cta_title" 
                           value="{{ old('landing_cta_title', $settings['landing_cta_title'] ?? '') }}"
                           class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                           placeholder="Siap Memulai Perjalanan Finansial Anda?">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Subtitle CTA</label>
                    <input type="text" name="landing_cta_subtitle" 
                           value="{{ old('landing_cta_subtitle', $settings['landing_cta_subtitle'] ?? '') }}"
                           class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                           placeholder="Bergabunglah dengan ribuan anggota lainnya...">
                </div>
            </div>
        </div>

        <!-- Footer Section -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-gray-600 to-gray-800">
                <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    Footer
                </h2>
            </div>
            <div class="p-6 space-y-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Deskripsi Footer</label>
                    <textarea name="landing_footer_desc" rows="3"
                              class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                              placeholder="Platform koperasi digital terpercaya yang mengutamakan kesejahteraan anggota...">{{ old('landing_footer_desc', $settings['landing_footer_desc'] ?? '') }}</textarea>
                </div>
                
                <h4 class="font-bold text-gray-900 dark:text-white pt-4 border-t">Link Social Media</h4>
                <div class="grid md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-400" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                            Twitter / X
                        </label>
                        <input type="url" name="landing_social_twitter" 
                               value="{{ old('landing_social_twitter', $settings['landing_social_twitter'] ?? '') }}"
                               class="w-full rounded-lg border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"
                               placeholder="https://twitter.com/...">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 24 24"><path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/></svg>
                            Facebook
                        </label>
                        <input type="url" name="landing_social_facebook" 
                               value="{{ old('landing_social_facebook', $settings['landing_social_facebook'] ?? '') }}"
                               class="w-full rounded-lg border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"
                               placeholder="https://facebook.com/...">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 flex items-center gap-2">
                            <svg class="w-4 h-4 text-pink-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                            Instagram
                        </label>
                        <input type="url" name="landing_social_instagram" 
                               value="{{ old('landing_social_instagram', $settings['landing_social_instagram'] ?? '') }}"
                               class="w-full rounded-lg border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"
                               placeholder="https://instagram.com/...">
                    </div>
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="flex justify-end">
            <button type="submit" 
                    class="inline-flex items-center gap-2 px-8 py-3 rounded-xl bg-green-600 hover:bg-green-700 text-white font-bold shadow-lg shadow-green-500/30 hover:shadow-xl hover:-translate-y-0.5 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                Simpan Perubahan
            </button>
        </div>
    </form>
    
    <!-- Team Member Management Section -->
    <div class="mt-8 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-primary-600 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                Struktur Organisasi / Tim ({{ $teamMembers->count() }})
            </h2>
            <button @click="showAddTeamModal = true" type="button"
                    class="px-4 py-2 bg-white/20 hover:bg-white/30 text-white rounded-lg text-sm font-bold transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Anggota
            </button>
        </div>
        <div class="p-6">
            @if($teamMembers->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($teamMembers as $member)
                        <div class="relative group bg-white dark:bg-gray-700 rounded-xl border border-gray-100 dark:border-gray-600 shadow-sm hover:shadow-md transition-all">
                            <div class="aspect-square w-full overflow-hidden rounded-t-xl bg-gray-100 relative">
                                <img src="{{ Storage::url($member->image) }}" alt="{{ $member->name }}" class="w-full h-full object-cover">
                                <!-- Delete Button -->
                                <form action="{{ route('settings.landing.member.destroy', $member->id) }}" method="POST" class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity" onsubmit="return confirm('Yakin ingin menghapus anggota ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 bg-red-500 text-white rounded-full shadow-lg hover:bg-red-600 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                            <div class="p-4 text-center">
                                <h4 class="font-bold text-gray-900 dark:text-white">{{ $member->name }}</h4>
                                <p class="text-sm text-green-600 font-medium mb-2">{{ $member->role }}</p>
                                <div class="flex justify-center gap-3 mt-3">
                                    @if($member->twitter_link)<span class="text-gray-400 text-xs">TW</span>@endif
                                    @if($member->facebook_link)<span class="text-gray-400 text-xs">FB</span>@endif
                                    @if($member->instagram_link)<span class="text-gray-400 text-xs">IG</span>@endif
                                    @if($member->linkedin_link)<span class="text-gray-400 text-xs">IN</span>@endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 text-gray-500 flex flex-col items-center">
                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                    <p class="mb-4">Belum ada anggota tim untuk ditampilkan.</p>
                    <button @click="showAddTeamModal = true" type="button"
                            class="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl text-sm font-bold shadow-lg shadow-green-500/30 transition-all hover:-translate-y-0.5 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Anggota Tim
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Tambah Anggota -->
    <!-- Modal Tambah Anggota (AlpineJS) -->
    <div x-show="showAddTeamModal" 
         style="display: none;"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden border border-gray-200 dark:border-gray-700"
             @click.away="showAddTeamModal = false"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-90"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-90">
            
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-700/50">
                <h3 class="font-bold text-lg text-gray-800 dark:text-white">Tambah Anggota Tim</h3>
                <button type="button" @click="showAddTeamModal = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form action="{{ route('settings.landing.member.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" required class="w-full rounded-lg border-gray-200 dark:border-gray-600 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Jabatan (Role)</label>
                    <input type="text" name="role" required class="w-full rounded-lg border-gray-200 dark:border-gray-600 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Foto Profile</label>
                    <input type="file" name="image" required accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                    <p class="text-xs text-gray-400 mt-1">Ukuran rasio 1:1 (persegi) direkomendasikan.</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Sosial Media (Opsional)</label>
                    <div class="grid grid-cols-2 gap-3">
                        <input type="url" name="twitter_link" placeholder="Twitter URL" class="w-full rounded-lg border-gray-200 text-xs">
                        <input type="url" name="facebook_link" placeholder="Facebook URL" class="w-full rounded-lg border-gray-200 text-xs">
                        <input type="url" name="instagram_link" placeholder="Instagram URL" class="w-full rounded-lg border-gray-200 text-xs">
                        <input type="url" name="linkedin_link" placeholder="LinkedIn URL" class="w-full rounded-lg border-gray-200 text-xs">
                    </div>
                </div>
                <div class="pt-4 flex justify-end gap-2">
                    <button type="button" @click="showAddTeamModal = false" class="px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-100 font-semibold text-sm">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 font-semibold text-sm">Simpan Anggota</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Work Program Management Section -->
    <div class="mt-8 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-orange-600 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Program Kerja ({{ $workPrograms->count() }})
            </h2>
            <button @click="showAddProgramModal = true" type="button"
                    class="px-4 py-2 bg-white/20 hover:bg-white/30 text-white rounded-lg text-sm font-bold transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Program
            </button>
        </div>
        <div class="p-6">
            @if($workPrograms->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($workPrograms as $program)
                        <div class="relative group bg-white dark:bg-gray-700 rounded-xl border border-gray-100 dark:border-gray-600 shadow-sm hover:shadow-md transition-all p-6">
                            <!-- Delete Button -->
                            <form action="{{ route('settings.landing.program.destroy', $program->id) }}" method="POST" class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity" onsubmit="return confirm('Yakin ingin menghapus program ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 bg-red-500 text-white rounded-full shadow-lg hover:bg-red-600 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                            
                            <!-- Color Badge -->
                            <div class="flex items-center gap-3 mb-4">
                                @php
                                    $colorClasses = [
                                        'green' => 'bg-green-100 text-green-700',
                                        'blue' => 'bg-blue-100 text-blue-700',
                                        'purple' => 'bg-purple-100 text-purple-700',
                                        'orange' => 'bg-orange-100 text-orange-700',
                                        'teal' => 'bg-teal-100 text-teal-700',
                                        'pink' => 'bg-pink-100 text-pink-700',
                                    ];
                                    $colorClass = $colorClasses[$program->color] ?? 'bg-blue-100 text-blue-700';
                                @endphp
                                <span class="px-3 py-1 rounded-full text-xs font-bold {{ $colorClass }}">
                                    {{ ucfirst($program->color) }}
                                </span>
                            </div>

                            <!-- Icon Preview -->
                            @if($program->icon && Storage::disk('public')->exists($program->icon))
                                <div class="w-16 h-16 rounded-xl bg-gray-100 dark:bg-gray-600 flex items-center justify-center mb-4">
                                    <img src="{{ Storage::url($program->icon) }}" alt="{{ $program->title }}" class="w-10 h-10 object-contain">
                                </div>
                            @endif

                            <h3 class="font-bold text-gray-900 dark:text-white text-lg mb-2">{{ $program->title }}</h3>
                            @if($program->description)
                                <p class="text-sm text-gray-600 dark:text-gray-300 line-clamp-2">{{ $program->description }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-16">
                    <div class="w-20 h-20 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Belum Ada Program Kerja</h3>
                    <p class="text-slate-600 dark:text-gray-400 mb-6">Tambahkan program kerja pertama Anda dengan klik tombol di atas.</p>
                    <button @click="showAddProgramModal = true" type="button"
                            class="inline-flex items-center gap-2 px-6 py-3 bg-orange-600 hover:bg-orange-700 text-white rounded-xl font-bold shadow-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Program Kerja
                    </button>
                </div>
            @endif
        </div>
    </div>


    <!-- Add Program Modal -->
    <div x-show="showAddProgramModal" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 dark:bg-gray-900/80 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden border border-gray-200 dark:border-gray-700"
             @click.away="showAddProgramModal = false"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-90"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-90">
            
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-700/50">
                <h3 class="font-bold text-lg text-gray-800 dark:text-white">Tambah Program Kerja</h3>
                <button type="button" @click="showAddProgramModal = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form action="{{ route('settings.landing.program.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Judul Program</label>
                    <input type="text" name="title" required class="w-full rounded-lg border-gray-200 dark:border-gray-600 text-sm" placeholder="Contoh: Digitalisasi Layanan Simpan Pinjam">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Deskripsi (Opsional)</label>
                    <textarea name="description" rows="3" class="w-full rounded-lg border-gray-200 dark:border-gray-600 text-sm" placeholder="Jelaskan program ini secara singkat"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Warna Icon</label>
                    <div class="grid grid-cols-3 gap-2">
                        <label class="relative cursor-pointer block">
                            <input type="radio" name="color" value="green" required class="peer sr-only">
                            <div class="p-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 peer-checked:border-green-600 peer-checked:bg-green-50 dark:peer-checked:bg-green-900/20 hover:border-gray-400 transition-all flex items-center justify-center gap-2 bg-white dark:bg-gray-700">
                                <div class="w-4 h-4 rounded-full bg-green-600"></div>
                                <span class="text-xs font-bold" style="color: #374151 !important;">Green</span>
                            </div>
                        </label>
                        <label class="relative cursor-pointer block">
                            <input type="radio" name="color" value="blue" class="peer sr-only">
                            <div class="p-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 peer-checked:border-blue-600 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 hover:border-gray-400 transition-all flex items-center justify-center gap-2 bg-white dark:bg-gray-700">
                                <div class="w-4 h-4 rounded-full bg-blue-600"></div>
                                <span class="text-xs font-bold" style="color: #374151 !important;">Blue</span>
                            </div>
                        </label>
                        <label class="relative cursor-pointer block">
                            <input type="radio" name="color" value="purple" class="peer sr-only">
                            <div class="p-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 peer-checked:border-purple-600 peer-checked:bg-purple-50 dark:peer-checked:bg-purple-900/20 hover:border-gray-400 transition-all flex items-center justify-center gap-2 bg-white dark:bg-gray-700">
                                <div class="w-4 h-4 rounded-full bg-purple-600"></div>
                                <span class="text-xs font-bold" style="color: #374151 !important;">Purple</span>
                            </div>
                        </label>
                        <label class="relative cursor-pointer block">
                            <input type="radio" name="color" value="orange" class="peer sr-only">
                            <div class="p-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 peer-checked:border-orange-600 peer-checked:bg-orange-50 dark:peer-checked:bg-orange-900/20 hover:border-gray-400 transition-all flex items-center justify-center gap-2 bg-white dark:bg-gray-700">
                                <div class="w-4 h-4 rounded-full bg-orange-600"></div>
                                <span class="text-xs font-bold" style="color: #374151 !important;">Orange</span>
                            </div>
                        </label>
                        <label class="relative cursor-pointer block">
                            <input type="radio" name="color" value="teal" class="peer sr-only">
                            <div class="p-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 peer-checked:border-teal-600 peer-checked:bg-teal-50 dark:peer-checked:bg-teal-900/20 hover:border-gray-400 transition-all flex items-center justify-center gap-2 bg-white dark:bg-gray-700">
                                <div class="w-4 h-4 rounded-full bg-teal-600"></div>
                                <span class="text-xs font-bold" style="color: #374151 !important;">Teal</span>
                            </div>
                        </label>
                        <label class="relative cursor-pointer block">
                            <input type="radio" name="color" value="pink" class="peer sr-only">
                            <div class="p-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 peer-checked:border-pink-600 peer-checked:bg-pink-50 dark:peer-checked:bg-pink-900/20 hover:border-gray-400 transition-all flex items-center justify-center gap-2 bg-white dark:bg-gray-700">
                                <div class="w-4 h-4 rounded-full bg-pink-600"></div>
                                <span class="text-xs font-bold" style="color: #374151 !important;">Pink</span>
                            </div>
                        </label>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Icon Kustom (Opsional)</label>
                    <input type="file" name="icon" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                    <p class="text-xs text-gray-400 mt-1">Jika tidak diisi, akan menggunakan icon default. Format: PNG, JPG, SVG (max 1MB)</p>
                </div>
                <div class="pt-4 flex justify-end gap-2">
                    <button type="button" @click="showAddProgramModal = false" class="px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-100 font-semibold text-sm">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-orange-600 text-white hover:bg-orange-700 font-semibold text-sm">Simpan Program</button>
                </div>
            </form>
        </div>
    </div>


    <!-- Preview Section -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-purple-500 to-pink-600">
            <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                Preview Landing Page
            </h2>
        </div>
        <div class="p-6">
            <div class="flex items-center justify-between">
                <p class="text-gray-600 dark:text-gray-400">Lihat tampilan landing page Anda secara langsung.</p>
                <a href="{{ route('landing') }}" target="_blank" 
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-semibold hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                    Buka Landing Page
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
