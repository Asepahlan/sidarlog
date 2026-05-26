<?php

namespace App\Repositories\Eloquent;

use App\Models\StockTransaction;
use App\Repositories\Interfaces\TransactionRepositoryInterface;

class TransactionRepository extends BaseRepository implements TransactionRepositoryInterface
{
    /**
     * Default relations to eager-load for transaction display.
     */
    protected const DEFAULT_RELATIONS = [
        'barang',
        'gudang',
        'pengguna',
        'pihakKesatu',
        'pihakKedua',
    ];

    public function __construct(StockTransaction $model)
    {
        parent::__construct($model);
    }

    /**
     * Override: paginated transactions with full eager loading.
     * Eliminates N+1 on Barang Masuk/Keluar listing pages.
     */
    public function paginate(int $perPage = 15, array $relations = [])
    {
        $relations = empty($relations) ? self::DEFAULT_RELATIONS : $relations;

        return $this->model
            ->with($relations)
            ->latest('tgl_transaksi')
            ->paginate($perPage);
    }

    /**
     * Get recent transactions with full relations for Dashboard.
     */
    public function getRecent($limit = 10)
    {
        return $this->model
            ->with(self::DEFAULT_RELATIONS)
            ->orderBy('tgl_transaksi', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Generate a unique reference number for a given transaction type.
     */
    public function generateReference($type)
    {
        $prefix = match($type) {
            'masuk'  => 'BM',
            'keluar' => 'BK',
            default  => 'ADJ',
        };
        $date   = now()->format('Ymd');
        $last   = $this->model->where('jenis', $type)->whereDate('created_at', now())->count();
        $number = str_pad($last + 1, 4, '0', STR_PAD_LEFT);

        return "{$prefix}-{$date}-{$number}";
    }
}
