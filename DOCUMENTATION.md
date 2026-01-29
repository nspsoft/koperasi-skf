# Koperasi Digital Management System - Documentation

Dokumentasi teknis dan panduan penggunaan untuk Sistem Manajemen Koperasi Digital (Simpan Pinjam & Retail).

---

## 1. Procurement (Kebutuhan Sistem)

Bagian ini menjelaskan kebutuhan perangkat keras dan perangkat lunak untuk menjalankan aplikasi ini di lingkungan produksi atau lokal.

### A. Server Requirements (Hardware)
*   **Processor**: Minimal 2 Core CPU (Recommended 4 Core untuk traffic tinggi).
*   **RAM**: Minimal 4GB RAM (Recommended 8GB jika menjalankan database di server yang sama).
*   **Storage**: Minimal 20GB SSD (Tergantung jumlah data transaksi dan gambar produk).
*   **Network**: Koneksi internet stabil (Public IP) jika diakses online, atau LAN stabil untuk intranet.

### B. Client/Device Requirements (Pengguna)
*   **Komputer/Laptop** (Admin & Kasir):
    *   Browser Modern (Chrome, Edge, Firefox).
    *   Resolusi layar minimal 1366x768.
    *   **Perangkat Tambahan Kasir (POS)**: Scanner Barcode (USB/Bluetooth), Printer Thermal (58mm/80mm).
*   **Smartphone** (Member):
    *   Browser Mobile (Chrome/Safari).
    *   Koneksi internet.

### C. Server Environment (Software)
*   **Operating System**: Linux (Ubuntu 20.04/22.04 LTS recommended) atau Windows Server.
*   **Web Server**: Nginx atau Apache.
*   **Database Engine**: MySQL 8.0+ atau MariaDB 10.6+.
*   **PHP Runtime**: Versi 8.1 atau 8.2.

---

## 2. Tools & Technologies

Aplikasi ini dibangun menggunakan tumpukan teknologi (Tech Stack) modern berbasis PHP dan JavaScript.

### Backend
*   **Framework**: [Laravel](https://laravel.com) (Versi 10.x/11.x).
*   **Language**: PHP 8.1+.
*   **Features Used**: Eloquent ORM, Middleware Check, Policies, Artisan Console.

### Frontend
*   **Templating**: Blade Template Engine.
*   **CSS Framework**: [Tailwind CSS](https://tailwindcss.com) (Utility-first framework).
*   **Interactivity**: [Alpine.js](https://alpinejs.dev) (Lightweight JavaScript framework).
*   **Icons**: Heroicons (SVG).

### Database & Storage
*   **Database**: MySQL.
*   **Cache/Session**: File/Database (Configurable to Redis).
*   **File Storage**: Local Storage (Public folder) for product images & documents.

### Key Libraries/Packages
*   `barryvdh/laravel-dompdf`: Export Laporan via PDF.
*   `phpoffice/phpspreadsheet`: Import/Export Data via Excel.
*   `simplesoftwareio/simple-qrcode`: Generate QR Code Anggota.

---

## 3. Setup & Deployment

Panduan instalasi untuk pengembang atau deployment server.

### Prasyarat
Pastikan `Composer`, `PHP`, `Node.js`, dan `MySQL` sudah terinstall.

### Langkah-langkah Instalasi

1.  **Clone Repository**
    ```bash
    git clone https://your-repository-url.com/koperasi.git
    cd koperasi
    ```

2.  **Install Dependencies**
    ```bash
    # Backend Dependencies
    composer install

    # Frontend Dependencies
    npm install
    ```

3.  **Environment Setup**
    *   Duplikat file `.env.example` menjadi `.env`.
    *   Generate Application Key:
        ```bash
        php artisan key:generate
        ```
    *   Konfigurasi Database di file `.env`:
        ```env
        DB_DATABASE=koperasi_db
        DB_USERNAME=root
        DB_PASSWORD=
        ```

4.  **Database Migration & Seeding**
    ```bash
    # Migrasi tabel dan isi data dummy/awal
    php artisan migrate --seed
    ```

5.  **Build Assets**
    ```bash
    # Untuk Development
    npm run dev
    
    # Untuk Production
    npm run build
    ```

6.  **Jalankan Aplikasi**
    ```bash
    php artisan serve
    ```

---

## 4. Features Guide (Panduan Fitur)

### A. Modul Anggota (Membership)
*   **Registrasi & Approval**: Pendaftaran anggota baru dengan status *Pending* hingga disetujui Admin.
*   **Kartu Anggota Digital**: QR Code unik untuk identifikasi anggota.
*   **Status Keanggotaan**: Status Aktif/Non-aktif mempengaruhi akses ke fitur pinjaman dan belanja.

### B. Modul Keuangan (Finance)
*   **Simpanan (Savings)**:
    *   Mencatat Simpanan Pokok, Wajib, dan Sukarela.
    *   Cetak Buku Tabungan.
*   **Pinjaman (Loans)**:
    *   **Pengajuan**: Anggota mengajukan pinjaman dengan tenor tertentu.
    *   **Approval**: Admin menyetujui, menolak, atau mencairkan dana.
    *   **Simulasi**: Kalkulator angsuran untuk estimasi pembayaran.
    *   **Pembayaran**: Pencatatan angsuran bulanan.

### C. Koperasi Mart (Commerce)
*   **Manajemen Produk (Admin)**:
    *   CRUD Produk, Kategori, Stok.
    *   **Fitur Pre-Order (PO)**: Mengaktifkan status PO pada produk kosong agar tetap bisa dipesan dengan estimasi waktu (ETA).
    *   Bulk Upload via Excel.
*   **Point of Sales (POS)**:
    *   Interface Kasir untuk penjualan offline.
    *   Scan Barcode.
    *   Pembayaran Tunai, Transfer, atau **Potong Saldo / Kredit Anggota**.
    *   Cetak Struk Thermal.
*   **Member Shop (E-Commerce)**:
    *   Katalog Online untuk anggota.
    *   Keranjang Belanja & Checkout.
    *   Riwayat Transaksi & Order Tracking (Melacak status pesanan).

### D. Laporan (Reports) & Informasi
*   **Export Data**: Laporan Anggota, Simpanan, dan Pinjaman dalam format PDF dan Excel.
*   **AD-ART**: Halaman informasi Anggaran Dasar & Rumah Tangga.
*   **Pengumuman**: Broadcast informasi penting ke dashboard anggota.

### E. Administrasi (Settings)
*   **Master Data**: Pengaturan Departemen, Jabatan, dll.
*   **Role Management**: Mengatur hak akses pengguna (Admin, Petugas, Anggota).
*   **App Settings**: Konfigurasi nama aplikasi, logo, dan parameter dasar.

---

## 5. Database Schema Overview

Gambaran umum struktur database utama:

### User & Authentication
*   `users`: Menyimpan data login (email, password, role) untuk Admin dan Petugas.
*   `members`: Data profil anggota (NIK, Nama, Alamat, Status, Points). Terhubung ke `users` (opsional jika anggota bisa login).

### Finance (Simpan Pinjam)
*   `savings`: Transaksi simpanan (tipe: pokok, wajib, sukarela).
*   `loans`: Data pengajuan pinjaman (jumlah, bunga, tenor, status).
*   `loan_payments`: Riwayat pembayaran angsuran pinjaman.

### Commerce (Toko)
*   `categories`: Kategori produk.
*   `products`: Data barang (stok, harga, modal, is_preorder).
*   `transactions`: Header transaksi penjualan (total, user_id, payment_method).
*   `transaction_items`: Detail barang dalam setiap transaksi.
*   `vouchers`: Data kode promo/diskon.

---

## 6. Troubleshooting & FAQ

### Q: Tombol "Pre-Order" tidak muncul, padahal stok 0.
**A**: Pastikan Anda sudah mengedit produk tersebut di Admin Panel dan mencentang opsi **"Aktifkan Pre-Order"**. Jika tidak aktif, produk dengan stok 0 akan dianggap "Habis".

### Q: Error "Class not found" saat Checkout.
**A**: Biasanya terjadi karena cache classmap. Jalankan perintah:
```bash
composer dump-autoload
php artisan optimize:clear
```

### Q: Gambar produk tidak muncul (404).
**A**: Pastikan *symlink* storage sudah dibuat. Jalankan:
```bash
php artisan storage:link
```
Dan pastikan folder `public/storage` dapat diakses.

### Q: Printer Thermal tidak mencetak struk layout dengan benar.
**A**: Sesuaikan ukuran kertas di pengaturan printer Windows/Linux (58mm atau 80mm). Layout struk di aplikasi menggunakan CSS responsive, namun driver printer kadang membutuhkan setting margin 0.

---

## 7. Backups & Maintenance

### Daily Backup (Database)
Disarankan melakukan backup database harian menggunakan `mysqldump` atau fitur otomatis Laragon/Panel Hosting.
```bash
# Contoh command backup manual
mysqldump -u root -p koperasi_db > backup_koperasi_$(date +%F).sql
```

### File Backup
Lakukan backup berkala pada folder:
1.  `storage/app/public` (Gambar produk & dokumen anggota).
2.  `.env` (Konfigurasi aplikasi).

### Log Monitoring
Cek file log di `storage/logs/laravel.log` jika terjadi error 500 pada aplikasi untuk diagnosa lebih lanjut.

