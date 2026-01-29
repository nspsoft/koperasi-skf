@extends('layouts.app')

@section('title', __('messages.titles.settings'))

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Pengaturan Sistem</h1>
            <p class="page-subtitle">Konfigurasi profil koperasi dan parameter sistem</p>
        </div>
    </div>

    <!-- Quick Access Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <a href="{{ route('settings.ai') }}" class="glass-card p-4 hover:shadow-lg transition-all group">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center text-purple-600 group-hover:scale-110 transition-transform">
                    🤖
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900 dark:text-white text-sm">AI Assistant</h4>
                    <p class="text-xs text-gray-500">Konfigurasi AI</p>
                </div>
            </div>
        </a>
        <a href="{{ route('settings.landing') }}" class="glass-card p-4 hover:shadow-lg transition-all group">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center text-blue-600 group-hover:scale-110 transition-transform">
                    🌐
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900 dark:text-white text-sm">Landing Page</h4>
                    <p class="text-xs text-gray-500">Halaman Publik</p>
                </div>
            </div>
        </a>
        <a href="{{ route('settings.backup') }}" class="glass-card p-4 hover:shadow-lg transition-all group">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center text-green-600 group-hover:scale-110 transition-transform">
                    💾
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900 dark:text-white text-sm">Backup</h4>
                    <p class="text-xs text-gray-500">Cadangan Data</p>
                </div>
            </div>
        </a>
        <a href="{{ route('settings.audit-logs') }}" class="glass-card p-4 hover:shadow-lg transition-all group">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900/30 rounded-xl flex items-center justify-center text-amber-600 group-hover:scale-110 transition-transform">
                    📋
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900 dark:text-white text-sm">Audit Log</h4>
                    <p class="text-xs text-gray-500">Riwayat Aktivitas</p>
                </div>
            </div>
        </a>
    </div>

    <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- General Profile -->
            <div class="glass-card-solid p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    Profil Koperasi
                </h3>

                <div class="space-y-4">
                    <!-- Logo Upload -->
                    <div class="form-group">
                        <label class="form-label">Logo Koperasi</label>
                        <div class="flex items-center gap-4">
                            @if(isset($settings['coop_logo']))
                                <img src="{{ Storage::url($settings['coop_logo']) }}" alt="Logo" class="w-16 h-16 object-contain bg-gray-100 rounded-lg p-1 border">
                            @endif
                            <input type="file" name="logo" accept="image/*" class="block w-full text-sm text-gray-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-full file:border-0
                                file:text-sm file:font-semibold
                                file:bg-primary-50 file:text-primary-700
                                hover:file:bg-primary-100
                            "/>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Format: PNG/JPG, Max 2MB. Digunakan untuk Kartu Anggota & Laporan.</p>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Nama Koperasi</label>
                        <input type="text" name="coop_name" value="{{ $settings['coop_name'] ?? 'SPINDO KARAWANG FACTORY' }}" class="form-input">
                    </div>
                     <div class="form-group">
                        <label class="form-label">Alamat Lengkap</label>
                        <textarea name="coop_address" rows="3" class="form-input">{{ $settings['coop_address'] ?? '' }}</textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nomor Telepon / WA</label>
                        <input type="text" name="coop_phone" value="{{ $settings['coop_phone'] ?? '' }}" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email Resmi</label>
                        <input type="email" name="coop_email" value="{{ $settings['coop_email'] ?? '' }}" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">URL Akses Mobile (untuk QR Code)</label>
                        <input type="text" name="app_url" value="{{ $settings['app_url'] ?? 'http://192.168.110.39/Koperasi/public' }}" class="form-input" placeholder="http://192.168.1.100/Koperasi/public">
                        <p class="text-xs text-gray-400 mt-1">URL ini akan ditampilkan sebagai QR Code di Dashboard untuk akses dari HP</p>
                    </div>

                    <!-- Document Logos -->
                    <div class="pt-4 mt-4 border-t border-gray-100 dark:border-gray-700">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Logo Kop Surat & Dokumen</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="form-group">
                                <label class="form-label text-xs">Logo 1 (Koperasi Indonesia)</label>
                                <div class="flex items-center gap-3">
                                    @if(isset($settings['doc_logo_1']))
                                        <img src="{{ Storage::url($settings['doc_logo_1']) }}" class="w-10 h-10 object-contain bg-gray-50 rounded border">
                                    @endif
                                    <input type="file" name="doc_logo_1" accept="image/*" class="text-xs text-gray-500 file:mr-2 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-xs file:bg-primary-50">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label text-xs">Logo 2 (Kopkar SKF)</label>
                                <div class="flex items-center gap-3">
                                    @if(isset($settings['doc_logo_2']))
                                        <img src="{{ Storage::url($settings['doc_logo_2']) }}" class="w-10 h-10 object-contain bg-gray-50 rounded border">
                                    @endif
                                    <input type="file" name="doc_logo_2" accept="image/*" class="text-xs text-gray-500 file:mr-2 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-xs file:bg-primary-50">
                                </div>
                            </div>
                        </div>
                        <p class="text-[10px] text-gray-400 mt-2">Logo ini akan muncul di bagian kiri dan kanan header (Kop Surat) pada setiap dokumen yang di-generate.</p>
                    </div>
                </div>
            </div>

            <!-- WhatsApp Configuration -->
            <div class="glass-card-solid p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    WhatsApp Floating Button
                </h3>

                <div class="space-y-4">
                    <div class="form-group">
                        <label class="form-label">Nomor WhatsApp Admin</label>
                        <input type="text" name="whatsapp_number" value="{{ $settings['whatsapp_number'] ?? '' }}" class="form-input" placeholder="6281234567890">
                        <p class="text-xs text-gray-400 mt-1">Gunakan format internasional tanpa + (contoh: 6281234567890). Kosongkan untuk menyembunyikan tombol.</p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Pesan Default</label>
                        <textarea name="whatsapp_message" rows="2" class="form-input" placeholder="Halo Admin...">{{ $settings['whatsapp_message'] ?? 'Halo, saya butuh bantuan.' }}</textarea>
                        <p class="text-xs text-gray-400 mt-1">Pesan otomatis yang akan muncul saat chat dibuka.</p>
                    </div>
                </div>
            </div>

            <!-- Savings Configuration -->
            <div class="glass-card-solid p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Parameter Simpanan (Default)
                </h3>

                <div class="space-y-4">
                    <div class="form-group">
                        <label class="form-label">Simpanan Pokok (Rp)</label>
                        <input type="number" name="saving_principal" value="{{ $settings['saving_principal'] ?? '' }}" class="form-input" placeholder="Contoh: 100000">
                        <p class="text-xs text-gray-400 mt-1">Nilai default untuk input Simpanan Pokok</p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Simpanan Wajib (Rp)</label>
                        <input type="number" name="saving_mandatory" value="{{ $settings['saving_mandatory'] ?? '' }}" class="form-input" placeholder="Contoh: 50000">
                        <p class="text-xs text-gray-400 mt-1">Nilai default untuk input Simpanan Wajib</p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Default Limit Kredit Mart (Rp)</label>
                        <input type="number" name="default_credit_limit" value="{{ $settings['default_credit_limit'] ?? '500000' }}" class="form-input" placeholder="Contoh: 500000">
                        <p class="text-xs text-gray-400 mt-1">Limit kredit belanja di Koperasi Mart untuk member baru</p>
                    </div>
                </div>
            </div>

            <!-- Koperasi Mart Settings -->
            <div class="glass-card-solid p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="flex items-center gap-2 font-bold text-gray-900 dark:text-white">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        Pengaturan Koperasi Mart
                    </h3>
                    
                    <!-- Mass Recalculate Tool Interface -->
                    <a href="{{ route('settings.sync-prices.preview') }}" class="flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 text-amber-700 dark:bg-amber-900/20 dark:text-amber-400 rounded-lg text-xs font-bold hover:bg-amber-100 transition-colors border border-amber-200 dark:border-amber-800">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        PREVIEW PERUBAHAN HARGA
                    </a>
                </div>

                <div class="space-y-4">
                    <!-- Inventory Costing Method -->
                    <div class="form-group">
                        <label class="form-label">Metode Costing Persediaan</label>
                        <select name="inventory_costing_method" class="form-input">
                            <option value="last_price" {{ ($settings['inventory_costing_method'] ?? 'last_price') == 'last_price' ? 'selected' : '' }}>
                                📌 Keep Last Price (Harga Terakhir)
                            </option>
                            <option value="wac" {{ ($settings['inventory_costing_method'] ?? 'last_price') == 'wac' ? 'selected' : '' }}>
                                📊 WAC (Weighted Average Cost)
                            </option>
                        </select>
                        <div class="mt-2 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg text-xs text-blue-800 dark:text-blue-300">
                            <p class="font-semibold mb-1">Penjelasan Metode:</p>
                            <ul class="list-disc list-inside space-y-1">
                                <li><strong>Keep Last Price:</strong> Harga modal = harga pembelian terakhir</li>
                                <li><strong>WAC:</strong> Harga modal = rata-rata tertimbang (stok lama × modal lama + stok baru × modal baru) / total stok</li>
                            </ul>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-group">
                            <label class="form-label">Point Earn Rate (Rp)</label>
                            <input type="number" name="point_earn_rate" value="{{ $settings['point_earn_rate'] ?? '10000' }}" class="form-input">
                            <p class="text-xs text-gray-400 mt-1">Belanja berapa untuk dapat 1 poin</p>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Point Conversion Rate (Rp)</label>
                            <input type="number" name="point_conversion_rate" value="{{ $settings['point_conversion_rate'] ?? '1' }}" class="form-input">
                            <p class="text-xs text-gray-400 mt-1">Nilai 1 poin dalam Rupiah</p>
                        </div>
                    </div>

                    <!-- Pricing Settings -->
                    <div class="pt-4 mt-4 border-t border-gray-100 dark:border-gray-700">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">💰 Pengaturan Harga Produk</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="form-group">
                                <label class="form-label">Default Margin (%)</label>
                                <input type="number" name="default_margin" value="{{ $settings['default_margin'] ?? '20' }}" class="form-input" min="0" step="0.5">
                                <p class="text-xs text-gray-400 mt-1">Margin default untuk produk baru</p>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Pembulatan Harga (Rp)</label>
                                <select name="price_ceiling" class="form-input">
                                    @php $currentCeiling = $settings['price_ceiling'] ?? 1000; @endphp
                                    <option value="100" {{ $currentCeiling == 100 ? 'selected' : '' }}>Rp 100</option>
                                    <option value="500" {{ $currentCeiling == 500 ? 'selected' : '' }}>Rp 500</option>
                                    <option value="1000" {{ $currentCeiling == 1000 ? 'selected' : '' }}>Rp 1.000</option>
                                    <option value="5000" {{ $currentCeiling == 5000 ? 'selected' : '' }}>Rp 5.000</option>
                                    <option value="10000" {{ $currentCeiling == 10000 ? 'selected' : '' }}>Rp 10.000</option>
                                </select>
                                <p class="text-xs text-gray-400 mt-1">Harga dibulatkan ke atas (ceiling)</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Loan Configuration -->
            <div class="glass-card-solid p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Parameter Pinjaman (Default)
                </h3>

                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-group">
                            <label class="form-label">Bunga Reguler (%)</label>
                            <input type="number" step="0.01" name="loan_interest_regular" value="{{ $settings['loan_interest_regular'] ?? '1.5' }}" class="form-input">
                            <p class="text-xs text-gray-400 mt-1">Per bulan</p>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Max Tenor (Bulan)</label>
                            <input type="number" name="loan_max_duration" value="{{ $settings['loan_max_duration'] ?? '60' }}" class="form-input">
                        </div>
                    </div>
                     <div class="form-group">
                        <label class="form-label">Batas Pinjaman Maksimal (Rp)</label>
                        <input type="number" name="loan_limit_max" value="{{ $settings['loan_limit_max'] ?? '50000000' }}" class="form-input">
                    </div>
                    
                    <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-700">
                        <p class="text-sm text-yellow-800 dark:text-yellow-300">
                            <strong>Catatan:</strong> Pengubahan nilai default ini tidak akan mengubah kontrak pinjaman yang sudah berjalan (Aktif), hanya berlaku untuk pengajuan baru.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Payment Configuration (QRIS) -->
            <div class="glass-card-solid p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                    Rekening Pembayaran & QRIS
                </h3>

                <div class="space-y-4">
                    <div class="form-group">
                        <label class="form-label">Nama Bank</label>
                        <input type="text" name="bank_name" value="{{ $settings['bank_name'] ?? '' }}" class="form-input" placeholder="Contoh: Bank Mandiri">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nomor Rekening</label>
                        <input type="text" name="bank_account" value="{{ $settings['bank_account'] ?? '' }}" class="form-input" placeholder="Contoh: 1234567890">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Atas Nama</label>
                        <input type="text" name="bank_holder" value="{{ $settings['bank_holder'] ?? '' }}" class="form-input" placeholder="Contoh: Koperasi Karyawan">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Gambar QRIS</label>
                        <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-xl border border-dashed border-gray-300 dark:border-gray-600 text-center">
                            @if(isset($settings['payment_qris_image']) && $settings['payment_qris_image'])
                                <img src="{{ Storage::url($settings['payment_qris_image']) }}" alt="QRIS" class="mx-auto h-48 object-contain rounded-lg mb-4 shadow-md">
                            @else
                                <div class="mx-auto h-32 bg-gray-200 dark:bg-gray-700 rounded-lg mb-4 flex items-center justify-center text-gray-400">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                                </div>
                            @endif
                            
                            <input type="file" name="qris_image" accept="image/*" class="block w-full text-sm text-gray-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-full file:border-0
                                file:text-sm file:font-semibold
                                file:bg-blue-50 file:text-blue-700
                                hover:file:bg-blue-100
                            "/>
                            <p class="text-xs text-gray-400 mt-2">Upload gambar QRIS untuk pembayaran digital</p>
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-t border-blue-200 dark:border-blue-800">
                        <p class="text-[10px] text-blue-600 dark:text-blue-500 font-medium italic">
                            *QRIS bersifat legal dan diawasi Bank Indonesia. Satu QRIS bisa menerima pembayaran dari semua jenis e-wallet/mobile banking.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Mail Server Configuration -->
            <div class="glass-card-solid p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    Mail Server (SMTP)
                </h3>

                <div class="space-y-4">
                    <!-- Detailed HTML Guide -->
                    <div x-data="{ showGuide: false }" class="mb-6">
                        <button type="button" @click="showGuide = !showGuide" class="flex items-center justify-between w-full px-4 py-3 bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-100 dark:border-indigo-800 rounded-xl text-indigo-700 dark:text-indigo-300 hover:bg-indigo-100 transition-all">
                            <span class="flex items-center gap-2 font-bold text-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                BACA PANDUAN KONFIGURASI (GMAIL & HOSTING)
                            </span>
                            <svg class="w-5 h-5 transition-transform" :class="showGuide ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>

                        <div x-show="showGuide" x-transition class="mt-3 p-5 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-inner">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Gmail Guide -->
                                <div>
                                    <h4 class="flex items-center gap-2 font-bold text-red-600 mb-3">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.48 10.92v3.28h7.84c-.24 1.84-.908 3.153-1.908 4.153-1.228 1.228-3.14 2.508-6.412 2.508-5.16 0-9.28-4.187-9.28-9.347s4.12-9.347 9.28-9.347c2.787 0 4.88 1.1 6.387 2.547l2.173-2.173c-2.027-1.92-5.067-3.373-8.56-3.373-7.147 0-13 5.853-13 13s5.853 13 13 13c3.853 0 6.747-1.267 9.013-3.64 2.333-2.333 3.067-5.587 3.067-8.187 0-.773-.067-1.507-.2-2.173h-11.88z"/></svg>
                                        Panduan Gmail
                                    </h4>
                                    <ol class="text-xs text-gray-600 dark:text-gray-400 space-y-2 list-decimal list-inside">
                                        <li>Aktifkan <strong>Verifikasi 2 Langkah</strong> di akun Google Anda.</li>
                                        <li>Buka <a href="https://myaccount.google.com/apppasswords" target="_blank" class="text-blue-600 underline">Google App Passwords</a>.</li>
                                        <li>Pilih "Lainnya" dan beri nama <strong>"Sistem Koperasi"</strong>.</li>
                                        <li>Salin 16 digit kode yang muncul ke kolom <strong>Mail Password</strong>.</li>
                                        <li>Gunakan Host: <code>smtp.gmail.com</code> & Port: <code>587</code>.</li>
                                    </ol>
                                </div>

                                <!-- Hosting Guide -->
                                <div>
                                    <h4 class="flex items-center gap-2 font-bold text-blue-600 mb-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                        Panduan Hosting (cPanel)
                                    </h4>
                                    <ul class="text-xs text-gray-600 dark:text-gray-400 space-y-2 list-disc list-inside">
                                        <li>Login ke cPanel > <strong>Email Accounts</strong>.</li>
                                        <li>Klik <strong>Manage</strong> > <strong>Connect Devices</strong>.</li>
                                        <li>Pilih pengaturan <strong>SSL/TLS (Recommended)</strong>.</li>
                                        <li>Gunakan Port <strong>465</strong> untuk SSL atau <strong>587</strong> untuk TLS.</li>
                                        <li>Host biasanya berupa <code>mail.domainanda.com</code>.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-group">
                            <label class="form-label">Mail Mailer</label>
                            <input type="text" name="mail_mailer" value="{{ $settings['mail_mailer'] ?? 'smtp' }}" class="form-input" placeholder="smtp">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Mail Host</label>
                            <input type="text" name="mail_host" value="{{ $settings['mail_host'] ?? '' }}" class="form-input" placeholder="smtp.mailtrap.io">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-group">
                            <label class="form-label">Mail Port</label>
                            <input type="text" name="mail_port" value="{{ $settings['mail_port'] ?? '587' }}" class="form-input" placeholder="587">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Encryption</label>
                            <select name="mail_encryption" class="form-input">
                                <option value="tls" {{ ($settings['mail_encryption'] ?? 'tls') == 'tls' ? 'selected' : '' }}>TLS</option>
                                <option value="ssl" {{ ($settings['mail_encryption'] ?? 'tls') == 'ssl' ? 'selected' : '' }}>SSL</option>
                                <option value="null" {{ ($settings['mail_encryption'] ?? 'tls') == 'null' ? 'selected' : '' }}>None</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-group">
                            <label class="form-label">Mail Username</label>
                            <input type="text" name="mail_username" value="{{ $settings['mail_username'] ?? '' }}" class="form-input">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Mail Password</label>
                            <input type="password" name="mail_password" value="{{ $settings['mail_password'] ?? '' }}" class="form-input">
                        </div>
                    </div>

                    <div class="pt-4 mt-4 border-t border-gray-100 dark:border-gray-700">
                        <h4 class="text-xs font-semibold text-gray-900 dark:text-white mb-4">Pengirim (Sender)</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="form-group">
                                <label class="form-label">From Address</label>
                                <input type="email" name="mail_from_address" value="{{ $settings['mail_from_address'] ?? 'noreply@koperasi.com' }}" class="form-input">
                            </div>
                            <div class="form-group">
                                <label class="form-label">From Name</label>
                                <input type="text" name="mail_from_name" value="{{ $settings['mail_from_name'] ?? 'Koperasi SKF' }}" class="form-input">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Test Connection -->
                <div class="mt-8 p-4 bg-indigo-50 dark:bg-indigo-900/20 rounded-xl border border-indigo-100 dark:border-indigo-800">
                    <h4 class="text-sm font-bold text-indigo-900 dark:text-indigo-300 mb-2 flex items-center gap-2">
                        <span>🚀 Test Koneksi Email</span>
                    </h4>
                    <p class="text-[11px] text-indigo-700 dark:text-indigo-400 mb-4">Pastikan Anda telah "Simpan Perubahan" sebelum melakukan tes ini.</p>
                    
                    @csrf
                    <div class="flex gap-2">
                        <input type="email" id="test_email_target" class="form-input text-xs !py-2" placeholder="Masukkan email penerima tes">
                        <button type="button" @click="sendTestEmail()" class="btn-primary !py-2 !text-xs whitespace-nowrap bg-indigo-600 hover:bg-indigo-700">
                            Kirim Tes
                        </button>
                    </div>
                </div>

                <script>
                    function sendTestEmail() {
                        const email = document.getElementById('test_email_target').value;
                        if (!email) {
                            alert('Harap masukkan email tujuan.');
                            return;
                        }
                        
                        // Create a temporary form to submit
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = "{{ route('settings.test-email') }}";
                        
                        const csrfInput = document.createElement('input');
                        csrfInput.type = 'hidden';
                        csrfInput.name = '_token';
                        csrfInput.value = "{{ csrf_token() }}";
                        
                        const emailInput = document.createElement('input');
                        emailInput.type = 'hidden';
                        emailInput.name = 'test_email';
                        emailInput.value = email;
                        
                        form.appendChild(csrfInput);
                        form.appendChild(emailInput);
                        document.body.appendChild(form);
                        form.submit();
                    }
                </script>
            </div>


            <!-- Submit Button -->
            <div class="lg:col-span-2 flex justify-end pt-6 border-t border-gray-100 dark:border-gray-700">
                 <button type="submit" class="btn-primary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                    </svg>
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </form>
@endsection
