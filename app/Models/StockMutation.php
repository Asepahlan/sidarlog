<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMutation extends Model
{
    protected $fillable = [
        'no_mutasi', 'barang_id', 'gudang_asal_id', 'gudang_tujuan_id',
        'pengguna_id', 'jumlah_barang_kecil', 'jumlah_barang_besar', 'keterangan', 'status', 'tgl_mutasi'
    ];

    protected $casts = ['tgl_mutasi' => 'datetime'];

    public function item()
    {
        return $this->belongsTo(Item::class, 'barang_id')->withTrashed();
    }

    public function barang()
    {
        return $this->item();
    }

    public function fromWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'gudang_asal_id');
    }

    public function gudangAsal()
    {
        return $this->fromWarehouse();
    }

    public function toWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'gudang_tujuan_id');
    }

    public function gudangTujuan()
    {
        return $this->toWarehouse();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'pengguna_id');
    }

    public function pembuat()
    {
        return $this->creator();
    }
}
