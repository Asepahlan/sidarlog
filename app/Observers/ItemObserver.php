<?php

namespace App\Observers;

use App\Models\Item;
use App\Models\ActivityLog;

class ItemObserver
{
    /**
     * Handle the Item "created" event.
     */
    public function created(Item $item): void
    {
        ActivityLog::log("Menambah barang: {$item->nama_barang} (Kode: {$item->kode_barang})", "Master Barang", $item->toArray());
        \App\Services\NotificationService::checkAndNotifyForItem($item);
    }

    /**
     * Handle the Item "updated" event.
     */
    public function updated(Item $item): void
    {
        ActivityLog::log("Memperbarui barang: {$item->nama_barang} (Kode: {$item->kode_barang})", "Master Barang", $item->getChanges());
        \App\Services\NotificationService::checkAndNotifyForItem($item);
    }

    /**
     * Handle the Item "deleted" event.
     */
    public function deleted(Item $item): void
    {
        if ($item->isForceDeleting()) {
            ActivityLog::log("Menghapus barang PERMANEN: {$item->nama_barang} (Kode: {$item->kode_barang})", "Master Barang");
        } else {
            ActivityLog::log("Menghapus barang (Soft Delete): {$item->nama_barang} (Kode: {$item->kode_barang})", "Master Barang");
        }
    }

    /**
     * Handle the Item "restored" event.
     */
    public function restored(Item $item): void
    {
        ActivityLog::log("Mengembalikan barang dari Trash: {$item->nama_barang} (Kode: {$item->kode_barang})", "Master Barang");
    }
}
