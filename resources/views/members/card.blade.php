<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Karyawan & Anggota - {{ $member->user->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: #e2e8f0;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .card {
            width: 54mm;
            height: 86mm;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            position: relative;
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
            display: flex;
            flex-direction: column;
        }

        .header-wave {
            background: linear-gradient(160deg, #0a1628 0%, #1a3a5c 50%, #0f2744 100%);
            min-height: 80px;
            position: relative;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding-top: 10px;
        }
        .header-wave::after {
            content: '';
            position: absolute;
            bottom: -20px;
            left: 0;
            right: 0;
            height: 40px;
            background: #ffffff;
            border-radius: 50% 50% 0 0 / 100% 100% 0 0;
            border-top: 3px solid #c9a227;
        }

        .footer-wave {
            background: linear-gradient(160deg, #0a1628 0%, #1a3a5c 50%, #0f2744 100%);
            height: 32px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: auto;
            border-top: 2px solid #c9a227;
        }

        .photo-frame {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            border: 3.5px solid #c9a227;
            overflow: hidden;
            background: #f1f5f9;
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
            flex-shrink: 0;
        }

        .info-table {
            width: 100%;
        }
        .info-table tr td {
            font-size: 7.8px;
            color: #334155;
            padding: 2px 0;
            vertical-align: top;
        }
        .info-table tr td:first-child {
            font-weight: 600;
            color: #0f2744;
            width: 65px;
        }

        @media print {
            @page { size: 54mm 86mm; margin: 0; }
            body { background: white; }
            .no-print { display: none !important; }
            .card { box-shadow: none; page-break-after: always; }
        }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center justify-center p-6">

    <!-- Actions -->
    <div class="no-print mb-6 flex gap-3">
        <button onclick="window.print()" class="px-5 py-2.5 bg-blue-900 hover:bg-blue-800 text-white rounded-lg font-semibold flex items-center gap-2 transition shadow-lg">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Cetak
        </button>
        <a href="{{ route('members.digital-card', $member) }}" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-lg font-medium transition">
            Digital Card
        </a>
    </div>

    <p class="no-print text-slate-500 text-sm mb-4">Depan: ID Karyawan | Belakang: Member Koperasi</p>

    @php
        $employeeId = $member->employee_id ?? $member->member_id;
        $barcodeUrl = 'https://barcodeapi.org/api/code128/' . urlencode($employeeId);
        $logo = \App\Models\Setting::get('coop_logo');
        $coopName = \App\Models\Setting::get('coop_name', 'KOPERASI KARYAWAN SKF');
    @endphp

    <div class="flex flex-col md:flex-row gap-8 items-center">

        <!-- ========== FRONT: EMPLOYEE ID ========== -->
        <div class="card">
            <!-- Header -->
            <div class="header-wave">
                <div class="bg-white rounded-lg px-2.5 py-1.5 shadow-md text-center relative z-10">
                    <img src="{{ asset('images/spindo-logo.png') }}" alt="SPINDO" class="h-6 mx-auto">
                    <p class="text-[4.5px] text-slate-500 mt-0.5 leading-tight">PT. STEEL PIPE INDUSTRY OF INDONESIA, Tbk.</p>
                </div>
            </div>

            <!-- Content Area -->
            <div class="flex-1 flex flex-col px-3 pb-2">
                <!-- Photo -->
                <div class="flex justify-center mt-2 relative z-10">
                    <div class="photo-frame">
                        @if($member->photo)
                            <img src="{{ Storage::url($member->photo) }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-slate-200 to-slate-300">
                                <svg class="w-9 h-9 text-slate-400" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                </svg>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Name & Position -->
                <div class="text-center mt-2">
                    <h2 class="text-[12px] font-bold text-blue-900 leading-tight">{{ strtoupper($member->user->name) }}</h2>
                    <p class="text-[9px] text-[#c9a227] font-semibold">{{ $member->position ?? 'Staff' }}</p>
                </div>

                <!-- Info Table -->
                <div class="mt-2 px-1">
                    <table class="info-table">
                        <tr>
                            <td>NIK</td>
                            <td>: {{ $member->employee_id ?? $member->nik ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Departemen</td>
                            <td>: {{ $member->department ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Contact</td>
                            <td>: {{ $member->user->phone ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <td>: <span class="{{ $member->status == 'active' ? 'text-green-600' : 'text-red-600' }} font-bold">{{ $member->status == 'active' ? 'AKTIF' : 'NON-AKTIF' }}</span></td>
                        </tr>
                    </table>
                </div>

                <!-- Barcode -->
                <div class="mt-auto pt-1 text-center overflow-hidden" style="height: 28px;">
                    <img src="{{ $barcodeUrl }}" alt="Barcode" class="h-10 mx-auto" style="max-width: 120px; margin-top: -2px;">
                </div>
            </div>

            <!-- Footer -->
            <div class="footer-wave">
                <p class="text-[6.5px] text-white font-medium tracking-wider">KARTU IDENTITAS KARYAWAN</p>
            </div>
        </div>

        <!-- ========== BACK: KOPERASI MEMBER ========== -->
        <div class="card">
            <!-- Header -->
            <div class="header-wave">
                <div class="flex items-center gap-1.5 relative z-10">
                    <div class="w-8 h-8 rounded-full bg-white border-2 border-[#c9a227] flex items-center justify-center overflow-hidden shadow">
                        @if($logo)
                            <img src="{{ Storage::url($logo) }}" class="w-5 h-5 object-contain">
                        @else
                            <span class="text-blue-900 font-bold text-[8px]">KSK</span>
                        @endif
                    </div>
                    <div>
                        <h1 class="text-[9px] font-bold text-white leading-tight">{{ strtoupper($coopName) }}</h1>
                        <p class="text-[6px] text-blue-200">Member Card</p>
                    </div>
                </div>
            </div>

            <!-- Content Area -->
            <div class="flex-1 flex flex-col items-center px-3 pb-2">
                <!-- Badge -->
                <div class="bg-[#c9a227] text-white text-[7px] font-bold px-3 py-0.5 rounded-full shadow mt-1 mb-2">
                    SCAN QR CODE
                </div>

                <!-- QR Code -->
                <div class="bg-white rounded-xl p-2 shadow-lg border-2 border-[#0f2744]">
                    @if(isset($qrCode))
                        <img src="{{ $qrCode }}" alt="QR" class="w-[90px] h-[90px]">
                    @else
                        <div class="w-[90px] h-[90px] bg-gray-50 flex items-center justify-center">
                            <span class="text-gray-300 text-sm">QR</span>
                        </div>
                    @endif
                </div>

                <!-- Member ID -->
                <p class="text-[13px] font-mono font-bold text-blue-900 mt-1.5 tracking-[0.15em]">{{ $member->member_id }}</p>
                @if(isset($generatedDocument))
                    <p class="text-[7px] text-slate-500">{{ $generatedDocument->document_number }}</p>
                @endif

                <!-- Terms Box -->
                <div class="mt-auto w-full mb-1">
                    <div class="bg-slate-50 rounded-lg px-2.5 py-1.5 border border-slate-200">
                        <p class="text-[5.5px] text-slate-600 text-center leading-relaxed">
                            Kartu ini merupakan bukti keanggotaan Koperasi yang sah.<br>
                            Tunjukkan QR Code saat bertransaksi di Koperasi Mart.<br>
                            Jika hilang, segera hubungi pengurus.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="footer-wave">
                <p class="text-[6.5px] text-white font-medium tracking-wider">KARTU ANGGOTA KOPERASI</p>
            </div>
        </div>

    </div>

    <!-- Print Info -->
    <p class="no-print mt-6 text-slate-500 text-sm">💡 Cetak 2 sisi (duplex) • Kertas glossy 300gsm</p>

</body>
</html>
