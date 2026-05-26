<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemClassification extends Model
{
    protected $fillable = ['jenis_barang_id', 'nama_klasifikasi', 'deskripsi'];

    public function itemType()
    {
        return $this->belongsTo(ItemType::class, 'jenis_barang_id');
    }

    public function jenisBarang()
    {
        return $this->itemType();
    }
}
