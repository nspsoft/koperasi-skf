<?php

namespace App\Http\Requests;

use App\Models\Member;
use Illuminate\Foundation\Http\FormRequest;

class SavingRequest extends FormRequest
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
        return [
            'member_id' => ['required', 'exists:members,id'],
            'type' => ['required', 'in:pokok,wajib,sukarela'],
            'transaction_type' => ['required', 'in:deposit,withdrawal'],
            'amount' => ['required', 'numeric', 'min:1000'],
            'transaction_date' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:255'],
        ];
    }
    
    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Logic validasi saldo jika penarikan
            if ($this->transaction_type === 'withdrawal') {
                $member = Member::find($this->member_id);
                if ($member) {
                    // Hitung total saldo per tipe simpanan
                    $currentBalance = $member->savings()
                        ->where('type', $this->type)
                        ->selectRaw('SUM(CASE WHEN transaction_type = "deposit" THEN amount ELSE -amount END) as balance')
                        ->value('balance') ?? 0;
                    
                    if ($this->amount > $currentBalance) {
                        $validator->errors()->add('amount', 'Saldo ' . $this->type . ' tidak mencukupi. Saldo saat ini: Rp ' . number_format($currentBalance, 0, ',', '.'));
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
            'type' => 'jenis simpanan',
            'transaction_type' => 'jenis transaksi',
            'amount' => 'jumlah',
            'transaction_date' => 'tanggal transaksi',
            'description' => 'keterangan',
        ];
    }
}
