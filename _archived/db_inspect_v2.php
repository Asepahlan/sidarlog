<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$targetTables = ['items', 'stock_transactions', 'stock_opnames', 'units', 'item_locations', 'budget_sources'];

foreach ($targetTables as $table) {
    if (Schema::hasTable($table)) {
        echo "Table: $table\n";
        $columns = DB::select("DESCRIBE $table");
        foreach ($columns as $column) {
            echo "  - {$column->Field}\n";
        }
    } else {
        echo "Table: $table (NOT FOUND)\n";
    }
    echo "\n";
}
