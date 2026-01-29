<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LoansTemplateExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize
{
    public function array(): array
    {
        return [
            ['MBR0001', 'LN20240001', 'regular', 10000000, 12, 1.5, '2024-12-01', 'Renovasi rumah', 'approved', '2024-12-05'],
            ['MBR0002', 'LN20240002', 'emergency', 5000000, 6, 2.0, '2024-12-10', 'Biaya pengobatan', 'pending', null],
            ['MBR0003', 'LN20240003', 'education', 15000000, 24, 1.5, '2024-12-15', 'Biaya kuliah anak', 'approved', '2024-12-18'],
        ];
    }

    public function headings(): array
    {
        return [
            'id_anggota',
            'no_pinjaman',
            'jenis',
            'jumlah',
            'tenor',
            'bunga',
            'tanggal_pengajuan',
            'tujuan',
            'status',
            'tanggal_persetujuan'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Define Mandatory Columns (Red)
        // A=id_anggota, C=jenis, D=jumlah, E=tenor, F=bunga, G=tanggal_pengajuan
        $mandatoryColumns = ['A', 'C', 'D', 'E', 'F', 'G'];
        
        // All columns A-J
        $allColumns = range('A', 'J');
        
        $styles = [];

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
