<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Item;
use App\Models\StockTransaction;
use App\Models\StockOpname;
use App\Models\Unit;

$pass = 0;
$fail = 0;

function runTest(string $label, callable $fn): void {
    global $pass, $fail;
    try {
        $result = $fn();
        echo "[PASS] $label\n";
        if ($result !== null && $result !== true) {
            if (is_object($result)) {
                $arr = method_exists($result, 'toArray') ? $result->toArray() : (array)$result;
                // Only show key columns
                $preview = array_intersect_key($arr, array_flip(['id', 'nama_barang', 'kode_barang', 'stok_kecil', 'stok_besar', 'current_stock', 'current_stock_kecil', 'current_stock_besar', 'nama_satuan', 'item_id', 'jenis']));
                echo "       → " . json_encode($preview) . "\n";
            } else {
                echo "       → " . json_encode($result) . "\n";
            }
        }
        $pass++;
    } catch (\Throwable $e) {
        echo "[FAIL] $label\n";
        echo "       → ERROR: " . $e->getMessage() . "\n";
        $fail++;
    }
}

echo "=== MODEL VALIDATION ===\n\n";

runTest('Item::first()', fn() => Item::first());
runTest('StockTransaction::first()', fn() => StockTransaction::first());
runTest('StockOpname::first()', fn() => StockOpname::first());
runTest('Unit::first()', fn() => Unit::first());

echo "\n=== RELATIONSHIP VALIDATION ===\n\n";

runTest(
    'Item::with(unitKecil, unitBesar, category, itemLocation, budgetSource)->first()',
    function () {
        $item = Item::with(['unitKecil', 'unitBesar', 'category', 'itemLocation', 'budgetSource'])->first();
        if (!$item) return "No items in DB";
        return [
            'id' => $item->id,
            'nama_barang' => $item->nama_barang,
            'unitKecil' => $item->unitKecil?->nama_satuan,
            'unitBesar' => $item->unitBesar?->nama_satuan,
            'category' => $item->category?->nama_kategori,
            'itemLocation' => $item->itemLocation?->nama_lokasi,
            'budgetSource' => $item->budgetSource?->nama_sumber,
        ];
    }
);

echo "\n=== ACCESSOR VALIDATION ===\n\n";

runTest('Item::first()?->current_stock', function () {
    $item = Item::first();
    return $item ? ['current_stock' => $item->current_stock] : 'No item found';
});

runTest('Item::first()?->current_stock_kecil', function () {
    $item = Item::first();
    return $item ? ['current_stock_kecil' => $item->current_stock_kecil] : 'No item found';
});

runTest('Item::first()?->current_stock_besar', function () {
    $item = Item::first();
    return $item ? ['current_stock_besar' => $item->current_stock_besar] : 'No item found';
});

echo "\n=== RESULT: $pass PASS, $fail FAIL ===\n";
