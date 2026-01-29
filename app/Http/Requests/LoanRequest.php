<?php

namespace App\Http\Requests;

use App\Models\Member;
use Illuminate\Foundation\Http\FormRequest;

class LoanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'member_id' => ['required', 'exists:members,id'],
            'loan_type' => ['required', 'in:regular,emergency,education,special'],
            'amount' => [
                'required', 
                'numeric', 
                'min:100000',
                // Custom validation untuk limit pinjaman berdasarkan gaji atau simpanan bisa ditambahkan nanti
            ],
            'duration_months' => ['required', 'integer', 'min:1', 'max:60'],
            'purpose' => ['required', 'string', 'max:500'],
            'interest_rate' => ['required', 'numeric', 'min:0', 'max:100'], // % per month or year
            'application_date' => ['required', 'date'],
        ];
    }
    
    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Cek apakah member sedang ada pinjaman aktif (opsional, tergantung rule koperasi)
            // Rule: Maksimal 1 pinjaman active + pending per member
            if ($this->isMethod('post')) {
                $member = Member::find($this->member_id);
                if ($member) {
                    $hasActiveLoan = $member->loans()
                        ->whereIn('status', ['active', 'pending', 'approved'])
                        ->exists();
                        
                    if ($hasActiveLoan) {
                        // Uncomment jika ingin enforce rule ini
                        // $validator->errors()->add('member_id', 'Anggota ini masih memiliki pinjaman yang berjalan atau menunggu persetujuan.');
                    }
                }
            }
        });
    }

    /**
     * Get custom attribute names.
     */
    public function attributes(): array
    {
        return [
            'member_id' => 'anggota',
            'loan_type' => 'jenis pinjaman',
            'amount' => 'jumlah pengajuan',
            'duration_months' => 'jangka waktu (bulan)',
            'interest_rate' => 'suku bunga',
            'purpose' => 'keperluan',
            'application_date' => 'tanggal pengajuan',
        ];
    }
}
