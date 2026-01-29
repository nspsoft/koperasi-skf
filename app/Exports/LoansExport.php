<?php

namespace App\Exports;

use App\Models\Loan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class LoansExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $startDate = $this->request->start_date ? Carbon::parse($this->request->start_date) : Carbon::now()->startOfYear();
        $endDate = $this->request->end_date ? Carbon::parse($this->request->end_date) : Carbon::now()->endOfDay();

        return Loan::with('member.user')
            ->whereBetween('application_date', [$startDate, $endDate])
            ->when(!auth()->user()->hasAdminAccess(), function($q) {
                $q->where('member_id', auth()->user()->member->id);
            })
            ->latest('application_date')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'No Pinjaman',
            'ID Anggota',
            'Nama Anggota',
            'Jenis Pinjaman',
            'Jumlah',
            'Tenor (Bulan)',
            'Bunga (%)',
            'Status',
            'Tgl Pengajuan',
            'Tgl Persetujuan'
        ];
    }

    public function map($loan): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $loan->loan_number,
            $loan->member->member_id,
            $loan->member->user->name,
            $loan->loan_type_label,
            $loan->amount,
            $loan->duration_months,
            $loan->interest_rate,
            $loan->status_label,
            $loan->application_date->format('d/m/Y'),
            $loan->approved_at ? $loan->approved_at->format('d/m/Y') : '-'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'F59E0B']]],
        ];
    }
}
