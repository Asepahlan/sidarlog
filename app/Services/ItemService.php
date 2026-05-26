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

    public function getAllItems($perPage = 10)
    {
        return $this->itemRepository->paginate((int) $perPage);
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
        $data['stok_saat_ini_besar'] = $data['stok_saat_ini_besar'] ?? 0;

        $item = $this->itemRepository->create($data);
        ActivityLog::log("Menambah barang: {$item->nama_barang} (Kode: {$item->kode_barang})", "Master Barang", $data);
        return $item;
    }

    public function updateItem($id, array $data)
    {
        $item = $this->itemRepository->update($id, $data);
        ActivityLog::log("Memperbarui barang: {$item->nama_barang} (Kode: {$item->kode_barang})", "Master Barang", $data);
        return $item;
    }

    public function deleteItem($id)
    {
        $item = $this->itemRepository->find($id);
        ActivityLog::log("Menghapus barang (Soft Delete): {$item->nama_barang} (Kode: {$item->kode_barang})", "Master Barang");
        return $this->itemRepository->delete($id);
    }

    public function getTrashedItems()
    {
        return $this->itemRepository->onlyTrashed();
    }

    public function restoreItem($id)
    {
        $item = $this->itemRepository->restore($id);
        ActivityLog::log("Mengembalikan barang dari Trash: ID #{$id}", "Master Barang");
        return $item;
    }

    public function forceDeleteItem($id)
    {
        ActivityLog::log("Menghapus barang PERMANEN: ID #{$id}", "Master Barang");
        return $this->itemRepository->forceDelete($id);
    }
}

