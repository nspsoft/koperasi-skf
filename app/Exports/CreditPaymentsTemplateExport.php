<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class CreditPaymentsTemplateExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function collection()
    {
        // Get unpaid credit transactions as template data
        return Transaction::where('payment_method', 'kredit')
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->with(['user.member'])
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get();
    }

    public function headings(): array
    {
        return [
            'no_invoice',
            'tanggal_transaksi',
            'id_anggota',
            'nama_anggota',
            'jumlah',
            'keterangan',
        ];
    }

    public function map($transaction): array
    {
        return [
            $transaction->invoice_number,
            $transaction->created_at->format('d/m/Y'),
            $transaction->user->member->member_id ?? '',
            $transaction->user->name ?? 'Guest',
            $transaction->total_amount,
            'Pelunasan via payroll', // Default note
        ];
    }
}
