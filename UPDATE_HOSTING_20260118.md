# 📦 Daftar File Update - 18 Januari 2026

## 🔴 BUG FIXES (Prioritas Tinggi - Upload Dulu)

### 1. Perbaikan Bug Aplikasi
| File | Keterangan |
|------|------------|
| `app/Notifications/LoanApprovedNotification.php` | Fix: `tenor` → `duration_months` |
| `app/Http/Controllers/PosController.php` | Fix: amount negatif, customer_name, duplikat |
| `app/Http/Controllers/ShopController.php` | Fix: hapus payment_method yang tidak ada |
| `app/Http/Controllers/MemberController.php` | Fix: 403 Unauthorized pada Membership Data |

---

## 🟢 PWA ENHANCEMENTS (Fitur Baru)

### 2. Icon PWA (Upload ke `public/icons/`)
| File | Ukuran |
|------|--------|
| `public/icons/icon-72x72.png` | 72x72 |
| `public/icons/icon-96x96.png` | 96x96 |
| `public/icons/icon-128x128.png` | 128x128 |
| `public/icons/icon-144x144.png` | 144x144 |
| `public/icons/icon-152x152.png` | 152x152 |
| `public/icons/icon-192x192.png` | 192x192 |
| `public/icons/icon-384x384.png` | 384x384 |
| `public/icons/icon-512x512.png` | 512x512 |

### 3. PWA Core Files
| File | Keterangan |
|------|------------|
| `public/manifest.json` | Manifest PWA lengkap |
| `public/sw.js` | Service Worker v4 dengan push notification |
| `public/offline.html` | Halaman offline |

### 4. Blade Components (Upload ke `resources/views/components/`)
| File | Keterangan |
|------|------------|
| `resources/views/components/pwa-install-banner.blade.php` | Banner install PWA |
| `resources/views/components/push-notification.blade.php` | Push notification UI |

### 5. Layout Updates
| File | Keterangan |
|------|------------|
| `resources/views/layouts/app.blade.php` | PWA meta tags & components |
| `resources/views/auth/login.blade.php` | PWA meta tags |

---

## 🟡 PUSH NOTIFICATION (Opsional - Perlu Setup VAPID)

### 6. Backend Files
| File | Keterangan |
|------|------------|
| `app/Http/Controllers/PushNotificationController.php` | Controller push notif |
| `app/Models/PushSubscription.php` | Model subscription |
| `config/webpush.php` | Config VAPID keys |
| `routes/web.php` | Routes push notification |

### 7. Database Migration
| File | Keterangan |
|------|------------|
| `database/migrations/2026_01_18_151900_create_push_subscriptions_table.php` | Tabel subscriptions |

---

## 📋 LANGKAH UPDATE DI HOSTING

### Step 1: Upload File Bug Fixes (WAJIB)
```bash
# Upload 4 file ini dulu
app/Notifications/LoanApprovedNotification.php
app/Http/Controllers/PosController.php
app/Http/Controllers/ShopController.php
app/Http/Controllers/MemberController.php
```

### Step 2: Upload PWA Files
```bash
# Upload folder icons (8 file)
public/icons/

# Upload PWA core files
public/manifest.json
public/sw.js
public/offline.html

# Upload blade components
resources/views/components/pwa-install-banner.blade.php
resources/views/components/push-notification.blade.php

# Upload layout updates
resources/views/layouts/app.blade.php
resources/views/auth/login.blade.php
```

### Step 3: Upload Push Notification Backend (OPSIONAL)
```bash
app/Http/Controllers/PushNotificationController.php
app/Models/PushSubscription.php
config/webpush.php
routes/web.php
database/migrations/2026_01_18_151900_create_push_subscriptions_table.php
```

### Step 4: Jalankan Perintah di Server
```bash
cd /path/to/koperasi

# Clear cache
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan config:clear

# Jalankan migration (jika upload push notification)
php artisan migrate --force

# Install package push notification (opsional)
composer require minishlink/web-push
```

### Step 5: Setup VAPID Keys (Jika pakai Push Notification)
Tambahkan ke file `.env` di hosting:
```env
VAPID_SUBJECT=https://kopkarskf.com
VAPID_PUBLIC_KEY=your_public_key_here
VAPID_PRIVATE_KEY=your_private_key_here
```

Generate VAPID keys di: https://web-push-codelab.glitch.me/

---

## ✅ CHECKLIST SETELAH UPDATE

- [ ] Bug fix loan notification sudah terupload
- [ ] Bug fix PosController sudah terupload
- [ ] Bug fix ShopController sudah terupload
- [ ] Bug fix MemberController sudah terupload (403 error)
- [ ] Icon PWA sudah terupload (8 file)
- [ ] manifest.json, sw.js, offline.html sudah terupload
- [ ] Blade components PWA sudah terupload
- [ ] Layout app.blade.php dan login.blade.php sudah terupload
- [ ] Cache sudah di-clear
- [ ] Test akses Membership Data dari akun anggota
- [ ] Test PWA di mobile (Add to Home Screen)
