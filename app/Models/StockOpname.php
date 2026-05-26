<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockOpname extends Model
{
    protected $fillable = [
        'barang_id',
        'gudang_id',
        'pengguna_id',
        'stok_sistem',
        'stok_fisik',
        'selisih',
        'keterangan'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


    public function item()
    {
        return $this->belongsTo(Item::class, 'barang_id')->withTrashed();
    }

    public function barang()
    {
        return $this->item();
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'gudang_id');
    }

    public function gudang()
    {
        return $this->warehouse();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'pengguna_id');
    }

    public function pengguna()
    {
        return $this->user();
    }
}
