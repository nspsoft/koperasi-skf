<?php
/**
 * SCRIPT OTOMATIS UNTUK MEMPERBAIKI BUILD FILES
 * 
 * Script ini akan:
 * 1. Download manifest.json dan file CSS/JS dari GitHub
 * 2. Menyimpan ke folder public/build yang benar
 * 3. Menghapus cache Laravel
 * 
 * CARA PAKAI:
 * 1. Upload file ini ke kopkarskf/public/fix_build.php
 * 2. Akses via browser: https://kopkarskf.com/fix_build.php
 * 3. Hapus file ini setelah selesai
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔧 Script Perbaikan Build Files</h1>";
echo "<pre>";

// Definisi path
$basePath = dirname(__DIR__); // /home/kopw7369/kopkarskf
$buildPath = __DIR__ . '/build'; // /home/kopw7369/kopkarskf/public/build
$assetsPath = $buildPath . '/assets';

// GitHub raw URLs untuk file build
$githubBase = 'https://raw.githubusercontent.com/nspsoft/koperasi-skf/master/public/build';
$files = [
    'manifest.json' => $buildPath . '/manifest.json',
    'assets/app-BinTSYpn.css' => $assetsPath . '/app-BinTSYpn.css',
    'assets/app-CUb4F-Bi.js' => $assetsPath . '/app-CUb4F-Bi.js',
];

// Step 1: Buat folder jika belum ada
echo "📁 Step 1: Membuat folder build...\n";
if (!is_dir($buildPath)) {
    if (mkdir($buildPath, 0755, true)) {
        echo "   ✓ Folder build dibuat: $buildPath\n";
    } else {
        echo "   ✗ Gagal membuat folder build!\n";
    }
} else {
    echo "   ✓ Folder build sudah ada\n";
}

if (!is_dir($assetsPath)) {
    if (mkdir($assetsPath, 0755, true)) {
        echo "   ✓ Folder assets dibuat: $assetsPath\n";
    } else {
        echo "   ✗ Gagal membuat folder assets!\n";
    }
} else {
    echo "   ✓ Folder assets sudah ada\n";
}

// Step 2: Download file dari GitHub
echo "\n📥 Step 2: Download file dari GitHub...\n";
$success = true;
foreach ($files as $remotePath => $localPath) {
    $url = $githubBase . '/' . $remotePath;
    echo "   Downloading: $remotePath\n";
    
    $content = @file_get_contents($url);
    if ($content !== false) {
        if (file_put_contents($localPath, $content)) {
            $size = strlen($content);
            echo "   ✓ Berhasil ($size bytes)\n";
        } else {
            echo "   ✗ Gagal menyimpan file!\n";
            $success = false;
        }
    } else {
        echo "   ✗ Gagal download dari GitHub!\n";
        $success = false;
    }
}

// Step 3: Clear Laravel cache
echo "\n🧹 Step 3: Membersihkan cache Laravel...\n";
$artisan = $basePath . '/artisan';
if (file_exists($artisan)) {
    chdir($basePath);
    
    exec('php artisan view:clear 2>&1', $output1, $code1);
    echo "   view:clear: " . ($code1 === 0 ? "✓" : "⚠") . "\n";
    
    exec('php artisan config:clear 2>&1', $output2, $code2);
    echo "   config:clear: " . ($code2 === 0 ? "✓" : "⚠") . "\n";
    
    exec('php artisan cache:clear 2>&1', $output3, $code3);
    echo "   cache:clear: " . ($code3 === 0 ? "✓" : "⚠") . "\n";
} else {
    echo "   ⚠ Artisan tidak ditemukan, skip cache clear\n";
}

// Step 4: Verifikasi
echo "\n✅ Step 4: Verifikasi file...\n";
foreach ($files as $remotePath => $localPath) {
    if (file_exists($localPath)) {
        $size = filesize($localPath);
        echo "   ✓ " . basename($localPath) . " ($size bytes)\n";
    } else {
        echo "   ✗ " . basename($localPath) . " TIDAK ADA!\n";
        $success = false;
    }
}

echo "\n" . str_repeat("=", 50) . "\n";
if ($success) {
    echo "🎉 SELESAI! Production seharusnya sudah normal.\n";
    echo "   Silakan refresh website Anda (CTRL+F5).\n";
    echo "\n⚠️  PENTING: Hapus file fix_build.php ini setelah selesai!\n";
} else {
    echo "⚠️  Ada beberapa error. Silakan cek log di atas.\n";
}

echo "</pre>";
?>
