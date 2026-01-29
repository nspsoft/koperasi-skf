<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$app->boot();

use App\Models\DocumentTemplate;

foreach(DocumentTemplate::all() as $t) {
    echo $t->name . ": " . $t->placeholders . "\n";
}
