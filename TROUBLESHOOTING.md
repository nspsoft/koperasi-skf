# 🔧 PANDUAN SOLUSI MASALAH LENGKAP (TROUBLESHOOTING)
**Aplikasi Koperasi Karyawan Digital v3.0**

Panduan ini berisi bank solusi untuk mengatasi kendala teknis maupun operasional di seluruh modul aplikasi.

---

## 🔑 1. MASALAH LOGIN & AKUN

### 1.1 Lupa Password
**Masalah:** User tidak bisa masuk.
**Solusi:**
1. Klik link "Lupa Password" di halaman login.
2. Cek email (termasuk Spam).
3. Jika email tidak masuk, Admin bisa **Manual Reset** via menu `User Management > Edit User > Set Password Baru`.

### 1.2 Akun Terkunci / Suspended
**Penyebab:** Status anggota `Non-Aktif` (Resign) atau salah password > 5x.
**Solusi:**
- Admin cek menu `Anggota > Daftar`. Ubah status menjadi `Active`.

### 1.3 Error 403 "Forbidden"
**Masalah:** "Anda tidak memiliki akses ini".
**Solusi:**
- Cek Role user di `Settings > Users`. Pastikan Jabatan user memiliki **Permission** yang sesuai.

---

## 💰 2. MASALAH KEUANGAN (SIMPAN PINJAM)

### 2.1 Saldo Simpanan Tidak Update
**Masalah:** Sudah transfer tapi saldo tetap.
**Solusi:**
1. Cek menu `Keuangan > Approval Setoran`. Admin harus Approve.
2. Jika via Midtrans, cek dashboard Midtrans apakah status `Settlement`. Jika `Pending`, minta anggota bayar ulang.

### 2.2 Pengajuan Pinjaman Ditolak Sistem
**Masalah:** Tombol "Ajukan" tidak bisa diklik atau auto-reject.
**Penyebab:**
- Sisa gaji < 40% (Melanggar aturan DSR).
- Masih ada pinjaman menunggak (NPL).
- Masa kerja < Syarat minimal (misal 1 tahun).
**Solusi:**
- Admin/Pengurus cek profil anggota. Beri penjelasan aturan koperasi.
- Jika *override* kebijakan diperlukan, Admin bisa input via `Pinjaman > Input Manual` (Bypass system check).

### 2.3 Gagal Tanda Tangan Digital
**Masalah:** Canvas tanda tangan tidak muncul atau gagal simpan.
**Solusi:**
1. Gunakan browser **Chrome/Safari** terbaru.
2. Matikan ekstensi "AdBlock" browser.
3. Coba akses via mode Incognito.

### 2.4 Gagal Tarik Dana (Withdraw)
**Penyebab:** Saldo mengendap kurang dari saldo minimum (aturan AD/ART).
**Solusi:**
- Cek parameter `Minimum Saldo` di Settings. Penarikan tidak boleh menghabiskan saldo sisa.

---

## 🛒 3. MASALAH OPERASIONAL MART

### 3.1 Scanner Barcode Error
**Masalah:** Scan barang tidak muncul di POS.
**Solusi:**
1. Pastikan kursor mouse aktif di kolom "Search".
2. Cek apakah produk tersebut aktif? (Menu `Produk > Edit > Status: Active`).
3. Cek apakah Barcode di sistem sama dengan fisik barang?

### 3.2 Transaksi Gagal Checkout
**Pesan Error:** "Stok tidak mencukupi".
**Solusi:**
1. Cek stok fisik barang.
2. Jika ada barang, lakukan **Adjustment Stok** di menu Inventory.
3. Atau aktifkan opsi *"Allow Negative Stock"* (Darurat) di Settings Mart.

### 3.3 Void / Pembatalan Transaksi
**Masalah:** Konsumen batal beli atau salah input setelah struk keluar.
**Solusi:**
1. Kasir tidak bisa Void (Security). Panggil **Supervisor/Manager**.
2. Masuk menu `Riwayat Penjualan`.
3. Klik tombol **Void**. Stok akan otomatis kembali (Re-stock).
*Note: Uang fisik harus dikembalikan ke konsumen.*

### 3.4 Kode Voucher Tidak Bisa Dipakai
**Penyebab:** Kuota habis, expired, atau minimum belanja belum tercapai.
**Solusi:**
- Cek detail Voucher di menu `Marketing`. Infokan syarat & ketentuan ke member.

---

## 🛍️ 4. MASALAH BELANJA ONLINE & MEMBER

### 4.1 Keranjang Belanja Error
**Masalah:** Barang masuk keranjang, lalu hilang saat checkout.
**Penyebab:** Barang keduluan dibeli orang lain (Stock race condition).
**Solusi:**
- Member harus refresh halaman katalog untuk lihat stok terkini.

### 4.2 Notifikasi WA Tidak Masuk
**Penyebab:** Token WA Gateway expired atau HP Server WA mati.
**Solusi:**
1. Admin cek menu `Integrasi > WhatsApp`.
2. Status harus "Connected".
3. Jika "Disconnected", scan ulang QR Code WA Gateway.

---

## � 5. MASALAH DATA & LAPORAN

### 5.1 Data Dashboard vs Laporan Beda
**Masalah:** Saldo dashboard beda dengan Laporan PDF.
**Penyebab:** Cache sistem belum refresh.
**Solusi:**
1. Admin jalankan `Clear Cache` di menu Utilities.
2. Tunggu jadwal *cron job* malam hari (rekap harian).

### 5.2 Gagal Export PDF/Excel
**Error:** "500 Server Error" / "Timeout".
**Penyebab:** Data terlalu banyak (misal: Laporan setahun penuh).
**Solusi:**
1. Filter periode laporan jadi lebih pendek (per Bulan/Minggu).
2. Naikkan `memory_limit` dan `max_execution_time` di server PHP.

---

## 🖥️ 6. MASALAH INFRASTRUKTUR & SERVER

### 6.1 Server Mati Lampu / Down
**Solusi:**
1. Cek koneksi internet kantor (jika server fisik lokal).
2. Nyalakan ulang server & pastikan service `nginx`, `mysql`, `supervisor` auto-start.

### 6.2 Database Error "Too Many Connections"
**Masalah:** Web lambat/error saat jam sibuk.
**Solusi:**
- Restart service MySQL.
- Optimasi config `my.cnf`: naikkan `max_connections`.

### 6.3 Email SMTP Gagal Kirim
**Error:** "Connection Refused" / "Auth Failed".
**Solusi:**
- Cek password email di file `.env`.
- Jika pakai Gmail, pastikan "App Password" aktif (bukan password login biasa).

---

**Kontak Darurat Tim IT:**
- **Infrastruktur/Jaringan:** 0811-xxxx-xxxx
- **Aplikasi/Database:** 0812-xxxx-xxxx
- **Vendor Payment (Midtrans):** support@midtrans.com
