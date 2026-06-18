<?php

namespace App\Observers;

use App\Models\StockMutation;
use App\Models\ActivityLog;

class StockMutationObserver
{
    /**
     * Handle the StockMutation "created" event.
     */
    public function created(StockMutation $mutation): void
    {
        ActivityLog::log(
            "Mutasi stok {$mutation->jumlah_barang_kecil} unit barang ID#{$mutation->barang_id} dari gudang #{$mutation->gudang_asal_id} ke #{$mutation->gudang_tujuan_id}",
            "Mutasi Gudang",
            $mutation->toArray()
        );

        if ($mutation->barang) {
            \App\Services\NotificationService::checkAndNotifyForItem($mutation->barang);
        }
    }

    public function deleted(StockMutation $mutation): void
    {
        $itemName = $mutation->barang ? $mutation->barang->nama_barang : 'ID #' . $mutation->barang_id;
        ActivityLog::log(
            "Membatalkan Mutasi: {$mutation->no_mutasi} | Barang: {$itemName} | {$mutation->jumlah_barang_kecil} unit dari gudang #{$mutation->gudang_asal_id} ke #{$mutation->gudang_tujuan_id}",
            "Mutasi Gudang"
        );
    }
}
