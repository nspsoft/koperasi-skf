<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Anggota Koperasi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 10px;
            border-bottom: 3px solid #1E40AF;
        }
        .header h1 {
            margin: 0;
            color: #1E40AF;
            font-size: 20px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .meta {
            margin-bottom: 20px;
        }
        .meta table {
            width: 100%;
        }
        .meta td {
            padding: 3px 0;
        }
        .stats {
            display: flex;
            margin-bottom: 20px;
        }
        .stat-card {
            flex: 1;
            padding: 10px;
            margin-right: 10px;
            background: #f3f4f6;
            border-radius: 5px;
        }
        .stat-card:last-child {
            margin-right: 0;
        }
        .stat-label {
            font-size: 10px;
            color: #666;
            margin-bottom: 5px;
        }
        .stat-value {
            font-size: 18px;
            font-weight: bold;
            color: #1E40AF;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th {
            background: #1E40AF;
            color: white;
            padding: 8px;
            text-align: left;
            font-size: 11px;
        }
        td {
            padding: 6px 8px;
            border-bottom: 1px solid #e5e7eb;
        }
        tr:nth-child(even) {
            background: #f9fafb;
        }
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ccc;
            text-align: right;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>KOPERASI KARYAWAN PT. SPINDO KARAWANG FACTORY</h1>
        <p>Laporan Data Anggota</p>
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
        <div class="stat-card">
            <div class="stat-label">Total Anggota</div>
            <div class="stat-value">{{ $totalMembers }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Anggota Aktif</div>
            <div class="stat-value">{{ $activeMembers }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Anggota Baru</div>
            <div class="stat-value">{{ $newMembers }}</div>
        </div>
    </div>

    <h3 style="margin-top: 20px; color: #1E40AF;">Daftar Anggota</h3>
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">ID Anggota</th>
                <th width="25%">Nama</th>
                <th width="20%">Department</th>
                <th width="15%">Tgl Bergabung</th>
                <th width="10%">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($members as $index => $member)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $member->member_id }}</td>
                <td>{{ $member->user->name }}</td>
                <td>{{ $member->department }}</td>
                <td>{{ $member->join_date->format('d/m/Y') }}</td>
                <td>{{ $member->status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada {{ now()->format('d F Y, H:i') }} WIB</p>
    </div>
</body>
</html>
