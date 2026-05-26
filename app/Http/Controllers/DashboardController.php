<?php

namespace App\Http\Controllers;

use App\Repositories\Eloquent\ItemRepository;
use App\Repositories\Eloquent\TransactionRepository;
use App\Models\Item;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected $itemRepository;
    protected $transactionRepository;

    public function __construct(ItemRepository $itemRepository, TransactionRepository $transactionRepository)
    {
        $this->itemRepository        = $itemRepository;
        $this->transactionRepository = $transactionRepository;
    }

    public function index()
    {
        $lowStockItems   = $this->itemRepository->getLowStock();
        $nearExpiryItems = $this->itemRepository->getNearExpiry(30);
        $expiredItems    = $this->getExpiredItems();

        $stats = [
            'total_barang'   => Item::count(),
            'total_pegawai'  => User::count(),
            'stok_menipis'   => $lowStockItems->count(),
            'barang_expired' => $expiredItems->count(),
            'near_expiry'    => $nearExpiryItems->count(),
        ];

        $recent_transactions = $this->transactionRepository->getRecent(8);
        $notifications       = Auth::user()->unreadNotifications->take(5);

        $health = [
            'db_sync' => 99.8,
            'load'    => rand(15, 30),
            'storage' => 65.2,
        ];

        // Pre-map data for Alpine.js (avoids complex inline PHP in Blade)
        $lowStockJson = $lowStockItems->take(10)->map(function ($i) {
            return [
                'id'            => $i->id,
                'nama_barang'   => $i->nama_barang,
                'kode_barang'   => $i->kode_barang,
                'lokasi'        => optional($i->lokasiBarang)->nama_lokasi ?: '-',
                'stok_saat_ini' => $i->stok_saat_ini_kecil ?? 0,
                'stok_minimal'  => $i->stok_minimal ?? 0,
                'satuan'        => optional($i->satuanKecil)->nama_satuan ?: 'pcs',
            ];
        })->values();

        $expiredJson = $expiredItems->take(10)->map(function ($i) {
            return [
                'id'             => $i->id,
                'nama_barang'    => $i->nama_barang,
                'kode_barang'    => $i->kode_barang,
                'lokasi'         => optional($i->lokasiBarang)->nama_lokasi ?: '-',
                'tgl_kadaluarsa' => $i->tgl_kadaluarsa->format('d/m/Y'),
                'expired_days'   => Carbon::today()->diffInDays($i->tgl_kadaluarsa),
            ];
        })->values();

        $nearExpiryJson = $nearExpiryItems->take(10)->map(function ($i) {
            return [
                'id'             => $i->id,
                'nama_barang'    => $i->nama_barang,
                'kode_barang'    => $i->kode_barang,
                'lokasi'         => optional($i->lokasiBarang)->nama_lokasi ?: '-',
                'tgl_kadaluarsa' => $i->tgl_kadaluarsa->format('d/m/Y'),
                'days_left'      => Carbon::today()->diffInDays($i->tgl_kadaluarsa),
            ];
        })->values();

        return view('dashboard', compact(
            'stats',
            'recent_transactions',
            'notifications',
            'health',
            'lowStockItems',
            'nearExpiryItems',
            'expiredItems',
            'lowStockJson',
            'expiredJson',
            'nearExpiryJson'
        ));
    }

    /**
     * AJAX endpoint: return live dashboard stats for polling refresh.
     */
    public function realtimeData()
    {
        $lowStock    = $this->itemRepository->getLowStock();
        $nearExpiry  = $this->itemRepository->getNearExpiry(30);
        $expired     = $this->getExpiredItems();

        return response()->json([
            'stats' => [
                'total_barang'   => Item::count(),
                'total_pegawai'  => User::count(),
                'stok_menipis'   => $lowStock->count(),
                'barang_expired' => $expired->count(),
                'near_expiry'    => $nearExpiry->count(),
            ],
            'low_stock' => $lowStock->take(10)->map(fn($item) => [
                'id'            => $item->id,
                'nama_barang'   => $item->nama_barang,
                'kode_barang'   => $item->kode_barang,
                'lokasi'        => optional($item->lokasiBarang)->nama_lokasi ?? '-',
                'stok_saat_ini' => $item->stok_saat_ini_kecil ?? 0,
                'stok_minimal'  => $item->stok_minimal ?? 0,
                'satuan'        => optional($item->satuanKecil)->nama_satuan ?? 'pcs',
            ]),
            'expired' => $expired->take(10)->map(fn($item) => [
                'id'             => $item->id,
                'nama_barang'    => $item->nama_barang,
                'kode_barang'    => $item->kode_barang,
                'lokasi'         => optional($item->lokasiBarang)->nama_lokasi ?? '-',
                'tgl_kadaluarsa' => $item->tgl_kadaluarsa->format('d/m/Y'),
                'expired_days'   => Carbon::today()->diffInDays($item->tgl_kadaluarsa),
            ]),
            'near_expiry' => $nearExpiry->take(10)->map(fn($item) => [
                'id'             => $item->id,
                'nama_barang'    => $item->nama_barang,
                'kode_barang'    => $item->kode_barang,
                'lokasi'         => optional($item->lokasiBarang)->nama_lokasi ?? '-',
                'tgl_kadaluarsa' => $item->tgl_kadaluarsa->format('d/m/Y'),
                'days_left'      => Carbon::today()->diffInDays($item->tgl_kadaluarsa),
            ]),
            'unread_notifications' => Auth::user()->unreadNotifications()->count(),
        ]);
    }

    /**
     * Get expired items with eager loading.
     */
    private function getExpiredItems()
    {
        return Item::with(['kategori', 'satuanKecil', 'lokasiBarang'])
            ->whereNotNull('tgl_kadaluarsa')
            ->where('tgl_kadaluarsa', '<', Carbon::today())
            ->get();
    }

    public function optimize()
    {
        \Illuminate\Support\Facades\Artisan::call('optimize:clear');
        \Illuminate\Support\Facades\Artisan::call('stock:recalculate');
        \Illuminate\Support\Facades\Artisan::call('notifications:push', ['--fresh' => true]);
        \App\Models\ActivityLog::log("Optimasi sistem + recalculate stok + push notifikasi", "System");
        return back()->with('success', 'Sistem berhasil dioptimasi dan notifikasi diperbarui.');
    }
}
