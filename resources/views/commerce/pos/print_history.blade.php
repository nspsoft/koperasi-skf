<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Penjualan - {{ now()->format('d/m/Y') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Courier New', monospace;
            font-size: 11px;
            width: 80mm;
            padding: 10px;
            background: white;
            color: black;
        }
        .receipt {
            width: 100%;
        }
        .header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 5px;
            margin-bottom: 8px;
        }
        .header h1 {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .header p {
            font-size: 10px;
        }
        .info-section {
            margin-bottom: 8px;
            border-bottom: 1px dashed #000;
            padding-bottom: 5px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
        }
        .divider {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }
        .trans-table {
            width: 100%;
            margin-bottom: 10px;
        }
        .trans-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }
        .trans-details {
            font-size: 10px;
            color: #333;
            margin-left: 5px;
        }
        .summary-section {
            border-top: 2px dashed #000;
            border-bottom: 2px dashed #000;
            padding: 5px 0;
            margin-bottom: 8px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            font-weight: bold;
            font-size: 12px;
        }
        .breakdown-row {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            margin-top: 2px;
        }
        .footer {
            text-align: center;
            font-size: 10px;
            margin-top: 15px;
        }
        @media print {
            body { width: 80mm; }
            .no-print { display: none !important; }
        }
        .print-btn {
            position: fixed; top: 10px; right: 10px;
            padding: 8px 15px; background: #000; color: #fff;
            border: none; border-radius: 4px; cursor: pointer;
        }
    </style>
</head>
<body>
    <button class="print-btn no-print" onclick="window.print()">Cetak Rekap</button>

    <div class="receipt">
        <div class="header">
            <h1>KOPERASI MART</h1>
            <p>LAPORAN REKAP PENJUALAN</p>
            <p>{{ now()->format('d/m/Y H:i') }}</p>
        </div>

        <div class="info-section">
            <div class="info-row">
                <span>Filter Tgl:</span>
                <span>{{ request('date') ? \Carbon\Carbon::parse(request('date'))->format('d/m/Y') : 'HARI INI' }}</span>
            </div>
            <div class="info-row">
                <span>Tipe:</span>
                <span>{{ request('type') ? strtoupper(request('type')) : 'SEMUA' }}</span>
            </div>
            <div class="info-row">
                <span>User:</span>
                <span>{{ auth()->user()->name }}</span>
            </div>
        </div>

        <div style="font-weight: bold; margin-bottom: 5px;">DAFTAR TRANSAKSI:</div>
        
        @foreach($transactions as $trx)
        <div class="trans-row">
            <span>{{ $trx->created_at->format('H:i') }} | {{ $trx->invoice_number }}</span>
            <span>{{ number_format($trx->total_amount, 0, ',', '.') }}</span>
        </div>
        @if($trx->status != 'completed' && $trx->status != 'paid')
        <div class="trans-details">Status: {{ ucfirst($trx->status) }}</div>
        @endif
        @endforeach

        @if($transactions->isEmpty())
        <div style="text-align: center; margin: 20px 0;">(Tidak ada transaksi)</div>
        @endif

        <div class="summary-section">
            <div class="summary-row">
                <span>TOTAL TRANSAKSI:</span>
                <span>{{ $transactions->count() }}</span>
            </div>
            <div class="summary-row" style="margin-top: 5px;">
                <span>TOTAL PENJUALAN:</span>
                <span>Rp {{ number_format($transactions->sum('total_amount'), 0, ',', '.') }}</span>
            </div>
        </div>

        <div style="margin-bottom: 10px;">
            <div style="font-weight: bold; text-decoration: underline;">Rincian Pembayaran:</div>
            
            @php
                $cash = $transactions->where('payment_method', 'cash')->sum('total_amount');
                $transfer = $transactions->where('payment_method', 'transfer')->sum('total_amount');
                $credit = $transactions->where('payment_method', 'kredit')->sum('total_amount');
                $others = $transactions->whereNotIn('payment_method', ['cash', 'transfer', 'kredit'])->sum('total_amount');
            @endphp

            <div class="breakdown-row">
                <span>Tunai (Cash):</span>
                <span>{{ number_format($cash, 0, ',', '.') }}</span>
            </div>
            <div class="breakdown-row">
                <span>Transfer:</span>
                <span>{{ number_format($transfer, 0, ',', '.') }}</span>
            </div>
            <div class="breakdown-row">
                <span>Kredit:</span>
                <span>{{ number_format($credit, 0, ',', '.') }}</span>
            </div>
            @if($others > 0)
            <div class="breakdown-row">
                <span>Lainnya:</span>
                <span>{{ number_format($others, 0, ',', '.') }}</span>
            </div>
            @endif
        </div>

        <div class="footer">
            <p>--- Akhir Laporan ---</p>
            <p>{{ config('app.name') }}</p>
        </div>
    </div>
    
    <script>
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
