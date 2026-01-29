<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Simpanan</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; padding-bottom: 10px; border-bottom: 3px solid #10B981; }
        .header h1 { margin: 0; color: #10B981; font-size: 20px; }
        .header p { margin: 5px 0; color: #666; }
        .meta table { width: 100%; margin-bottom: 20px; }
        .meta td { padding: 3px 0; }
        .stats { margin-bottom: 20px; }
        .stat-row { display: table; width: 100%; margin-bottom: 5px; }
        .stat-item { display: table-cell; padding: 10px; background: #f3f4f6; border-radius: 5px; margin-right: 10px; }
        .stat-label { font-size: 10px; color: #666; }
        .stat-value { font-size: 16px; font-weight: bold; color: #10B981; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background: #10B981; color: white; padding: 8px; text-align: left; font-size: 11px; }
        td { padding: 6px 8px; border-bottom: 1px solid #e5e7eb; }
        tr:nth-child(even) { background: #f9fafb; }
        .positive { color: #10B981; }
        .negative { color: #EF4444; }
        .footer { margin-top: 30px; padding-top: 10px; border-top: 1px solid #ccc; text-align: right; font-size: 10px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>KOPERASI KARYAWAN PT. SPINDO KARAWANG FACTORY</h1>
        <p>Laporan Simpanan</p>
        <p>Periode: {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}</p>
    </div>

    <div class="meta">
        <table>
            <tr>
                <td width="20%">Tanggal Cetak</td>
                <td>: {{ now()->format('d F Y, H:i') }} WIB</td>
            </tr>
            <tr>
                <td>Dicetak Oleh</td>
                <td>: {{ auth()->user()->name }}</td>
            </tr>
        </table>
    </div>


    <h3 style="margin-top: 10px; margin-bottom: 10px; color: #10B981;">Ringkasan Keuangan</h3>
    <table style="margin-bottom: 20px;">
        <tr>
            <td width="25%" style="background: #f3f4f6; padding: 10px; vertical-align: top;">
                <div style="font-size: 10px; color: #666; margin-bottom: 5px;">Total Setoran</div>
                <div style="font-size: 16px; font-weight: bold; color: #10B981;">Rp {{ number_format($totalDeposits, 0, ',', '.') }}</div>
            </td>
            <td width="25%" style="background: #f3f4f6; padding: 10px; vertical-align: top;">
                <div style="font-size: 10px; color: #666; margin-bottom: 5px;">Total Penarikan</div>
                <div style="font-size: 16px; font-weight: bold; color: #10B981;">Rp {{ number_format($totalWithdrawals, 0, ',', '.') }}</div>
            </td>
            <td width="25%" style="background: #f3f4f6; padding: 10px; vertical-align: top;">
                <div style="font-size: 10px; color: #666; margin-bottom: 5px;">Saldo Bersih</div>
                <div style="font-size: 16px; font-weight: bold; color: #10B981;">Rp {{ number_format($totalDeposits - $totalWithdrawals, 0, ',', '.') }}</div>
            </td>
            <td width="25%" style="background: #f3f4f6; padding: 10px; vertical-align: top;">
                <div style="font-size: 10px; color: #666; margin-bottom: 5px;">Total Transaksi</div>
                <div style="font-size: 16px; font-weight: bold; color: #10B981;">{{ $totalTransactions }}</div>
            </td>
        </tr>
    </table>


    <h3 style="margin-top: 20px; color: #10B981;">Rincian Transaksi</h3>
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="12%">Tanggal</th>
                <th width="23%">Anggota</th>
                <th width="15%">Jenis</th>
                <th width="15%">Transaksi</th>
                <th width="15%" style="text-align: right;">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach($savings as $index => $saving)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $saving->transaction_date->format('d/m/Y') }}</td>
                <td>{{ $saving->member->user->name }}</td>
                <td>{{ $saving->type_label }}</td>
                <td>{{ $saving->transaction_type === 'deposit' ? 'Setoran' : 'Penarikan' }}</td>
                <td style="text-align: right; font-weight: bold;" class="{{ $saving->transaction_type === 'deposit' ? 'positive' : 'negative' }}">
                    Rp {{ number_format($saving->amount, 0, ',', '.') }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada {{ now()->format('d F Y, H:i') }} WIB</p>
    </div>
</body>
</html>
