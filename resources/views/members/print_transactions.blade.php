<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>History Belanja - {{ $member->user->name }}</title>
    <style>
        body {
            font-family: monospace;
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
            padding: 2px;
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
        .item-row td {
            padding-top: 2px;
            padding-bottom: 2px;
            color: #555;
            font-size: 11px;
        }
        .transaction-header {
            background-color: #f9f9f9;
            font-weight: bold;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .badge {
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            border: 1px solid #000;
        }
        
        @media print {
            @page { margin: 0; size: A4; }
            body { padding: 10mm; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="no-print" style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <form method="GET" style="display: inline-flex; gap: 10px; align-items: center;">
                <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" style="padding: 5px;">
                <span>s/d</span>
                <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" style="padding: 5px;">
                <button type="submit" style="padding: 5px 10px;">Filter Tanggal</button>
            </form>
        </div>
        <button onclick="window.close()" style="padding: 5px 10px;">Tutup</button>
    </div>

    <div class="header">
        <h2 style="margin: 0;">HISTORY TRANSAKSI BELANJA</h2>
        <h3 style="margin: 5px 0;">KOPERASI KARYAWAN SPINDO</h3>
        <p style="margin: 5px 0;">Periode: {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}</p>
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
            <td>Total Belanja</td>
            <td>:</td>
            <td>Rp {{ number_format($totalSpent, 0, ',', '.') }}</td>
        </tr>
    </table>

    <table class="transactions-table">
        <thead>
            <tr>
                <th style="width: 15%">Tanggal / Invoice</th>
                <th style="width: 40%">Detail Barang</th>
                <th style="width: 15%">Metode</th>
                <th style="width: 15%">Kasir</th>
                <th style="width: 15%" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $trx)
            <tr class="transaction-header">
                <td>
                    {{ $trx->created_at->format('d/m/y H:i') }}<br>
                    <small>{{ $trx->invoice_number }}</small>
                </td>
                <td colspan="2">
                    @if($trx->status == 'pending')
                        <span class="badge">BELUM LUNAS</span>
                    @elseif($trx->status == 'cancelled')
                        <span class="badge">BATAL</span>
                    @endif
                </td>
                <td>{{ $trx->cashier->name ?? 'System' }}</td>
                <td class="text-right">
                    Rp {{ number_format($trx->total_amount, 0, ',', '.') }}
                </td>
            </tr>
                
                @foreach($trx->items as $item)
                <tr class="item-row">
                    <td></td>
                    <td colspan="3">
                        {{ $item->product->name ?? 'Produk dihapus' }} 
                        <span style="color: #888;">x {{ $item->quantity }}</span>
                    </td>
                    <td class="text-right" style="color: #777;">
                        {{ number_format($item->subtotal, 0, ',', '.') }}
                    </td>
                </tr>
                @endforeach
                
                <tr><td colspan="5" style="border-bottom: 1px dotted #ccc;"></td></tr>
            @empty
            <tr>
                <td colspan="5" class="text-center" style="padding: 20px;">Tidak ada transaksi pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" style="border-top: 1px dashed #000; padding-top: 10px;">
                    <div style="display: flex; justify-content: flex-end; gap: 40px;">
                        <div class="text-right">
                            <strong>Total Cash/Transfer/QRIS:</strong><br>
                            Rp {{ number_format($totalPaid - ($totalCredit - ($transactions->where('payment_method', 'kredit')->where('status', 'completed')->sum('total_amount'))), 0, ',', '.') }}
                        </div>
                        <div class="text-right">
                            <strong>Total Kredit:</strong><br>
                            Rp {{ number_format($transactions->where('payment_method', 'kredit')->sum('total_amount'), 0, ',', '.') }}
                        </div>
                        <div class="text-right">
                            <strong>Grand Total:</strong><br>
                            Rp {{ number_format($totalSpent, 0, ',', '.') }}
                        </div>
                    </div>
                </td>
            </tr>
        </tfoot>
    </table>
    
    <div style="margin-top: 30px; text-align: center; font-size: 10px; color: #777;">
        Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}
    </div>
</body>
</html>
