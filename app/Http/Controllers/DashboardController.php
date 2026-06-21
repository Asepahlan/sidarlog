<?php

namespace App\Http\Controllers;

use App\Repositories\Eloquent\ItemRepository;
use App\Repositories\Eloquent\TransactionRepository;
use App\Models\Item;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\StockTransaction;
use App\Models\StockMutation;
use App\Models\StockOpname;

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
            'total_gudang'   => \App\Models\Warehouse::count(),
            'stok_menipis'   => $lowStockItems->count(),
            'barang_expired' => $expiredItems->count(),
            'near_expiry'    => $nearExpiryItems->count(),
        ];

        $recent_transactions = $this->transactionRepository->getRecent(8);
        $notifications       = Auth::user()->unreadNotifications->take(5);

        $operasional = [
            'masuk_hari_ini'   => StockTransaction::where('jenis', 'masuk')->whereDate('tgl_transaksi', Carbon::today())->count(),
            'keluar_hari_ini'  => StockTransaction::where('jenis', 'keluar')->whereDate('tgl_transaksi', Carbon::today())->count(),
            'mutasi_hari_ini'  => StockMutation::whereDate('created_at', Carbon::today())->count(),
            'opname_bulan_ini' => StockOpname::whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year)->count(),
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
            'operasional',
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
                'total_gudang'   => \App\Models\Warehouse::count(),
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
            'operasional' => [
                'masuk_hari_ini'   => StockTransaction::where('jenis', 'masuk')->whereDate('tgl_transaksi', Carbon::today())->count(),
                'keluar_hari_ini'  => StockTransaction::where('jenis', 'keluar')->whereDate('tgl_transaksi', Carbon::today())->count(),
                'mutasi_hari_ini'  => StockMutation::whereDate('created_at', Carbon::today())->count(),
                'opname_bulan_ini' => StockOpname::whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year)->count(),
            ],
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
        try {
            \Illuminate\Support\Facades\Artisan::call('optimize:clear');
            \Illuminate\Support\Facades\Artisan::call('stock:recalculate');
            \Illuminate\Support\Facades\Artisan::call('notifications:push');
            
            \App\Models\ActivityLog::log("Optimasi sistem + recalculate stok + push notifikasi", "System");
            
            return back()->with('success', 'Sistem berhasil dioptimasi, kalkulasi stok diselaraskan, dan notifikasi terbaru diperbarui.');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Maintenance Error: " . $e->getMessage(), [
                'exception' => $e
            ]);
            return back()->with('error', 'Gagal menjalankan maintenance rutin: ' . $e->getMessage());
        }
    }
}
