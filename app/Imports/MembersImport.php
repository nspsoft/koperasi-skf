<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Member;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class MembersImport implements OnEachRow, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure, WithChunkReading
{
    use SkipsErrors, SkipsFailures, \App\Traits\DateParserTrait;

    public function onRow(Row $row)
    {
        $rowArray = $row->toArray();
        
        // Parse role (default to member)
        $role = $this->parseRole($rowArray['role'] ?? 'member');
        $gender = $this->parseGender($rowArray['jenis_kelamin'] ?? null);
        $joinDate = $this->parseDate($rowArray['tanggal_bergabung']) ?? now();
        $birthDate = $this->parseDate($rowArray['tanggal_lahir'] ?? null);

        // 1. Try to find Member by ID
        $member = Member::where('member_id', $rowArray['id_anggota'])->first();

        // 2. Try to find User by Email
        $user = User::where('email', $rowArray['email'])->first();

        DB::beginTransaction();
        try {
            if ($user) {
                // Update existing User
                $userData = [
                    'name' => $rowArray['nama'],
                    // Only update phone if provided
                    'phone' => $rowArray['no_hp'] ?? $rowArray['telepon'] ?? $user->phone, 
                    'role' => $role,
                ];
                // Only update password if provided and not empty
                if (!empty($rowArray['password'])) {
                    $userData['password'] = Hash::make($rowArray['password']);
                }
                $user->update($userData);
            } else {
                // Create new User
                $user = User::create([
                    'name' => $rowArray['nama'],
                    'email' => $rowArray['email'],
                    'phone' => $rowArray['no_hp'] ?? $rowArray['telepon'] ?? null,
                    'password' => Hash::make($rowArray['password'] ?? 'password123'),
                    'role' => $role,
                    'is_active' => true,
                ]);
            }

            // 3. Update or Create Member
            if ($member) {
                // Update existing Member
                $member->update([
                    'user_id' => $user->id, // Ensure linked to correct user (if email changed context)
                    'employee_id' => $rowArray['nik'] ?? $rowArray['nik_karyawan'] ?? $member->employee_id,
                    'department' => $rowArray['department'] ?? $rowArray['divisi'] ?? $member->department,
                    'position' => $rowArray['jabatan'] ?? $rowArray['posisi'] ?? $member->position,
                    'gender' => $gender,
                    'join_date' => $joinDate,
                    'birth_date' => $birthDate,
                    'id_card_number' => $rowArray['no_ktp'] ?? $member->id_card_number,
                    'address' => $rowArray['alamat'] ?? $member->address,
                    'status' => $rowArray['status'] ?? $member->status,
                ]);
            } else {
                // Create new Member
                $member = new Member([
                    'user_id' => $user->id,
                    'member_id' => $rowArray['id_anggota'],
                    'employee_id' => $rowArray['nik'] ?? $rowArray['nik_karyawan'] ?? null,
                    'department' => $rowArray['department'] ?? $rowArray['divisi'] ?? null,
                    'position' => $rowArray['jabatan'] ?? $rowArray['posisi'] ?? 'Staff',
                    'gender' => $gender,
                    'join_date' => $joinDate,
                    'birth_date' => $birthDate,
                    'id_card_number' => $rowArray['no_ktp'] ?? null,
                    'address' => $rowArray['alamat'] ?? null,
                    'status' => $rowArray['status'] ?? 'active',
                ]);
                $member->save();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function chunkSize(): int
    {
        return 100; // Process 100 rows at a time
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
