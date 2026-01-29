# 🐛 FORM LAPORAN BUG (BUG REPORT)
**Aplikasi Koperasi Karyawan Digital v3.0**

Gunakan template ini untuk melaporkan setiap kesalahan/error yang ditemukan selama pengujian UAT.

---

## 📋 1. INFORMASI DASAR

**Bug ID:** `BUG-[YYYYMMDD]-001` (Diisi oleh QA Lead)
**Judul Bug:** [Ringkas & Jelas, max 10 kata]
*(Contoh: Total Harga di Keranjang Belanja Salah Hitung)*

**Modul:**
[ ] Otentikasi
[ ] Keuangan (Simpan Pinjam)
[ ] Koperasi Mart (POS)
[ ] Belanja Online
[ ] Laporan
[ ] Organisasi
[ ] Lainnya: _________

**Severity (Tingkat Keparahan):**
[ ] **Critical** (Sistem Crash / Data Hilang / Blokir Go-Live)
[ ] **High** (Fitur Utama Error, tidak ada workaround)
[ ] **Medium** (Fitur Error, ada cara alternatif)
[ ] **Low** (Typo / Masalah Kosmetik tampilan)

**Environment (Lingkungan Tes):**
- **URL:** `https://uat.kopkarskf.com`
- **Browser:** [Chrome / Firefox / Edge / Safari]
- **OS:** [Windows / MacOS / Android / iOS]
- **Akun Test:** [Email user yang dipakai]

---

## � 2. DETAIL MASALAH

### Prabsyarat (Pre-conditions):
*(Kondisi apa yang harus ada sebelum bug muncul?)*
1. User sudah login sebagai Anggota.
2. Keranjang belanja berisi 2 item.
3. ...

### Langkah Reproduksi (Steps to Reproduce):
*(Jelaskan langkah demi langkah sampai error muncul)*
1. Buka menu "Mart > Belanja".
2. Tambahkan produk "Kopi Kapal Api" (Harga 5.000).
3. Ubah jumlah (Qty) menjadi 3.
4. Lihat "Subtotal" di baris produk tersebut.

### Hasil yang Diharapkan (Expected Result):
*(Apa yang seharusnya terjadi?)*
- Subtotal seharusnya Rp 15.000 (5.000 x 3).

### Hasil Aktual (Actual Result):
*(Apa yang sebenarnya terjadi?)*
- Subtotal tertulis Rp 10.000. Perhitungan qty delay atau salah rumus.

---

## 📸 3. BUKTI PENDUKUNG (EVIDENCE)

**Screenshot / Screen Recording:**
*(Lampirkan gambar disini)*

**Pesan Error (Jika ada):**
*(Copy-paste pesan error merah yang muncul)*
> Error: Uncaught ReferenceError: price is not defined at CartController.js:42

**Log Console (F12):**
*(Jika tester mengerti teknis, lampirkan log browser console)*

---

## �️ 4. ANALISIS DAMPAK (Diisi Developer)

**Akar Masalah (Root Cause):**
- [ ] Coding Error
- [ ] Database Issue
- [ ] Server Config
- [ ] 3rd Party API

**Estimasi Perbaikan:** [ ... ] Jam
**Ditugaskan Ke:** [ Nama Developer ]
**Status:** [ ] Open [ ] In Progress [ ] Fixed [ ] Won't Fix

---

**Dilaporkan Oleh:** ____________________
**Tanggal:** ____________________
