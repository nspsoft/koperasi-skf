---
description: Prosedur Deploy Produksi (Hosting cPanel)
---

# Prosedur Penyiapan File Update Hosting (cPanel)

Workflow ini memastikan file ZIP yang dihasilkan memiliki struktur dan konfigurasi yang TEPAT untuk hosting dengan struktur folder terpisah (`public_html` vs `kopkarskf`).

## 1. Persiapan Awal
- [ ] Jalankan `php artisan optimize:clear`
- [ ] Hapus file debug/temporary: `file_fix.php`, `jurus_jitu.php`, dll.
- [ ] Jalankan `npm run build` untuk aset terbaru.

## 2. Struktur Folder Deployment
Alih-alih menyatukan semua dalam satu folder, buat struktur staging sementara:
- `/deploy_stage/kopkarskf/` -> Berisi semua code inti (app, bootstrap, config, database, lang, resources, routes, storage, tests, vendor, artisan, composer.json, dll).
- `/deploy_stage/public_html/` -> Berisi hasil `public/` lokal (build, images, dll).

## 3. Modifikasi Kritis: index.php
**JANGAN** gunakan `public/index.php` lokal mentah-mentah untuk `deploy_stage/public_html/`.
Buat file baru atau timpa dengan konten berikut agar mengarah ke folder `kopkarskf` yang sejajar dengan `public_html`:

```php
<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Arahkan ke folder core 'kopkarskf' yang berada satu level di atas public_html
if (file_exists($maintenance = __DIR__.'/../kopkarskf/storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../kopkarskf/vendor/autoload.php';

$app = require_once __DIR__.'/../kopkarskf/bootstrap/app.php';

$app->handleRequest(Request::capture());
```

## 4. Packing
- Zip folder `deploy_stage` menjadi `koperasi_production_update.zip`.
- Instruksikan user untuk mengekstrak zip tersebut di `home` directory hosting, sehingga folder `kopkarskf` dan `public_html` langsung menimpa target dengan benar.
