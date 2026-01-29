# 📋 DOKUMENTASI LENGKAP FITUR APLIKASI (FEATURES)

Berikut adalah daftar lengkap fitur aplikasi Koperasi Karyawan, mencakup fungsi detail dan manfaat bisnis untuk setiap modul.

---

## 1. 🏠 DASHBOARD
**Akses:** Semua User

### Fungsi:
- **Real-time Overview:** Tampilan ringkasan data koperasi secara real-time.
- **Visualisasi Data:** Grafik kinerja keuangan dan operasional.
- **Quick Stats:** Statistik simpanan, pinjaman, dan transaksi mart.
- **Alerts:** Notifikasi penting dan akses cepat ke fungsi utama.

### Manfaat:
- **One-stop overview** untuk memonitor kesehatan koperasi.
- Memudahkan **pengambilan keputusan** berdasarkan data terkini.

---

## 2. 💰 KEUANGAN (Finance)
**Akses:** Semua Anggota (terbatas) & Pengurus (full)

### 2.1 Simpanan (Savings)
**Fungsi:**
- Pencatatan simpanan **Pokok, Wajib, dan Sukarela**.
- **Tracking saldo** simpanan per anggota real-time.
- **History transaksi** deposit dan penarikan.
- Export data dan cetak buku simpanan digital.

**Manfaat:**
- **Transparansi saldo** simpanan bagi setiap anggota.
- Memudahkan **audit** dan pelaporan keuangan.

### 2.2 Pinjaman (Loans)
**Fungsi:**
- **Pengajuan pinjaman online** oleh anggota (Self-service).
- **Approval workflow** berjenjang untuk pengurus.
- Tracking status **cicilan** dan outstanding balance.
- **Digital signature** untuk perjanjian pinjaman sah.
- Notifikasi otomatis jatuh tempo.

**Manfaat:**
- Proses pinjaman menjadi **paperless dan efisien**.
- Mengurangi kredit macet (NPL) dengan reminder otomatis.

### 2.3 Simulasi Pinjaman
**Fungsi:**
- **Kalkulator angsuran** pinjaman interaktif.
- Preview rincian **bunga** dan total pembayaran.
- Support berbagai metode bunga: **Flat, Efektif, Anuitas**.

**Manfaat:**
- Anggota bisa menghitung **kemampuan bayar** (Self-Assessment) sebelum mengajukan.
- **Transparansi** perhitungan bunga koperasi.

### 2.4 Angsuran Pinjaman (Loan Payments)
**Fungsi:**
- Record pembayaran angsuran harian/bulanan.
- Generate **bukti pembayaran digital** (Kuitansi).
- Tracking status pelunasan (Lugas/Belum).

**Manfaat:**
- **Otomasi perhitungan** sisa hutang dan penalti.
- Bukti pembayaran yang **sah dan terekam**.

### 2.5 Penarikan Simpanan (Withdrawals)
**Fungsi:**
- Request penarikan simpanan sukarela via aplikasi.
- **Approval workflow 3 tingkat** (Request → Approve → Complete).
- Notification status persetujuan ke angota.

**Manfaat:**
- Kontrol **cashflow** koperasi yang lebih baik.
- Dokumentasi tertib untuk setiap dana keluar.

### 2.6 SHU (Sisa Hasil Usaha)
**Fungsi:**
- **Kalkulator pembagian SHU** otomatis sesuai parameter AD/ART.
- Distribusi otomatis berdasarkan **Jasa Modal** & **Jasa Usaha**.
- Cetak **slip SHU individual** per anggota.
- Export laporan rekapitulasi SHU.

**Manfaat:**
- Perhitungan SHU yang **akurat dan adil** (Human error free).
- **Transparansi** pembagian keuntungan yang meningkatkan kepercayaan anggota.

---

## 3. 🛒 KOPERASI MART (Commerce)
**Akses:** Admin, Pengurus, Manager Toko, Kasir

### 3.1 POS (Point of Sale)
**Fungsi:**
- Kasir digital untuk transaksi mart operasional.
- **Barcode scanner integration** (USB).
- Support pembayaran: **Tunai, Kredit Anggota (Potong Gaji), E-Wallet (QRIS)**.
- Struk belanja **Digital (WA/Email)** dan **Fisik (Thermal)**.

**Manfaat:**
- Transaksi pelayanan anggota lebih **cepat dan akurat**.
- Tracking **hutang kredit anggota** tercatat otomatis ke sistem Finance.

### 3.2 Riwayat Penjualan (Sales History)
**Fungsi:**
- Log lengkap semua transaksi penjualan.
- **Filter canggih**: by tanggal, kasir, metode bayar.
- Detail drill-down per transaksi dan item terjual.

**Manfaat:**
- Analisis tren **penjualan harian/bulanan**.
- Deteksi dini fraud atau kesalahan input kasir.

### 3.3 Laporan Kredit (Credit Reports)
**Fungsi:**
- Daftar real-time anggota dengan hutang kredit mart.
- **Aging analysis** hutang belanja.
- **Export format khusus** untuk payroll deduction (HRD).

**Manfaat:**
- Kontrol ketat **piutang mart**.
- Memudahkan koordinasi dengan HRD perusahaan untuk **potong gaji**.

### 3.4 📷 Scan Order
**Fungsi:**
- Scan **QR Code member** untuk identifikasi cepat di kasir.
- **Auto-fill** data member (Limit kredit, Points).
- Verification status member aktif/tidak.

**Manfaat:**
- **Kecepatan transaksi** meningkat signifikan (antrian berkurang).
- Menghindari kesalahan input manual ID anggota.

### 3.5 Produk (Products)
**Fungsi:**
- Database produk lengkap dengan barcode/SKU.
- Support **Multi-kategori** dan pricing bertingkat (Member vs Non-Member).
- Upload foto produk.
- **Stock tracking real-time**.

**Manfaat:**
- **Inventory management modern**.
- Barang mudah dicari dan diidentifikasi saat transaksi.

### 3.6 Stok Menipis (Low Stock Alert)
**Fungsi:**
- Notifikasi otomatis produk di bawah **minimum threshold**.
- Rekomendasi kuantitas pembelian ulang (Re-order point).

**Manfaat:**
- Cegah **Lost Sales** akibat kehabisan stok produk populer.
- Optimalkan **modal kerja** (tidak overstock).

### 3.7 Kategori Produk (Categories)
**Fungsi:**
- Grouping produk hirarkis (Sembako, ATK, Frozen, dll).
- Memudahkan navigasi katalog di POS dan Online Shop.

**Manfaat:**
- Organisasi display produk lebih rapi.
- Analisis pelaporan penjualan **per kategori**.

### 3.8 Stock Opname
**Fungsi:**
- Modul audit fisik inventory vs sistem.
- **Adjustment otomatis** stok sistem.
- Laporan selisih stock (Variance Report).

**Manfaat:**
- Deteksi **kehilangan (shrinkage)** atau kesalahan input.
- Menjamin **keakuratan data inventory** untuk laporan keuangan.

### 3.9 Supplier
**Fungsi:**
- Database lengkap pemasok barang.
- Kontak person dan term pembayaran (TOP).
- Tracking history pembelian per supplier.

**Manfaat:**
- Manajemen hubungan supplier (Vendor Management).
- Data historis untuk **negosiasi harga** lebih baik.

### 3.10 Pembelian (Purchases)
**Fungsi:**
- Pembuatan **Purchase Order (PO)**.
- Proses **Receiving Goods** (Penerimaan barang).
- Tracking status pengiriman dan pembayaran ke supplier.

**Manfaat:**
- Tertib administrasi pembelian barang dagang.
- Kontrol **hutang dagang** koperasi.

### 3.11 Biaya Operasional (Expenses)
**Fungsi:**
- Pencatatan biaya toko (listrik, gaji, sewa, ATK).
- Approval workflow pengeluaran biaya.
- Pengelompokan kategori biaya.

**Manfaat:**
- Monitoring ketat **pengeluaran operasional**.
- Basis alokasi biaya yang akurat saat perhitungan **Laba Rugi Unit Toko**.

### 3.12 Kategori Biaya (Expense Categories)
**Fungsi:**
- Klasifikasi jenis biaya standar akuntansi.

**Manfaat:**
- Analisis **cost center** yang detail.
- Perencanaan budget (Budget Planning) lebih mudah.

### 3.13 Titip Jual (Consignment)
**Fungsi:**
- Pencatatan barang masuk sistem titipan.
- **Settlement otomatis** dengan pemilik barang terjual.
- Tracking komisi/fee koperasi.

**Manfaat:**
- Diversifikasi produk **tanpa modal besar** (Inventory risk minimal).
- **Revenue sharing** yang transparan dengan mitra UMKM/Anggota.

### 3.14 Voucher Promo
**Fungsi:**
- Generator voucher diskon/potongan harga.
- Setting periode validitas dan kuota.
- Tracking redemption rate.

**Manfaat:**
- Meningkatkan **traffic belanja** ke mart.
- Program **loyalty member** yang menarik.

---

## 4. 🛍️ BELANJA (Shop - Member Area)
**Akses:** Semua Anggota

### 4.1 Katalog Produk (Browse)
**Fungsi:**
- Etalase online produk yang tersedia di Mart.
- Fitur **Search** dan **Filter** kategori.
- Fitur **Pre-order** untuk anggota.

**Manfaat:**
- Anggota bisa cek **ketersediaan stok** sebelum datang.
- Perencanaan belanja bulanan anggota lebih baik.

### 4.2 Riwayat Belanja (Order History)
**Fungsi:**
- History transaksi belanja pribadi (Online & Offline Mart).
- **Digital receipt** (Unduh ulang struk).
- Tracking status pelunasan hutang belanja.

**Manfaat:**
- Anggota bisa **mengontrol pengeluaran** pribadi.
- Bukti transaksi tersimpan aman selamanya.

---

## 5. 📊 LAPORAN & INFORMASI (Reports)
**Akses:** Anggota (Read Selected), Admin (Full Access)

### 5.1 Semua Laporan (All Reports)
**Fungsi:**
- **Dashboard reporting center** terpusat.
- Laporan Keuangan (Neraca, Laba Rugi), Operasional, Statistik Anggota.
- Export ke **Excel/PDF**.

**Manfaat:**
- Persiapan data **RAT (Rapat Anggota Tahunan)** menjadi instan.
- **Transparansi** penuh pengelolaan koperasi kepada anggota.

### 5.2 Pengumuman (Announcements)
**Fungsi:**
- Broadcast info penting/kegiatan ke dashboard anggota.
- Support Notifikasi Push / Email.

**Manfaat:**
- Komunikasi **efektif dan massal** dengan biaya murah.
- Menggantikan papan pengumuman fisik yang jarang dibaca.

### 5.3 AD/ART (Dokumen Legal)
**Fungsi:**
- **Digital library** untuk dokumen legal (AD/ART, Peraturan Khusus).
- View dan Download PDF.

**Manfaat:**
- Anggota memahami **hak dan kewajiban** sesuai aturan.
- Kepatuhan dokumentasi organisasi (Compliance).

### 5.4 Tata Kelola (Governance)
**Fungsi:**
- Repositori SOP operasional koperasi.
- Panduan Good Cooperative Governance (GCG).

**Manfaat:**
- **Standarisasi proses bisnis** siapapun pengurusnya.
- Panduan onboarding yang jelas untuk pengurus baru.

### 5.5 Dokumentasi Sistem
**Fungsi:**
- User manual penggunaan aplikasi.
- FAQ dan guide troubleshooting mandiri.

**Manfaat:**
- **Self-service support** bagi user.
- Mengurangi beban tanya-jawab ke Admin IT.

### 5.6 UAT (User Acceptance Test)
**Fungsi:**
- Portal feedback fitur baru.
- Halaman pelaporan bug (Bug Reporting).

**Manfaat:**
- **Continuous improvement** aplikasi.
- Pelibatan user (User Involvement) dalam pengembangan sistem.

### 5.7 Polling/Voting
**Fungsi:**
- Modul survey atau **pemilihan/voting online** (e-Voting).
- Real-time result visual.
- Jaminan anonimitas pemilih.

**Manfaat:**
- Penerapan **demokrasi digital** (misal: Pemilihan Ketua).
- Pengambilan keputusan (Quick Decision) berbasis suara mayoritas.

### 5.8 Aspirasi Anggota
**Fungsi:**
- **Kotak saran digital**.
- Tracking follow-up/balasan pengurus.

**Manfaat:**
- Saluran komunikasi **bottom-up**.
- Meningkatkan kualitas layanan berdasarkan masukan anggota.

---

## 6. 🏛️ KEPENGURUSAN (Organization Management)
**Akses:** Admin & Pengurus

### 6.1 Dashboard Kepengurusan
**Fungsi:**
- Ringkasan administrasi organisasi.
- Quick access ke **16 Buku Wajib Koperasi**.

**Manfaat:**
- **Centralized management tools** untuk pengurus.

### 6.2 Daftar Anggota
**Fungsi:**
- Database anggota Master.
- **Digital Member Card** generator (QR Code).
- Manajemen status keanggotaan (Pending, Active, Inactive/Resigned).
- Export dan Import data anggota bulk.

**Manfaat:**
- Manajemen keanggotaan yang **modern dan rapi**.
- Kartu anggota hybrid (Digital di HP + Fisik Printable).

### 6.3 Inventaris Aset
**Fungsi:**
- Katalog aset tetap koperasi (tanah, gedung, kendaraan, elektronik).
- Status kondisi dan lokasi aset.
- **Depreciation tracking** (Penyusutan nilai).

**Manfaat:**
- Kepatuhan terhadap **Buku Inventaris** (Wajib UU Koperasi).
- Manajemen aset (Asset Lifecycle) yang terkontrol.

### 6.4 Notulen Rapat
**Fungsi:**
- Arsip digital hasil rapat pengurus/anggota.
- Dokumentasi keputusan dan *action items*.
- Searchable archive.

**Manfaat:**
- Kepatuhan terhadap **Buku Risalah Rapat** (Wajib).
- Referensi mudah untuk keputusan historis.

### 6.5 Daftar Pengurus
**Fungsi:**
- Pencatatan struktur organisasi dan periode jabatan.
- Info kontak pengurus.

**Manfaat:**
- Kepatuhan **Buku Daftar Pengurus** (Wajib).
- Kejelasan tanggung jawab (Clarity of Responsibility).

### 6.6 Surat & Dokumen
**Fungsi:**
- **Smart Generator** surat resmi (SK, Undangan, Surat Pernyataan).
- **Penomoran otomatis** terstruktur (Auto-numbering).
- **QR Code verification** untuk keaslian surat.
- Arsip digital surat terbit/masuk.

**Manfaat:**
- Manajemen dokumen **profesional dan seragam**.
- Administrasi **paperless**.
- Surat yang **legal dan verifiable**.

### 6.7 Jurnal Umum (Manual Journal)
**Fungsi:**
- Input jurnal akuntansi manual (Adjustment).
- Konsep **Double-entry bookkeeping**.
- Posting otomatis ke ledger.

**Manfaat:**
- Fleksibilitas pencatatan untuk transaksi non-rutin.
- Koreksi akuntansi (Adjustment) yang audit-able.

### 6.8 Rekonsiliasi Bank
**Fungsi:**
- Import mutasi rekening koran bank (Excel/CSV).
- **Auto-matching** dengan jurnal sistem.
- Identifikasi selisih saldo.

**Manfaat:**
- Deteksi dini **error pembukuan** atau fraud.
- Memastikan akurasi **Cash Flow**.

---

## 7. ⚙️ ADMINISTRASI (Admin System)
**Akses:** System Administrator Only

### 7.1 Master Data
**Fungsi:**
- Setup struktur organisasi (Departemen, Posisi, Jabatan).
- Konfigurasi **COA (Chart of Accounts)**.
- Setup parameter sistem global.

**Manfaat:**
- **Pondasi data** aplikasi yang kuat.
- Customization fleksibel sesuai struktur organisasi koperasi.

### 7.2 Import Data
**Fungsi:**
- **Bulk import** via Excel untuk Anggota, Produk, dan Saldo Awal.
- Download template standar.

**Manfaat:**
- Migrasi data dari sistem lama/manual sangat **cepat**.
- Onboarding ribuan anggota sekaligus dalam hitungan menit.

### 7.3 Roles & Permissions
**Fungsi:**
- Manajemen hak akses user granular.
- Pembuatan **Custom Role**.

**Manfaat:**
- **Security** & Separation of Duties (SoD).
- Kontrol akses spesifik (siapa bisa lihat/edit apa).

### 7.4 Pengaturan Koperasi (Settings)
**Fungsi:**
- Konfigurasi identitas (Nama, Logo, Alamat).
- Parameter bisnis (Bunga, Denda, % SHU).
- Integrasi **Email & WhatsApp Gateway**.

**Manfaat:**
- **Customization** penuh per entitas koperasi.
- Penguatan **Branding** koperasi.

### 7.5 Landing Page Editor
**Fungsi:**
- Editor konten homepage publik (CMS Sederhana).
- Banner slider, gallery foto, info kontak.

**Manfaat:**
- Media **marketing digital** koperasi.
- **Professional online presence** yang meyakinkan.

### 7.6 Backup & Restore
**Fungsi:**
- Jadwal backup database otomatis.
- Trigger backup manual.
- Restore point management.

**Manfaat:**
- **Disaster Recovery Plan** (DRP).
- Perlindungan maksimal terhadap kehilangan data.

### 7.7 Audit Logs
**Fungsi:**
- Tracking detil semua aktivitas user (Log Activity).
- Info 5W (Who, What, Where, When, Why).

**Manfaat:**
- **Security audit** trail.
- Akuntabilitas (Accountability) setiap aksi user.

### 7.8 💳 Payment Gateway
**Fungsi:**
- Integrasi native dengan **Midtrans**.
- Setting API Key (Sandbox/Production).
- Auto-verify pembayaran online.

**Manfaat:**
- Menerima pembayaran **online 24/7** (VA, E-Wallet, CC).
- Transformasi menuju **Cashless Society**.
