@extends('layouts.app')

@section('title', __('messages.titles.member_edit'))

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="flex items-center gap-4">
            <a href="{{ route('members.show', $member) }}" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                <svg class="w-6 h-6 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h1 class="page-title">Edit Anggota</h1>
                <p class="page-subtitle">{{ $member->user->name }} - {{ $member->member_id }}</p>
            </div>
        </div>
    </div>

    <form action="{{ route('members.update', $member) }}" method="POST" enctype="multipart/form-data" class="max-w-4xl">
        @csrf
        @method('PUT')

        <div class="glass-card-solid p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Informasi Akun</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nama -->
                <div>
                    <label for="name" class="form-label">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name', $member->user->name) }}" required
                           class="form-input @error('name') border-red-500 @enderror">
                    @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="form-label">Email <span class="text-red-500">*</span></label>
                    <input type="email" id="email" name="email" value="{{ old('email', $member->user->email) }}" required
                           class="form-input @error('email') border-red-500 @enderror">
                    @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="form-label">Nomor Telepon</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', $member->user->phone) }}"
                           placeholder="08xxxxxxxxxx" autocomplete="off"
                           class="form-input @error('phone') border-red-500 @enderror">
                    @error('phone')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="form-label">Password (Kosongkan jika tidak diubah)</label>
                    <input type="password" id="password" name="password" autocomplete="new-password"
                           class="form-input @error('password') border-red-500 @enderror">
                    @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Minimal 8 karakter</p>
                </div>

                <!-- Password Confirmation -->
                <div class="md:col-span-2">
                    <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                           class="form-input">
                </div>
            </div>
        </div>

        <div class="glass-card-solid p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Informasi Karyawan</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Employee ID -->
                <div class="form-group">
                    <label for="employee_id" class="form-label">NIK Karyawan</label>
                    <input type="text" id="employee_id" name="employee_id" value="{{ old('employee_id', $member->employee_id) }}"
                           placeholder="Contoh: EMP001"
                           class="form-input @error('employee_id') !border-red-500 @enderror">
                    @error('employee_id')
                    <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Department -->
                <div class="form-group">
                    <label for="department" class="form-label">Departemen</label>
                    <input type="text" id="department" name="department" value="{{ old('department', $member->department) }}"
                           placeholder="Contoh: IT Department"
                           class="form-input @error('department') !border-red-500 @enderror">
                    @error('department')
                    <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Position -->
                <div class="form-group">
                    <label for="position" class="form-label">Jabatan</label>
                    <input type="text" id="position" name="position" value="{{ old('position', $member->position) }}"
                           placeholder="Contoh: Staff"
                           class="form-input @error('position') !border-red-500 @enderror">
                    @error('position')
                    <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Join Date -->
                <div>
                    <label for="join_date" class="form-label">Tanggal Bergabung <span class="text-red-500">*</span></label>
                    <input type="date" id="join_date" name="join_date" value="{{ old('join_date', $member->join_date?->format('Y-m-d')) }}" required
                           class="form-input @error('join_date') !border-red-500 @enderror">
                    @error('join_date')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Credit Limit -->
                <div>
                    <label for="credit_limit" class="form-label">Limit Kredit (Mart)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                        <input type="number" id="credit_limit" name="credit_limit" 
                               value="{{ old('credit_limit', $member->credit_limit ?? 500000) }}"
                               min="0" step="50000"
                               placeholder="500000"
                               class="form-input pl-10 @error('credit_limit') !border-red-500 @enderror">
                    </div>
                    @error('credit_limit')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Batas maksimal kredit belanja di Koperasi Mart</p>
                </div>
            </div>
        </div>

        <div class="glass-card-solid p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Informasi Pribadi</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- ID Card Number -->
                <div class="form-group">
                    <label for="id_card_number" class="form-label">Nomor KTP</label>
                    <input type="text" id="id_card_number" name="id_card_number" value="{{ old('id_card_number', $member->id_card_number) }}"
                           placeholder="16 digit nomor KTP" maxlength="16"
                           class="form-input @error('id_card_number') !border-red-500 @enderror">
                    @error('id_card_number')
                    <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Birth Date -->
                <div>
                    <label for="birth_date" class="form-label">Tanggal Lahir</label>
                    <input type="date" id="birth_date" name="birth_date" value="{{ old('birth_date', $member->birth_date?->format('Y-m-d')) }}"
                           class="form-input @error('birth_date') !border-red-500 @enderror">
                    @error('birth_date')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Gender -->
                <div>
                    <label for="gender" class="form-label">Jenis Kelamin</label>
                    <select id="gender" name="gender" class="form-input @error('gender') !border-red-500 @enderror">
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="male" {{ old('gender', $member->gender) == 'male' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="female" {{ old('gender', $member->gender) == 'female' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                    @error('gender')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Photo -->
                <div>
                    <label for="photo" class="form-label">Foto Profil</label>
                    <input type="file" id="photo" name="photo" accept="image/*"
                           class="form-input @error('photo') border-red-500 @enderror">
                    @error('photo')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Max 2MB (JPG, PNG)</p>
                </div>

                <!-- Address -->
                <div class="md:col-span-2">
                    <label for="address" class="form-label">Alamat Lengkap</label>
                    <textarea id="address" name="address" rows="3"
                              placeholder="Masukkan alamat lengkap"
                              class="form-input @error('address') !border-red-500 @enderror">{{ old('address', $member->address) }}</textarea>
                    @error('address')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
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
