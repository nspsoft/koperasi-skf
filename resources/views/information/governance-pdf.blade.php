<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Dokumen Resmi Tugas & Wewenang - {{ $settings['coop_name'] ?? 'Koperasi' }}</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 9pt;
            line-height: 1.3;
            color: #1a1a1a;
            margin: 0;
        }
        @page {
            margin: 1cm 1.5cm 2cm 1.5cm;
        }
        .header-table {
            width: 100%;
            border-bottom: 2.5pt solid #000;
            margin-bottom: 1pt;
        }
        .header-sub-border {
            border-top: 0.5pt solid #000;
            margin-bottom: 15px;
            width: 100%;
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
        }
        .header-text h1 {
            font-size: 14pt;
            margin: 0;
            text-transform: uppercase;
            color: #003366;
            font-weight: bold;
        }
        .header-text h2 {
            font-size: 12pt;
            margin: 1px 0;
            text-transform: uppercase;
            color: #003366;
            font-weight: bold;
        }
        .header-text h3 {
            font-size: 10pt;
            margin: 1px 0;
            text-transform: uppercase;
            color: #003366;
        }
        .header-text p {
            font-size: 7.5pt;
            margin: 2px 0;
            color: #444;
        }
        .doc-title {
            text-align: center;
            font-size: 13pt;
            font-weight: bold;
            margin: 15px 0 2px 0;
            text-decoration: underline;
            text-transform: uppercase;
        }
        .doc-subtitle {
            text-align: center;
            font-size: 8.5pt;
            margin-bottom: 20px;
            font-style: italic;
        }
        .section-header {
            background-color: #003366;
            color: #ffffff;
            padding: 4px 10px;
            font-size: 10pt;
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 8px;
            text-transform: uppercase;
        }
        .sub-header {
            font-weight: bold;
            font-size: 9.5pt;
            border-bottom: 1pt solid #ccc;
            margin-bottom: 5px;
            margin-top: 10px;
            color: #003366;
        }
        .highlight-box {
            background-color: #f1f5f9;
            border-left: 3pt solid #003366;
            padding: 6px 10px;
            margin: 8px 0;
            font-size: 8.5pt;
        }
        .grid-table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
        }
        .grid-table td {
            width: 50%;
            vertical-align: top;
            padding: 0 5px;
        }
        .role-box {
            border: 0.5pt solid #ddd;
            padding: 6px;
            margin-bottom: 8px;
        }
        .role-title {
            font-weight: bold;
            font-size: 9pt;
            color: #003366;
            margin-bottom: 3px;
            text-transform: uppercase;
        }
        ul {
            margin: 2px 0;
            padding-left: 15px;
        }
        li {
            margin-bottom: 1.5px;
            text-align: justify;
            font-size: 8.5pt;
        }
        .page-break {
            page-break-after: always;
        }
        .footer {
            position: fixed;
            bottom: -1.2cm;
            left: 0;
            right: 0;
            height: 1.5cm;
            border-top: 0.5pt solid #ccc;
            padding-top: 5px;
        }
        .footer-table {
            width: 100%;
        }
        .footer-qr {
            width: 50px;
        }
        .footer-info {
            font-size: 7pt;
            color: #777;
        }
        .text-blue { color: #2563eb; }
        .text-green { color: #16a34a; }
        .text-amber { color: #d97706; }
        .text-purple { color: #9333ea; }
        .text-rose { color: #e11d48; }
        .font-bold { font-weight: bold; }
        .page-count:after { content: counter(page); }
    </style>
</head>
<body>
    <header>
        <table class="header-table">
            <tr>
                <td class="logo-cell">
                    @if(isset($logo1) && $logo1)
                        <img src="{{ $logo1 }}" height="55">
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
                        <img src="{{ $logo2 }}" height="55">
                    @endif
                </td>
            </tr>
        </table>
        <div class="header-sub-border"></div>
    </header>

    <div class="doc-title">TUGAS, WEWENANG & TANGGUNG JAWAB</div>
    <div class="doc-subtitle">Berdasarkan AD/ART dan UU No. 25 Tahun 1992 tentang Perkoperasian</div>

    <!-- I. STRUKTUR ORGANISASI -->
    <div class="section">
        <div class="section-header">I. STRUKTUR ORGANISASI KOPERASI</div>
        
        <table class="grid-table">
            <tr>
                <td>
                    <div class="role-box">
                        <div class="role-title text-blue">1. PEMBINA</div>
                        <ul>
                            <li>Memberikan bimbingan dan arahan strategis kepada pengurus.</li>
                            <li>Memberikan nasihat/saran dalam pengambilan keputusan besar.</li>
                            <li>Menjadi penengah bila terjadi perselisihan internal.</li>
                            <li>Memberikan perlindungan dan dukungan institusional.</li>
                            <li>Mewakili kepentingan koperasi di tingkat manajemen perusahaan.</li>
                        </ul>
                    </div>
                </td>
                <td>
                    <div class="role-box">
                        <div class="role-title text-amber">2. PENGAWAS (Pasal 38-40)</div>
                        <ul>
                            <li>Mengawasi pelaksanaan kebijakan dan pengelolaan koperasi.</li>
                            <li>Meneliti catatan dan pembukuan keuangan secara berkala.</li>
                            <li>Membuat laporan tertulis hasil pengawasan untuk RAT.</li>
                            <li>Menjaga kepatuhan terhadap AD/ART dan peraturan.</li>
                            <li>Bertanggung jawab langsung kepada Rapat Anggota.</li>
                        </ul>
                    </div>
                </td>
            </tr>
        </table>

        <div class="role-box">
            <div class="role-title text-purple">3. PENGURUS - PELAKSANA OPERASIONAL UTAMA</div>
            <table class="grid-table">
                <tr>
                    <td>
                        <div class="font-bold underline mb-1">Ketua:</div>
                        <ul>
                            <li>Memimpin dan mengkoordinasikan seluruh kegiatan usaha.</li>
                            <li>Memimpin Rapat Anggota dan Rapat Pengurus.</li>
                            <li>Mewakili Koperasi di dalam dan luar pengadilan.</li>
                        </ul>
                        <div class="font-bold underline mb-1 mt-2">Wakil Ketua:</div>
                        <ul>
                            <li>Membantu Ketua dan menggantikan bila berhalangan.</li>
                            <li>Mengkoordinasikan kegiatan operasional harian.</li>
                        </ul>
                    </td>
                    <td>
                        <div class="font-bold underline mb-1">Sekretaris:</div>
                        <ul>
                            <li>Menyelenggarakan administrasi umum & persuratan.</li>
                            <li>Mengelola Buku Daftar Anggota & Pengurus.</li>
                            <li>Menyiapkan notulen, undangan, dan materi rapat.</li>
                        </ul>
                        <div class="font-bold underline mb-1 mt-2">Bendahara:</div>
                        <ul>
                            <li>Mengelola keuangan, simpanan, dan pinjaman anggota.</li>
                            <li>Menyusun laporan keuangan, neraca, dan SHU.</li>
                            <li>Menyelenggarakan pembukuan keuangan tertib.</li>
                        </ul>
                    </td>
                </tr>
            </table>
        </div>

        <div class="sub-header text-rose">BAGIAN OPERASIONAL & PELAKSANA USAHA</div>
        <table class="grid-table">
            <tr>
                <td>
                    <div class="font-bold text-xs">A. Unit Toko / Waserda:</div>
                    <ul style="font-size: 8pt;">
                        <li>Mengelola stok barang dan inventaris usaha.</li>
                        <li>Melayani transaksi penjualan kepada anggota.</li>
                        <li>Membuat laporan penjualan harian/bulanan.</li>
                    </ul>
                </td>
                <td>
                    <div class="font-bold text-xs">B. Unit Simpan Pinjam:</div>
                    <ul style="font-size: 8pt;">
                        <li>Memproses pengajuan pinjaman anggota.</li>
                        <li>Mengelola pembayaran angsuran dan tabungan.</li>
                        <li>Mencatat mutasi transaksi simpanan anggota.</li>
                    </ul>
                </td>
            </tr>
        </table>
    </div>

    <!-- II. RAPAT ANGGOTA -->
    <div class="section">
        <div class="section-header">II. RAPAT ANGGOTA (KEKUASAAN TERTINGGI)</div>
        <div class="highlight-box">
            <strong>Pasal 23:</strong> Rapat Anggota merupakan kekuasaan tertinggi, wadah anggota untuk membahas dan memutuskan kebijakan fundamental koperasi.
        </div>
        
        <div class="font-bold mb-1">Wewenang Rapat Anggota (Pasal 24):</div>
        <table class="grid-table">
            <tr>
                <td>
                    <ul>
                        <li>Menetapkan/mengubah Anggaran Dasar.</li>
                        <li>Menetapkan kebijakan umum organisasi & usaha.</li>
                        <li>Memilih/memberhentikan Pengurus & Pengawas.</li>
                    </ul>
                </td>
                <td>
                    <ul>
                        <li>Mengesahkan Rencana Kerja, RAPB dan Keuangan.</li>
                        <li>Menetapkan pembagian SHU (Sisa Hasil Usaha).</li>
                        <li>Memutuskan penggabungan atau pembubaran.</li>
                    </ul>
                </td>
            </tr>
        </table>

        <table class="grid-table">
            <tr>
                <td>
                    <div class="sub-header">Bentuk Rapat (Pasal 25-27)</div>
                    <ul>
                        <li><strong>RAT (Tahunan):</strong> Wajib tiap tahun, maks 3 bulan setelah tutup buku.</li>
                        <li><strong>Rapat Luar Biasa:</strong> Atas permintaan anggota/pengurus untuk hal mendesak.</li>
                        <li><strong>Pengambilan Keputusan:</strong> Musyawarah mufakat, jika gagal maka Voting.</li>
                    </ul>
                </td>
                <td>
                    <div class="sub-header">Kuorum & Keabsahan (Pasal 28)</div>
                    <ul>
                        <li>Rapat sah bila dihadiri min. 50% + 1 dari jumlah anggota.</li>
                        <li>Jika kuorum tidak tercapai, rapat ditunda dan keputusan rapat tunda tetap sah sesuai AD.</li>
                        <li>Tiap anggota memiliki hak 1 suara (One Man One Vote).</li>
                    </ul>
                </td>
            </tr>
        </table>
    </div>

    <div class="page-break"></div>

    <!-- III. DETAIL TUGAS PENGURUS -->
    <div class="section">
        <div class="section-header">III. RINCIAN TUGAS, WEWENANG & TANGGUNG JAWAB PENGURUS</div>
        <div class="highlight-box">
            <strong>Pasal 30:</strong> Pengurus mengelola Koperasi, mewakili kepentingan hukum, dan bertanggung jawab penuh secara kolegial maupun individual atas kerugian akibat kelalaian (Pasal 34).
        </div>

        <div class="role-box">
            <div class="role-title">1. KETUA</div>
            <div class="grid-table">
                <div style="width:100%; font-size: 8.5pt;">
                    <strong>Tugas Utama:</strong> Memimpin jalannya koperasi, memimpin rapat-rapat, menyusun rencana kerja strategis, menandatangani surat penting, dan menyampaikan LPJ ke RAT.
                    <br><strong>Wewenang:</strong> Mewakili koperasi di dalam/luar pengadilan, mengambil keputusan strategis mendesak, dan membina hubungan eksternal.
                    <br><strong>Tanggung Jawab:</strong> Kelancaran organisasi, kepatuhan AD/ART, dan integritas aset koperasi.
                </div>
            </div>
        </div>

        <div class="role-box">
            <div class="role-title">2. WAKIL KETUA</div>
            <div class="grid-table">
                <div style="width:100%; font-size: 8.5pt;">
                    <strong>Tugas Utama:</strong> Membantu Ketua dalam menjalankan tugas, menggantikan Ketua apabila berhalangan hadir, dan mengkoordinasikan kegiatan operasional sehari-hari.
                    <br><strong>Wewenang:</strong> Menjalankan wewenang Ketua saat Ketua berhalangan, menandatangani surat bersama Ketua, dan memimpin rapat internal.
                    <br><strong>Tanggung Jawab:</strong> Mendukung Ketua dalam pencapaian target kerja dan kelancaran operasional.
                </div>
            </div>
        </div>

        <div class="role-box">
            <div class="role-title">3. SEKRETARIS</div>
            <div class="grid-table">
                <div style="width:100%; font-size: 8.5pt;">
                    <strong>Tugas Utama:</strong> Menyelenggarakan administrasi umum, Buku Daftar Anggota (BDA) & Pengurus (BDP), notulen rapat, persuratan, dan kearsipan dokumen penting.
                    <br><strong>Wewenang:</strong> Menandatangani surat bersama Ketua dan menerbitkan keterangan keanggotaan.
                    <br><strong>Tanggung Jawab:</strong> Ketertiban data, kerahasiaan dokumen anggota, dan komunikasi organisasi.
                </div>
            </div>
        </div>

        <div class="role-box">
            <div class="role-title">4. BENDAHARA</div>
            <div class="grid-table">
                <div style="width:100%; font-size: 8.5pt;">
                    <strong>Tugas Utama:</strong> Mengelola kas/bank, pembukuan tertib, simpan-pinjam, laporan keuangan bulanan, neraca akhir tahun, dan inventarisasi aset.
                    <br><strong>Wewenang:</strong> Menandatangani bukti transaksi, mengelola rekening bank bersama Ketua, dan mengatur penempatan dana.
                    <br><strong>Tanggung Jawab:</strong> Keamanan uang/harta koperasi, keakuratan laporan keuangan, dan kepatuhan pajak.
                </div>
            </div>
        </div>
        
        <div class="role-box">
            <div class="role-title">5. BAGIAN OPERASIONAL & USAHA</div>
            <div class="grid-table">
                <div style="width:100%; font-size: 8.5pt;">
                    <strong>Tugas Utama:</strong> Melaksanakan kegiatan usaha (Toko/Simpan Pinjam), melayani anggota secara langsung, dan mengelola stok/inventaris harian.
                    <br><strong>Wewenang:</strong> Melakukan transaksi sesuai SOP, menolak transaksi yang tidak sesuai prosedur, dan mengusulkan pengadaan barang.
                    <br><strong>Tanggung Jawab:</strong> Kelancaran pelayanan anggota, kesesuaian kas harian, dan keamanan stok barang/uang di unit masing-masing.
                </div>
            </div>
        </div>

        <div class="highlight-box" style="background-color: #fffbeb; border-color: #f59e0b;">
            <strong>Buku Wajib Pengurus:</strong> (1) Buku Daftar Anggota, (2) Buku Daftar Pengurus, (3) Buku Notulen Rapat, (4) Buku Kas/Bank, (5) Buku Simpanan, (6) Buku Pinjaman, (7) Buku Inventaris.
        </div>
    </div>

    <!-- IV. ANGGOTA -->
    <div class="section">
        <div class="section-header">IV. HAK & KEWAJIBAN ANGGOTA (Pasal 17-20)</div>
        <table class="grid-table">
            <tr>
                <td style="border-right: 0.5pt solid #eee;">
                    <div class="font-bold text-blue underline mb-1">HAK ANGGOTA:</div>
                    <ul>
                        <li>Menghadiri, berpendapat, & voting di Rapat Anggota.</li>
                        <li>Memilih & dipilih menjadi Pengurus atau Pengawas.</li>
                        <li>Meminta diadakannya Rapat Anggota sesuai AD.</li>
                        <li>Mendapatkan pelayanan & pemanfaatan usaha.</li>
                        <li>Mendapatkan SHU & keterangan perkembangan.</li>
                    </ul>
                </td>
                <td>
                    <div class="font-bold text-rose underline mb-1">KEWAJIBAN ANGGOTA:</div>
                    <ul>
                        <li>Mematuhi AD/ART dan keputusan Rapat Anggota.</li>
                        <li>Membayar Simpanan Pokok dan Simpanan Wajib.</li>
                        <li>Berpartisipasi aktif dalam kegiatan usaha.</li>
                        <li>Menjaga nama baik & kebersamaan (kekeluargaan).</li>
                        <li>Menanggung kerugian sesuai ketentuan AD/ART.</li>
                    </ul>
                </td>
            </tr>
        </table>

        <div class="sub-header">Berakhirnya Keanggotaan (Pasal 19)</div>
        <p style="font-size: 8.5pt;">Keanggotaan berakhir karena: (a) Meninggal dunia, (b) Mengundurkan diri atas permintaan sendiri, (c) Diberhentikan karena pelanggaran AD/ART atau tidak memenuhi syarat lagi.</p>
    </div>

    <div class="footer">
        <table class="footer-table">
            <tr>
                <td class="footer-qr">
                    @if($qrCode)
                        <img src="{{ $qrCode }}" width="45" height="45">
                    @endif
                </td>
                <td class="footer-info">
                    <div class="font-bold">VERIFIKASI DOKUMEN DIGITAL</div>
                    <div>Dicetak pada: {{ date('d/m/Y H:i') }} | ID: DOC-GV-{{ date('Ymd') }}</div>
                    <div>Dokumen ini sah secara hukum sebagai referensi tata kelola Koperasi Karyawan SPINDO.</div>
                    <div>Halaman <span class="page-count"></span></div>
                </td>
                <td style="text-align: right; vertical-align: bottom; font-size: 7pt; color: #999;">
                    &copy; {{ date('Y') }} {{ $settings['coop_name'] ?? 'Koperasi Karyawan' }} - Sistem Informasi Koperasi Digital
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
