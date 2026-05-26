<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransaction extends Model
{
    protected $fillable = [
        'no_referensi',
        'barang_id',
        'gudang_id',
        'pengguna_id',
        'pihak_kesatu_id',
        'pihak_kedua_id',
        'jenis',
        'jumlah_barang_kecil',
        'jumlah_barang_besar',
        'penerima_penyerah',
        'keperluan',
        'keterangan',
        'tgl_transaksi'
    ];

    protected $casts = [
        'tgl_transaksi' => 'datetime',
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

    public function firstParty()
    {
        return $this->belongsTo(FirstParty::class, 'pihak_kesatu_id');
    }

    public function pihakKesatu()
    {
        return $this->firstParty();
    }

    public function secondParty()
    {
        return $this->belongsTo(SecondParty::class, 'pihak_kedua_id');
    }

    public function pihakKedua()
    {
        return $this->secondParty();
    }
}
