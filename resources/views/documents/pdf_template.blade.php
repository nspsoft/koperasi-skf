<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #333;
            margin: 0.5cm;
        }
        .header-table {
            width: 100%;
            margin-bottom: 2px;
        }
        .logo-cell {
            width: 80px;
            text-align: center;
            vertical-align: middle;
            padding-bottom: 5px;
        }
        .header-text {
            text-align: center;
            vertical-align: middle;
            padding: 0 5px 5px 5px;
        }
        .header-text h1 {
            font-size: 14pt;
            margin: 0;
            padding: 0;
            line-height: 1.1;
            text-transform: uppercase;
        }
        .header-text h2 {
            font-size: 11pt;
            margin: 1px 0;
            padding: 0;
            line-height: 1.1;
            font-weight: bold;
        }

        .content {
            margin-top: 20px;
        }
        table {
            border-collapse: collapse;
        }
        @page {
            margin: 1.5cm 2cm 3cm 2cm; /* Increased bottom margin for footer */
        }
        .footer {
            position: fixed;
            bottom: -2cm;
            left: 0cm;
            right: 0cm;
            height: 2.5cm;
            text-align: center;
            font-size: 8pt;
            color: #555;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
        .footer table {
            width: 100%;
        }
        .footer-qr {
            width: 70px;
            vertical-align: middle;
        }
        .footer-text {
            vertical-align: middle;
            text-align: left;
            padding-left: 10px;
        }
    </style>
</head>
<body>
    <div class="footer">
        <table>
            <tr>
                <td class="footer-qr">
                    @if(isset($qrCode) && $qrCode)
                        <img src="{{ $qrCode }}" style="width: 60px; height: 60px;">
                    @else
                        <div style="width: 60px; height: 60px; border: 1px dotted #ccc; display: flex; align-items: center; justify-content: center; font-size: 8px; color: #999;">No QR</div>
                    @endif
                </td>
                <td class="footer-text">
                    <strong>Dokumen ini sah dan diterbitkan secara digital oleh Koperasi Spindo.</strong><br>
                    Scan QR Code ini untuk memastikan keaslian dokumen.<br>
                    Document ID: {{ $documentId ?? '-' }} | Tanggal Cetak: {{ date('d/m/Y H:i') }}
                </td>
            </tr>
        </table>
    </div>
    <table class="header-table">
        <tr>
            <td class="logo-cell">
                @if($logo1)
                    <img src="{{ $logo1 }}" style="max-height: 75px; width: auto; display: block; margin: 0 auto;">
                @else
                    <div style="width: 70px; height: 70px; border: 1px dashed #ccc; line-height: 70px; font-size: 8pt; color: #999;">LOGO 1</div>
                @endif
            </td>
            <td class="header-text">
                <h1>KOPERASI KARYAWAN</h1>
                <h1>SPINDO KARAWANG FACTORY</h1>
                <h2>PT Steel Pipe Industry of Indonesia Tbk</h2>
            </td>
            <td class="logo-cell">
                @if($logo2)
                    <img src="{{ $logo2 }}" style="max-height: 75px; width: auto; display: block; margin: 0 auto;">
                @else
                    <div style="width: 70px; height: 70px; border: 1px dashed #ccc; line-height: 70px; font-size: 8pt; color: #999;">LOGO 2</div>
                @endif
            </td>
        </tr>
    </table>

    <div style="border-top: 3px solid #000; border-bottom: 1px solid #000; padding: 4px 0; text-align: center; margin-bottom: 20px;">
        <p style="font-size: 9pt; font-weight: bold; margin: 0;">Jl. Mitra Raya Blok F2 Kawasan Industri Mitra Karawang, Ds. Parungmulya Kec. Ciampel Karawang</p>
    </div>

    <div class="content">
        {!! $content !!}
    </div>
</body>
</html>
