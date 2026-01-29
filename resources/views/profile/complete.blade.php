@extends('layouts.app')

@section('title', 'Lengkapi Profil')

@section('content')
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="w-20 h-20 bg-gradient-to-br from-amber-400 to-orange-500 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Lengkapi Profil Anda</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2">Silakan lengkapi data berikut untuk melanjutkan menggunakan sistem</p>
        </div>

        <!-- Missing Fields Alert -->
        @if(count($missingFields) > 0)
        <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-xl p-4 mb-6">
            <div class="flex items-start gap-3">
                <svg class="w-6 h-6 text-amber-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <div>
                    <h4 class="font-semibold text-amber-800 dark:text-amber-300">Data yang belum lengkap:</h4>
                    <ul class="list-disc list-inside text-sm text-amber-700 dark:text-amber-400 mt-1">
                        @foreach($missingFields as $field => $label)
                            <li>{{ $label }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif

        <!-- Form -->
        <div class="glass-card-solid p-6">
            <form action="{{ route('profile.complete.update') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Photo Upload -->
                <div class="mb-6">
                    <label class="form-label">Foto Profil <span class="text-red-500">*</span></label>
                    <div class="flex items-center gap-4">
                        <div class="flex-shrink-0">
                            @if($member->photo && Storage::disk('public')->exists($member->photo))
                                <img src="{{ Storage::url($member->photo) }}" class="w-24 h-24 rounded-full object-cover border-4 border-gray-200 dark:border-gray-700">
                            @else
                                <div class="w-24 h-24 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <input type="file" name="photo" accept="image/jpeg,image/png,image/jpg" class="form-input">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Format: JPG, JPEG, PNG. Maks: 2MB</p>
                        </div>
                    </div>
                    @error('photo')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- NIK Karyawan -->
                    <div>
                        <label class="form-label">NIK Karyawan <span class="text-red-500">*</span></label>
                        <input type="text" name="employee_id" value="{{ old('employee_id', $member->employee_id) }}" 
                               class="form-input @error('employee_id') border-red-500 @enderror" 
                               placeholder="Contoh: 12345678">
                        @error('employee_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Department -->
                    <div>
                        <label class="form-label">Departemen <span class="text-red-500">*</span></label>
                        <select name="department" class="form-input @error('department') border-red-500 @enderror">
                            <option value="">Pilih Departemen</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->name }}" {{ old('department', $member->department) == $dept->name ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('department')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Position -->
                    <div>
                        <label class="form-label">Jabatan <span class="text-red-500">*</span></label>
                         <select name="position" class="form-input @error('position') border-red-500 @enderror">
                             <option value="">Pilih Jabatan</option>
                             @foreach($positions as $pos)
                                <option value="{{ $pos->name }}" {{ old('position', $member->position) == $pos->name ? 'selected' : '' }}>
                                    {{ $pos->name }}
                                </option>
                             @endforeach
                        </select>
                        @error('position')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Gender -->
                    <div>
                        <label class="form-label">Jenis Kelamin <span class="text-red-500">*</span></label>
                        <select name="gender" class="form-input @error('gender') border-red-500 @enderror">
                            <option value="">Pilih Jenis Kelamin</option>
                            <option value="male" {{ old('gender', $member->gender) === 'male' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="female" {{ old('gender', $member->gender) === 'female' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('gender')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Birth Date -->
                    <div>
                        <label class="form-label">Tanggal Lahir <span class="text-red-500">*</span></label>
                        <input type="date" name="birth_date" 
                               value="{{ old('birth_date', $member->birth_date ? $member->birth_date->format('Y-m-d') : '') }}" 
                               class="form-input @error('birth_date') border-red-500 @enderror">
                        @error('birth_date')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- ID Card Number -->
                    <div>
                        <label class="form-label">Nomor KTP <span class="text-red-500">*</span></label>
                        <input type="text" name="id_card_number" value="{{ old('id_card_number', $member->id_card_number) }}" 
                               class="form-input @error('id_card_number') border-red-500 @enderror" 
                               placeholder="16 digit nomor KTP"
                               maxlength="16">
                        @error('id_card_number')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Address -->
                <div class="mt-4">
                    <label class="form-label">Alamat Lengkap <span class="text-red-500">*</span></label>
                    <textarea name="address" rows="3" 
                              class="form-input @error('address') border-red-500 @enderror" 
                              placeholder="Masukkan alamat lengkap sesuai KTP">{{ old('address', $member->address) }}</textarea>
                    @error('address')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Terms and Conditions -->
                <div class="mt-6 p-4 bg-primary-50 dark:bg-primary-900/10 rounded-xl border border-primary-100 dark:border-primary-800/50">
                    <div class="flex items-start gap-3">
                        <div class="flex items-center h-5">
                            <input id="terms" name="terms" type="checkbox" value="1"
                                   class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500 @error('terms') border-red-500 @enderror"
                                   {{ old('terms') ? 'checked' : '' }}>
                        </div>
                        <div class="text-sm">
                            <label for="terms" class="font-medium text-gray-900 dark:text-white cursor-pointer">
                                Saya menyatakan setuju dan tunduk terhadap <a href="{{ route('ad-art') }}" target="_blank" class="text-primary-600 hover:text-primary-700 underline underline-offset-4">Peraturan ANGGOTA KOPERASI (AD-ART)</a>
                            </label>
                            <p class="text-gray-500 dark:text-gray-400 mt-1">Dengan mencentang kotak ini, Anda mengakui telah membaca dan menyetujui seluruh ketentuan yang berlaku.</p>
                            @error('terms')
                                <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="mt-6 flex justify-end">
                    <button type="submit" class="btn-primary px-8">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Simpan & Lanjutkan
                    </button>
                </div>
            </form>
        </div>

        <!-- Info -->
        <p class="text-center text-sm text-gray-500 dark:text-gray-400 mt-6">
            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Semua data wajib diisi untuk dapat mengakses fitur-fitur aplikasi
        </p>
    </div>
@endsection
