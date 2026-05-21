<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Retur Konsinyasi - {{ $return->transaction_number }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 14px;
            line-height: 1.5;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        .logo {
            max-height: 60px;
            margin-bottom: 10px;
        }
        .kop-title {
            font-size: 20px;
            font-weight: bold;
            margin: 0 0 5px 0;
        }
        .kop-address {
            font-size: 12px;
            margin: 0;
            color: #666;
        }
        .doc-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin: 20px 0;
            text-transform: uppercase;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-table td {
            vertical-align: top;
            padding: 4px 0;
        }
        .info-table .label {
            width: 150px;
            font-weight: bold;
        }
        .info-table .colon {
            width: 20px;
            text-align: center;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .items-table th, .items-table td {
            border: 1px solid #ddd;
            padding: 8px 12px;
            text-align: left;
        }
        .items-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .items-table .text-center {
            text-align: center;
        }
        .items-table .text-right {
            text-align: right;
        }
        .signature-area {
            display: table;
            width: 100%;
            margin-top: 50px;
        }
        .signature-box {
            display: table-cell;
            width: 33.33%;
            text-align: center;
        }
        .signature-title {
            margin-bottom: 60px;
        }
        .signature-name {
            font-weight: bold;
            text-decoration: underline;
        }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
        .btn-print {
            display: inline-block;
            background: #4F46E5;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 20px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="no-print" style="text-align: right;">
            <button onclick="window.print()" class="btn-print">Cetak Dokumen</button>
        </div>

        <div class="header">
            @php
                $settings = \App\Models\Setting::first();
            @endphp
            @if($settings && $settings->logo_url)
                <img src="{{ Storage::url($settings->logo_url) }}" alt="Logo" class="logo">
            @endif
            <h1 class="kop-title">{{ $settings->company_name ?? 'KOPERASI' }}</h1>
            <p class="kop-address">{{ $settings->company_address ?? 'Alamat belum diatur' }}</p>
            <p class="kop-address">Telp: {{ $settings->company_phone ?? '-' }} | Email: {{ $settings->company_email ?? '-' }}</p>
        </div>

        <div class="doc-title">
            TANDA TERIMA RETUR KONSINYASI
        </div>

        <table class="info-table">
            <tr>
                <td style="width: 50%;">
                    <table width="100%">
                        <tr>
                            <td class="label">No. Retur</td>
                            <td class="colon">:</td>
                            <td>{{ $return->transaction_number }}</td>
                        </tr>
                        <tr>
                            <td class="label">Tanggal Retur</td>
                            <td class="colon">:</td>
                            <td>{{ $return->return_date->format('d/m/Y') }}</td>
                        </tr>
                    </table>
                </td>
                <td style="width: 50%;">
                    <table width="100%">
                        <tr>
                            <td class="label">Pihak Mitra / Supplier</td>
                            <td class="colon">:</td>
                            <td>
                                <strong>{{ $return->consignor->name ?? '-' }}</strong><br>
                                @if($return->consignor_type === 'member')
                                    Anggota ({{ $return->consignor->member_id ?? '-' }})
                                @endif
                            </td>
                        </tr>
                        @if($return->notes)
                        <tr>
                            <td class="label">Catatan</td>
                            <td class="colon">:</td>
                            <td>{{ $return->notes }}</td>
                        </tr>
                        @endif
                    </table>
                </td>
            </tr>
        </table>

        <p>Telah dikembalikan barang konsinyasi kepada Mitra/Supplier dengan rincian sebagai berikut:</p>

        <table class="items-table">
            <thead>
                <tr>
                    <th width="5%" class="text-center">No</th>
                    <th width="15%">Kode</th>
                    <th width="45%">Nama Barang</th>
                    <th width="10%" class="text-center">Qty</th>
                    <th width="25%">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($return->items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->product->code ?? '-' }}</td>
                    <td>{{ $item->product->name }}</td>
                    <td class="text-center font-bold">{{ $item->quantity }}</td>
                    <td>{{ $item->notes ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" class="text-right">TOTAL BARANG</th>
                    <th class="text-center">{{ $return->total_items }}</th>
                    <th></th>
                </tr>
            </tfoot>
        </table>

        <div class="signature-area">
            <div class="signature-box">
                <div class="signature-title">Diterima Oleh,<br>(Pihak Mitra/Supplier)</div>
                <div class="signature-name">{{ $return->consignor->name ?? '.......................' }}</div>
            </div>
            <div class="signature-box">
                <div class="signature-title">Mengetahui,<br>(Pengurus Koperasi)</div>
                <div class="signature-name">.......................</div>
            </div>
            <div class="signature-box">
                <div class="signature-title">Diserahkan Oleh,<br>(Petugas Toko)</div>
                <div class="signature-name">{{ $return->creator->name ?? '.......................' }}</div>
            </div>
        </div>
    </div>
    
    <script>
        window.onload = function() {
            // Optional: auto print dialog
            // window.print();
        }
    </script>
</body>
</html>
