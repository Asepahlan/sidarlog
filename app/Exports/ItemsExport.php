<?php

namespace App\Exports;

use App\Models\Item;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ItemsExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Item::with(['kategori', 'satuanKecil', 'satuanBesar'])->get();
    }

    public function headings(): array
    {
        return [
            'Kode Barang',
            'Nama Barang',
            'Kategori',
            'Stok Kecil',
            'Satuan Kecil',
            'Stok Besar',
            'Satuan Besar',
            'Tgl Kadaluarsa',
        ];
    }

    public function map($item): array
    {
        return [
            $item->kode_barang,
            $item->nama_barang,
            $item->kategori->nama_kategori ?? '-',
            $item->stok_saat_ini_kecil,
            $item->satuanKecil->nama_satuan ?? '-',
            $item->stok_saat_ini_besar,
            $item->satuanBesar->nama_satuan ?? '-',
            $item->tgl_kadaluarsa ? $item->tgl_kadaluarsa->format('d/m/Y') : '-',
        ];
    }
}
