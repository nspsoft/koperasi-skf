<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.shu_slip.title') }} {{ $distribution->period_year }} - {{ $distribution->member->user->name ?? __('messages.dashboard.member') }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Arial, sans-serif; 
            font-size: 12px; 
            line-height: 1.5;
            color: #333;
            background: #f5f5f5;
        }
        
        .slip-container {
            width: 210mm;
            max-width: 100%;
            margin: 20px auto;
            background: white;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }

        .slip {
            padding: 25px 30px;
            border: 2px solid #0054a6;
            margin: 10px;
            position: relative;
        }

        /* Header */
        .header {
            display: flex;
            align-items: center;
            gap: 15px;
            padding-bottom: 15px;
            border-bottom: 2px solid #0054a6;
            margin-bottom: 20px;
        }
        .logo {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #0054a6, #0077cc);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 20px;
        }
        .header-text h1 {
            font-size: 18px;
            color: #0054a6;
        }
        .header-text p {
            font-size: 11px;
            color: #666;
        }
        .slip-title {
            position: absolute;
            top: 25px;
            right: 30px;
            text-align: right;
        }
        .slip-title h2 {
            font-size: 14px;
            color: #0054a6;
            text-transform: uppercase;
        }
        .slip-title .year {
            font-size: 28px;
            font-weight: bold;
            color: #0054a6;
        }

        /* Member Info */
        .member-info {
            background: #f8fafc;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        .info-item {
            display: flex;
            gap: 10px;
        }
        .info-item .label {
            color: #64748b;
            min-width: 100px;
        }
        .info-item .value {
            font-weight: 600;
            color: #333;
        }

        /* SHU Details */
        .shu-details {
            margin-bottom: 20px;
        }
        .shu-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px dashed #e2e8f0;
        }
        .shu-row:last-child {
            border-bottom: none;
        }
        .shu-row .label {
            color: #64748b;
        }
        .shu-row .value {
            font-weight: 600;
            text-align: right;
        }
        .shu-row.highlight {
            background: linear-gradient(90deg, #0054a6, #0077cc);
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-top: 10px;
        }
        .shu-row.highlight .label {
            color: rgba(255,255,255,0.8);
            font-size: 14px;
        }
        .shu-row.highlight .value {
            font-size: 24px;
            color: white;
        }

        /* Breakdown */
        .breakdown {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }
        .breakdown-box {
            background: #f8fafc;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid;
        }
        .breakdown-box.modal { border-color: #22c55e; }
        .breakdown-box.usaha { border-color: #3b82f6; }
        .breakdown-box .title {
            font-size: 10px;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .breakdown-box .contribution {
            font-size: 11px;
            color: #94a3b8;
            margin-bottom: 5px;
        }
        .breakdown-box .amount {
            font-size: 18px;
            font-weight: bold;
        }
        .breakdown-box.modal .amount { color: #22c55e; }
        .breakdown-box.usaha .amount { color: #3b82f6; }

        /* Footer */
        .footer {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }
        .signature {
            text-align: center;
            width: 150px;
        }
        .signature-line {
            border-bottom: 1px solid #333;
            margin-top: 50px;
            margin-bottom: 5px;
        }
        .signature p {
            font-size: 10px;
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 60px;
            color: rgba(0, 84, 166, 0.05);
            font-weight: bold;
            pointer-events: none;
            white-space: nowrap;
        }

        .note {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            padding: 10px;
            border-radius: 6px;
            font-size: 10px;
            color: #92400e;
            margin-top: 15px;
        }

        /* Print */
        @media print {
            body { background: white; }
            .slip-container { box-shadow: none; margin: 0; }
            .no-print { display: none; }
        }

        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #0054a6;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            z-index: 100;
        }
        .print-btn:hover { background: #003d7a; }
    </style>
</head>
<body>
    <button onclick="window.print()" class="print-btn no-print">🖨️ {{ __('messages.shu_my.print_slip') }}</button>

    <div class="slip-container">
        <div class="slip">
            <div class="watermark">SHU {{ $distribution->period_year }}</div>

            <!-- Header -->
            <div class="header">
                <div class="logo">KSK</div>
                <div class="header-text">
                    <h1>KOPERASI KARYAWAN SKF</h1>
                    <p>PT. SPINDO TBK</p>
                    <p>Jl. Raya Cakung Cilincing Km 3, Jakarta Timur</p>
                </div>
                <div class="slip-title">
                    <h2>{{ __('messages.shu_slip.title') }}</h2>
                    <div class="year">{{ $distribution->period_year }}</div>
                </div>
            </div>

            <!-- Member Info -->
            <div class="member-info">
                <div class="info-item">
                    <span class="label">{{ __('messages.shu_report.member_id') }}</span>
                    <span class="value">{{ $distribution->member->member_id ?? '-' }}</span>
                </div>
                <div class="info-item">
                    <span class="label">{{ __('messages.common.status') }}</span>
                    <span class="value">{{ $distribution->status === 'distributed' ? '✓ ' . __('messages.shu_calculator.already_posted') : __('messages.shu_calculator.calculate_shu') }}</span>
                </div>
                <div class="info-item">
                    <span class="label">{{ __('messages.common.name') }}</span>
                    <span class="value">{{ $distribution->member->user->name ?? '-' }}</span>
                </div>
                <div class="info-item">
                    <span class="label">{{ __('messages.shu_history.print_date') }}</span>
                    <span class="value">{{ now()->format('d/m/Y') }}</span>
                </div>
            </div>

            <!-- Contribution Breakdown -->
            <div class="breakdown">
                <div class="breakdown-box modal">
                    <div class="title">{{ __('messages.shu_my.from_savings') }}</div>
                    <div class="contribution">{{ __('messages.shu_report.savings_balance') }}: Rp {{ number_format($distribution->total_savings, 0, ',', '.') }}</div>
                    <div class="amount">Rp {{ number_format($distribution->shu_savings, 0, ',', '.') }}</div>
                </div>
                <div class="breakdown-box modal">
                    <div class="title">{{ __('messages.shu_my.from_transactions') }}</div>
                    <div class="contribution">{{ __('messages.shu_report.total_transactions') }}: Rp {{ number_format($distribution->total_transactions, 0, ',', '.') }}</div>
                    <div class="amount">Rp {{ number_format($distribution->shu_transactions, 0, ',', '.') }}</div>
                </div>
            </div>

            <!-- Total SHU -->
            <div class="shu-details">
                <div class="shu-row">
                    <span class="label">{{ __('messages.shu_calculator.jasa_modal') }} ({{ $setting->persen_jasa_modal ?? 30 }}%)</span>
                    <span class="value">Rp {{ number_format($distribution->shu_savings, 0, ',', '.') }}</span>
                </div>
                <div class="shu-row">
                    <span class="label">{{ __('messages.shu_calculator.jasa_usaha') }} ({{ $setting->persen_jasa_usaha ?? 25 }}%)</span>
                    <span class="value">Rp {{ number_format($distribution->shu_transactions, 0, ',', '.') }}</span>
                </div>
                <div class="shu-row highlight">
                    <span class="label">{{ __('messages.shu_my.your_total_shu') }}</span>
                    <span class="value">Rp {{ number_format($distribution->total_shu, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="note">
                <strong>{{ __('messages.common.note') }}:</strong> SHU dihitung berdasarkan UU No. 25 Tahun 1992 tentang Perkoperasian. 
                Jasa Modal dihitung berdasarkan proporsi simpanan Anda terhadap total simpanan seluruh anggota. 
                Jasa Usaha dihitung berdasarkan proporsi transaksi Anda di Koperasi Mart.
            </div>

            <!-- Signature -->
            <div class="footer">
                <div class="signature">
                    <p>{{ __('messages.pos.customer') }},</p>
                    <div class="signature-line"></div>
                    <p><strong>{{ $distribution->member->user->name ?? '-' }}</strong></p>
                </div>
                <div class="signature">
                    <p>{{ __('messages.shu_report.knowing') }},</p>
                    <p>{{ __('messages.shu_report.treasurer') }}</p>
                    <div class="signature-line"></div>
                    <p>(_______________________)</p>
                </div>
            </div>

            <!-- QR Verification -->
            @if(isset($qrCode) && isset($generatedDocument))
            <div style="margin-top: 20px; padding-top: 15px; border-top: 1px dashed #e2e8f0; display: flex; align-items: center; gap: 15px;">
                <img src="{{ $qrCode }}" alt="QR Code" style="width: 80px; height: 80px;">
                <div style="font-size: 10px; color: #64748b;">
                    <p style="margin: 0 0 3px 0;"><strong>No. Dokumen:</strong> {{ $generatedDocument->document_number }}</p>
                    <p style="margin: 0 0 3px 0;"><strong>Diterbitkan:</strong> {{ $generatedDocument->created_at->format('d/m/Y H:i') }}</p>
                    <p style="margin: 0; font-size: 9px;">Scan QR untuk memverifikasi keaslian dokumen ini.</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</body>
</html>
