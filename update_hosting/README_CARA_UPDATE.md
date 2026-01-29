# 🚀 Panduan Update Koperasi (CARA SUPER MUDAH)

Update tanggal: 18 Januari 2026

---

## 🟢 Cara Otomatis (Direkomendasikan) 

Anda tidak perlu memindahkan file satu per satu. Cukup ikuti 3 langkah ini:

1.  **Upload & Extract**
    Upload file `update_hosting_20260118.zip` ke folder utama website Anda (biasanya `public_html`), lalu Extract. Anda akan melihat folder baru bernama `update_hosting`.

2.  **Jalankan Script Update**
    Buka browser dan akses alamat berikut:
    ```
    https://domain-anda.com/update_hosting/install_update.php
    ```
    *(Ganti domain-anda.com dengan nama domain asli)*

3.  **Klik Tombol Update**
    Tekan tombol hijau **"Mulai Update Otomatis"**. Tunggu hingga muncul pesan "Update berhasil diselesaikan".

**Selesai!** Aplikasi Anda sudah terupdate.
Jangan lupa hapus folder `update_hosting` jika sudah selesai agar bersih.

---

## 🔴 Cara Manual (Jika Cara Otomatis Gagal)

Jika karena alasan keamanan server script otomatis tidak jalan, silakan copy file secara manual:

1.  Copy isi `update_hosting/bug_fixes/*` -> ke folder aplikasi asli.
2.  Copy isi `update_hosting/pwa/*` -> ke folder aplikasi asli.
3.  Copy isi `update_hosting/push_notification/*` -> ke folder aplikasi asli.
4.  Jalankan `php artisan cache:clear` via terminal atau gunakan menu "Clear Cache" di cPanel Laravel Manager.
