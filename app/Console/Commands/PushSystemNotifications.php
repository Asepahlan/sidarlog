<?php

namespace App\Console\Commands;

use App\Models\Item;
use App\Models\StockTransaction;
use App\Models\User;
use App\Notifications\ExpiredItemNotification;
use App\Notifications\StockAlertNotification;
use App\Notifications\TransactionNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class PushSystemNotifications extends Command
{
    protected $signature   = 'notifications:push {--fresh : Delete all existing notifications first}';
    protected $description = 'Push system notifications for low stock, expired items, and recent transactions';

    public function handle(): int
    {
        $admins = User::all();

        if ($this->option('fresh')) {
            \Illuminate\Support\Facades\DB::table('notifications')->truncate();
            $this->info('Existing notifications cleared.');
        }

        $pushed = 0;

        // ── 1. STOK HABIS ─────────────────────────────────────────────
        $emptyItems = Item::with(['lokasiBarang', 'satuanKecil'])->where('stok_saat_ini_kecil', 0)->get();
        foreach ($emptyItems as $item) {
            foreach ($admins as $user) {
                // Avoid duplicate: check if same notification exists in last 24h
                $exists = $user->notifications()
                    ->where('type', StockAlertNotification::class)
                    ->where('data->item_id', $item->id)
                    ->where('data->type', 'danger')
                    ->where('created_at', '>=', Carbon::now()->subHours(24))
                    ->exists();
                if (!$exists) {
                    $user->notify(new StockAlertNotification($item, 'empty'));
                    $pushed++;
                }
            }
        }
        $this->info("✓ Stok habis: {$emptyItems->count()} items");

        // ── 2. STOK MENIPIS ───────────────────────────────────────────
        $lowItems = Item::with(['lokasiBarang', 'satuanKecil'])->where('stok_saat_ini_kecil', '>', 0)
            ->whereColumn('stok_saat_ini_kecil', '<=', 'stok_minimal')
            ->get();
        foreach ($lowItems as $item) {
            foreach ($admins as $user) {
                $exists = $user->notifications()
                    ->where('type', StockAlertNotification::class)
                    ->where('data->item_id', $item->id)
                    ->where('data->type', 'warning')
                    ->where('created_at', '>=', Carbon::now()->subHours(24))
                    ->exists();
                if (!$exists) {
                    $user->notify(new StockAlertNotification($item, 'low'));
                    $pushed++;
                }
            }
        }
        $this->info("✓ Stok menipis: {$lowItems->count()} items");

        // ── 3. SUDAH KADALUARSA ───────────────────────────────────────
        $expiredItems = Item::with(['lokasiBarang', 'satuanKecil'])->whereNotNull('tgl_kadaluarsa')
            ->where('tgl_kadaluarsa', '<', Carbon::today())
            ->get();
        foreach ($expiredItems as $item) {
            foreach ($admins as $user) {
                $exists = $user->notifications()
                    ->where('type', ExpiredItemNotification::class)
                    ->where('data->item_id', $item->id)
                    ->where('data->type', 'danger')
                    ->where('created_at', '>=', Carbon::now()->subHours(24))
                    ->exists();
                if (!$exists) {
                    $user->notify(new ExpiredItemNotification($item, 'expired'));
                    $pushed++;
                }
            }
        }
        $this->info("✓ Sudah expired: {$expiredItems->count()} items");

        // ── 4. MENDEKATI KADALUARSA (30 hari) ────────────────────────
        $nearExpiry = Item::with(['lokasiBarang', 'satuanKecil'])->whereNotNull('tgl_kadaluarsa')
            ->whereBetween('tgl_kadaluarsa', [Carbon::today(), Carbon::today()->addDays(30)])
            ->get();
        foreach ($nearExpiry as $item) {
            foreach ($admins as $user) {
                $exists = $user->notifications()
                    ->where('type', ExpiredItemNotification::class)
                    ->where('data->item_id', $item->id)
                    ->where('data->type', 'warning')
                    ->where('created_at', '>=', Carbon::now()->subHours(24))
                    ->exists();
                if (!$exists) {
                    $user->notify(new ExpiredItemNotification($item, 'near_expired'));
                    $pushed++;
                }
            }
        }
        $this->info("✓ Mendekati expired: {$nearExpiry->count()} items");

        // ── 5. TRANSAKSI TERBARU (24 jam terakhir) ───────────────────
        $recentTx = StockTransaction::with('item')
            ->where('created_at', '>=', Carbon::now()->subHours(24))
            ->latest()
            ->take(10)
            ->get();
        foreach ($recentTx as $tx) {
            foreach ($admins as $user) {
                $exists = $user->notifications()
                    ->where('type', TransactionNotification::class)
                    ->where('data->tx_id', $tx->id)
                    ->exists();
                if (!$exists) {
                    $user->notify(new TransactionNotification($tx));
                    $pushed++;
                }
            }
        }
        $this->info("✓ Transaksi terbaru: {$recentTx->count()}");

        $this->newLine();
        $this->info("Total notifikasi dipush: {$pushed}");
        return 0;
    }
}
