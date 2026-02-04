<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$email = 'sutiknotri333@gmail.com';
$password = 'Alifa123';

$user = \App\Models\User::where('email', $email)->first();

if (!$user) {
    echo "User NOT FOUND\n";
    exit;
}

echo "User Found: " . $user->name . "\n";
echo "Password Hash in DB: " . substr($user->password, 0, 20) . "...\n";

if (\Illuminate\Support\Facades\Hash::check($password, $user->password)) {
    echo "RESULT: Password MATCHES!\n";
} else {
    echo "RESULT: Password DOES NOT MATCH.\n";
    // Check if it was double hashed
    // We can't easily check double hash without knowing the first hash, but we can suspect.
}
