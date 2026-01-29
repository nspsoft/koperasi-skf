<!DOCTYPE html>
<html lang="id" x-data="registerWizard()" x-init="init()" :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrasi Anggota - Koperasi Karyawan SKF</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Tesseract.js CDN (V5) -->
    <script src='https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js'></script>
    
    <style>
        body { font-family: ui-sans-serif, system-ui, sans-serif; }
        .text-spindo { color: #0054a6; }
        .dark .text-spindo { color: #60a5fa; }
        .bg-spindo { background-color: #0054a6; }
        .bg-green-spindo { background-color: #009640; }
        .bg-green-spindo:hover { background-color: #007a33; }
        
        /* Step Indicator */
        .step-circle {
            @apply w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-all duration-300;
        }
        .step-circle.active {
            @apply bg-green-500 text-white shadow-lg scale-110;
        }
        .step-circle.completed {
            @apply bg-green-500 text-white;
        }
        .step-circle.pending {
            @apply bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400;
        }
        .step-line {
            @apply flex-1 h-1 mx-2 rounded transition-all duration-500;
        }
        .step-line.active { @apply bg-green-500; }
        .step-line.pending { @apply bg-gray-200 dark:bg-gray-700; }
        
        /* Slide Animation */
        .slide-enter { animation: slideIn 0.3s ease-out; }
        .slide-leave { animation: slideOut 0.3s ease-in; }
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(30px); }
            to { opacity: 1; transform: translateX(0); }
        }
        @keyframes slideOut {
            from { opacity: 1; transform: translateX(0); }
            to { opacity: 0; transform: translateX(-30px); }
        }
        
        /* Scanner Animation */
        .scanner-line {
            position: absolute; width: 100%; height: 2px;
            background: #009640; box-shadow: 0 0 4px #009640;
            top: 0; left: 0; animation: scan 2s infinite linear; display: none;
        }
        @keyframes scan { 0% { top: 0%; } 50% { top: 100%; } 100% { top: 0%; } }
    </style>
</head>
<body class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 transition-colors duration-300">
    
    <!-- Dark Mode Toggle -->
    <button @click="darkMode = !darkMode" class="fixed top-4 right-4 p-2 rounded-lg bg-white dark:bg-gray-800 shadow-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors z-50">
        <svg x-show="!darkMode" class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
        </svg>
        <svg x-show="darkMode" class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
        </svg>
    </button>

    <!-- Logo & Header -->
    <div class="mb-6 text-center">
        <img src="{{ asset('logo.png') }}" alt="Logo SPINDO" class="h-20 w-auto mx-auto mb-3 object-contain shadow-sm rounded-full bg-white dark:bg-gray-800 p-1" 
             onerror="this.style.display='none'; document.getElementById('text-logo').style.display='block';">
        <div id="text-logo" style="display:none;" class="mb-2">
            <h1 class="text-2xl font-bold text-spindo">KOPERASI SKF</h1>
        </div>
        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200">Pendaftaran Anggota Baru</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Lengkapi data berikut untuk menjadi anggota koperasi</p>
    </div>

    <!-- Main Card -->
    <div class="w-full sm:max-w-2xl px-8 py-8 bg-white dark:bg-gray-800 shadow-2xl overflow-hidden sm:rounded-2xl relative">
        
        <!-- Step Indicator -->
        <div class="flex items-center justify-center mb-8">
            <!-- Step 1 -->
            <div class="flex flex-col items-center">
                <div class="step-circle" :class="currentStep >= 1 ? (currentStep > 1 ? 'completed' : 'active') : 'pending'">
                    <template x-if="currentStep > 1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </template>
                    <template x-if="currentStep <= 1"><span>1</span></template>
                </div>
                <span class="text-xs mt-2 font-medium" :class="currentStep >= 1 ? 'text-green-600 dark:text-green-400' : 'text-gray-400'">Data Pribadi</span>
            </div>
            
            <div class="step-line" :class="currentStep > 1 ? 'active' : 'pending'"></div>
            
            <!-- Step 2 -->
            <div class="flex flex-col items-center">
                <div class="step-circle" :class="currentStep >= 2 ? (currentStep > 2 ? 'completed' : 'active') : 'pending'">
                    <template x-if="currentStep > 2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </template>
                    <template x-if="currentStep <= 2"><span>2</span></template>
                </div>
                <span class="text-xs mt-2 font-medium" :class="currentStep >= 2 ? 'text-green-600 dark:text-green-400' : 'text-gray-400'">Data Karyawan</span>
            </div>
            
            <div class="step-line" :class="currentStep > 2 ? 'active' : 'pending'"></div>
            
            <!-- Step 3 -->
            <div class="flex flex-col items-center">
                <div class="step-circle" :class="currentStep >= 3 ? 'active' : 'pending'">
                    <template x-if="currentStep > 3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </template>
                    <template x-if="currentStep <= 3"><span>3</span></template>
                </div>
                <span class="text-xs mt-2 font-medium" :class="currentStep >= 3 ? 'text-green-600 dark:text-green-400' : 'text-gray-400'">Akun</span>
            </div>
        </div>

        <!-- OCR Section (Only on Step 1) -->
        <div x-show="currentStep === 1 && showOcr" x-transition class="mb-6">
            <div class="bg-blue-50 dark:bg-blue-900/30 border border-blue-100 dark:border-blue-800 rounded-xl p-4 mb-4">
                <p class="text-sm text-blue-800 dark:text-blue-300">
                    <strong>💡 Tips:</strong> Upload foto KTP untuk mengisi data otomatis, atau tutup untuk input manual.
                </p>
            </div>
            <div class="relative border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-6 text-center hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors cursor-pointer" @click="$refs.ktpInput.click()">
                <input type="file" x-ref="ktpInput" accept="image/*" class="hidden" @change="processOCR($event)">
                <div x-show="!ocrPreview">
                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Klik untuk upload foto KTP</p>
                </div>
                <div x-show="ocrPreview" class="relative">
                    <img :src="ocrPreview" class="max-h-40 mx-auto rounded-lg shadow">
                    <div x-show="ocrLoading" class="scanner-line" style="display:block;"></div>
                </div>
            </div>
            <p x-show="ocrStatus" class="mt-3 text-center text-sm font-medium" :class="ocrError ? 'text-red-500' : 'text-green-600'" x-text="ocrStatus"></p>
            <button type="button" @click="showOcr = false" class="mt-3 w-full text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                ✕ Tutup dan input manual
            </button>
        </div>
        
        <!-- Show OCR Button when hidden -->
        <div x-show="currentStep === 1 && !showOcr" class="mb-4">
            <button type="button" @click="showOcr = true" class="w-full py-2 px-4 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-xl text-sm font-medium hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                Scan KTP untuk mengisi otomatis
            </button>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('register') }}" id="register-form" enctype="multipart/form-data" @submit="handleSubmit($event)">
            @csrf

            <!-- STEP 1: Data Pribadi -->
            <div x-show="currentStep === 1" x-transition:enter="slide-enter" class="space-y-5">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                    <span class="w-8 h-8 bg-green-100 dark:bg-green-900/30 text-green-600 rounded-lg flex items-center justify-center text-sm">👤</span>
                    Data Pribadi
                </h3>
                
                <div>
                    <label for="name" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input id="name" x-model="formData.name" class="block mt-1 w-full rounded-xl shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 py-3 px-4" 
                           type="text" name="name" required placeholder="Nama sesuai KTP" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="id_card_number" class="block font-medium text-sm text-gray-700 dark:text-gray-300">NIK KTP</label>
                        <input id="id_card_number" x-model="formData.id_card_number" class="block mt-1 w-full rounded-xl shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 py-3 px-4" 
                               type="text" name="id_card_number" placeholder="16 digit angka" maxlength="16" />
                    </div>
                    <div>
                        <label for="birth_date" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Tanggal Lahir <span class="text-red-500">*</span></label>
                        <input id="birth_date" x-model="formData.birth_date" class="block mt-1 w-full rounded-xl shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 py-3 px-4" 
                               type="date" name="birth_date" required />
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="gender" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Jenis Kelamin <span class="text-red-500">*</span></label>
                        <select id="gender" x-model="formData.gender" name="gender" class="block mt-1 w-full rounded-xl shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 py-3 px-4" required>
                            <option value="">Pilih Jenis Kelamin</option>
                            <option value="male">Laki-laki</option>
                            <option value="female">Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label for="phone" class="block font-medium text-sm text-gray-700 dark:text-gray-300">No. HP</label>
                        <input id="phone" x-model="formData.phone" class="block mt-1 w-full rounded-xl shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 py-3 px-4" 
                               type="tel" name="phone" placeholder="08xxxxxxxxxx" />
                    </div>
                </div>
                
                <div>
                    <label for="address" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Alamat Lengkap <span class="text-red-500">*</span></label>
                    <textarea id="address" x-model="formData.address" name="address" rows="2" class="block mt-1 w-full rounded-xl shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 py-3 px-4" required placeholder="Alamat sesuai KTP"></textarea>
                </div>
            </div>

            <!-- STEP 2: Data Karyawan -->
            <div x-show="currentStep === 2" x-transition:enter="slide-enter" class="space-y-5">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                    <span class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 text-blue-600 rounded-lg flex items-center justify-center text-sm">🏢</span>
                    Data Karyawan
                </h3>
                
                <div>
                    <label for="employee_id" class="block font-medium text-sm text-gray-700 dark:text-gray-300">NIK Karyawan <span class="text-red-500">*</span></label>
                    <input id="employee_id" x-model="formData.employee_id" class="block mt-1 w-full rounded-xl shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 py-3 px-4" 
                           type="text" name="employee_id" required placeholder="Nomor Induk Karyawan" />
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="department" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Departemen <span class="text-red-500">*</span></label>
                        <select id="department" x-model="formData.department" name="department" class="block mt-1 w-full rounded-xl shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 py-3 px-4" required>
                            <option value="">Pilih Departemen</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->name }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="position" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Jabatan <span class="text-red-500">*</span></label>
                        <select id="position" x-model="formData.position" name="position" class="block mt-1 w-full rounded-xl shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 py-3 px-4" required>
                            <option value="">Pilih Jabatan</option>
                            @foreach($positions as $pos)
                                <option value="{{ $pos->name }}">{{ $pos->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div>
                    <label for="photo" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Foto Profil</label>
                    <input id="photo" type="file" name="photo" accept="image/*" class="block mt-1 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 dark:file:bg-green-900/30 dark:file:text-green-400" />
                    <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG. Maks: 2MB (Opsional)</p>
                </div>
            </div>

            <!-- STEP 3: Akun -->
            <div x-show="currentStep === 3" x-transition:enter="slide-enter" class="space-y-5">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                    <span class="w-8 h-8 bg-purple-100 dark:bg-purple-900/30 text-purple-600 rounded-lg flex items-center justify-center text-sm">🔐</span>
                    Buat Akun
                </h3>
                
                <div>
                    <label for="email" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Email <span class="text-red-500">*</span></label>
                    <input id="email" x-model="formData.email" class="block mt-1 w-full rounded-xl shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 py-3 px-4" 
                           type="email" name="email" required placeholder="email@contoh.com" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Password <span class="text-red-500">*</span></label>
                        <input id="password" x-model="formData.password" class="block mt-1 w-full rounded-xl shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 py-3 px-4"
                               type="password" name="password" required autocomplete="new-password" placeholder="Minimal 8 karakter" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>
                    <div>
                        <label for="password_confirmation" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Konfirmasi Password <span class="text-red-500">*</span></label>
                        <input id="password_confirmation" x-model="formData.password_confirmation" class="block mt-1 w-full rounded-xl shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 py-3 px-4"
                               type="password" name="password_confirmation" required placeholder="Ulangi password" />
                    </div>
                </div>
                
                <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-100 dark:border-gray-600">
                    <div class="flex items-start gap-3">
                        <div class="flex items-center h-5">
                            <input id="terms" name="terms" type="checkbox" value="1" x-model="formData.terms" required 
                                   class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500" />
                        </div>
                        <div class="text-sm">
                            <label for="terms" class="font-medium text-gray-700 dark:text-gray-300">
                                Saya menyetujui <a href="#" class="text-green-600 hover:text-green-700 underline">Peraturan Anggota (AD-ART)</a>
                            </label>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Dengan mendaftar, Anda tunduk pada semua ketentuan koperasi.</p>
                            <x-input-error :messages="$errors->get('terms')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Summary Preview -->
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 mt-6">
                    <h4 class="font-bold text-sm text-gray-700 dark:text-gray-300 mb-3">📋 Ringkasan Data Anda</h4>
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <div class="text-gray-500 dark:text-gray-400">Nama:</div>
                        <div class="font-medium text-gray-800 dark:text-white" x-text="formData.name || '-'"></div>
                        <div class="text-gray-500 dark:text-gray-400">NIK Karyawan:</div>
                        <div class="font-medium text-gray-800 dark:text-white" x-text="formData.employee_id || '-'"></div>
                        <div class="text-gray-500 dark:text-gray-400">Departemen:</div>
                        <div class="font-medium text-gray-800 dark:text-white" x-text="formData.department || '-'"></div>
                        <div class="text-gray-500 dark:text-gray-400">Email:</div>
                        <div class="font-medium text-gray-800 dark:text-white" x-text="formData.email || '-'"></div>
                    </div>
                </div>
            </div>

            <!-- Navigation Buttons -->
            <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <div>
                    <a x-show="currentStep === 1" class="text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-300" href="{{ route('login') }}">
                        ← Kembali ke Login
                    </a>
                    <button x-show="currentStep > 1" type="button" @click="prevStep()" class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-medium text-sm hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        Sebelumnya
                    </button>
                </div>
                
                <div>
                    <button x-show="currentStep < 3" type="button" @click="nextStep()" class="inline-flex items-center px-6 py-3 bg-green-500 text-white rounded-xl font-bold text-sm hover:bg-green-600 shadow-lg shadow-green-500/30 hover:shadow-xl transition-all transform hover:-translate-y-0.5">
                        Lanjut
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                    <button x-show="currentStep === 3" type="submit" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl font-bold text-sm hover:from-green-600 hover:to-emerald-700 shadow-lg shadow-green-500/30 hover:shadow-xl transition-all transform hover:-translate-y-0.5"
                            :class="{'opacity-50 cursor-not-allowed': !formData.terms}" :disabled="!formData.terms">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Daftar Sekarang
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        function registerWizard() {
            return {
                darkMode: localStorage.getItem('darkMode') === 'true',
                currentStep: 1,
                showOcr: true,
                ocrPreview: null,
                ocrLoading: false,
                ocrStatus: '',
                ocrError: false,
                formData: {
                    name: '{{ old("name") }}',
                    id_card_number: '{{ old("id_card_number") }}',
                    birth_date: '{{ old("birth_date") }}',
                    gender: '{{ old("gender") }}',
                    phone: '{{ old("phone") }}',
                    address: '{{ old("address") }}',
                    employee_id: '{{ old("employee_id") }}',
                    department: '{{ old("department") }}',
                    position: '{{ old("position") }}',
                    email: '{{ old("email") }}',
                    password: '',
                    password_confirmation: '',
                    terms: false
                },
                
                init() {
                    this.$watch('darkMode', val => localStorage.setItem('darkMode', val));
                },
                
                nextStep() {
                    if (this.validateCurrentStep()) {
                        this.currentStep++;
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                },
                
                prevStep() {
                    this.currentStep--;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                },
                
                validateCurrentStep() {
                    if (this.currentStep === 1) {
                        if (!this.formData.name) { alert('Nama lengkap wajib diisi'); return false; }
                        if (!this.formData.birth_date) { alert('Tanggal lahir wajib diisi'); return false; }
                        if (!this.formData.gender) { alert('Jenis kelamin wajib dipilih'); return false; }
                        if (!this.formData.address) { alert('Alamat wajib diisi'); return false; }
                    }
                    if (this.currentStep === 2) {
                        if (!this.formData.employee_id) { alert('NIK Karyawan wajib diisi'); return false; }
                        if (!this.formData.department) { alert('Departemen wajib dipilih'); return false; }
                        if (!this.formData.position) { alert('Jabatan wajib dipilih'); return false; }
                    }
                    return true;
                },
                
                handleSubmit(e) {
                    if (this.formData.password.length < 8) {
                        e.preventDefault();
                        alert('Password minimal 8 karakter');
                        return false;
                    }
                    if (this.formData.password !== this.formData.password_confirmation) {
                        e.preventDefault();
                        alert('Konfirmasi password tidak cocok');
                        return false;
                    }
                    return true;
                },
                
                async processOCR(event) {
                    const file = event.target.files[0];
                    if (!file) return;
                    
                    // Show preview
                    const reader = new FileReader();
                    reader.onload = (e) => { this.ocrPreview = e.target.result; };
                    reader.readAsDataURL(file);
                    
                    this.ocrLoading = true;
                    this.ocrStatus = 'Memproses gambar...';
                    this.ocrError = false;
                    
                    try {
                        const worker = await Tesseract.createWorker('eng', 1, {
                            logger: m => {
                                if (m.status === 'recognizing text') {
                                    this.ocrStatus = `Membaca teks... ${(m.progress * 100).toFixed(0)}%`;
                                }
                            }
                        });
                        
                        const ret = await worker.recognize(file);
                        this.parseOCR(ret.data.text);
                        await worker.terminate();
                        
                        this.ocrStatus = '✓ Selesai! Data terisi otomatis.';
                        this.ocrLoading = false;
                    } catch (err) {
                        this.ocrStatus = 'Gagal memproses: ' + err.message;
                        this.ocrError = true;
                        this.ocrLoading = false;
                    }
                },
                
                parseOCR(text) {
                    const lines = text.split('\n');
                    
                    lines.forEach(line => {
                        let cl = line.trim().toUpperCase();
                        
                        // NIK (16 digits)
                        if (!this.formData.id_card_number) {
                            let nums = cl.replace(/[^0-9]/g, '');
                            if (nums.length >= 16) {
                                this.formData.id_card_number = nums.substring(0, 16);
                            }
                        }
                        
                        // Name
                        if (!this.formData.name && cl.includes('NAMA')) {
                            let val = cl.replace(/NAMA/i, '').replace(/[:]/g, '').trim();
                            if (val.length > 2) {
                                this.formData.name = this.titleCase(val);
                            }
                        }
                        
                        // Birth Date
                        if (!this.formData.birth_date) {
                            const dm = cl.match(/(\d{2})[-\s\/]+(\d{2})[-\s\/]+(\d{4})/);
                            if (dm && parseInt(dm[1]) <= 31 && parseInt(dm[2]) <= 12) {
                                this.formData.birth_date = `${dm[3]}-${dm[2]}-${dm[1]}`;
                            }
                        }
                        
                        // Gender
                        if (!this.formData.gender) {
                            if (cl.match(/LAKI/i)) this.formData.gender = 'male';
                            else if (cl.match(/PEREM|WANITA/i)) this.formData.gender = 'female';
                        }
                    });
                },
                
                titleCase(str) {
                    return str.toLowerCase().split(' ').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ');
                }
            }
        }
    </script>
</body>
</html>
