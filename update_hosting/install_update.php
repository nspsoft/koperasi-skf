<?php
// Script Update Otomatis Koperasi
// Versi: 2026-01-18
error_reporting(E_ALL);
ini_set('display_errors', 1);

$baseDir = dirname(__DIR__); // Root folder aplikasi (default)

// Smart Detection: Jika artisan tidak ada di sini, coba naik satu level lagi (misal jika ditaruh di dalam folder public)
if (!file_exists($baseDir . '/artisan') && file_exists(dirname($baseDir) . '/artisan')) {
    $baseDir = dirname($baseDir);
}

// Konfigurasi Mapping Folder (Dari folder update -> ke folder asli)
$mappings = [
    'bug_fixes/LoanApprovedNotification.php' => 'app/Notifications/LoanApprovedNotification.php',
    'bug_fixes/PosController.php' => 'app/Http/Controllers/PosController.php',
    'bug_fixes/ShopController.php' => 'app/Http/Controllers/ShopController.php',
    'bug_fixes/MemberController.php' => 'app/Http/Controllers/MemberController.php',
    
    'pwa/manifest.json' => 'public/manifest.json',
    'pwa/sw.js' => 'public/sw.js',
    'pwa/offline.html' => 'public/offline.html',
    
    'pwa/components/pwa-install-banner.blade.php' => 'resources/views/components/pwa-install-banner.blade.php',
    'pwa/components/push-notification.blade.php' => 'resources/views/components/push-notification.blade.php',
    'pwa/layouts/app.blade.php' => 'resources/views/layouts/app.blade.php',
    'pwa/layouts/login.blade.php' => 'resources/views/auth/login.blade.php',
    
    'push_notification/PushNotificationController.php' => 'app/Http/Controllers/PushNotificationController.php',
    'push_notification/PushSubscription.php' => 'app/Models/PushSubscription.php',
    'push_notification/webpush.php' => 'config/webpush.php',
    'push_notification/web.php' => 'routes/web.php',
    // Migration is wildcard handled below
];

$message = "";
$status = "ready"; // ready, success, error

if (isset($_POST['run_update'])) {
    $log = [];
    $errors = [];
    
    // 1. Copy Files defined in mapping
    foreach ($mappings as $source => $dest) {
        $sourcePath = __DIR__ . '/' . $source;
        $destPath = $baseDir . '/' . $dest;
        
        if (file_exists($sourcePath)) {
            // Create directory if not exists
            if (!file_exists(dirname($destPath))) {
                mkdir(dirname($destPath), 0755, true);
            }
            
            if (copy($sourcePath, $destPath)) {
                $log[] = "✅ Updated: $dest";
            } else {
                $errors[] = "❌ Failed to copy: $source -> $dest";
            }
        } else {
            // Optional files might not exist, just log warning
            // $log[] = "⚠️ Source missing (skipped): $source";
        }
    }
    
    // 2. Copy Icons Directory (Special case for folder)
    $iconSource = __DIR__ . '/pwa/icons';
    $iconDest = $baseDir . '/public/icons';
    if (is_dir($iconSource)) {
        if (!is_dir($iconDest)) mkdir($iconDest, 0755, true);
        $files = scandir($iconSource);
        foreach ($files as $file) {
            if ($file != "." && $file != "..") {
                copy("$iconSource/$file", "$iconDest/$file");
            }
        }
        $log[] = "✅ Updated: Icons folder";
    }

    // 3. Copy Migrations (Special case for wildcard)
    $migSource = __DIR__ . '/push_notification';
    $migDest = $baseDir . '/database/migrations';
    if (is_dir($migSource)) {
        $files = scandir($migSource);
        foreach ($files as $file) {
            if (strpos($file, 'create_push_subscriptions_table.php') !== false) {
                 copy("$migSource/$file", "$migDest/$file");
                 $log[] = "✅ Updated: Migration file";
            }
        }
    }

    // 4. Run Artisan Commands
    try {
        // We can't easily run artisan from web if exec() is disabled, 
        // but we can try calling the kernel directly or using system calls if available.
        // Fallback: Instruct user to use the route cleaner.
        
        // Simulating Artisan calls by clearing files if possible
        // Clear views
        array_map('unlink', glob("$baseDir/storage/framework/views/*.php"));
        $log[] = "✅ Views Cleared (File removal)";
        
        // Disable Route Cache (delete file)
        if(file_exists("$baseDir/bootstrap/cache/routes-v7.php")) unlink("$baseDir/bootstrap/cache/routes-v7.php");
        if(file_exists("$baseDir/bootstrap/cache/routes.php")) unlink("$baseDir/bootstrap/cache/routes.php");
        $log[] = "✅ Route Cache Cleared";

        // Disable Config Cache
        if(file_exists("$baseDir/bootstrap/cache/config.php")) unlink("$baseDir/bootstrap/cache/config.php");
        $log[] = "✅ Config Cache Cleared";
        
        // Trigger generic cache clear if possible via shell
        if (function_exists('shell_exec')) {
            shell_exec("cd $baseDir && php artisan migrate --force");
            $log[] = "✅ Migration attempted via shell";
        } else {
             $log[] = "⚠️ Cannot run migration automatically (shell_exec disabled). Database might need manual migration if you enabled push notifications.";
        }

    } catch (Exception $e) {
        $errors[] = "Error clearing cache: " . $e->getMessage();
    }

    if (empty($errors)) {
        $status = "success";
        $message = "Update berhasil diselesaikan!";
    } else {
        $status = "error";
        $message = "Terjadi beberapa kesalahan.";
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Koperasi Auto-Updater</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background: #f3f4f6; display: flex; justify-content: center; padding-top: 50px; }
        .card { background: white; width: 600px; padding: 30px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        h1 { color: #059669; margin-top: 0; }
        .btn { background: #059669; color: white; border: none; padding: 12px 24px; border-radius: 6px; cursor: pointer; font-size: 16px; font-weight: bold; width: 100%; transition: background 0.2s; }
        .btn:hover { background: #047857; }
        .log { background: #1f2937; color: #10b981; padding: 15px; border-radius: 6px; font-family: monospace; margin-top: 20px; max-height: 300px; overflow-y: auto; }
        .error { color: #ef4444; }
        .warning { color: #f59e0b; }
        .item { padding: 8px 0; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; font-size: 14px; }
        .success-box { background: #dcfce7; color: #166534; padding: 15px; border-radius: 6px; margin-bottom: 20px; text-align: center; font-weight: bold; }
    </style>
</head>
<body>
    <div class="card">
        <h1>🚀 Koperasi Auto-Updater</h1>
        
        <?php if ($status === 'success'): ?>
            <div class="success-box"><?php echo $message; ?></div>
            <p>Update selesai. Silakan cek aplikasi Anda.</p>
            <div class="log">
                <?php foreach ($log as $l) echo "<div>$l</div>"; ?>
                <?php foreach ($errors as $e) echo "<div class='error'>$e</div>"; ?>
            </div>
            <p style="margin-top: 20px; text-align: center;"><a href="/" style="color: #059669; text-decoration: none;">&larr; Kembali ke Home</a></p>
        
        <?php elseif ($status === 'error'): ?>
            <div style="color: red; margin-bottom: 20px; font-weight: bold;">Terjadi Kesalahan:</div>
            <div class="log">
                <?php foreach ($log as $l) echo "<div>$l</div>"; ?>
                <?php foreach ($errors as $e) echo "<div class='error'>$e</div>"; ?>
            </div>
            <form method="post">
                <button type="submit" name="run_update" class="btn" style="margin-top: 20px;">Coba Lagi</button>
            </form>

        <?php else: ?>
            <p>Script ini akan secara otomatis:</p>
            <ul>
                <li>✅ Mengupdate file Bug Fixes</li>
                <li>✅ Menginstall fitur PWA (Icon & Manifest)</li>
                <li>✅ Mengupdate Layout & Components</li>
                <li>✅ Membersihkan Cache Route & View</li>
            </ul>
            
            <div style="background: #fffbeb; padding: 10px; border-radius: 6px; border: 1px solid #fcd34d; margin: 20px 0; font-size: 0.9em; color: #92400e;">
                ⚠️ <strong>PENTING:</strong> Pastikan Anda sudah mem-backup website sebelum melanjutkan.
            </div>

            <form method="post">
                <button type="submit" name="run_update" class="btn">Mulai Update Otomatis</button>
            </form>
            
            <div style="margin-top: 20px; border-top: 1px solid #eee; padding-top: 10px;">
                <strong>Files to update:</strong>
                <?php foreach ($mappings as $src => $dst): ?>
                    <div class="item">
                        <span style="color: #6b7280;"><?php echo basename($dst); ?></span>
                        <span style="color: #059669; font-size: 11px;">Ready</span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
