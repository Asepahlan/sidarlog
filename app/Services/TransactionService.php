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

            /** @var Item $item */
            $item = $this->itemRepository->findWithLock($data['barang_id']);

            if ($type === 'keluar') {
                // Validate stock is sufficient using total global stock
                $stokTersedia = $item->stok_saat_ini_kecil;

                if ($data['jumlah_barang_kecil'] > 0 && $stokTersedia < $data['jumlah_barang_kecil']) {
                    throw ValidationException::withMessages([
                        'jumlah_barang_kecil' => "Stok tidak mencukupi. Total stok tersedia: {$stokTersedia} {$item->satuanKecil?->nama_satuan}.",
                    ]);
                }

                // Deduct from total stock cache
                $item->stok_saat_ini_kecil = max(0, $item->stok_saat_ini_kecil - $data['jumlah_barang_kecil']);
                $item->save();

            } elseif ($type === 'masuk') {
                // Increment stock cache
                $item->stok_saat_ini_kecil += $data['jumlah_barang_kecil'];
                $item->save();
            }

            \App\Services\NotificationService::checkAndNotifyForItem($item);

            $tx = $this->transactionRepository->create($data);

            return $tx;
        });
    }

    public function updateTransaction(StockTransaction $transaction, array $data)
    {
        return DB::transaction(function () use ($transaction, $data) {
            $data['jumlah_barang_kecil'] = (int) ($data['jumlah_barang_kecil'] ?? 0);

            // 1. Rollback old stock effect
            /** @var Item $oldItem */
            $oldItem = Item::lockForUpdate()->findOrFail($transaction->barang_id);
            if ($transaction->jenis === 'keluar') {
                $oldItem->stok_saat_ini_kecil += $transaction->jumlah_barang_kecil;
            } elseif ($transaction->jenis === 'masuk') {
                $oldItem->stok_saat_ini_kecil = max(0, $oldItem->stok_saat_ini_kecil - $transaction->jumlah_barang_kecil);
            }
            $oldItem->save();

            // 2. Apply new stock effect
            /** @var Item $newItem */
            $newItem = Item::lockForUpdate()->findOrFail($data['barang_id']);
            if ($transaction->jenis === 'keluar') {
                $stokTersedia = $newItem->stok_saat_ini_kecil;
                if ($data['jumlah_barang_kecil'] > 0 && $stokTersedia < $data['jumlah_barang_kecil']) {
                    // Revert oldItem save if we fail here, but since we are in DB transaction it auto-rollbacks.
                    throw ValidationException::withMessages([
                        'jumlah_barang_kecil' => "Stok tidak mencukupi. Total stok tersedia: {$stokTersedia} {$newItem->satuanKecil?->nama_satuan}.",
                    ]);
                }
                $newItem->stok_saat_ini_kecil = max(0, $newItem->stok_saat_ini_kecil - $data['jumlah_barang_kecil']);
            } elseif ($transaction->jenis === 'masuk') {
                $newItem->stok_saat_ini_kecil += $data['jumlah_barang_kecil'];
            }
            $newItem->save();

            // 3. Update transaction record
            $transaction->update($data);

            // 4. Trigger notifications
            \App\Services\NotificationService::checkAndNotifyForItem($oldItem);
            if ($oldItem->id !== $newItem->id) {
                \App\Services\NotificationService::checkAndNotifyForItem($newItem);
            }

            return $transaction;
        });
    }

    public function getRecentTransactions($limit = 10)
    {
        return $this->transactionRepository->getRecent($limit);
    }
}
