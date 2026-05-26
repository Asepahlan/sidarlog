<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

$legacyTables = ['tbl_barang', 'tbl_barang_masuk', 'tbl_barang_keluar', 'tbl_barang_opname', 'tbl_reff_brg_satuan', 'tbl_reff_brg_lokasi', 'tbl_reff_brg_sumberanggaran'];

foreach ($legacyTables as $table) {
    echo "$table: " . (Schema::hasTable($table) ? 'EXISTS' : 'MISSING') . "\n";
}
