<?php

namespace App\Services;

use App\Repositories\Interfaces\ItemRepositoryInterface;
use App\Models\ActivityLog;

class ItemService
{
    public $itemRepository;

    public function __construct(ItemRepositoryInterface $itemRepository)
    {
        $this->itemRepository = $itemRepository;
    }

    public function getItemById($id)
    {
        return $this->itemRepository->find($id);
    }

    public function getAllItems($perPage = 10, $search = null, $kategoriId = null)
    {
        return $this->itemRepository->paginate((int) $perPage, [], $search, $kategoriId);
    }

    public function createItem(array $data)
    {
        // Auto-generate kode_barang jika tidak diisi
        if (empty($data['kode_barang'])) {
            $lastId = \App\Models\Item::withTrashed()->max('id') ?? 0;
            $nextNumber = $lastId + 1;
            $data['kode_barang'] = 'BRG-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            
            // Ensure uniqueness
            while (\App\Models\Item::withTrashed()->where('kode_barang', $data['kode_barang'])->exists()) {
                $nextNumber++;
                $data['kode_barang'] = 'BRG-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            }
        }

        // Initialize stock cache columns to 0 if not supplied
        $data['stok_saat_ini_kecil'] = $data['stok_saat_ini_kecil'] ?? 0;

        $item = $this->itemRepository->create($data);

        // If initial stock is > 0 and warehouse is specified, create stock transaction
        if ($item->stok_saat_ini_kecil > 0 && !empty($data['gudang_id'])) {
            \App\Models\StockTransaction::create([
                'no_referensi'        => 'SA-' . str_pad($item->id, 5, '0', STR_PAD_LEFT) . '-' . date('dmy'),
                'barang_id'           => $item->id,
                'gudang_id'           => $data['gudang_id'],
                'pengguna_id'         => auth()->id() ?? 1,
                'jenis'               => 'masuk',
                'jumlah_barang_kecil' => $item->stok_saat_ini_kecil,
                'penerima_penyerah'   => 'Stok Awal',
                'keperluan'           => 'Stok Awal Sistem',
                'keterangan'          => 'Stok awal ditambahkan saat registrasi barang.',
                'tgl_transaksi'       => now(),
            ]);
        }

        return $item;
    }

    public function updateItem($id, array $data)
    {
        $item = $this->itemRepository->update($id, $data);
        return $item;
    }

    public function deleteItem($id)
    {
        return $this->itemRepository->delete($id);
    }

    public function getTrashedItems()
    {
        return $this->itemRepository->onlyTrashed();
    }

    public function restoreItem($id)
    {
        $item = $this->itemRepository->restore($id);
        return $item;
    }

    public function forceDeleteItem($id)
    {
        return $this->itemRepository->forceDelete($id);
    }
}

