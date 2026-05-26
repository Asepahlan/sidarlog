<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

Schema::table('stock_transactions', function (Blueprint $table) {
    if (Schema::hasColumn('stock_transactions', 'user_id')) {
        $table->renameColumn('user_id', 'pengguna_id');
        echo "Renamed stock_transactions.user_id to pengguna_id\n";
    }
    if (Schema::hasColumn('stock_transactions', 'jumlah')) {
        $table->dropColumn('jumlah');
        echo "Dropped stock_transactions.jumlah\n";
    }
});
