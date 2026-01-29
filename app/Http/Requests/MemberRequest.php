<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MemberRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasAdminAccess();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('member') ? $this->route('member')->user_id : null;
        
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $userId],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => [$this->isMethod('POST') ? 'required' : 'nullable', 'string', 'min:8', 'confirmed'],
            'employee_id' => ['nullable', 'string', 'max:50'],
            'department' => ['nullable', 'string', 'max:100'],
            'position' => ['nullable', 'string', 'max:100'],
            'join_date' => ['required', 'date'],
            'address' => ['nullable', 'string'],
            'id_card_number' => ['nullable', 'string', 'max:20'],
            'birth_date' => ['nullable', 'date'],
            'gender' => ['nullable', 'in:male,female'],
            'photo' => ['nullable', 'image', 'max:2048'], // Max 2MB
        ];
    }

    /**
     * Get custom attribute names.
     */
    public function attributes(): array
    {
        return [
            'name' => 'nama',
            'email' => 'email',
            'phone' => 'nomor telepon',
            'password' => 'password',
            'employee_id' => 'NIK karyawan',
            'department' => 'departemen',
            'position' => 'jabatan',
            'join_date' => 'tanggal bergabung',
            'address' => 'alamat',
            'id_card_number' => 'nomor KTP',
            'birth_date' => 'tanggal lahir',
            'gender' => 'jenis kelamin',
            'photo' => 'foto',
        ];
    }
}
