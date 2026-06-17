<?php

namespace App\Services;

use App\Models\Item;
use App\Models\User;
use App\Notifications\ExpiredItemNotification;
use App\Notifications\NearExpiredNotification;
use App\Notifications\LowStockNotification;
use App\Notifications\StockAlertNotification;
use Carbon\Carbon;

class NotificationService
{
    /**
     * Check notification conditions for a given item and notify admins.
     */
    public static function checkAndNotifyForItem(Item $item): void
    {
        // Prevent notifications during database seeding or migrations
        if (app()->runningInConsole() && !str_contains(implode(' ', $_SERVER['argv'] ?? []), 'notifications:push')) {
            // If running a seeder or migration, skip triggering notifications to prevent DB lock issues
            if (str_contains(implode(' ', $_SERVER['argv'] ?? []), 'db:seed') || str_contains(implode(' ', $_SERVER['argv'] ?? []), 'migrate')) {
                return;
            }
        }

        // Eager load relationships to prevent N+1 queries during alerts
        $item->loadMissing(['lokasiBarang', 'satuanKecil']);

        $users = User::all();

        // 1. STOK HABIS (stok_saat_ini_kecil == 0)
        if ($item->stok_saat_ini_kecil == 0) {
            foreach ($users as $user) {
                $exists = $user->notifications()
                    ->where('type', StockAlertNotification::class)
                    ->where('data->item_id', $item->id)
                    ->where('data->type', 'danger')
                    ->where('created_at', '>=', Carbon::now()->subHours(24))
                    ->exists();

                if (!$exists) {
                    $user->notify(new StockAlertNotification($item, 'empty'));
                }
            }
        }

        // 2. STOK MINIMUM (0 < stok_saat_ini_kecil <= stok_minimal)
        if ($item->stok_saat_ini_kecil > 0 && $item->stok_saat_ini_kecil <= $item->stok_minimal) {
            foreach ($users as $user) {
                $exists = $user->notifications()
                    ->where('type', LowStockNotification::class)
                    ->where('data->item_id', $item->id)
                    ->where('created_at', '>=', Carbon::now()->subHours(24))
                    ->exists();

                if (!$exists) {
                    $user->notify(new LowStockNotification($item));
                }
            }
        }

        // 3. EXPIRED (tgl_kadaluarsa < Carbon::today())
        if ($item->tgl_kadaluarsa && $item->tgl_kadaluarsa->lt(Carbon::today())) {
            foreach ($users as $user) {
                $exists = $user->notifications()
                    ->where('type', ExpiredItemNotification::class)
                    ->where('data->item_id', $item->id)
                    ->where('data->type', 'danger')
                    ->where('created_at', '>=', Carbon::now()->subHours(24))
                    ->exists();

                if (!$exists) {
                    $user->notify(new ExpiredItemNotification($item, 'expired'));
                }
            }
        }

        // 4. NEAR EXPIRED (Carbon::today() <= tgl_kadaluarsa <= Carbon::today() + 30 days)
        if ($item->tgl_kadaluarsa &&
            $item->tgl_kadaluarsa->gte(Carbon::today()) &&
            $item->tgl_kadaluarsa->lte(Carbon::today()->addDays(30))) {
            foreach ($users as $user) {
                $exists = $user->notifications()
                    ->where('type', NearExpiredNotification::class)
                    ->where('data->item_id', $item->id)
                    ->where('created_at', '>=', Carbon::now()->subHours(24))
                    ->exists();

                if (!$exists) {
                    $user->notify(new NearExpiredNotification($item));
                }
            }
        }
    }
}
