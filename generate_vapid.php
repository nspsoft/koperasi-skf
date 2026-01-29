<?php
require 'vendor/autoload.php';

use Minishlink\WebPush\VAPID;

try {
    $keys = VAPID::createVapidKeys();
    echo "\n=== GUNAKAN KUNCI INI DI .ENV HOSTING ===\n\n";
    echo "VAPID_SUBJECT=mailto:admin@kopkarskf.com\n";
    echo "VAPID_PUBLIC_KEY=" . $keys['publicKey'] . "\n";
    echo "VAPID_PRIVATE_KEY=" . $keys['privateKey'] . "\n";
    echo "\n=========================================\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
