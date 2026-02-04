<?php
// ALAT PERBAIKAN LOGIN (FIX LOGIN TOOL)
// File ini akan di-upload ke folder public (public_html)
// Akses: https://domain-anda.com/FIX_LOGIN_TOOL.php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Http\Request;

$message = '';
$messageType = '';

if (isset($_POST['email']) && isset($_POST['password'])) {
    $email = $_POST['email'];
    $newPassword = $_POST['password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Format email tidak valid!";
        $messageType = "error";
    } elseif (strlen($newPassword) < 1) {
        $message = "Password tidak boleh kosong!";
        $messageType = "error";
    } else {
        $user = User::where('email', $email)->first();
        
        if ($user) {
            // DIRECT ASSIGNMENT to bypass manual hashing in controller if any
            // User model handles 'hashed' cast
            $user->password = $newPassword;
            $user->save();
            
            $message = "✅ Sukses! Password untuk <b>{$user->name}</b> ($email) berhasil di-reset menjadi: <b>$newPassword</b>";
            $messageType = "success";
        } else {
            $message = "❌ User dengan email <b>$email</b> tidak ditemukan.";
            $messageType = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fix Login Tool - Koperasi SKF</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f3f4f6; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .card { background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); width: 100%; max-width: 400px; }
        h1 { margin-top: 0; color: #1f2937; font-size: 1.5rem; text-align: center; }
        .alert { padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem; font-size: 0.875rem; }
        .alert.error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .alert.success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        label { display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem; }
        input { width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem; margin-bottom: 1rem; box-sizing: border-box; }
        button { width: 100%; background: #2563eb; color: white; padding: 0.75rem; border: none; border-radius: 0.375rem; font-weight: 600; cursor: pointer; transition: background 0.2s; }
        button:hover { background: #1d4ed8; }
        .footer { margin-top: 1.5rem; text-align: center; font-size: 0.75rem; color: #6b7280; }
    </style>
</head>
<body>
    <div class="card">
        <h1>🔧 Fix Login Tool</h1>
        
        <?php if ($message): ?>
            <div class="alert <?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div>
                <label for="email">Email User Bermasalah</label>
                <input type="email" id="email" name="email" required placeholder="contoh: user@email.com">
            </div>

            <div>
                <label for="password">Set Password Baru</label>
                <input type="text" id="password" name="password" required value="password">
            </div>

            <button type="submit">Reset Password Sekarang</button>
        </form>

        <div class="footer">
            <p>Gunakan alat ini untuk mereset password user.</p>
            <p style="color: #ef4444; font-weight: bold;">⚠️ SANGAT DISARANKAN MENGHAPUS FILE INI DARI HOSTING SETELAH SELESAI!</p>
        </div>
    </div>
</body>
</html>
