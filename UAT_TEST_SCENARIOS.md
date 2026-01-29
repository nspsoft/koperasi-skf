# SKENARIO PENGUJIAN UAT - KOPERASI KARYAWAN SKF

## 1.1 Informasi Dokumen
- **Versi:** 2.0
- **Total Skenario:** 60+
- **Terakhir Diperbarui:** 18 Januari 2026

## 1.2 Cara Menggunakan Dokumen Ini
### Format Skenario Pengujian:
- **TC-XXX:** [Judul Test Case]
- **Modul:** [Nama Modul]
- **Prioritas:** P0 (Kritis) / P1 (Tinggi) / P2 (Sedang)
- **Prasyarat:** [Setup yang dibutuhkan]
- **Data Tes:** [Data spesifik yang digunakan]
- **Langkah-langkah:**
  1. [Aksi 1]
  2. [Aksi 2]
- **Hasil yang Diharapkan:**
  - [Output yang diharapkan]
- **Hasil Aktual:** [Diisi Tester]
- **Status:** [ ] Pass / [ ] Fail / [ ] Blocked

---

## 1.3 MODULE 0: OTENTIKASI (AUTHENTICATION)

### TC-001: Login Pengguna - Kredensial Valid
- **Modul:** Otentikasi
- **Prioritas:** P0 (Kritis)
- **Prasyarat:** Akun pengguna tersedia dan aktif
- **Data Tes:** Email: `member1@uat.kopkarskf.com`, Password: `Test@123`
- **Langkah-langkah:**
  1. Buka `https://uat.kopkarskf.com`
  2. Masukkan email di kolom "Email"
  3. Masukkan password di kolom "Password"
  4. Klik tombol "Masuk"
- **Hasil yang Diharapkan:**
  - Redirect ke dashboard
  - Nama pengguna tampil di pojok kanan atas
  - Menu sesuai hak akses (role) tampil
  - Notifikasi selamat datang muncul

### TC-002: Login Pengguna - Password Salah
- **Modul:** Otentikasi
- **Prioritas:** P0 (Kritis)
- **Prasyarat:** Akun pengguna tersedia
- **Data Tes:** Email: `member1@uat.kopkarskf.com`, Password: `WrongPassword123`
- **Langkah-langkah:**
  1. Buka halaman login
  2. Masukkan email valid
  3. Masukkan password salah
  4. Klik "Masuk"
- **Hasil yang Diharapkan:**
  - Login gagal
  - Pesan error: "Email atau password salah"
  - Pengguna tetap di halaman login
  - Kolom password dikosongkan

### TC-003: Alur Lupa Password
- **Modul:** Otentikasi
- **Prioritas:** P1 (Tinggi)
- **Langkah-langkah:**
  1. Klik "Lupa Password?" pada halaman login
  2. Masukkan alamat email
  3. Klik "Kirim Link Reset"
  4. Cek inbox email dan klik link
  5. Masukkan password baru (2x) dan simpan
- **Hasil yang Diharapkan:**
  - Email diterima < 2 menit
  - Link reset valid
  - Password berhasil diubah & bisa login

---

## 1.4 MODULE 1: DASHBOARD

### TC-004: Dashboard Load - Tampilan Admin
- **Modul:** Dashboard
- **Prioritas:** P0 (Kritis)
- **Prasyarat:** Login sebagai Admin
- **Langkah-langkah:**
  1. Login admin
  2. Amati waktu load dashboard
  3. Verifikasi widget tampil
- **Hasil yang Diharapkan:**
  - Load page < 3 detik
  - Total anggota akurat
  - Total simpanan & pinjaman tampil
  - Grafik render dengan benar

### TC-005: Update Data Real-time Dashboard
- **Modul:** Dashboard
- **Prioritas:** P2 (Sedang)
- **Langkah-langkah:**
  1. Catat "Total Transaksi Hari Ini"
  2. Buka tab baru, buat transaksi Mart
  3. Kembali ke tab dashboard, refresh page
- **Hasil yang Diharapkan:**
  - Counter bertambah 1
  - Total nominal transaksi update

---

## 1.5 MODULE 2: KEUANGAN

### TC-006: Lihat Saldo Simpanan
- **Modul:** Keuangan - Simpanan
- **Prioritas:** P0 (Kritis)
- **Langkah-langkah:**
  1. Klik "Keuangan" → "Simpanan"
  2. Cek tampilan saldo
- **Hasil yang Diharapkan:**
  - Total saldo tampil jelas
  - Breakdown: Pokok, Wajib, Sukarela terpisah
  - Tabel riwayat transaksi muncul

### TC-007: Riwayat Transaksi Simpanan
- **Modul:** Keuangan - Simpanan
- **Prioritas:** P1 (Tinggi)
- **Langkah-langkah:**
  1. Masuk menu Simpanan
  2. Scroll ke riwayat transaksi
  3. Klik salah satu untuk detail
  4. Coba filter tanggal & Export Excel
- **Hasil yang Diharapkan:**
  - Detail transaksi lengkap (Tgl, Tipe, Jumlah)
  - Modal detail muncul
  - Filter berfungsi
  - File Excel terunduh dengan data benar

### TC-008: Pengajuan Pinjaman - Happy Path
- **Modul:** Keuangan - Pinjaman
- **Prioritas:** P0 (Kritis)
- **Data Tes:** Nominal: 5jt, Tenor: 12 bln, Bunga: Flat
- **Langkah-langkah:**
  1. Klik "Keuangan" → "Pinjaman" → "+ Ajukan"
  2. Isi form lengkap
  3. Klik "Lanjut Tanda Tangan"
  4. Tanda tangan di canvas & Simpan
  5. Klik "Kirim Pengajuan"
- **Hasil yang Diharapkan:**
  - Validasi form jalan
  - Tanda tangan tersimpan
  - Status = "Menunggu Persetujuan"
  - Notifikasi email terkirim

### TC-009: Approval Pinjaman - Pengurus
- **Modul:** Keuangan - Pinjaman
- **Prioritas:** P0 (Kritis)
- **Langkah-langkah:**
  1. Login Pengurus
  2. Buka menu Pinjaman
  3. Pilih status "Menunggu Persetujuan"
  4. Review pengajuan & Klik "Approve"
- **Hasil yang Diharapkan:**
  - Status berubah jadi "Disetujui"
  - Email notifikasi ke member
  - Tercatat siapa yang approve

### TC-010: Reject Pinjaman dengan Alasan
- **Modul:** Keuangan - Pinjaman
- **Prioritas:** P1 (Tinggi)
- **Langkah-langkah:**
  1. Login Pengurus
  2. Pilih pengajuan
  3. Klik "Tolak"
  4. Masukkan alasan penolakan
- **Hasil yang Diharapkan:**
  - Status = "Ditolak"
  - Alasan wajib diisi
  - Notifikasi alasan terkirim ke member

### TC-011: Simulasi Pinjaman - Bunga Flat
- **Modul:** Keuangan - Simulasi
- **Prioritas:** P1 (Tinggi)
- **Langkah-langkah:**
  1. Buka menu Simulasi
  2. Input 10jt, 1.5% Flat, 12 Bulan
  3. Klik "Hitung"
- **Hasil yang Diharapkan:**
  - Angsuran per bulan akurat
  - Total bunga sesuai
  - Tabel angsuran 12 baris muncul

### TC-012: Simulasi - Efektif vs Anuitas
- **Modul:** Keuangan - Simulasi
- **Prioritas:** P2 (Sedang)
- **Langkah-langkah:**
  1. Hitung metode "Efektif"
  2. Hitung metode "Anuitas"
  3. Bandingkan total bayar
- **Hasil yang Diharapkan:**
  - Efektif: Angsuran menurun
  - Anuitas: Angsuran tetap
  - Perhitungan matematis valid

### TC-013: Pembayaran Angsuran
- **Modul:** Keuangan - Angsuran
- **Prioritas:** P0 (Kritis)
- **Langkah-langkah:**
  1. Login Admin
  2. Cari pinjaman aktif
  3. Klik "Bayar Angsuran"
  4. Input nominal & Simpan
- **Hasil yang Diharapkan:**
  - Status tagihan update jadi "Lunas"
  - Sisa hutang berkurang
  - Kuitansi ter-generate

### TC-014: Deteksi Keterlambatan (Overdue)
- **Modul:** Keuangan - Angsuran
- **Prioritas:** P1 (Tinggi)
- **Langkah-langkah:**
  1. Buka dashboard Angsuran
  2. Cek tab "Jatuh Tempo"
- **Hasil yang Diharapkan:**
  - Pinjaman telat di-highlight merah
  - Denda terhitung otomatis
  - Reminder terkirim

### TC-015: Request Penarikan Simpanan
- **Modul:** Keuangan - Penarikan
- **Prioritas:** P1 (Tinggi)
- **Langkah-langkah:**
  1. Login Member
  2. Menu "Tarik Saldo"
  3. Input nominal (<= saldo sukarela)
  4. Klik "Ajukan"
- **Hasil yang Diharapkan:**
  - Validasi saldo cukup
  - Status = "Menunggu Approval"
  - Saldo "Hold" sementara

### TC-016: Approve Penarikan - Multi Level
- **Modul:** Keuangan - Penarikan
- **Prioritas:** P1 (Tinggi)
- **Langkah-langkah:**
  1. Pengurus 1 setujui
  2. Admin transfer & klik "Complete"
- **Hasil yang Diharapkan:**
  - Status tracking jelas
  - Saldo berkurang permanen setelah Complete

### TC-017: Kalkulasi SHU Otomatis
- **Modul:** Keuangan - SHU
- **Prioritas:** P0 (Kritis)
- **Langkah-langkah:**
  1. Admin Setup SHU (Jasa Modal 25%, Jasa Usaha 55%)
  2. Klik "Hitung SHU"
  3. Klik "Distribusi"
- **Hasil yang Diharapkan:**
  - Nilai SHU per anggota valid sesuai andil
  - Total distribusi = Total Alokasi
  - Saldo masuk ke simpanan anggota

### TC-018: Cetak Slip SHU dengan QR
- **Modul:** Keuangan - SHU
- **Prioritas:** P1 (Tinggi)
- **Langkah-langkah:**
  1. Login member
  2. Menu SHU -> Download Slip
  3. Scan QR di slip
- **Hasil yang Diharapkan:**
  - PDF terunduh
  - Data di slip benar
  - Scan QR valid

---

## 1.6 MODULE 3: KOPERASI MART

### TC-019: POS - Transaksi Scan Barcode
- **Modul:** Mart - POS
- **Prioritas:** P0 (Kritis)
- **Langkah-langkah:**
  1. Login Kasir
  2. Scan barcode produk
  3. Ubah Qty jadi 2
  4. Bayar Tunai & Cetak Struk
- **Hasil yang Diharapkan:**
  - Produk masuk keranjang otomatis
  - Total harga benar
  - Stok berkurang
  - Struk tercetak

### TC-020: POS - Belanja Kredit Member
- **Modul:** Mart - POS
- **Prioritas:** P0 (Kritis)
- **Langkah-langkah:**
  1. Scan QR Member
  2. Scan produk belanja
  3. Pilih bayar "Kredit"
  4. Konfirmasi
- **Hasil yang Diharapkan:**
  - Limit kredit member tampil
  - Validasi sisa limit cukup
  - Transaksi sukses
  - Piutang member bertambah

### TC-021: POS - Pembayaran E-Wallet Midtrans
- **Modul:** Mart - POS
- **Prioritas:** P1 (Tinggi)
- **Langkah-langkah:**
  1. Pilih bayar E-Wallet (GoPay)
  2. Scan QRIS di layar
  3. Bayar via simulator
- **Hasil yang Diharapkan:**
  - QR Code muncul
  - Status update otomatis setelah bayar
  - Transaksi lunas

### TC-022: Riwayat Penjualan - Filter & Export
- **Modul:** Mart - Laporan
- **Prioritas:** P2 (Sedang)
- **Langkah-langkah:**
  1. Menu Riwayat Penjualan
  2. Filter tanggal & kasir
  3. Export Excel
- **Hasil yang Diharapkan:**
  - Data tampil sesuai filter
  - File Excel bisa dibuka

### TC-023: Laporan Kredit - Analisis Umur Hutang
- **Modul:** Mart - Laporan
- **Prioritas:** P1 (Tinggi)
- **Langkah-langkah:**
  1. Buka Laporan Kredit
  2. Cek kategori umur hutang (30/60/90 hari)
  3. Export data potong gaji
- **Hasil yang Diharapkan:**
  - Pengelompokan umur hutang benar
  - Format export sesuai formath payroll

### TC-024: Manajemen Produk - Tambah Baru
- **Modul:** Mart - Produk
- **Prioritas:** P1 (Tinggi)
- **Langkah-langkah:**
  1. Menu Tambah Produk
  2. Isi Nama, Barcode, Harga Beli, Harga Jual
  3. Simpan
- **Hasil yang Diharapkan:**
  - Produk tersimpan
  - Bisa dicari di POS

### TC-025: Alert Stok Menipis
- **Modul:** Mart - Inventory
- **Prioritas:** P1 (Tinggi)
- **Langkah-langkah:**
  1. Setup Min Stock = 10
  2. Buat transaksi hingga sisa stok < 10
  3. Cek menu "Stok Menipis"
- **Hasil yang Diharapkan:**
  - Produk muncul di list alert
  - Notifikasi email manager (opsional)

### TC-026: Stock Opname Fisik
- **Modul:** Mart - Inventory
- **Prioritas:** P1 (Tinggi)
- **Langkah-langkah:**
  1. Menu Stock Opname
  2. Input jumlah fisik (beda dengan sistem)
  3. Submit Adjustment
- **Hasil yang Diharapkan:**
  - Selisih terhitung (Varian)
  - Stok sistem terupdate
  - Jurnal penyesuaian tercipta

### TC-027: Pembuatan Purchase Order (PO)
- **Modul:** Mart - Pembelian
- **Prioritas:** P1 (Tinggi)
- **Langkah-langkah:**
  1. Menu Buat PO
  2. Pilih Supplier & Produk
  3. Simpan & Cetak
- **Hasil yang Diharapkan:**
  - No. PO urut
  - PDF PO tergenerate
  - Status = Draft/Sent

### TC-028: Penerimaan Barang (GRN)
- **Modul:** Mart - Pembelian
- **Prioritas:** P1 (Tinggi)
- **Langkah-langkah:**
  1. Buka PO status Sent
  2. Klik "Terima Barang"
  3. Validasi Qty terima
  4. Simpan
- **Hasil yang Diharapkan:**
  - Stok bertambah
  - Status PO = Received
  - Hutang dagang terbentuk

### TC-029: Catat Biaya Operasional
- **Modul:** Mart - Biaya
- **Prioritas:** P2 (Sedang)
- **Langkah-langkah:**
  1. Menu Biaya
  2. Input "Biaya Listrik", Nominal, & Upload Foto Bukti
  3. Simpan
- **Hasil yang Diharapkan:**
  - Biaya tercatat
  - Foto terupload
  - Masuk Laporan Laba Rugi

### TC-030: Titip Jual (Konsinyasi)
- **Modul:** Mart - Konsinyasi
- **Prioritas:** P2 (Sedang)
- **Langkah-langkah:**
  1. Input Barang Masuk Konsinyasi
  2. Jual item tsb di POS
  3. Cek laporan settlement
- **Hasil yang Diharapkan:**
  - Stok konsinyasi terpisah
  - Perhitungan bagi hasil benar

### TC-031: Voucher Promo
- **Modul:** Mart - Promo
- **Prioritas:** P2 (Sedang)
- **Langkah-langkah:**
  1. Buat Voucher Diskon 10%
  2. Gunakan di POS
- **Hasil yang Diharapkan:**
  - Diskon memotong total belanja
  - Kuota voucher berkurang

---

## 1.7 MODULE 4: BELANJA (MEMBER)

### TC-032: Katalog Produk
- **Modul:** Belanja
- **Prioritas:** P2 (Sedang)
- **Langkah-langkah:**
  1. Member buka menu Belanja
  2. Filter Kategori & Search
- **Hasil yang Diharapkan:**
  - Produk tampil sesuai filter
  - Detail produk lengkap

### TC-033: Pre-Order Barang Kosong
- **Modul:** Belanja
- **Prioritas:** P3 (Rendah)
- **Langkah-langkah:**
  1. Cari barang stok 0
  2. Klik Pre-Order
- **Hasil yang Diharapkan:**
  - Request tercatat
  - Admin terima notifikasi

### TC-034: Riwayat Belanja Pribadi
- **Modul:** Belanja
- **Prioritas:** P1 (Tinggi)
- **Langkah-langkah:**
  1. Buka Riwayat Belanja
  2. Download Struk
- **Hasil yang Diharapkan:**
  - History transaksi tampil
  - Struk bisa diunduh

---

## 1.8 MODULE 5: LAPORAN & INFORMASI

### TC-035: Laporan Neraca (Balance Sheet)
- **Modul:** Laporan
- **Prioritas:** P1 (Tinggi)
- **Langkah-langkah:**
  1. Generate Neraca
  2. Cek Balance (Aset = Kewajiban + Modal)
- **Hasil yang Diharapkan:**
  - Laporan seimbang (Balance)
  - Export PDF sukses

### TC-036: Broadcast Pengumuman
- **Modul:** Informasi
- **Prioritas:** P1 (Tinggi)
- **Langkah-langkah:**
  1. Admin buat pengumuman baru
  2. Publish
  3. Cek di akun member
- **Hasil yang Diharapkan:**
  - Pengumuman tampil di dashboard member

### TC-037: Dokumen AD/ART
- **Modul:** Informasi
- **Prioritas:** P2 (Sedang)
- **Langkah-langkah:**
  1. Admin upload PDF
  2. Member download
- **Hasil yang Diharapkan:**
  - File terunduh sempurna

### TC-038: Lapor Bug UAT
- **Modul:** UAT
- **Prioritas:** P1 (Tinggi)
- **Langkah-langkah:**
  1. Member submit form bug + screenshot
- **Hasil yang Diharapkan:**
  - Tiket ID terbentuk
  - Admin terima notifikasi

### TC-039: Polling & Voting
- **Modul:** Polling
- **Prioritas:** P2 (Sedang)
- **Langkah-langkah:**
  1. Admin buat voting
  2. Member vote pilihan A
- **Hasil yang Diharapkan:**
  - Vote tercatat (1x per user)
  - Hasil prosentase update

### TC-040: Aspirasi Anggota
- **Modul:** Aspirasi
- **Prioritas:** P2 (Sedang)
- **Langkah-langkah:**
  1. Member kirim saran
  2. Admin balas
- **Hasil yang Diharapkan:**
  - Status berubah jadi "Resolved"
  - Member dapat balasan

---

## 1.9 MODULE 6: KEPENGURUSAN

### TC-041: Tambah Anggota & Kartu
- **Modul:** Anggota
- **Prioritas:** P0 (Kritis)
- **Langkah-langkah:**
  1. Input data anggota baru
  2. Generate Kartu
- **Hasil yang Diharapkan:**
  - Data tersimpan
  - Kartu PDF + QR terbentuk

### TC-042: Import Excel Anggota
- **Modul:** Anggota
- **Prioritas:** P2 (Medium)
- **Langkah-langkah:** 
  1. Upload template Excel 10 user
  2. Import
- **Hasil yang Diharapkan:**
  - 10 user masuk database sukses

### TC-043: Inventaris Aset
- **Modul:** Aset
- **Prioritas:** P2 (Sedang)
- **Langkah-langkah:**
  1. Tambah aset Laptop
  2. Cek penyusutan
- **Hasil yang Diharapkan:**
  - Aset tercatat
  - Nilai buku benar

### TC-044: Notulen Rapat
- **Modul:** Organisasi
- **Prioritas:** P2 (Medium)
- **Langkah-langkah:**
  1. Simpan hasil rapat
  2. Export PDF
- **Hasil yang Diharapkan:**
  - Dokumen tersimpan rapi

### TC-045: Daftar Pengurus
- **Modul:** Organisasi
- **Prioritas:** P2 (Sedang)
- **Langkah-langkah:**
  1. Update struktur organisasi
- **Hasil yang Diharapkan:**
  - Bagan struktur terupdate

### TC-046: Generate Surat Resmi QR
- **Modul:** Persuratan
- **Prioritas:** P0 (Kritis)
- **Langkah-langkah:**
  1. Pilih template Undangan
  2. Generate
  3. Scan QR
- **Hasil yang Diharapkan:**
  - No. surat urut
  - QR Code valid

### TC-047: Edit Dokumen Arsip
- **Modul:** Persuratan
- **Prioritas:** P1 (Tinggi)
- **Langkah-langkah:**
  1. Edit surat lama
  2. Regenerate
- **Hasil yang Diharapkan:**
  - No surat tetap sama (tidak berubah)

### TC-048: Jurnal Umum Manual
- **Modul:** Akuntansi
- **Prioritas:** P1 (Tinggi)
- **Langkah-langkah:**
  1. Input Debet/Kredit manual
  2. Posting
- **Hasil yang Diharapkan:**
  - Jurnal imbang (Balance) tersimpan

### TC-049: Rekonsiliasi Bank
- **Modul:** Akuntansi
- **Prioritas:** P1 (Tinggi)
- **Langkah-langkah:**
  1. Import CSV Bank
  2. Match dengan sistem
- **Hasil yang Diharapkan:**
  - Auto-match berhasil
  - Selisih teridentifikasi

---

## 1.10 MODULE 7: ADMINISTRASI

### TC-050: Tambah Akun COA
- **Modul:** Master Data
- **Prioritas:** P1 (Tinggi)
- **Langkah-langkah:**
  1. Tambah akun GL baru
- **Hasil yang Diharapkan:**
  - Akun bisa dipakai di jurnal

### TC-051: Role & Permission (RBAC)
- **Modul:** Users
- **Prioritas:** P1 (Tinggi)
- **Langkah-langkah:**
  1. Buat role "Auditor" (Read Only)
  2. Login user Auditor
  3. Coba Edit data
- **Hasil yang Diharapkan:**
  - Edit ditolak (Access Denied)

### TC-052: Setting Identitas Koperasi
- **Modul:** Settings
- **Prioritas:** P2 (Sedang)
- **Langkah-langkah:**
  1. Ganti Logo & Nama
- **Hasil yang Diharapkan:**
  - Logo di header & laporan berubah

### TC-053: Backup Database Manual
- **Modul:** Tools
- **Prioritas:** P1 (Tinggi)
- **Langkah-langkah:**
  1. Klik "Backup Now"
- **Hasil yang Diharapkan:**
  - File .sql terdownload

### TC-054: Restore Database (Test Only)
- **Modul:** Tools
- **Prioritas:** P1 (High/Risky)
- **Langkah-langkah:**
  1. Restore file lama di server dev
- **Hasil yang Diharapkan:**
  - Data kembali ke kondisi lama

### TC-055: Audit Log
- **Modul:** Security
- **Prioritas:** P2 (Sedang)
- **Langkah-langkah:**
  1. Cek log aktivitas user
- **Hasil yang Diharapkan:**
  - Semua aksi user tercatat (Siapa, Kapan, Apa)

### TC-056: Test Payment Gateway
- **Modul:** Integrasi
- **Prioritas:** P1 (Tinggi)
- **Langkah-langkah:**
  1. Test koneksi Midtrans
- **Hasil yang Diharapkan:**
  - Status Connected / Valid

---

## 1.11 CROSS-FUNCTIONAL PROCESS

### TC-057: End-to-End Anggota Baru
- **Skenario:** Daftar -> Simpan -> Pinjam -> Belanja
- **Hasil:** Flow data lancar antar modul

### TC-058: End-to-End RAT
- **Skenario:** Laporan -> Hitung SHU -> Dokumen Rapat -> Voting
- **Hasil:** Siklus tahunan sukses

### TC-059: Performance Test
- **Skenario:** 10 User akses bersamaan
- **Hasil:** Server stabil, load time aman

### TC-060: Security Test
- **Skenario:** Akses paksa URL admin tanpa login
- **Hasil:** Blocked (403 Forbidden)

---

## 1.12 RINGKASAN TEST

| Modul | Total TC | Pass | Fail | Blocked | % Pass |
|---|---|---|---|---|---|
| Authentication | 3 | | | | |
| Dashboard | 2 | | | | |
| Keuangan | 13 | | | | |
| Koperasi Mart | 13 | | | | |
| Belanja | 3 | | | | |
| Laporan & Info | 6 | | | | |
| Kepengurusan | 9 | | | | |
| Administrasi | 7 | | | | |
| Cross-Functional | 4 | | | | |
| **TOTAL** | **60** | | | | |

**Disetujui Oleh:**
- **QA Lead:** ____________________ Tanggal: ____________
- **Project Manager:** ____________________ Tanggal: ____________
