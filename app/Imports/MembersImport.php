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
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class MembersImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure, WithChunkReading, WithBatchInserts
{
    use SkipsErrors, SkipsFailures, \App\Traits\DateParserTrait;

    public function model(array $row)
    {
        // Parse role (default to member)
        $role = $this->parseRole($row['role'] ?? 'member');
        $gender = $this->parseGender($row['jenis_kelamin'] ?? null);
        $joinDate = $this->parseDate($row['tanggal_bergabung']) ?? now();
        $birthDate = $this->parseDate($row['tanggal_lahir'] ?? null);

        // 1. Try to find Member by ID
        $member = Member::where('member_id', $row['id_anggota'])->first();

        // 2. Try to find User by Email
        $user = User::where('email', $row['email'])->first();

        DB::beginTransaction();
        try {
            if ($user) {
                // Update existing User
                $userData = [
                    'name' => $row['nama'],
                    // Only update phone if provided
                    'phone' => $row['no_hp'] ?? $row['telepon'] ?? $user->phone, 
                    'role' => $role,
                ];
                // Only update password if provided and not empty
                if (!empty($row['password'])) {
                    $userData['password'] = Hash::make($row['password']);
                }
                $user->update($userData);
            } else {
                // Create new User
                $user = User::create([
                    'name' => $row['nama'],
                    'email' => $row['email'],
                    'phone' => $row['no_hp'] ?? $row['telepon'] ?? null,
                    'password' => Hash::make($row['password'] ?? 'password123'),
                    'role' => $role,
                    'is_active' => true,
                ]);
            }

            // 3. Update or Create Member
            if ($member) {
                // Update existing Member
                $member->update([
                    'user_id' => $user->id, // Ensure linked to correct user (if email changed context)
                    'employee_id' => $row['nik'] ?? $row['nik_karyawan'] ?? $member->employee_id,
                    'department' => $row['department'] ?? $row['divisi'] ?? $member->department,
                    'position' => $row['jabatan'] ?? $row['posisi'] ?? $member->position,
                    'gender' => $gender,
                    'join_date' => $joinDate,
                    'birth_date' => $birthDate,
                    'id_card_number' => $row['no_ktp'] ?? $member->id_card_number,
                    'address' => $row['alamat'] ?? $member->address,
                    'status' => $row['status'] ?? $member->status,
                ]);
            } else {
                // Create new Member
                $member = new Member([
                    'user_id' => $user->id,
                    'member_id' => $row['id_anggota'],
                    'employee_id' => $row['nik'] ?? $row['nik_karyawan'] ?? null,
                    'department' => $row['department'] ?? $row['divisi'] ?? null,
                    'position' => $row['jabatan'] ?? $row['posisi'] ?? 'Staff',
                    'gender' => $gender,
                    'join_date' => $joinDate,
                    'birth_date' => $birthDate,
                    'id_card_number' => $row['no_ktp'] ?? null,
                    'address' => $row['alamat'] ?? null,
                    'status' => $row['status'] ?? 'active',
                ]);
                $member->save();
            }

            DB::commit();
            return $member;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function chunkSize(): int
    {
        return 100; // Process 100 rows at a time
    }

    public function batchSize(): int
    {
        return 100; // Insert 100 rows at a time
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
            'email' => 'required|email', // Removed unique:users,email
            'id_anggota' => 'required',  // Removed unique:members,member_id
            'tanggal_bergabung' => 'required',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nama.required' => 'Kolom nama wajib diisi',
            'email.required' => 'Kolom email wajib diisi',
            'id_anggota.required' => 'Kolom id_anggota wajib diisi',
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
