<?php

namespace App\Http\Requests;

use App\Models\Loan;
use Illuminate\Foundation\Http\FormRequest;

class LoanPaymentRequest extends FormRequest
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
            'loan_id' => ['required', 'exists:loans,id'],
            'amount' => ['required', 'numeric', 'min:1'],
            'payment_date' => ['required', 'date'],
            'payment_method' => ['required', 'in:cash,transfer,salary_deduction'],
            'notes' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->loan_id) {
                $loan = Loan::find($this->loan_id);
                if ($loan) {
                    if ($this->amount > $loan->remaining_amount) {
                        $validator->errors()->add('amount', 'Jumlah pembayaran melebihi sisa pinjaman (Rp ' . number_format($loan->remaining_amount, 0, ',', '.') . ').');
                    }
                    if ($loan->status !== 'active') {
                        $validator->errors()->add('loan_id', 'Hanya pinjaman aktif yang dapat menerima pembayaran.');
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
            'loan_id' => 'pinjaman',
            'amount' => 'jumlah pembayaran',
            'payment_date' => 'tanggal pembayaran',
            'payment_method' => 'metode pembayaran',
            'notes' => 'catatan',
        ];
    }
}
