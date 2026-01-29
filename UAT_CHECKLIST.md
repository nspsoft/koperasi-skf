# ✅ LEMBAR KERJA UAT (CHECKLIST)
**Aplikasi Koperasi Karyawan Digital v3.0**

**Nama Tester:** ____________________
**Peran:** Admin / Pengurus / Anggota / Kasir
**Tanggal:** ____________________
**Perangkat:** PC / Tablet / Smartphone

---

## � 1. OTENTIKASI & GENERAL

| ID | Item Pengujian | Status | Catatan / ID Bug |
|---|---|---|---|
| TC-001 | Login User (Email & Password Benar) | [ ] OK [ ] Fail | |
| TC-002 | Login Gagal (Password Salah) | [ ] OK [ ] Fail | |
| TC-003 | Reset Password via Email | [ ] OK [ ] Fail | |
| TC-004 | Load Dashboard Admin (< 3 detik) | [ ] OK [ ] Fail | |
| TC-005 | Real-time Update Dashboard | [ ] OK [ ] Fail | |

---

## 💰 2. KEUANGAN (SIMPAN PINJAM)

| ID | Item Pengujian | Status | Catatan / ID Bug |
|---|---|---|---|
| TC-006 | Cek Saldo Simpanan (Breakdown detail) | [ ] OK [ ] Fail | |
| TC-007 | Filter & Export Riwayat Simpanan | [ ] OK [ ] Fail | |
| TC-008 | Pengajuan Pinjaman Baru | [ ] OK [ ] Fail | |
| TC-009 | Approval Pinjaman (Pengurus) | [ ] OK [ ] Fail | |
| TC-010 | Penolakan Pinjaman (Reject Reason) | [ ] OK [ ] Fail | |
| TC-011 | Simulasi Pinjaman (Flat Interest) | [ ] OK [ ] Fail | |
| TC-012 | Simulasi Pinjaman (Efektif/Anuitas) | [ ] OK [ ] Fail | |
| TC-013 | Pembayaran Angsuran (Manual) | [ ] OK [ ] Fail | |
| TC-014 | Deteksi Denda Keterlambatan | [ ] OK [ ] Fail | |
| TC-015 | Request Penarikan Simpanan | [ ] OK [ ] Fail | |
| TC-016 | Multi-level Approval Penarikan | [ ] OK [ ] Fail | |
| TC-017 | Kalkulasi SHU Otomatis | [ ] OK [ ] Fail | |
| TC-018 | Download Slip SHU (+ QR Code) | [ ] OK [ ] Fail | |

---

## 🛒 3. KOPERASI MART (POS)

| ID | Item Pengujian | Status | Catatan / ID Bug |
|---|---|---|---|
| TC-019 | Scan Barcode & Checkout Tunai | [ ] OK [ ] Fail | |
| TC-020 | Belanja Kredit Member (Potong Gaji) | [ ] OK [ ] Fail | |
| TC-021 | Pembayaran E-Wallet (Midtrans) | [ ] OK [ ] Fail | |
| TC-022 | Export Laporan Penjualan | [ ] OK [ ] Fail | |
| TC-023 | Laporan Umur Hutang (Aging) | [ ] OK [ ] Fail | |
| TC-024 | Tambah Produk Baru | [ ] OK [ ] Fail | |
| TC-025 | Notifikasi Stok Menipis | [ ] OK [ ] Fail | |
| TC-026 | Stock Opname (Audit Fisik) | [ ] OK [ ] Fail | |
| TC-027 | Buat Purchase Order (PO) | [ ] OK [ ] Fail | |
| TC-028 | Penerimaan Barang (GRN) | [ ] OK [ ] Fail | |
| TC-029 | Upload Biaya Operasional Toko | [ ] OK [ ] Fail | |
| TC-030 | Transaksi Titip Jual (Konsinyasi) | [ ] OK [ ] Fail | |
| TC-031 | Redeem Voucher Promo | [ ] OK [ ] Fail | |

---

## 🛍️ 4. BELANJA (MEMBER APP)

| ID | Item Pengujian | Status | Catatan / ID Bug |
|---|---|---|---|
| TC-032 | Katalog & Pencarian Produk | [ ] OK [ ] Fail | |
| TC-033 | Pre-Order Stok Kosong | [ ] OK [ ] Fail | |
| TC-034 | Riwayat Belanja & Struk Digital | [ ] OK [ ] Fail | |

---

## 📊 5. LAPORAN & INFORMASI

| ID | Item Pengujian | Status | Catatan / ID Bug |
|---|---|---|---|
| TC-035 | Generate Neraca (Balance Sheet) | [ ] OK [ ] Fail | |
| TC-036 | Broadcast Pengumuman | [ ] OK [ ] Fail | |
| TC-037 | Download Dokumen AD/ART | [ ] OK [ ] Fail | |
| TC-038 | Lapor Bug (UAT Feedback) | [ ] OK [ ] Fail | |
| TC-039 | Ikut Polling / Voting | [ ] OK [ ] Fail | |
| TC-040 | Kirim Aspirasi Member | [ ] OK [ ] Fail | |

---

## 🏛️ 6. KEPENGURUSAN & ORGANISASI

| ID | Item Pengujian | Status | Catatan / ID Bug |
|---|---|---|---|
| TC-041 | Register Anggota & Cetak Kartu | [ ] OK [ ] Fail | |
| TC-042 | Import Anggota via Excel | [ ] OK [ ] Fail | |
| TC-043 | Manajemen Inventaris Aset | [ ] OK [ ] Fail | |
| TC-044 | Simpan Notulen Rapat | [ ] OK [ ] Fail | |
| TC-045 | Update Daftar Pengurus | [ ] OK [ ] Fail | |
| TC-046 | Generate Surat Resmi (+QR) | [ ] OK [ ] Fail | |
| TC-047 | Edit Arsip Surat | [ ] OK [ ] Fail | |
| TC-048 | Input Jurnal Manual | [ ] OK [ ] Fail | |
| TC-049 | Import Rekonsiliasi Bank | [ ] OK [ ] Fail | |

---

## ⚙️ 7. ADMINISTRASI SISTEM

| ID | Item Pengujian | Status | Catatan / ID Bug |
|---|---|---|---|
| TC-050 | Tambah Akun COA Baru | [ ] OK [ ] Fail | |
| TC-051 | Test Role Permission (Akses Ditolak) | [ ] OK [ ] Fail | |
| TC-052 | Ganti Logo & Nama Koperasi | [ ] OK [ ] Fail | |
| TC-053 | Manual Backup Database | [ ] OK [ ] Fail | |
| TC-054 | Restore Database (hati-hati!) | [ ] OK [ ] Fail | |
| TC-055 | Cek Audit Log Aktivitas | [ ] OK [ ] Fail | |
| TC-056 | Test Koneksi Midtrans | [ ] OK [ ] Fail | |

---

## 🔄 8. CROSS-FUNCTIONAL

| ID | Item Pengujian | Status | Catatan / ID Bug |
|---|---|---|---|
| TC-057 | Flow Anggota Baru s/d Belanja | [ ] OK [ ] Fail | |
| TC-058 | Flow RAT Tahunan (Simulasi) | [ ] OK [ ] Fail | |
| TC-059 | Load Test (10 User bersamaan) | [ ] OK [ ] Fail | |
| TC-060 | Security Test (Force URL) | [ ] OK [ ] Fail | |

---

## 📝 KESIMPULAN PENGUJIAN

**Total Test Case:** 60
**Total Pass:** _______
**Total Fail:** _______
**Total Blocked:** _______

**Rekomendasi Tester:**
[ ] **GO-LIVE** (Aplikasi Siap Rilis)
[ ] **GO-LIVE with NOTES** (Rilis dengan catatan perbaikan minor)
[ ] **NO-GO** (Perlu perbaikan major, rilis ditunda)

**Tanda Tangan Tester:**


_________________________
(Nama Jelas)
