<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>AD-ART {{ $settings['coop_name'] ?? 'Koperasi' }}</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt;
            line-height: 1.5;
            color: #333;
            margin: 0.5cm;
        }
        @page {
            margin: 1.5cm 2cm 3cm 2cm;
        }
        .header-table {
            width: 100%;
            border-bottom: 3.5pt solid #000;
            margin-bottom: 2pt;
        }
        .header-sub-border {
            border-top: 1pt solid #000;
            margin-bottom: 15px;
            width: 100%;
        }
        .logo-cell {
            width: 100px;
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
            font-size: 16pt;
            margin: 0;
            padding: 0;
            line-height: 1.2;
            text-transform: uppercase;
            color: #003366;
            font-weight: bold;
        }
        .header-text h2 {
            font-size: 14pt;
            margin: 1px 0;
            padding: 0;
            line-height: 1.2;
            text-transform: uppercase;
            color: #003366;
            font-weight: bold;
        }
        .header-text h3 {
            font-size: 12pt;
            margin: 1px 0;
            padding: 0;
            line-height: 1.2;
            text-transform: uppercase;
            color: #003366;
            font-weight: bold;
        }
        .header-text p {
            font-size: 8pt;
            margin: 2px 0 0 0;
            line-height: 1.2;
            color: #003366;
            text-align: center;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            background-color: #16a34a;
            color: white;
            padding: 8px 12px;
            font-size: 11pt;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .section-content {
            padding: 10px 15px;
            background-color: #f9fafb;
            border-left: 3px solid #16a34a;
        }
        .article {
            margin-bottom: 15px;
        }
        .article-title {
            font-weight: bold;
            margin-bottom: 5px;
            color: #16a34a;
        }
        ul, ol {
            margin: 5px 0;
            padding-left: 25px;
        }
        li {
            margin-bottom: 3px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        table.data-table th {
            background-color: #16a34a;
            color: white;
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
        .page-number:after { content: counter(page); }
        .page-break { page-break-before: always; }
        .version-info {
            text-align: center;
            margin-bottom: 20px;
            font-weight: bold;
            color: #16a34a;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        .info-box {
            background-color: #dbeafe;
            border-left: 4px solid #3b82f6;
            padding: 10px;
            margin: 10px 0;
            font-size: 9pt;
        }
    </style>
</head>
<body>
    <div class="footer">
        <table>
            <tr>
                <td class="footer-qr">
                    @if(isset($qrCode))
                        <img src="{{ $qrCode }}" style="width: 60px; height: 60px;">
                    @endif
                </td>
                <td class="footer-text">
                    <strong>Dokumen Resmi Koperasi Spindo: Versi {{ $settings['ad_art_version'] ?? '3.0' }} - Disahkan {{ $settings['ad_art_ratification_date'] ?? '15 Januari 2026' }}</strong><br>
                    Scan QR Code ini untuk memastikan keaslian dokumen.<br>
                    Halaman <span class="page-number"></span> | Tanggal Cetak: {{ date('d/m/Y H:i') }}
                </td>
            </tr>
        </table>
    </div>

    <table class="header-table">
        <tr>
            <td class="logo-cell">
                @if(isset($logo1) && $logo1)
                    <img src="{{ $logo1 }}" style="max-height: 85px; width: auto;">
                @endif
            </td>
            <td class="header-text">
                <h1>KOPERASI KARYAWAN</h1>
                <h2>SPINDO KARAWANG FACTORY</h2>
                <h3>PT STEEL PIPE INDUSTRY OF INDONESIA TBK</h3>
                <p>Jl. Mitra Raya Blok F2 Kawasan Industri Mitra Karawang, Ds. Parungmulya Kec. Ciampel Karawang</p>
            </td>
            <td class="logo-cell">
                @if(isset($logo2) && $logo2)
                    <img src="{{ $logo2 }}" style="max-height: 85px; width: auto;">
                @endif
            </td>
        </tr>
    </table>
    <div class="header-sub-border"></div>

    <div class="version-info">
        ANGGARAN DASAR & ANGGARAN RUMAH TANGGA<br>
        VERSI {{ $settings['ad_art_version'] ?? '3.0' }} ({{ $settings['ad_art_ratification_date'] ?? '15 Januari 2026' }})
    </div>

    <!-- ANGGARAN DASAR -->
    <h2 style="text-align: center; color: #16a34a; margin-bottom: 20px; text-transform: uppercase;">ANGGARAN DASAR</h2>

    <div class="section">
        <div class="section-title">BAB I: NAMA, TEMPAT & WILAYAH KERJA</div>
        <div class="section-content">
            <div class="article">
                <div class="article-title">Pasal 1</div>
                <ol>
                    <li>Koperasi ini bernama <strong>{{ $settings['coop_name'] ?? 'KOPERASI KARYAWAN PT. SPINDO TBK' }}</strong>.</li>
                    <li>Koperasi berkedudukan di {{ $settings['coop_address'] ?? 'Karawang, Jawa Barat' }}.</li>
                    <li>Wilayah kerja meliputi seluruh unit kerja {{ $settings['coop_name'] ?? 'PT. SPINDO TBK' }}.</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">BAB II: LANDASAN, ASAS & PRINSIP</div>
        <div class="section-content">
            <div class="article">
                <div class="article-title">Pasal 2 - Landasan & Asas</div>
                <p>Koperasi berlandaskan Pancasila dan UUD 1945 serta berasaskan kekeluargaan.</p>
            </div>
            <div class="article">
                <div class="article-title">Pasal 3 - Prinsip Koperasi</div>
                <ul>
                    <li>Keanggotaan bersifat sukarela dan terbuka</li>
                    <li>Pengelolaan dilakukan secara demokratis</li>
                    <li>Pembagian SHU secara adil sesuai jasa anggota</li>
                    <li>Pemberian balas jasa terbatas terhadap modal</li>
                    <li>Kemandirian</li>
                    <li>Pendidikan perkoperasian</li>
                    <li>Kerjasama antar koperasi</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">BAB III: TUJUAN & KEGIATAN USAHA</div>
        <div class="section-content">
            <div class="article">
                <div class="article-title">Pasal 4 - Tujuan</div>
                <p>Memajukan kesejahteraan anggota dan masyarakat serta membangun tatanan perekonomian nasional.</p>
            </div>
            <div class="article">
                <div class="article-title">Pasal 5 - Kegiatan Usaha</div>
                <ul>
                    <li>Usaha Simpan Pinjam</li>
                    <li>Usaha Pertokoan (Koperasi Mart)</li>
                    <li>Usaha Jasa Keuangan</li>
                    <li>Usaha lain yang sah dan bermanfaat</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">BAB IV: KEANGGOTAAN</div>
        <div class="section-content">
            <div class="article">
                <div class="article-title">Pasal 6 - Syarat Keanggotaan</div>
                <ul>
                    <li>Karyawan tetap {{ $settings['coop_name'] ?? 'PT. SPINDO TBK' }}</li>
                    <li>Mengajukan permohonan secara tertulis</li>
                    <li>Menyetujui isi AD-ART</li>
                    <li>Membayar simpanan pokok dan wajib</li>
                </ul>
            </div>
            <div class="article">
                <div class="article-title">Pasal 7 - Hak Anggota</div>
                <ul>
                    <li>Menghadiri dan bersuara dalam RAT</li>
                    <li>Memilih dan dipilih sebagai pengurus/pengawas</li>
                    <li>Mendapat pelayanan yang sama</li>
                    <li>Mendapat SHU</li>
                </ul>
            </div>
            <div class="article">
                <div class="article-title">Pasal 8 - Kewajiban Anggota</div>
                <ul>
                    <li>Mematuhi AD-ART dan keputusan RAT</li>
                    <li>Berpartisipasi dalam kegiatan usaha</li>
                    <li>Membayar simpanan tepat waktu</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">BAB V: MODAL</div>
        <div class="section-content">
            <div class="article">
                <div class="article-title">Pasal 9 - Sumber Modal</div>
                <ul>
                    <li><strong>Modal Sendiri:</strong> Simpanan Pokok, Simpanan Wajib, Dana Cadangan, Hibah</li>
                    <li><strong>Modal Pinjaman:</strong> Anggota, Bank, Lembaga Keuangan lain</li>
                </ul>
                <div class="info-box">
                    <p><strong>Simpanan Pokok:</strong> Rp {{ number_format($settings['saving_principal'] ?? 100000, 0, ',', '.') }} (dibayar sekali)</p>
                    <p><strong>Simpanan Wajib:</strong> Rp {{ number_format($settings['saving_mandatory'] ?? 50000, 0, ',', '.') }}/bulan</p>
                </div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">BAB VI: SISA HASIL USAHA (SHU)</div>
        <div class="section-content">
            <div class="article">
                <div class="article-title">Pasal 10 - Pembagian SHU</div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Alokasi</th>
                            <th style="text-align: right;">Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>Dana Cadangan</td><td style="text-align: right;">{{ $shuSetting->persen_cadangan ?? 25 }}%</td></tr>
                        <tr><td>Jasa Modal</td><td style="text-align: right;">{{ $shuSetting->persen_jasa_modal ?? 20 }}%</td></tr>
                        <tr><td>Jasa Usaha Anggota</td><td style="text-align: right;">{{ $shuSetting->persen_jasa_usaha ?? 30 }}%</td></tr>
                        <tr><td>Dana Pengurus</td><td style="text-align: right;">{{ $shuSetting->persen_pengurus ?? 10 }}%</td></tr>
                        <tr><td>Dana Karyawan</td><td style="text-align: right;">{{ $shuSetting->persen_karyawan ?? 5 }}%</td></tr>
                        <tr><td>Dana Pendidikan</td><td style="text-align: right;">{{ $shuSetting->persen_pendidikan ?? 5 }}%</td></tr>
                        <tr><td>Dana Sosial</td><td style="text-align: right;">{{ $shuSetting->persen_sosial ?? 3 }}%</td></tr>
                        <tr><td>Dana Pembangunan</td><td style="text-align: right;">{{ $shuSetting->persen_pembangunan ?? 2 }}%</td></tr>
                        <tr style="background-color: #dcfce7; font-weight: bold;">
                            <td>TOTAL</td>
                            <td style="text-align: right;">{{ $shuSetting ? $shuSetting->total_persen : 100 }}%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="page-break"></div>

    <!-- ANGGARAN RUMAH TANGGA -->
    <h2 style="text-align: center; color: #3b82f6; margin-bottom: 20px;">ANGGARAN RUMAH TANGGA</h2>

    <div class="section">
        <div class="section-title" style="background-color: #3b82f6;">BAB I: KEANGGOTAAN</div>
        <div class="section-content" style="border-left-color: #3b82f6;">
            <div class="article">
                <div class="article-title" style="color: #3b82f6;">Pasal 1 - Prosedur Pendaftaran</div>
                <ol>
                    <li>Mengisi formulir pendaftaran</li>
                    <li>Menyerahkan fotokopi KTP dan ID Karyawan</li>
                    <li>Membayar simpanan pokok</li>
                    <li>Menandatangani surat pernyataan</li>
                </ol>
            </div>
            <div class="article">
                <div class="article-title" style="color: #3b82f6;">Pasal 2 - Berakhirnya Keanggotaan</div>
                <ul>
                    <li>Meninggal dunia</li>
                    <li>Mengundurkan diri</li>
                    <li>Diberhentikan karena melanggar AD-ART</li>
                    <li>Berhenti sebagai karyawan</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title" style="background-color: #3b82f6;">BAB II: SIMPANAN</div>
        <div class="section-content" style="border-left-color: #3b82f6;">
            <div class="article">
                <div class="article-title" style="color: #3b82f6;">Pasal 3 - Jenis Simpanan</div>
                <table class="data-table">
                    <tr>
                        <th>Jenis Simpanan</th>
                        <th>Ketentuan</th>
                    </tr>
                    <tr>
                        <td>Simpanan Pokok</td>
                        <td>Rp {{ number_format($settings['saving_principal'] ?? 100000, 0, ',', '.') }} (1x)</td>
                    </tr>
                    <tr>
                        <td>Simpanan Wajib</td>
                        <td>Rp {{ number_format($settings['saving_mandatory'] ?? 50000, 0, ',', '.') }}/bulan</td>
                    </tr>
                    <tr>
                        <td>Simpanan Sukarela</td>
                        <td>Min. Rp 10.000</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title" style="background-color: #3b82f6;">BAB III: PINJAMAN</div>
        <div class="section-content" style="border-left-color: #3b82f6;">
            <div class="article">
                <div class="article-title" style="color: #3b82f6;">Pasal 4 - Ketentuan Pinjaman</div>
                <ul>
                    <li>Maksimal pinjaman: Rp {{ number_format($settings['loan_limit_max'] ?? 50000000, 0, ',', '.') }}</li>
                    <li>Bunga: {{ $settings['loan_interest_regular'] ?? 1.5 }}% per bulan (flat)</li>
                    <li>Tenor: 3 - {{ $settings['loan_max_duration'] ?? 60 }} bulan</li>
                    <li>Agunan: Gaji bulanan (dipotong langsung)</li>
                </ul>
            </div>
            <div class="article">
                <div class="article-title" style="color: #3b82f6;">Pasal 5 - Prosedur Pengajuan</div>
                <ol>
                    <li>Mengisi formulir pinjaman</li>
                    <li>Mendapat persetujuan pengurus</li>
                    <li>Menandatangani perjanjian kredit</li>
                    <li>Dana cair dalam 3 hari kerja</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title" style="background-color: #3b82f6;">BAB IV: SANKSI</div>
        <div class="section-content" style="border-left-color: #3b82f6;">
            <div class="article">
                <div class="article-title" style="color: #3b82f6;">Pasal 6 - Jenis Sanksi</div>
                <ul>
                    <li>Teguran lisan</li>
                    <li>Teguran tertulis</li>
                    <li>Pembatasan hak pelayanan</li>
                    <li>Pemberhentian keanggotaan</li>
                </ul>
            </div>
        </div>
    </div>

    </div>
</body>
</html>
