<?php

namespace App\Observers;

use App\Models\StockTransaction;
use App\Models\ActivityLog;

class StockTransactionObserver
{
    /**
     * Handle the StockTransaction "created" event.
     */
    public function created(StockTransaction $tx): void
    {
        $itemName = $tx->barang ? $tx->barang->nama_barang : 'ID #' . $tx->barang_id;
        ActivityLog::log(
            "Transaksi {$tx->jenis}: {$itemName} | Kecil: {$tx->jumlah_barang_kecil} | Besar: {$tx->jumlah_barang_besar}",
            "Inventory",
            $tx->toArray()
        );

        if ($tx->item) {
            \App\Services\NotificationService::checkAndNotifyForItem($tx->item);
        }
    }

    /**
     * Handle the StockTransaction "deleted" event.
     */
    public function deleted(StockTransaction $tx): void
    {
        ActivityLog::log("Menghapus Transaksi {$tx->jenis}: {$tx->no_referensi}", "Inventory");
    }
}
