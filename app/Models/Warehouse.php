<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model
{
    use SoftDeletes;

    protected $fillable = ['kode_gudang', 'nama_gudang', 'lokasi', 'deskripsi'];

    public function transactions()
    {
        return $this->hasMany(StockTransaction::class, 'gudang_id');
    }

    public function opnames()
    {
        return $this->hasMany(StockOpname::class, 'gudang_id');
    }
}
