<?php

namespace App\Exports;

use App\Models\Saving;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class SavingsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $startDate = $this->request->start_date ? Carbon::parse($this->request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $this->request->end_date ? Carbon::parse($this->request->end_date) : Carbon::now()->endOfDay();

        return Saving::with('member.user')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->when(!auth()->user()->hasAdminAccess(), function($q) {
                $q->where('member_id', auth()->user()->member->id);
            })
            ->latest('transaction_date')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'ID Anggota',
            'Nama Anggota',
            'Jenis Simpanan',
            'Transaksi',
            'Jumlah'
        ];
    }

    public function map($saving): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $saving->transaction_date->format('d/m/Y'),
            $saving->member->member_id,
            $saving->member->user->name,
            $saving->type_label,
            $saving->transaction_type === 'deposit' ? 'Setoran' : 'Penarikan',
            $saving->amount
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '10B981']]],
        ];
    }
}
