<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.shu_report.title') }} {{ $year }} - Koperasi Karyawan SKF</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Arial, sans-serif; 
            font-size: 11px; 
            line-height: 1.4;
            color: #333;
            background: #fff;
        }
        .container { max-width: 210mm; margin: 0 auto; padding: 10mm; }
        
        /* Header */
        .header { 
            text-align: center; 
            border-bottom: 3px double #0054a6; 
            padding-bottom: 15px; 
            margin-bottom: 20px; 
        }
        .header h1 { 
            font-size: 18px; 
            color: #0054a6; 
            margin-bottom: 5px;
        }
        .header h2 { 
            font-size: 14px; 
            font-weight: normal;
            color: #333;
        }
        .header p { color: #666; font-size: 10px; }

        /* Summary Box */
        .summary-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
        }
        .summary-item {
            text-align: center;
        }
        .summary-item .label {
            font-size: 9px;
            color: #64748b;
            text-transform: uppercase;
        }
        .summary-item .value {
            font-size: 14px;
            font-weight: bold;
            color: #0054a6;
        }
        .summary-item .sub {
            font-size: 9px;
            color: #94a3b8;
        }

        /* Table */
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 20px;
            font-size: 10px;
        }
        th, td { 
            border: 1px solid #e2e8f0; 
            padding: 6px 8px; 
            text-align: left; 
        }
        th { 
            background: #0054a6; 
            color: white; 
            font-weight: 600;
            text-transform: uppercase;
            font-size: 9px;
        }
        tr:nth-child(even) { background: #f8fafc; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        
        tfoot td {
            background: #f1f5f9;
            font-weight: bold;
        }

        /* Breakdown */
        .breakdown {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }
        .breakdown-item {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px;
            text-align: center;
        }
        .breakdown-item .label {
            font-size: 8px;
            color: #64748b;
            text-transform: uppercase;
        }
        .breakdown-item .persen {
            font-size: 16px;
            font-weight: bold;
            color: #0054a6;
        }
        .breakdown-item .amount {
            font-size: 10px;
            color: #333;
        }

        /* Footer */
        .footer {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            text-align: center;
            width: 180px;
        }
        .signature-line {
            border-bottom: 1px solid #333;
            margin-top: 60px;
            margin-bottom: 5px;
        }

        /* Print */
        @media print {
            body { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
            .no-print { display: none; }
            .container { padding: 5mm; }
        }

        /* Print Button */
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
        }
        .print-btn:hover { background: #003d7a; }
    </style>
</head>
<body>
    <button onclick="window.print()" class="print-btn no-print">🖨️ {{ __('messages.shu_report.print_report') }}</button>

    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>KOPERASI KARYAWAN SKF</h1>
            <h2>PT. SPINDO TBK</h2>
            <p>Jl. Raya Cakung Cilincing Km 3, Jakarta Timur 13910</p>
            <p style="margin-top: 10px; font-size: 14px; font-weight: bold; color: #333;">
                {{ __('messages.shu_report.title') }} {{ $year }}
            </p>
        </div>

        <!-- Summary -->
        <div class="summary-box">
            <div class="summary-grid">
                <div class="summary-item">
                    <div class="label">{{ __('messages.shu_report.total_shu') }}</div>
                    <div class="value">Rp {{ number_format($setting->total_shu_pool, 0, ',', '.') }}</div>
                </div>
                <div class="summary-item">
                    <div class="label">{{ __('messages.shu_report.for_members') }}</div>
                    <div class="value">Rp {{ number_format($setting->pool_anggota, 0, ',', '.') }}</div>
                    <div class="sub">({{ $setting->persen_jasa_modal + $setting->persen_jasa_usaha }}%)</div>
                </div>
                <div class="summary-item">
                    <div class="label">{{ __('messages.shu_report.recipient_count') }}</div>
                    <div class="value">{{ $distributions->count() }}</div>
                    <div class="sub">{{ __('messages.shu_report.members') }}</div>
                </div>
                <div class="summary-item">
                    <div class="label">{{ __('messages.shu_report.avg_shu') }}</div>
                    <div class="value">Rp {{ number_format($distributions->avg('total_shu'), 0, ',', '.') }}</div>
                    <div class="sub">{{ __('messages.shu_report.per_member') }}</div>
                </div>
            </div>
        </div>

        <!-- Breakdown -->
        <div class="breakdown">
            <div class="breakdown-item">
                <div class="label">{{ __('messages.shu.reserve_fund') }}</div>
                <div class="persen">{{ $setting->persen_cadangan }}%</div>
                <div class="amount">Rp {{ number_format($setting->pool_cadangan, 0, ',', '.') }}</div>
            </div>
            <div class="breakdown-item">
                <div class="label">{{ __('messages.shu.management_fund') }}</div>
                <div class="persen">{{ $setting->persen_pengurus }}%</div>
                <div class="amount">Rp {{ number_format($setting->pool_pengurus, 0, ',', '.') }}</div>
            </div>
            <div class="breakdown-item">
                <div class="label">{{ __('messages.shu.employee_fund') }}</div>
                <div class="persen">{{ $setting->persen_karyawan }}%</div>
                <div class="amount">Rp {{ number_format($setting->pool_karyawan, 0, ',', '.') }}</div>
            </div>
            <div class="breakdown-item">
                <div class="label">{{ __('messages.shu.others') }}</div>
                <div class="persen">{{ $setting->persen_pendidikan + $setting->persen_sosial + $setting->persen_pembangunan }}%</div>
                <div class="amount">Rp {{ number_format($setting->pool_pendidikan + $setting->pool_sosial + $setting->pool_pembangunan, 0, ',', '.') }}</div>
            </div>
        </div>

        <!-- Table -->
        <table>
            <thead>
                <tr>
                    <th class="text-center" style="width: 30px;">{{ __('messages.shu_report.no') }}</th>
                    <th style="width: 80px;">{{ __('messages.shu_report.member_id') }}</th>
                    <th>{{ __('messages.shu_report.member_name') }}</th>
                    <th class="text-right">{{ __('messages.shu_report.savings_balance') }}</th>
                    <th class="text-right">{{ __('messages.shu_report.total_transactions') }}</th>
                    <th class="text-right">{{ __('messages.shu_report.jasa_modal_short') }}</th>
                    <th class="text-right">{{ __('messages.shu_report.jasa_usaha_short') }}</th>
                    <th class="text-right">Total SHU</th>
                </tr>
            </thead>
            <tbody>
                @foreach($distributions as $index => $dist)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $dist->member->member_id ?? '-' }}</td>
                    <td>{{ $dist->member->user->name ?? '-' }}</td>
                    <td class="text-right">{{ number_format($dist->total_savings, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($dist->total_transactions, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($dist->shu_savings, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($dist->shu_transactions, 0, ',', '.') }}</td>
                    <td class="text-right font-bold">{{ number_format($dist->total_shu, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" class="text-right">{{ __('messages.shu_report.total') }}</td>
                    <td class="text-right">{{ number_format($distributions->sum('shu_savings'), 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($distributions->sum('shu_transactions'), 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($distributions->sum('total_shu'), 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        <!-- Signature -->
        <div class="footer">
            <div class="signature-box">
                <p>{{ __('messages.shu_report.knowing') }},</p>
                <p><strong>{{ __('messages.shu_report.chairperson') }}</strong></p>
                <div class="signature-line"></div>
                <p>(_______________________)</p>
            </div>
            <div class="signature-box">
                <p>Jakarta, {{ now()->format('d F Y') }}</p>
                <p><strong>{{ __('messages.shu_report.treasurer') }}</strong></p>
                <div class="signature-line"></div>
                <p>(_______________________)</p>
            </div>
        </div>

        <p style="text-align: center; margin-top: 30px; font-size: 9px; color: #999;">
            {{ __('messages.shu_report.printed_by', ['date' => now()->format('d/m/Y H:i')]) }}
        </p>
    </div>
</body>
</html>
