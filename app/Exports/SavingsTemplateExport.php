<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SavingsTemplateExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize
{
    public function array(): array
    {
        return [
            ['MBR0001', 'pokok', 'setoran', 100000, '2024-12-01', 'SAV001', 'Setoran pokok wajib'],
            ['MBR0001', 'wajib', 'setoran', 50000, '2024-12-05', 'SAV002', 'Setoran rutin bulanan'],
            ['MBR0002', 'sukarela', 'setoran', 200000, '2024-12-10', 'SAV003', 'Tabungan sukarela'],
            ['MBR0001', 'wajib', 'penarikan', 25000, '2024-12-15', 'SAV004', 'Penarikan sebagian'],
        ];
    }

    public function headings(): array
    {
        return [
            'id_anggota',
            'jenis',
            'transaksi',
            'jumlah',
            'tanggal',
            'no_referensi',
            'keterangan'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Define Mandatory Columns (Red)
        // A=id_anggota, B=jenis, C=transaksi, D=jumlah, E=tanggal
        $mandatoryColumns = ['A', 'B', 'C', 'D', 'E'];
        
        // All columns A-G
        $allColumns = range('A', 'G');
        
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
