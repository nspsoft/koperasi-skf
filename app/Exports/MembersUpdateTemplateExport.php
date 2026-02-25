<?php

namespace App\Exports;

use App\Models\Member;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MembersUpdateTemplateExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithMapping
{
    public function collection()
    {
        return Member::with('user')->orderBy('member_id')->get();
    }

    public function headings(): array
    {
        return [
            'nama',
            'email',
            'no_hp',
            'role',
            'id_anggota',
            'id_amigo',
            'nik',
            'department',
            'jabatan',
            'jenis_kelamin',
            'tanggal_bergabung',
            'tanggal_lahir',
            'no_ktp',
            'alamat',
            'password', // Optional for update
            'status'
        ];
    }

    public function map($member): array
    {
        return [
            $member->user->name ?? '',
            $member->user->email ?? '',
            $member->user->phone ?? '',
            $member->user->role ?? 'member',
            $member->member_id,
            $member->id_amigo,
            $member->employee_id,
            $member->department,
            $member->position,
            $member->gender === 'male' ? 'Laki-laki' : ($member->gender === 'female' ? 'Perempuan' : ''),
            $member->join_date ? $member->join_date->format('Y-m-d') : '',
            $member->birth_date ? $member->birth_date->format('Y-m-d') : '',
            $member->id_card_number,
            $member->address,
            '', // Password left blank intentionally
            $member->status
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Define Key Columns (Red) - ID Anggota is key for update
        $sheet->getStyle('E1')->getFont()->getColor()->setARGB('FFFF0000');
        
        // Header Style
        $sheet->getStyle('A1:P1')->getFont()->setBold(true);
        $sheet->getStyle('A1:P1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFE0E0E0');

        return [];
    }
}
