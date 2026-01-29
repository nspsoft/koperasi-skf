<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Member;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class MembersImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
{
    use SkipsErrors, SkipsFailures, \App\Traits\DateParserTrait;

    public function model(array $row)
    {
        // Parse role (default to member)
        $role = $this->parseRole($row['role'] ?? 'member');

        // Create user first
        $user = User::create([
            'name' => $row['nama'],
            'email' => $row['email'],
            'phone' => $row['no_hp'] ?? $row['telepon'] ?? null,
            'password' => Hash::make($row['password'] ?? 'password123'),
            'role' => $role,
            'is_active' => true,
        ]);

        // Create member
        return new Member([
            'user_id' => $user->id,
            'member_id' => $row['id_anggota'],
            'employee_id' => $row['nik'] ?? $row['nik_karyawan'] ?? null,
            'department' => $row['department'] ?? $row['divisi'] ?? null,
            'position' => $row['jabatan'] ?? $row['posisi'] ?? 'Staff',
            'gender' => $this->parseGender($row['jenis_kelamin'] ?? null),
            'join_date' => $this->parseDate($row['tanggal_bergabung']) ?? now(),
            'birth_date' => $this->parseDate($row['tanggal_lahir'] ?? null),
            'id_card_number' => $row['no_ktp'] ?? null,
            'address' => $row['alamat'] ?? null,
            'status' => $row['status'] ?? 'active',
        ]);
    }

    protected function parseGender(?string $gender): string
    {
        if (!$gender) return 'male';
        
        $gender = strtolower(trim($gender));
        
        if (in_array($gender, ['perempuan', 'wanita', 'female', 'f', 'p'])) {
            return 'female';
        }
        
        return 'male';
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'id_anggota' => 'required|unique:members,member_id',
            'tanggal_bergabung' => 'required',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nama.required' => 'Kolom nama wajib diisi',
            'email.required' => 'Kolom email wajib diisi',
            'email.unique' => 'Email sudah terdaftar di sistem',
            'id_anggota.required' => 'Kolom id_anggota wajib diisi',
            'id_anggota.unique' => 'ID Anggota sudah terdaftar',
            'tanggal_bergabung.required' => 'Kolom tanggal_bergabung wajib diisi',
        ];
    }

    protected function parseRole(?string $role): string
    {
        if (!$role) return 'member';
        
        $role = strtolower(trim($role));
        
        if (in_array($role, ['admin', 'administrator'])) {
            return 'admin';
        }
        
        if (in_array($role, ['manager', 'manajer', 'supervisor'])) {
            return 'manager';
        }
        
        return 'member';
    }
}
