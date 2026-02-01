@extends('layouts.app')

@section('title', __('messages.titles.member_add'))

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="flex items-center gap-4">
            <a href="{{ route('members.index') }}" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                <svg class="w-6 h-6 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h1 class="page-title">Tambah Anggota Baru</h1>
                <p class="page-subtitle">Daftarkan anggota baru koperasi</p>
            </div>
        </div>
    </div>

    <form action="{{ route('members.store') }}" method="POST" enctype="multipart/form-data" class="max-w-4xl">
        @csrf

        <div class="glass-card-solid p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 pb-3 border-b border-gray-200 dark:border-gray-700">Informasi Akun</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6">
                <!-- Nama -->
                <div class="form-group">
                    <label for="name" class="form-label">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                           class="form-input @error('name') !border-red-500 @enderror"
                           placeholder="Masukkan nama lengkap">
                    @error('name')
                    <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="email" class="form-label">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                           class="form-input @error('email') !border-red-500 @enderror"
                           placeholder="nama@gmail.com">
                    @error('email')
                    <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div class="form-group">
                    <label for="phone" class="form-label">Nomor Telepon</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
                           placeholder="08xxxxxxxxxx" autocomplete="off"
                           class="form-input @error('phone') !border-red-500 @enderror">
                    @error('phone')
                    <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password" class="form-label">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <input type="password" id="password" name="password" required autocomplete="new-password"
                           class="form-input @error('password') !border-red-500 @enderror"
                           placeholder="Minimal 8 karakter">
                    @error('password')
                    <p class="form-error">{{ $message }}</p>
                    @else
                    <p class="form-hint">Minimal 8 karakter</p>
                    @enderror
                </div>

                <!-- Password Confirmation -->
                <div class="form-group md:col-span-2">
                    <label for="password_confirmation" class="form-label">
                        Konfirmasi Password <span class="text-red-500">*</span>
                    </label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                           class="form-input"
                           placeholder="Ulangi password">
                </div>
            </div>
        </div>

        <div class="glass-card-solid p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 pb-3 border-b border-gray-200 dark:border-gray-700">Informasi Karyawan</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6">
                <!-- Employee ID -->
                <div class="form-group">
                    <label for="employee_id" class="form-label">NIK Karyawan</label>
                    <input type="text" id="employee_id" name="employee_id" value="{{ old('employee_id') }}"
                           placeholder="Contoh: KA001"
                           class="form-input @error('employee_id') !border-red-500 @enderror">
                    @error('employee_id')
                    <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Department -->
                <div class="form-group">
                    <label for="department" class="form-label">Departemen</label>
                    <select id="department" name="department" class="form-input @error('department') !border-red-500 @enderror">
                        <option value="">Pilih Departemen</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->name }}" {{ old('department') == $dept->name ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('department')
                    <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Position -->
                <div class="form-group">
                    <label for="position" class="form-label">Jabatan</label>
                    <select id="position" name="position" class="form-input @error('position') !border-red-500 @enderror">
                        <option value="">Pilih Jabatan</option>
                        @foreach($positions as $pos)
                            <option value="{{ $pos->name }}" {{ old('position') == $pos->name ? 'selected' : '' }}>
                                {{ $pos->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('position')
                    <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Join Date -->
                <div class="form-group">
                    <label for="join_date" class="form-label">
                        Tanggal Bergabung <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="join_date" name="join_date" value="{{ old('join_date', date('Y-m-d')) }}" required
                           class="form-input @error('join_date') !border-red-500 @enderror">
                    @error('join_date')
                    <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="glass-card-solid p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 pb-3 border-b border-gray-200 dark:border-gray-700">Informasi Pribadi</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6">
                <!-- ID Card Number -->
                <div class="form-group">
                    <label for="id_card_number" class="form-label">Nomor KTP</label>
                    <input type="text" id="id_card_number" name="id_card_number" value="{{ old('id_card_number') }}"
                           placeholder="16 digit nomor KTP" maxlength="16"
                           class="form-input @error('id_card_number') !border-red-500 @enderror">
                    @error('id_card_number')
                    <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Birth Date -->
                <div class="form-group">
                    <label for="birth_date" class="form-label">Tanggal Lahir</label>
                    <input type="date" id="birth_date" name="birth_date" value="{{ old('birth_date') }}"
                           class="form-input @error('birth_date') !border-red-500 @enderror">
                    @error('birth_date')
                    <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Gender -->
                <div class="form-group">
                    <label for="gender" class="form-label">Jenis Kelamin</label>
                    <select id="gender" name="gender" class="form-input @error('gender') !border-red-500 @enderror">
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                    @error('gender')
                    <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Photo -->
                <div class="form-group">
                    <label for="photo" class="form-label">Foto Profil</label>
                    <input type="file" id="photo" name="photo" accept="image/*"
                           class="form-input @error('photo') !border-red-500 @enderror">
                    @error('photo')
                    <p class="form-error">{{ $message }}</p>
                    @else
                    <p class="form-hint">Maksimal 2MB (JPG, PNG)</p>
                    @enderror
                </div>

                <!-- Address -->
                <div class="form-group md:col-span-2">
                    <label for="address" class="form-label">Alamat Lengkap</label>
                    <textarea id="address" name="address" rows="3"
                              placeholder="Masukkan alamat lengkap"
                              class="form-input @error('address') !border-red-500 @enderror">{{ old('address') }}</textarea>
                    @error('address')
                    <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('members.index') }}" class="btn-secondary">
                Batal
            </a>
            <button type="submit" class="btn-primary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Simpan Anggota
            </button>
        </div>
    </form>
@endsection
