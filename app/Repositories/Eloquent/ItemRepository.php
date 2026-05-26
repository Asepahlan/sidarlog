<?php

namespace App\Repositories\Eloquent;

use App\Models\Item;
use App\Repositories\Interfaces\ItemRepositoryInterface;

class ItemRepository extends BaseRepository implements ItemRepositoryInterface
{
    /**
     * Default relations to eager-load whenever items are fetched for display.
     */
    protected const DEFAULT_RELATIONS = [
        'kategori',
        'satuanKecil',
        'satuanBesar',
        'lokasiBarang',
        'sumberAnggaran',
    ];

    public function __construct(Item $model)
    {
        parent::__construct($model);
    }

    /**
     * Override: fetch all items with default eager-loaded relations.
     * Eliminates N+1 queries on the Master Barang listing page.
     */
    public function all()
    {
        return $this->model->with(self::DEFAULT_RELATIONS)->get();
    }

    /**
     * Override: paginated items with default eager-loaded relations.
     */
    public function paginate(int $perPage = 15, array $relations = [])
    {
        $relations = empty($relations) ? self::DEFAULT_RELATIONS : $relations;

        return $this->model
            ->with($relations)
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Override: find single item with default eager-loaded relations.
     */
    public function find($id)
    {
        return $this->model->with(self::DEFAULT_RELATIONS)->findOrFail($id);
    }

    /**
     * Find item by kode_barang.
     */
    public function findByCode($code)
    {
        return $this->model
            ->with(self::DEFAULT_RELATIONS)
            ->where('kode_barang', $code)
            ->first();
    }

    /**
     * Get stock history for a single item, with related warehouse and user data.
     */
    public function getStockHistory($id)
    {
        return $this->find($id)
            ->transactions()
            ->with(['gudang', 'pengguna'])
            ->orderBy('tgl_transaksi', 'desc')
            ->get();
    }

    /**
     * Get items with low stock (below stok_minimal).
     * Used by Dashboard to avoid repeated individual queries.
     */
    public function getLowStock()
    {
        return $this->model
            ->with(['kategori', 'satuanKecil', 'lokasiBarang'])
            ->whereRaw('stok_saat_ini_kecil <= stok_minimal')
            ->get();
    }

    /**
     * Get items near expiry (within next 30 days).
     * Used by Dashboard alerts.
     */
    public function getNearExpiry(int $days = 30)
    {
        return $this->model
            ->with(['kategori', 'satuanKecil', 'lokasiBarang'])
            ->whereNotNull('tgl_kadaluarsa')
            ->whereBetween('tgl_kadaluarsa', [now(), now()->addDays($days)])
            ->get();
    }

    /**
     * Get only trashed items.
     */
    public function onlyTrashed()
    {
        return $this->model
            ->onlyTrashed()
            ->with(self::DEFAULT_RELATIONS)
            ->latest()
            ->get();
    }

    /**
     * Restore a trashed item.
     */
    public function restore($id)
    {
        $item = $this->model->withTrashed()->findOrFail($id);
        return $item->restore();
    }

    /**
     * Force delete an item (permanent).
     */
    public function forceDelete($id)
    {
        $item = $this->model->withTrashed()->findOrFail($id);
        return $item->forceDelete();
    }
}
