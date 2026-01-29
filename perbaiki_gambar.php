<?php
/**
 * Script Obat Kuat untuk Memperbaiki Symlink Storage Laravel
 * Upload ke folder PUBLIC (public_html) di hosting
 * Akses via browser: domain.com/perbaiki_gambar.php
 */

echo "<h3>🛠️ Perbaikan Jalur Gambar (Storage Link)</h3>";

// 1. Deteksi Lokasi Project
// Kita ada di folder public. Project root biasanya satu tingkat di atasnya.
$projectRoot = realpath(__DIR__ . '/..');

// Cek folder kopkarskf spesifik (jika folder terpisah)
if (!file_exists($projectRoot . '/storage/app/public') && file_exists(dirname($projectRoot) . '/kopkarskf/storage/app/public')) {
    $projectRoot = dirname($projectRoot) . '/kopkarskf';
}
// Cek folder kopkarskf di level yang sama (kadang public_html sejajar dengan kopkarskf)
elseif (!file_exists($projectRoot . '/storage/app/public') && file_exists(__DIR__ . '/../kopkarskf/storage/app/public')) {
    $projectRoot = realpath(__DIR__ . '/../kopkarskf');
}

$target = $projectRoot . '/storage/app/public';
$shortcut = __DIR__ . '/storage';

echo "📂 Lokasi Project Terdeteksi: <br><code>$projectRoot</code><br><br>";
echo "🎯 Target Gudang (Asli): <br><code>$target</code><br><br>";
echo "🔗 Shortcut (Link): <br><code>$shortcut</code><br><br>";

// 2. Validasi Target
if (!file_exists($target)) {
    die("<h2 style='color:red'>❌ GAGAL: Folder Storage Asli tidak ditemukan!</h2><p>Pastikan folder <code>storage/app/public</code> ada di dalam folder project.</p>");
}

// 3. Bersihkan Link Lama (Jika ada)
if (file_exists($shortcut)) {
    // Cek apakah itu folder asli atau link
    if (is_dir($shortcut) && !is_link($shortcut)) {
        echo "⚠️ Peringatan: Ada folder bernama 'storage' tapi BUKAN link (Mungkin folder manual).<br>";
        echo "Saya tidak berani menghapusnya otomatis demi keamanan. Silakan rename/hapus folder 'storage' di public_html secara manual lewat File Manager.<br>";
        exit;
    }
    
    echo "🧹 Membersihkan link lama yang rusak...<br>";
    @unlink($shortcut);
}

// 4. Buat Link Baru
try {
    if (symlink($target, $shortcut)) {
        echo "<h2 style='color:green'>✅ SUKSES! Jembatan Gambar Sudah Dibangun.</h2>";
        echo "<p>Silakan refresh website Bapak. Foto Produk & Profil harusnya sudah muncul.</p>";
        echo "<p style='color:gray'><i>Note: File ini boleh dihapus setelah dipakai.</i></p>";
    } else {
        throw new Exception("Fungsi symlink() gagal.");
    }
} catch (Exception $e) {
    echo "<h2 style='color:red'>❌ GAGAL MEMBUAT LINK</h2>";
    echo "Penyebab: " . $e->getMessage() . "<br>";
    echo "Solusi: Server Hosting memblokir pembuatan link via PHP. Silakan gunakan fitur 'Terminal' di cPanel dan ketik: <br>";
    echo "<code>ln -s $target $shortcut</code>";
}
?>
