<?php

namespace App\Exports;

use App\Models\Member;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class MembersExport implements FromCollection, WithHeadings, WithMapping, WithStyles
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

        return Member::with('user')
            ->whereBetween('join_date', [$startDate, $endDate])
            ->latest('join_date')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'ID Anggota',
            'Nama',
            'Email',
            'Department',
            'Gender',
            'Tanggal Bergabung',
            'Status'
        ];
    }

    public function map($member): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $member->member_id,
            $member->user->name,
            $member->user->email,
            $member->department,
            $member->gender === 'male' ? 'Laki-laki' : 'Perempuan',
            $member->join_date->format('d/m/Y'),
            ucfirst($member->status)
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1E40AF']]],
        ];
    }
}
