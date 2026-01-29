<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pinjaman</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; padding-bottom: 10px; border-bottom: 3px solid #F59E0B; }
        .header h1 { margin: 0; color: #F59E0B; font-size: 20px; }
        .header p { margin: 5px 0; color: #666; }
        .meta table { width: 100%; margin-bottom: 20px; }
        .meta td { padding: 3px 0; }
        .stats table { width: 100%; margin-bottom: 20px; }
        .stat-item { padding: 10px; background: #f3f4f6; border-radius: 5px; }
        .stat-label { font-size: 10px; color: #666; }
        .stat-value { font-size: 16px; font-weight: bold; color: #F59E0B; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background: #F59E0B; color: white; padding: 8px; text-align: left; font-size: 11px; }
        td { padding: 6px 8px; border-bottom: 1px solid #e5e7eb; font-size: 11px; }
        tr:nth-child(even) { background: #f9fafb; }
        .badge { padding: 2px 6px; border-radius: 3px; font-size: 9px; font-weight: bold; }
        .badge-pending { background: #FEF3C7; color: #92400E; }
        .badge-approved { background: #D1FAE5; color: #065F46; }
        .badge-active { background: #DBEAFE; color: #1E40AF; }
        .badge-completed { background: #E5E7EB; color: #374151; }
        .footer { margin-top: 30px; padding-top: 10px; border-top: 1px solid #ccc; text-align: right; font-size: 10px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>KOPERASI KARYAWAN PT. SPINDO KARAWANG FACTORY</h1>
        <p>Laporan Pinjaman</p>
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

    <div class="stats">
        <table>
            <tr>
                <td width="25%" style="padding-right: 10px;">
                    <div class="stat-item">
                        <div class="stat-label">Total Pinjaman</div>
                        <div class="stat-value">Rp {{ number_format($totalLoans, 0, ',', '.') }}</div>
                    </div>
                </td>
                <td width="25%" style="padding-right: 10px;">
                    <div class="stat-item">
                        <div class="stat-label">Total Dibayar</div>
                        <div class="stat-value">Rp {{ number_format($totalPaid, 0, ',', '.') }}</div>
                    </div>
                </td>
                <td width="25%" style="padding-right: 10px;">
                    <div class="stat-item">
                        <div class="stat-label">Sisa Pinjaman</div>
                        <div class="stat-value">Rp {{ number_format($totalRemaining, 0, ',', '.') }}</div>
                    </div>
                </td>
                <td width="25%">
                    <div class="stat-item">
                        <div class="stat-label">Jumlah Pinjaman</div>
                        <div class="stat-value">{{ $loanCount }}</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <h3 style="margin-top: 20px; color: #F59E0B;">Daftar Pinjaman</h3>
    <table>
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="13%">No Pinjaman</th>
                <th width="18%">Anggota</th>
                <th width="13%">Jenis</th>
                <th width="13%">Jumlah</th>
                <th width="8%">Tenor</th>
                <th width="13%">Tgl Cair</th>
                <th width="10%">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($loans as $index => $loan)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td style="font-family: monospace; font-size: 9px;">{{ $loan->loan_number }}</td>
                <td>{{ $loan->member->user->name }}</td>
                <td>{{ $loan->loan_type_label }}</td>
                <td style="font-weight: bold;">Rp {{ number_format($loan->amount, 0, ',', '.') }}</td>
                <td>{{ $loan->duration_months }} bln</td>
                <td>{{ $loan->disbursement_date ? $loan->disbursement_date->format('d/m/Y') : '-' }}</td>
                <td>
                    <span class="badge badge-{{ $loan->status }}">{{ $loan->status_label }}</span>
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
