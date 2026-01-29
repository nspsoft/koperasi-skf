# 📋 RENCANA PENGUJIAN UAT (UAT PLAN)
**Aplikasi Koperasi Karyawan Digital v3.0**

---

## 1. PENDAHULUAN

### 1.1 Tujuan
Tujuan dari User Acceptance Testing (UAT) ini adalah untuk memvalidasi bahwa aplikasi Koperasi Karyawan Digital telah memenuhi kebutuhan bisnis dan siap digunakan secara operasional (Go-Live).

Pengujian dilakukan langsung oleh pengguna (Pengurus & Anggota) untuk memastikan alur kerja sesuai dengan SOP Koperasi.

### 1.2 Ruang Lingkup (Scope)
#### ✅ Termasuk (In-Scope):
Pengujian fungsional pada 7 Modul Utama (60+ Fitur):
1.  **Otentikasi:** Login, Register, Lupa Password.
2.  **Dashboard:** Monitoring real-time.
3.  **Keuangan:** Simpanan, Pinjaman (Flow Pengajuan s/d Lunas), SHU.
4.  **Koperasi Mart:** POS Kasir, Stok, Pembelian, Penjualan Kredit.
5.  **Belanja:** Katalog Online, Keranjang, Checkout, Pre-order.
6.  **Kepengurusan:** Manajemen Anggota, Aset, Surat Menyurat, Voting.
7.  **Administrasi:** Setting, Backup, User Role, Audit Log.

#### ❌ Tidak Termasuk (Out-of-Scope):
- Performance Testing skala besar (> 10,000 user bersamaan).
- Security Penetration Testing mendalam (hanya cek role access).
- Kode program (White-box testing).

---

## 2. JADWAL PELAKSANAAN (TIMELINE)

Durasi UAT direncanakan selama **10 Hari Kerja**.

| Fase | Kegiatan | Estimasi Tanggal | Penanggung Jawab |
|---|---|---|---|
| **Persiapan** | Deploy ke server UAT, Siapkan Data Dummy | Hari 1-2 | Developer |
| **Briefing** | Demo aplikasi ke Pengurus & Tester | Hari 3 | PM & Tester |
| **Pelaksanaan 1** | Testing Modul Utama (Simpan Pinjam, Mart) | Hari 4-6 | Tester (Bendahara, Kasir) |
| **Pelaksanaan 2** | Testing Modul Member & Admin | Hari 7 | Tester (Anggota, Admin) |
| **Perbaikan** | Fixing Bug yang ditemukan (Periode 1) | Hari 8-9 | Developer |
| **Retest & Sign-off** | Uji ulang bug fix & Tanda tangan Go-Live | Hari 10 | QA Lead & Ketua |

---

## 3. LINGKUNGAN PENGUJIAN (ENVIRONMENT)

- **URL Akses:** `https://uat.kopkarskf.com`
- **Database:** Menggunakan data dummy (tiruan) yang mirip data asli.
- **Browser:** Google Chrome (Recommended), Firefox, Edge.
- **Perangkat:**
  - PC/Laptop (Admin, Pengurus, Kasir).
  - Smartphone Android/iOS (Anggota).

---

## 4. TIM PENGUJIAN (ROLES)

| Peran | User Asli | Tugas Pengujian |
|---|---|---|
| **UAT Lead** | Manajer Koperasi | Mengawasi jadwal & progres tes. |
| **Tester 1** | Bendahara | Modul Keuangan, Akuntansi, Approval Pinjaman/Penarikan. |
| **Tester 2** | Staff Toko/Kasir | Modul Mart, POS, Stok, Opname, Pembelian. |
| **Tester 3** | Sekretaris | Modul Organisasi, Surat, Aset, Anggota. |
| **Tester 4** | Admin IT | Modul Administrasi, Backup, Settings, User Role. |
| **Tester 5** | Perwakilan Anggota | Modul Belanja, Cek Saldo, Voting, Aspirasi. |

---

## 5. KRITERIA KEBERHASILAN

### 5.1 Kriteria Masuk (Entry Criteria)
UAT baru boleh dimulai jika:
- Aplikasi sudah deploy di server UAT.
- Tester sudah memiliki akun login & akses.
- Dokumen Skenario Tes (`UAT_TEST_SCENARIOS.md`) sudah siap.

### 5.2 Kriteria Keluar (Exit Criteria / Go-Live)
UAT dinyatakan SELESAI dan LULUS jika:
- 100% Skenario Prioritas **P0 (Critical)** status **PASS**.
- Minimal 95% Skenario Prioritas **P1 (High)** status **PASS**.
- Tidak ada Bug Critical yang statusnya "Open".
- Berita Acara UAT telah ditandatangani Ketua Koperasi.

---

## 6. MANAJEMEN CACAT (BUG MANAGEMENT)

Jika ditemukan error, tester wajib melaporkan menggunakan **Template Laporan Bug** (`UAT_BUG_TEMPLATE.md`).

### Klasifikasi Keparahan (Severity):
1.  **Critical:** Sistem mati, data hilang, atau proses bisnis utama macet total.
    *   *Target Fix:* < 24 jam.
2.  **High:** Fitur penting error atau hasil hitungan salah (misal: SHU salah).
    *   *Target Fix:* < 2 Hari.
3.  **Medium:** Fitur error tapi ada cara lain (workaround), atau error validasi.
    *   *Target Fix:* < 4 Hari.
4.  **Low:** Masalah kosmetik (warna, typo, letak tombol).
    *   *Target Fix:* Rilis berikutnya.

---

## 7. HASIL (DELIVERABLES)

Di akhir periode UAT, dokumen berikut akan diserahkan:
1.  **UAT Checklist:** Lembar hasil tes per skenario.
2.  **Log Bug:** Daftar temuan bug & status perbaikannya.
3.  **Berita Acara UAT:** Dokumen formal persetujuan Go-Live.

---

**Disetujui Oleh:**

**( ___________________ )**
Ketua Koperasi / Business Owner
Tanggal: ___________________
