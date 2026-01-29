<?php
/**
 * Script untuk menjalankan migration dan seeder via browser
 * HAPUS FILE INI SETELAH SELESAI DIGUNAKAN!
 */

// Security: hanya bisa diakses dengan parameter rahasia
$secretKey = 'run-migration-2026';

if (!isset($_GET['key']) || $_GET['key'] !== $secretKey) {
    die('Akses ditolak. Tambahkan ?key=' . $secretKey . ' di URL');
}

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "<pre style='font-family: monospace; background: #1a1a2e; color: #0f0; padding: 20px; border-radius: 10px;'>";
echo "===========================================\n";
echo "🚀 MIGRATION & SEEDER RUNNER\n";
echo "===========================================\n\n";

try {
    // Step 1: Run Migration
    echo "📦 Step 1: Running migrations...\n";
    $exitCode = Artisan::call('migrate', ['--force' => true]);
    echo Artisan::output();
    
    if ($exitCode === 0) {
        echo "✅ Migrations completed!\n\n";
    } else {
        echo "⚠️ Migration returned code: {$exitCode}\n\n";
    }
    
    // Step 2: Run PermissionSeeder
    echo "🌱 Step 2: Running PermissionSeeder...\n";
    $exitCode = Artisan::call('db:seed', [
        '--class' => 'PermissionSeeder',
        '--force' => true
    ]);
    echo Artisan::output();
    
    if ($exitCode === 0) {
        echo "✅ Seeder completed!\n\n";
    } else {
        echo "⚠️ Seeder returned code: {$exitCode}\n\n";
    }
    
    echo "===========================================\n";
    echo "🎉 SELESAI! Silakan refresh halaman roles.\n";
    echo "⚠️ HAPUS FILE INI SETELAH SELESAI!\n";
    echo "===========================================\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "</pre>";
