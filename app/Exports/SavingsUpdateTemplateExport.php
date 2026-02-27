<?php

namespace App\Exports;

use App\Models\Saving;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SavingsUpdateTemplateExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithMapping
{
    protected ?string $startDate;
    protected ?string $endDate;

    public function __construct(?string $startDate = null, ?string $endDate = null)
    {
        $this->startDate = $startDate ?: null;
        $this->endDate = $endDate ?: null;
    }

    public function collection()
    {
        return Saving::with('member')
            ->when($this->startDate, fn ($q) => $q->whereDate('transaction_date', '>=', $this->startDate))
            ->when($this->endDate, fn ($q) => $q->whereDate('transaction_date', '<=', $this->endDate))
            ->orderBy('transaction_date')
            ->orderBy('id')
            ->get();
    }

    public function headings(): array
    {
        return [
            'id_transaksi',
            'id_anggota',
            'jenis',
            'transaksi',
            'jumlah',
            'tanggal',
            'no_referensi',
            'keterangan',
        ];
    }

    public function map($saving): array
    {
        return [
            $saving->id,
            $saving->member->member_id ?? '',
            $saving->type,
            $saving->transaction_type === 'withdrawal' ? 'penarikan' : 'setoran',
            $saving->amount,
            $saving->transaction_date ? $saving->transaction_date->format('Y-m-d') : '',
            $saving->reference_number,
            $saving->description,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1')->getFont()->getColor()->setARGB('FFFF0000');
        $sheet->getStyle('A1:H1')->getFont()->setBold(true);
        $sheet->getStyle('A1:H1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFE0E0E0');

        return [];
    }
}
