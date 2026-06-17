<?php

namespace App\Services;

use App\Repositories\Interfaces\TransactionRepositoryInterface;
use App\Repositories\Interfaces\ItemRepositoryInterface;
use App\Models\ActivityLog;
use App\Models\Item;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class TransactionService
{
    protected $transactionRepository;
    protected $itemRepository;

    public function __construct(
        TransactionRepositoryInterface $transactionRepository,
        ItemRepositoryInterface $itemRepository
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->itemRepository = $itemRepository;
    }

    public function recordTransaction(array $data)
    {
        return DB::transaction(function () use ($data) {
            $type             = $data['jenis'];
            $data['no_referensi'] = $this->transactionRepository->generateReference($type);
            $data['pengguna_id']      = Auth::id();
            $data['tgl_transaksi'] = $data['tgl_transaksi'] ?? now();
            $data['jumlah_barang_kecil']  = (int) ($data['jumlah_barang_kecil'] ?? 0);
            $data['jumlah_barang_besar']  = (int) ($data['jumlah_barang_besar'] ?? 0);

            /** @var Item $item */
            $item = $this->itemRepository->findWithLock($data['barang_id']);

            if ($type === 'keluar') {
                // Validate stock is sufficient in chosen warehouse
                $stokKecilTersedia = $item->getStockKecilInWarehouse($data['gudang_id']);
                $stokBesarTersedia = $item->getStockBesarInWarehouse($data['gudang_id']);

                if ($data['jumlah_barang_kecil'] > 0 && $stokKecilTersedia < $data['jumlah_barang_kecil']) {
                    throw ValidationException::withMessages([
                        'jumlah_barang_kecil' => "Stok tidak mencukupi di gudang terpilih. Stok Kecil tersedia: {$stokKecilTersedia}.",
                    ]);
                }
                if ($data['jumlah_barang_besar'] > 0 && $stokBesarTersedia < $data['jumlah_barang_besar']) {
                    throw ValidationException::withMessages([
                        'jumlah_barang_besar' => "Stok tidak mencukupi di gudang terpilih. Stok Besar tersedia: {$stokBesarTersedia}.",
                    ]);
                }

                // Deduct from stock cache (using global current stock values)
                $item->stok_saat_ini_kecil = max(0, $item->stok_saat_ini_kecil - $data['jumlah_barang_kecil']);
                $item->stok_saat_ini_besar = max(0, $item->stok_saat_ini_besar - $data['jumlah_barang_besar']);
                $item->save();

            } elseif ($type === 'masuk') {
                // Increment stock cache
                $item->stok_saat_ini_kecil += $data['jumlah_barang_kecil'];
                $item->stok_saat_ini_besar += $data['jumlah_barang_besar'];
                $item->save();
            }

            \App\Services\NotificationService::checkAndNotifyForItem($item);

            $tx = $this->transactionRepository->create($data);

            return $tx;
        });
    }

    public function getRecentTransactions($limit = 10)
    {
        return $this->transactionRepository->getRecent($limit);
    }
}
