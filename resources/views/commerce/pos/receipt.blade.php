<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk - {{ $transaction->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Courier New', monospace;
            font-size: 11px;
            width: 58mm;
            padding: 5px;
            background: white;
            color: black;
        }
        .receipt {
            width: 100%;
        }
        .header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 8px;
            margin-bottom: 8px;
        }
        .header h1 {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 2px;
            text-transform: uppercase;
        }
        .header p {
            font-size: 9px;
            color: #000;
            line-height: 1.2;
        }
        .info {
            margin-bottom: 8px;
            font-size: 10px;
        }
        .info p {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
        }
        .items {
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            padding: 8px 0;
            margin-bottom: 8px;
        }
        .item {
            margin-bottom: 5px;
        }
        .item-name {
            font-weight: bold;
            display: block;
            margin-bottom: 2px;
        }
        .item-details {
            display: flex;
            justify-content: space-between;
            font-size: 10px;
        }
        .totals {
            margin-bottom: 8px;
        }
        .totals p {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
        }
        .totals .grand-total {
            font-size: 12px;
            font-weight: bold;
            border-top: 1px solid #000;
            padding-top: 4px;
            margin-top: 4px;
        }
        .footer {
            text-align: center;
            border-top: 1px dashed #000;
            padding-top: 8px;
            font-size: 9px;
        }
        .footer p {
            margin-bottom: 2px;
        }
        @media print {
            @page {
                size: 58mm auto;
                margin: 0;
            }
            body {
                width: 58mm;
                margin: 0;
                padding: 5px;
            }
            .no-print {
                display: none !important;
            }
        }
        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
        }
        .print-btn:hover {
            background: #2563eb;
        }
    </style>
</head>
<body>
    <button class="print-btn no-print" onclick="window.print()">🖨️ Cetak Struk</button>

    <div class="receipt">
        <div class="header">
            <h1>{{ \App\Models\Setting::get('coop_name', 'KOPERASI SKF') }}</h1>
            <p>{{ \App\Models\Setting::get('coop_address', 'Karawang, Jawa Barat') }}</p>
            <p>Telp: {{ \App\Models\Setting::get('coop_phone', '-') }}</p>
        </div>

        <div class="info">
            <p><span>No. Invoice:</span> <span>{{ $transaction->invoice_number }}</span></p>
            <p><span>Tanggal:</span> <span>{{ $transaction->created_at->format('d/m/Y H:i') }}</span></p>
            <p><span>Pembeli:</span> <span>{{ $transaction->user->name ?? 'Tamu' }}</span></p>
            <p><span>Kasir:</span> <span>{{ $transaction->cashier->name ?? '-' }}</span></p>
            <p><span>Metode:</span> <span>{{ strtoupper(str_replace('_', ' ', $transaction->payment_method)) }}</span></p>
        </div>

        <div class="items">
            @foreach($transaction->items as $item)
            <div class="item">
                <div class="item-name">{{ $item->product->name ?? 'Produk' }}</div>
                <div class="item-details">
                    <span>{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                    <span>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                </div>
            </div>
            @endforeach
        </div>

        <div class="totals">
            <p>
                <span>Subtotal ({{ $transaction->items->sum('quantity') }} item):</span>
                <span>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
            </p>
            <p class="grand-total">
                <span>TOTAL:</span>
                <span>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
            </p>
            <p>
                <span>Bayar:</span>
                <span>Rp {{ number_format($transaction->paid_amount, 0, ',', '.') }}</span>
            </p>
            <p>
                <span>Kembali:</span>
                <span>Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
            </p>
        </div>

        <div class="footer">
            <div style="margin-bottom: 10px; display: flex; justify-content: center;">
                 {!! QrCode::size(80)->generate($transaction->invoice_number) !!}
            </div>
            <p>================================</p>
            <p>Terima kasih atas kunjungan Anda!</p>
            <p>Barang yang sudah dibeli tidak dapat dikembalikan.</p>
            <p>================================</p>
            <p style="margin-top: 10px; font-size: 9px;">Dicetak: {{ now()->format('d/m/Y H:i:s') }}</p>
        </div>
    </div>

    <script>
        // Auto print on load (optional - uncomment if needed)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
