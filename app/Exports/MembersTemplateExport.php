<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MembersTemplateExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize
{
    public function array(): array
    {
        return [
            [
                'Budi Santoso',
                'budi.santoso@example.com',
                '081234567890',
                'member',
                'MBR0001',
                'EMP0001',
                'Production',
                'Staff',
                'Laki-laki',
                '2024-01-15',
                '1990-05-20',
                '3215012005900001',
                'Jl. Contoh No. 123, Karawang',
                'password'
            ],
            [
                'Siti Rahayu',
                'siti.rahayu@example.com',
                '081234567891',
                'manager',
                'MBR0002',
                'EMP0002',
                'QC',
                'Supervisor',
                'Perempuan',
                '2024-02-10',
                '1992-08-15',
                '3215011508920002',
                'Jl. Merdeka No. 45, Karawang',
                'password'
            ],
            [
                'Ahmad Wijaya',
                'ahmad.wijaya@example.com',
                '081234567892',
                'member',
                'MBR0003',
                'EMP0003',
                'Finance',
                'Staff',
                'Laki-laki',
                '2024-03-05',
                '1988-12-30',
                '3215013012880003',
                'Jl. Sudirman No. 78, Karawang',
                'password'
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'nama',
            'email',
            'no_hp',
            'role',
            'id_anggota',
            'nik',
            'department',
            'jabatan',
            'jenis_kelamin',
            'tanggal_bergabung',
            'tanggal_lahir',
            'no_ktp',
            'alamat',
            'password'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Define Mandatory Columns (Red)
        // A=nama, B=email, E=id_anggota, J=tanggal_bergabung
        $mandatoryColumns = ['A', 'B', 'E', 'J'];
        
        // All columns from A to N
        $allColumns = range('A', 'N');
        
        $styles = [];

        // Apply styles per cell
        foreach ($allColumns as $col) {
            $isMandatory = in_array($col, $mandatoryColumns);
            
            $color = $isMandatory ? 'EF4444' : '1E40AF'; // Red : Blue
            
            $styles[$col . '1'] = [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $color]
                ]
            ];
        }

        return $styles;
    }
}
