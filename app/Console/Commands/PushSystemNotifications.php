<?php

namespace App\Console\Commands;

use App\Models\Item;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Services\NotificationService;

class PushSystemNotifications extends Command
{
    /**
     * Scheduler safety-net.
     * Tidak lagi menghapus histori notifikasi.
     */
    protected $signature = 'notifications:push';

    protected $description = 'Reconcile notifications for expired items, low stock and near expiry items';

    public function handle(): int
    {
        $this->info('Running notification reconciliation...');

        $beforeCount = DB::table('notifications')->count();

        $items = Item::with([
                'lokasiBarang',
                'satuanKecil'
            ])
            ->where(function ($query) {
                $query
                    ->whereColumn('stok_saat_ini_kecil', '<=', 'stok_minimal')
                    ->orWhere('stok_saat_ini_kecil', 0)
                    ->orWhere(function ($q) {
                        $q->whereNotNull('tgl_kadaluarsa')
                          ->where(
                              'tgl_kadaluarsa',
                              '<=',
                              Carbon::today()->addDays(30)
                          );
                    });
            })
            ->get();

        foreach ($items as $item) {
            NotificationService::checkAndNotifyForItem($item);
        }

        $afterCount = DB::table('notifications')->count();

        $pushed = max(0, $afterCount - $beforeCount);

        $this->info(
            "Notification reconciliation completed. {$pushed} notification(s) created."
        );

        return self::SUCCESS;
    }
}