<?php

namespace App\Observers;

use App\Models\StockOpname;
use App\Models\ActivityLog;

class StockOpnameObserver
{
    /**
     * Handle the StockOpname "created" event.
     */
    public function created(StockOpname $opname): void
    {
        $itemName = $opname->barang ? $opname->barang->nama_barang : 'ID #' . $opname->barang_id;
        ActivityLog::log(
            "Stock Opname: {$itemName} | Sistem: {$opname->stok_sistem} → Fisik: {$opname->stok_fisik} | Selisih: {$opname->selisih}",
            "Inventory",
            $opname->toArray()
        );

        if ($opname->barang) {
            \App\Services\NotificationService::checkAndNotifyForItem($opname->barang);
        }
    }
}
