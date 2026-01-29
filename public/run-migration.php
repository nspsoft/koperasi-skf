<?php
/**
 * Script untuk deploy dan menjalankan migration otomatis
 * HAPUS FILE INI SETELAH SELESAI DIGUNAKAN!
 */

// Security: hanya bisa diakses dengan parameter rahasia
$secretKey = 'run-migration-2026';

if (!isset($_GET['key']) || $_GET['key'] !== $secretKey) {
    die('Akses ditolak. Tambahkan ?key=' . $secretKey . ' di URL');
}

// Fix CWD: Pindah ke root project (asumsi file ini ada di folder /public)
chdir(__DIR__ . '/..');

// Function to run shell command
function runCmd($cmd) {
    $output = [];
    $returnVar = 0;
    exec($cmd . ' 2>&1', $output, $returnVar);
    echo "<div style='color: #ccc;'>$ " . htmlspecialchars($cmd) . "</div>";
    echo "<div style='color: " . ($returnVar === 0 ? '#0f0' : '#f55') . "; margin-bottom: 10px;'>" . 
         implode("<br>", array_map('htmlspecialchars', $output)) . "</div>";
    return $returnVar === 0;
}

echo "<pre style='font-family: monospace; background: #1a1a2e; color: #fff; padding: 20px; border-radius: 10px; white-space: pre-wrap;'>";
echo "===========================================\n";
echo "🚀 AUTO DEPLOYMENT & MIGRATION RUNNER\n";
echo "===========================================\n";
echo "📂 Working Directory: " . getcwd() . "\n\n";

// Step 0: Try Git Pull
echo "📦 Step 0: Attempting Git Pull...\n";
if (runCmd('git pull origin master')) {
    echo "✅ Git Pull successful!\n\n";
} else {
    echo "⚠️ Git Pull failed. Lanjut ke migration...\n\n";
}

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

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
    
    // Step 3: Clear Cache
    echo "🧹 Step 3: Clearing cache...\n";
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    echo "✅ Cache cleared!\n\n";
    
    echo "===========================================\n";
    echo "🎉 SELESAI! Silakan refresh halaman roles.\n";
    echo "⚠️ HAPUS FILE INI SETELAH SELESAI!\n";
    echo "===========================================\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "</pre>";
