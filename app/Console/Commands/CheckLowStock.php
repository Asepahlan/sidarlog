<?php

namespace App\Console\Commands;

use App\Models\Item;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;
use App\Notifications\LowStockNotification;

class CheckLowStock extends Command
{
    protected $signature = 'inventory:check-low-stock';
    protected $description = 'Cek stok barang yang menipis dan kirim notifikasi';

    public function handle()
    {
        $items = Item::all()->filter(function ($item) {
            return $item->current_stock <= $item->stok_minimal;
        });

        if ($items->count() > 0) {
            $this->info("Ditemukan " . $items->count() . " barang dengan stok menipis.");
            
            // Send to users with role super_admin or admin_logistik
            $admins = User::role(['super_admin', 'admin_logistik'])->get();
            
            if ($admins->count() > 0) {
                Notification::send($admins, new LowStockNotification($items));
                $this->info("Notifikasi telah dikirim ke " . $admins->count() . " admin.");
            } else {
                $this->warn("Tidak ada user dengan role admin ditemukan.");
            }
        } else {
            $this->info("Semua stok dalam keadaan aman.");
        }
    }
}
