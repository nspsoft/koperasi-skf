# 🔧 PANDUAN MAINTENANCE SISTEM LENGKAP
**Aplikasi Koperasi Karyawan Digital v3.0**

Dokumen ini berisi Standard Operating Procedure (SOP) pemeliharaan sistem menyeluruh, mencakup Server, Aplikasi, Database, Hardware Toko, dan Integritas Data Keuangan.

---

## 📅 1. JADWAL MAINTENANCE RUTIN

### 1.1 Checklist Harian (Daily) - Pukul 08:00 WIB
*Wajib dilakukan oleh SysAdmin & SPV Toko sebelum operasional.*

| Area | Item Checklist | Cara Cek / Aksi |
|---|---|---|
| **Server** | Uptime & Aksesibilitas | Buka web `https://kopkarskf.com` |
| **Server** | Resource Load (CPU/RAM) | Command: `htop` |
| **App** | Error Log Hari Ini | `grep "ERROR" storage/logs/laravel.log` |
| **Queue** | Antrian Email/SHU | `php artisan queue:monitor database` |
| **Mart** | Koneksi Printer Thermal | Test Print struk kosong |
| **Mart** | Scanner Barcode | Test scan 1 barang random |
| **Finance** | Saldo Midtrans/WA | Cek dashboard vendor (kuota tersisa) |

### 1.2 Checklist Mingguan (Weekly) - Jumat Sore
| Area | Item Checklist | Cara Cek / Aksi |
|---|---|---|
| **Database** | Slow Query Log | Cek query > 3 detik |
| **Security** | Review Failed Login | Cek Audit Log menu Security |
| **Storage** | Kapasitas Disk | `df -h` (Waspada jika usage > 80%) |
| **Accounting** | Balance Check | Cek Neraca (Aset = Kewajiban + Modal) |

### 1.3 Checklist Bulanan (Monthly) - Akhir Bulan
| Area | Item Checklist | Cara Cek / Aksi |
|---|---|---|
| **Backup** | Full Offsite Backup | Download file .zip ke Harddisk Eksternal |
| **Performance** | Tuning Database | `mysqlcheck -o koperasi` (Optimize) |
| **Hardware** | Cleaning Perangkat | Bersihkan debu PC Kasir & Printer |
| **Security** | SSL & Domain Expiry | Cek tanggal expired Let's Encrypt |

---

## 🖥️ 2. MONITORING KESEHATAN SERVER

### 2.1 Service Status
Pastikan komponen utama berjalan "Running".
```bash
# Web Server & DB
sudo systemctl status nginx
sudo systemctl status mysql
sudo systemctl status php8.2-fpm

# Background Process (Penting untuk Email/SHU)
sudo systemctl status supervisor
sudo systemctl status redis-server
```
*Jika mati: `sudo systemctl start <service_name>`*

### 2.2 Disk & Storage Monitoring
Mencegah server down karena disk penuh.
```bash
df -h
```
Waspada folder logs atau backup yang membengkak di `/var/log` atau `/var/www/koperasi/storage`.

---

## 💹 3. MAINTENANCE INTEGRITAS DATA (FINANCE)

Prosedur khusus untuk memastikan akurasi data keuangan koperasi.

### 3.1 Cek Keseimbangan (Balance Check)
Lakukan setiap minggu untuk mencegah selisih pembukuan.
1. Buka Menu **Laporan > Neraca**.
2. Pastikan `Total Aset` = `Total Kewajiban + Ekuitas`.
3. Jika selisih, segera cek **Jurnal Umum** minggu tersebut.

### 3.2 Rekonsiliasi Transaksi Gantung
1. Cek tabel `transactions` dengan status `PENDING` > 24 jam.
2. Cek database Midtrans/Bank.
3. Update status manual jika uang sudah masuk tapi sistem belum update (Callback gagal).

---

## � 4. MAINTENANCE HARDWARE MART (TOKO)

Perawatan perangkat fisik di kasir agar transaksi lancar.

### 4.1 Printer Thermal (Struk)
- **Bersihkan Head Printer:** Gunakan alkohol isopropyl & kapas setiap ganti roll kertas.
- **Cek Kabel:** Pastikan kabel USB/LAN tidak terlipat/terjepit.

### 4.2 Barcode Scanner
- **Lensa:** Lap lensa scanner dari debu/minyak jari.
- **Kabel:** Cek konektivitas, jika sering putus-nyambung, ganti kabel USB.

### 4.3 PC Kasir
- **Disk Cleanup:** Hapus file temp browser (Cache/Cookies) agar aplikasi POS ringan.
- **Update Browser:** Pastikan Chrome/Edge versi terbaru untuk security patch.

---

## 💾 5. PROSEDUR BACKUP & RESTORE (DRP)

### 5.1 Strategi Backup (3-2-1)
Sistem menggunakan `spatie/laravel-backup` yang membackup **Database + File Storage** (Foto Produk/KTP).

- **Jadwal:** Otomatis tiap pukul 01:00 WIB.
- **Lokasi Server:** `/var/www/koperasi/storage/app/Koperasi/`
- **Lokasi Cloud:** AWS S3 / Google Drive (Terkonfigurasi).

### 5.2 Cara Restore Database (Jika Corrupt)
```bash
# 1. Unzip file backup
unzip backup-2026-01-18.zip

# 2. Restore Database MySQL
mysql -u root -p koperasi < db-dumps/mysql-koperasi.sql

# 3. Restore File Gambar/Dokumen
cp -r storage/app/public/* /var/www/koperasi/storage/app/public/
```

---

## 🧹 6. HOUSEKEEPING & OPTIMASI

### 6.1 Membersihkan Sampah Sistem
```bash
# Hapus cache aplikasi & view
php artisan optimize:clear

# Hapus log file > 30 hari
find /var/www/koperasi/storage/logs -name "*.log" -type f -mtime +30 -delete

# Hapus session login yang expired
php artisan auth:clear-resets
```

### 6.2 Database Optimization
Lakukan sebulan sekali saat toko tutup.
```bash
# Rebuild index & defrag tables
mysqlcheck -u root -p --optimize koperasi
```

---

## 🔐 7. SECURITY OPERATIONS

### 7.1 Update Software (Patching)
```bash
# Update OS Security Patch
sudo apt update && sudo apt upgrade -y

# Update Library Aplikasi (Hanya jika ada rilis dari developer)
composer update --no-dev
```

### 7.2 Audit Akses User
Review user yang memiliki akses `Admin` atau `Manager`.
- Jika ada staff resign, segera set status `Inactive`.
- Force reset password admin setiap 90 hari.

### 7.3 API Token Rotation
Jika ada indikasi kebocoran data, regenerate key integrasi.
- **Midtrans Server Key:** Generate baru di dashboard Midtrans > Update `.env`.
- **Whatsapp API Key:** Disconnect & Re-scan QR.

---

## 🚨 8. EMERGENCY RESPONSE (DARURAT)

### Skenario A: Server Down Total
1. Cek fisik server / Panel VPS Provider (DigitalOcean/AWS).
2. Jika server nyala tapi web mati, restart Nginx: `sudo systemctl restart nginx`.
3. Jika database error, restart MySQL: `sudo systemctl restart mysql`.

### Skenario B: Aplikasi Lambat / Hang
1. Cek load: `htop`. Jika CPU 100%, cek proses apa yang memakan resource.
2. Cek slow query log database.
3. Restart PHP-FPM: `sudo systemctl restart php8.2-fpm`.

### Skenario C: Data Terhapus / Ransomware
1. **ISOLASI SERVER:** Putuskan koneksi internet.
2. Re-install OS baru (Clean Install).
3. Restore data dari **Backup Offline** (Harddisk Eksternal/Cloud).

---

**Kontak Darurat (24/7):**
- **IT Support Lead:** 0812-xxxx-xxxx
- **Vendor Server:** support@provider.com (Ticket ID: KOP-SKF)
