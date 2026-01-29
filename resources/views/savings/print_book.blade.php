<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Buku Tabungan - {{ $member->user->name }}</title>
    <style>
        body {
            font-family: monospace; /* Font mesin ketik/dot matrix style */
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px dashed #000;
            padding-bottom: 10px;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-table td {
            padding: 4px;
        }
        .transactions-table {
            width: 100%;
            border-collapse: collapse;
        }
        .transactions-table th {
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            padding: 8px 4px;
            text-align: left;
        }
        .transactions-table td {
            padding: 6px 4px;
            vertical-align: top;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        @media print {
            @page { margin: 0; size: A5 landscape; }
            body { padding: 10mm; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.close()" style="padding: 5px 10px;">Tutup</button>
    </div>

    <div class="header">
        <h2 style="margin: 0;">BUKU TABUNGAN ANGGOTA</h2>
        <h3 style="margin: 5px 0;">KOPERASI KARYAWAN SPINDO</h3>
    </div>

    <table class="info-table">
        <tr>
            <td width="15%">No. Anggota</td>
            <td width="2%">:</td>
            <td width="33%">{{ $member->member_id }}</td>
            <td width="15%">NIK Karyawan</td>
            <td width="2%">:</td>
            <td>{{ $member->employee_id }}</td>
        </tr>
        <tr>
            <td>Nama</td>
            <td>:</td>
            <td>{{ $member->user->name }}</td>
            <td>Departemen</td>
            <td>:</td>
            <td>{{ $member->department }}</td>
        </tr>
        <tr>
            <td>Bergabung</td>
            <td>:</td>
            <td>{{ $member->join_date->format('d/m/Y') }}</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </table>

    <table class="transactions-table">
        <thead>
            <tr>
                <th style="width: 15%">Tanggal</th>
                <th style="width: 15%">Kode Ref</th>
                <th style="width: 10%">Sandi</th>
                <th style="width: 25%">Keterangan</th>
                <th style="width: 15%" class="text-right">Debit</th>
                <th style="width: 15%" class="text-right">Kredit</th>
                <th style="width: 20%" class="text-right">Saldo</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $balance = 0; 
            @endphp
            @foreach($savings as $saving)
                @php
                    if($saving->transaction_type == 'deposit') {
                        $balance += $saving->amount;
                        $debit = '-';
                        $credit = number_format($saving->amount, 0, ',', '.');
                        $code = 'STR'; // Setor
                    } else {
                        $balance -= $saving->amount;
                        $debit = number_format($saving->amount, 0, ',', '.');
                        $credit = '-';
                        $code = 'TRK'; // Tarik
                    }
                    
                    // Kode Jenis Simpanan
                    $typeCode = substr(strtoupper($saving->type), 0, 1); // P, W, S
                @endphp
            <tr>
                <td>{{ $saving->transaction_date->format('d/m/y') }}</td>
                <td>{{ $saving->reference_number }}</td>
                <td class="text-center">{{ $code }}-{{ $typeCode }}</td>
                <td>{{ $saving->description ?? '-' }}</td>
                <td class="text-right">{{ $debit }}</td>
                <td class="text-right">{{ $credit }}</td>
                <td class="text-right">{{ number_format($balance, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="7" style="border-top: 1px dashed #000; padding-top: 10px;">
                    <strong>Total Saldo Akhir: Rp {{ number_format($balance, 0, ',', '.') }}</strong>
                </td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
