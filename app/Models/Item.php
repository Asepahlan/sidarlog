<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

class Item extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'kategori_id',
        'satuan_kecil_id',
        'satuan_besar_id',
        'harga_satuan_kecil',
        'harga_satuan_besar',
        'sumber_anggaran_id',
        'lokasi_barang_id',
        'stok_minimal',
        'stok_saat_ini_kecil',
        'stok_saat_ini_besar',
        'deskripsi',
        'foto',
        'qr_code',
        'tgl_kadaluarsa',
        'tgl_diterima'
    ];

    protected $casts = [
        'tgl_kadaluarsa' => 'date',
        'tgl_diterima' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'kategori_id');
    }

    public function kategori()
    {
        return $this->category();
    }

    public function unitKecil()
    {
        return $this->belongsTo(Unit::class, 'satuan_kecil_id');
    }

    public function satuanKecil()
    {
        return $this->unitKecil();
    }

    public function unitBesar()
    {
        return $this->belongsTo(Unit::class, 'satuan_besar_id');
    }

    public function satuanBesar()
    {
        return $this->unitBesar();
    }

    public function budgetSource()
    {
        return $this->belongsTo(BudgetSource::class, 'sumber_anggaran_id');
    }

    public function sumberAnggaran()
    {
        return $this->budgetSource();
    }

    public function itemLocation()
    {
        return $this->belongsTo(ItemLocation::class, 'lokasi_barang_id');
    }

    public function lokasiBarang()
    {
        return $this->itemLocation();
    }

    public function transactions()
    {
        return $this->hasMany(StockTransaction::class, 'barang_id');
    }

    public function transaksi()
    {
        return $this->transactions();
    }

    /**
     * Stock calculation logic using cached columns
     */
    public function getCurrentStockKecilAttribute()
    {
        return $this->stok_saat_ini_kecil;
    }

    public function getCurrentStockBesarAttribute()
    {
        return $this->stok_saat_ini_besar;
    }

    /**
     * Compatibility Accessor for legacy code and Stock Opname
     */
    public function getCurrentStockAttribute()
    {
        return $this->current_stock_kecil;
    }

    public function getStockKecilInWarehouse($warehouseId)
    {
        return $this->transactions()
             ->where('gudang_id', $warehouseId)
             ->where('jenis', 'masuk')->sum('jumlah_barang_kecil') 
             - $this->transactions()
             ->where('gudang_id', $warehouseId)
             ->where('jenis', 'keluar')->sum('jumlah_barang_kecil')
             + $this->transactions()
             ->where('gudang_id', $warehouseId)
             ->where('jenis', 'penyesuaian')->sum('jumlah_barang_kecil');
    }

    public function getStockBesarInWarehouse($warehouseId)
    {
        return $this->transactions()
             ->where('gudang_id', $warehouseId)
             ->where('jenis', 'masuk')->sum('jumlah_barang_besar') 
             - $this->transactions()
             ->where('gudang_id', $warehouseId)
             ->where('jenis', 'keluar')->sum('jumlah_barang_besar')
             + $this->transactions()
             ->where('gudang_id', $warehouseId)
             ->where('jenis', 'penyesuaian')->sum('jumlah_barang_besar');
    }

    public function getQrCodeAttribute()
    {
        return QrCode::format('svg')->size(200)->generate($this->kode_barang);
    }
}

