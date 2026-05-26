<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Item;
use App\Models\StockTransaction;
use App\Repositories\Eloquent\ItemRepository;
use App\Repositories\Eloquent\TransactionRepository;
use Illuminate\Support\Facades\DB;

$pass = 0; $fail = 0;

function bench(string $label, callable $fn): void {
    global $pass, $fail;
    DB::flushQueryLog();
    DB::enableQueryLog();
    try {
        $result = $fn();
        $queries = DB::getQueryLog();
        $count   = count($queries);
        $records = is_countable($result) ? count($result) : 1;
        echo "[PASS] $label\n";
        echo "       Records: {$records} | DB Queries executed: {$count}\n";
        $pass++;
    } catch (\Throwable $e) {
        $fail++;
        echo "[FAIL] $label → " . $e->getMessage() . "\n";
    }
    DB::disableQueryLog();
}

echo "=== BEFORE (no eager loading simulation) ===\n\n";

// Simulate N+1: load items then access relation in loop
bench('Items WITHOUT eager loading (N+1 simulation)', function () {
    $items = Item::get(); // no with()
    foreach ($items as $item) {
        $_ = $item->category?->nama_kategori;   // triggers query per row
        $_ = $item->unitKecil?->nama_satuan;    // triggers query per row
    }
    return $items;
});

echo "\n=== AFTER (with eager loading via Repository) ===\n\n";

$itemRepo = app(ItemRepository::class);
$txRepo   = app(TransactionRepository::class);

bench('ItemRepository::all() – full eager loading', fn() => $itemRepo->all());

bench('ItemRepository::paginate(15) – paginated with eager loading', fn() => $itemRepo->paginate(15));

bench('ItemRepository::getLowStock()', fn() => $itemRepo->getLowStock());

bench('ItemRepository::getNearExpiry(30)', fn() => $itemRepo->getNearExpiry(30));

bench('TransactionRepository::getRecent(10) – full eager loading', fn() => $txRepo->getRecent(10));

bench('TransactionRepository::paginate(15)', fn() => $txRepo->paginate(15));

echo "\n=== RESULT: $pass PASS, $fail FAIL ===\n";
echo "\n[NOTE] With eager loading, total DB queries should be constant\n";
echo "       (e.g. 1 per relation) regardless of record count.\n";
