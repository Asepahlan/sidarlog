<?php

namespace App\Services;

use App\Repositories\Interfaces\StockOpnameRepositoryInterface;
use App\Repositories\Interfaces\TransactionRepositoryInterface;
use App\Repositories\Interfaces\ItemRepositoryInterface;
use App\Models\ActivityLog;
use App\Models\Item;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class StockOpnameService
{
    protected $opnameRepository;
    protected $transactionRepository;
    protected $itemRepository;

    public function __construct(
        StockOpnameRepositoryInterface $opnameRepository,
        TransactionRepositoryInterface $transactionRepository,
        ItemRepositoryInterface $itemRepository
    ) {
        $this->opnameRepository = $opnameRepository;
        $this->transactionRepository = $transactionRepository;
        $this->itemRepository = $itemRepository;
    }

    public function getAllOpnames()
    {
        return $this->opnameRepository->all(['barang', 'gudang', 'pengguna']);
    }

    public function processOpname(array $data)
    {
        return DB::transaction(function () use ($data) {
            /** @var Item $item */
            $item = $this->itemRepository->findWithLock($data['barang_id']);

            // Menggunakan stok spesifik gudang yang bersangkutan
            $stokSistem = $item->getStockKecilInWarehouse($data['gudang_id']);
            $stokFisik  = (int) $data['stok_fisik'];
            $selisih    = $stokFisik - $stokSistem;

            // 1. Save Opname Record
            $opname = $this->opnameRepository->create([
                'barang_id'    => $data['barang_id'],
                'gudang_id'    => $data['gudang_id'],
                'pengguna_id'  => Auth::id(),
                'stok_sistem'  => $stokSistem,
                'stok_fisik'   => $stokFisik,
                'selisih'      => $selisih,
                'keterangan'   => $data['keterangan'] ?? 'Stock Opname',
            ]);

            // 2. Create Adjustment Transaction & sync stock cache if there is a difference
            if ($selisih !== 0) {
                $this->transactionRepository->create([
                    'barang_id'     => $data['barang_id'],
                    'gudang_id'     => $data['gudang_id'],
                    'pengguna_id'       => Auth::id(),
                    'jenis'         => 'penyesuaian',
                    'jumlah_barang_kecil'  => $selisih,
                    'jumlah_barang_besar'  => 0,
                    'no_referensi'  => 'ADJ-' . now()->format('YmdHis'),
                    'tgl_transaksi' => now(),
                    'keterangan'    => "Penyesuaian otomatis dari Stock Opname #{$opname->id}",
                ]);

                // 3. Sync stock cache on the items table
                $item->stok_saat_ini_kecil = max(0, $item->stok_saat_ini_kecil + $selisih);
                $item->save();
            }

            \App\Services\NotificationService::checkAndNotifyForItem($item);

            return $opname;
        });
    }
}
