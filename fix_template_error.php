<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

// Include Laravel's autoloader
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "<h1>🛠️ Database Patch: Document Templates</h1>";

try {
    echo "1. Menambah kolom 'code' ke tabel 'document_templates'...<br>";
    Artisan::call('migrate', [
        '--path' => 'database/migrations/2026_01_29_125039_add_code_to_document_templates_table.php',
        '--force' => true
    ]);
    echo "✅ Migration Sukses!<br><br>";

    echo "2. Setting up default codes...<br>";
    Artisan::call('app:setup-document-codes');
    echo "✅ Setup Codes Sukses!<br><br>";

    echo "3. Clear Cache...<br>";
    Artisan::call('view:clear');
    Artisan::call('cache:clear');
    echo "✅ Cache Cleared!<br><br>";

    echo "<h3>🎉 SEMUA SELESAI! Silakan hapus file ini dan coba lagi menu Template Dokumen.</h3>";
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage();
}
