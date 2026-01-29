# 🚀 Panduan Update Koperasi - 18 Januari 2026

Panduan ini dirancang agar mudah diikuti langkah demi langkah, baik menggunakan **cPanel/File Manager** maupun **Terminal/SSH**.

---

## 📦 Tahap 1: Persiapan

1.  **Download File:** Pastikan Anda memiliki file `update_hosting_20260118.zip` yang baru saja dibuat.
2.  **Backup:** (Sangat Disarankan) Backup database dan satu folder project sebelum memulai, untuk berjaga-jaga.

---

## 📤 Tahap 2: Upload ke Hosting

1.  Buka **File Manager** (cPanel / Plesk) atau gunakan FTP.
2.  Masuk ke **root folder aplikasi** Anda (biasanya `public_html` atau folder `koperasi`).
3.  **Upload** file `update_hosting_20260118.zip` ke folder tersebut.
4.  **Ekstrak (Unzip)** file tersebut.
    *   *Akan muncul folder baru bernama `update_hosting`.*

---

## 📂 Tahap 3: Menyalin File (Inti Update)

Sekarang kita akan memindahkan file dari folder `update_hosting` ke lokasi yang seharusnya. Ikuti urutan ini:

### 1️⃣ Mengatasi BUG (PENTING 🔴)
Masuk ke folder: `update_hosting/bug_fixes`
Pindahkan/Copy file-file berikut ke lokasi tujuannya (Timpa/Overwrite jika diminta):

| Ambil File Dari Sini... | Pindahkan Ke Situ... (Root Folder Aplikasi) |
| :--- | :--- |
| `LoanApprovedNotification.php` | `app/Notifications/` |
| `PosController.php` | `app/Http/Controllers/` |
| `ShopController.php` | `app/Http/Controllers/` |
| `MemberController.php` | `app/Http/Controllers/` |

> **Catatan:** Jika menggunakan cPanel, Anda bisa select semua file -> Klik "Move" -> Hapus bagian `/update_hosting/bug_fixes` dari path tujuan agar langsung masuk ke folder asli.

### 2️⃣ Fitur PWA (Tampilan & Icon 🟢)
Masuk ke folder: `update_hosting/pwa`

**A. Folder Icons:**
*   Ambil isi folder `icons` (ada 8 gambar).
*   Pindahkan ke: `public/icons/`

**B. File Utama PWA:**
*   Pindahkan file `manifest.json` ➡ ke folder `public/`
*   Pindahkan file `sw.js` ➡ ke folder `public/`
*   Pindahkan file `offline.html` ➡ ke folder `public/`

**C. Components (Tampilan Banner):**
Masuk ke subfolder `components`, pindahkan keduanya ke `resources/views/components/`:
*   `pwa-install-banner.blade.php`
*   `push-notification.blade.php`

**D. Layouts (Setingan Tampilan):**
Masuk ke subfolder `layouts`, pindahkan ke lokasi masing-masing:
*   `app.blade.php` ➡ ke `resources/views/layouts/`
*   `login.blade.php` ➡ ke `resources/views/auth/`

### 3️⃣ Fitur Notifikasi (Opsional 🟡)
Jika Anda ingin mengaktifkan fitur notifikasi canggih, lakukan ini. Jika tidak, bisa dilewati.
Masuk ke folder: `update_hosting/push_notification`

*   `PushNotificationController.php` ➡ ke `app/Http/Controllers/`
*   `PushSubscription.php` ➡ ke `app/Models/`
*   `webpush.php` ➡ ke `config/`
*   `web.php` ➡ ke `routes/` (Hati-hati, ini menimpa routes, pastikan tidak ada route custom lain yang hilang)
*   `..._create_push_subscriptions_table.php` ➡ ke `database/migrations/`

---

## 🧹 Tahap 4: Pembersihan (Finalisasi)

Setelah semua file dipindahkan, kita perlu membersihkan "ingatan" (cache) aplikasi agar perubahan terbaca.

**Cara A: Menggunakan Terminal (SSH)** - *Paling Mudah*
Jalankan perintah ini di terminal server:
```bash
php artisan migrate --force
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan config:clear
```

**Cara B: Tanpa Terminal (Lewat Browser)**
Jika Anda tidak punya akses SSH, Anda bisa membuat "Route Pembersih" sementara.
1. Edit file `routes/web.php` di hosting.
2. Tambahkan baris ini di paling bawah:
   ```php
   Route::get('/clear-cache', function() {
       Artisan::call('cache:clear');
       Artisan::call('view:clear');
       Artisan::call('route:clear');
       Artisan::call('config:clear');
       Artisan::call('migrate', ['--force' => true]);
       return "Cache cleared & Migrated!";
   });
   ```
3. Buka browser, akses: `https://domain-anda.com/clear-cache`
4. Jika muncul tulisan "Cache cleared & Migrated!", berarti sukses.
5. **PENTING:** Hapus kembali kode tadi dari `routes/web.php` agar aman.

---

## ✅ Tahap 5: Pengecekan

1.  **Cek Bug Membership:** Login sebagai anggota -> buka menu "Membership Data". Seharusnya tidak ada error 403 lagi.
2.  **Cek PWA:** Buka di HP (Chrome/Safari). Cek apakah ada banner "Install Aplikasi" di bawah.
3.  **Cek Icon:** Saat install ("Add to Home Screen"), pastikan icon yang muncul adalah logo Koperasi, bukan logo default Laravel/Robot.

---
**Selesai!** Selamat, aplikasi Anda sudah terupdate.
