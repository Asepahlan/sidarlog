<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$migrations = ['2026_05_16_072800_patch_items_dual_unit', '2026_05_16_072900_patch_stock_transactions_dual_unit'];

foreach ($migrations as $m) {
    $exists = DB::table('migrations')->where('migration', $m)->exists();
    echo "$m: " . ($exists ? 'RAN' : 'PENDING') . "\n";
}
