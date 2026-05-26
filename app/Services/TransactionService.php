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
            $item = $this->itemRepository->find($data['barang_id']);

            if ($type === 'keluar') {
                // Validate stock is sufficient using cached columns (fast, no N+1)
                $stokKecilTersedia = $item->stok_saat_ini_kecil;
                $stokBesarTersedia = $item->stok_saat_ini_besar;

                if ($data['jumlah_barang_kecil'] > 0 && $stokKecilTersedia < $data['jumlah_barang_kecil']) {
                    throw ValidationException::withMessages([
                        'jumlah_barang_kecil' => "Stok tidak mencukupi. Stok Kecil tersedia: {$stokKecilTersedia}.",
                    ]);
                }
                if ($data['jumlah_barang_besar'] > 0 && $stokBesarTersedia < $data['jumlah_barang_besar']) {
                    throw ValidationException::withMessages([
                        'jumlah_barang_besar' => "Stok tidak mencukupi. Stok Besar tersedia: {$stokBesarTersedia}.",
                    ]);
                }

                // Deduct from stock cache
                $item->stok_saat_ini_kecil = max(0, $stokKecilTersedia - $data['jumlah_barang_kecil']);
                $item->stok_saat_ini_besar = max(0, $stokBesarTersedia - $data['jumlah_barang_besar']);
                $item->save();

            } elseif ($type === 'masuk') {
                // Increment stock cache
                $item->stok_saat_ini_kecil += $data['jumlah_barang_kecil'];
                $item->stok_saat_ini_besar += $data['jumlah_barang_besar'];
                $item->save();
            }

            $tx = $this->transactionRepository->create($data);

            ActivityLog::log(
                "Transaksi {$tx->jenis}: {$item->nama_barang} | Kecil: {$data['jumlah_barang_kecil']} | Besar: {$data['jumlah_barang_besar']}",
                "Inventory",
                $data
            );

            return $tx;
        });
    }

    public function getRecentTransactions($limit = 10)
    {
        return $this->transactionRepository->getRecent($limit);
    }
}
