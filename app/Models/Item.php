<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\Warehouse;

class Item extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'kategori_id',
        'satuan_kecil_id',
        'harga_satuan_kecil',
        'sumber_anggaran_id',
        'gudang_id',
        'stok_minimal',
        'stok_saat_ini_kecil',
        'deskripsi',
        'foto',
        'qr_code',
        'tgl_kadaluarsa',
    ];

    protected $casts = [
        'tgl_kadaluarsa' => 'date',
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

    public function gudangPenyimpanan()
    {
        return $this->belongsTo(Warehouse::class, 'gudang_id');
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
     * Gudang-gudang tempat barang ini pernah disimpan (via transaksi).
     */
    public function gudang()
    {
        return $this->hasManyThrough(
            Warehouse::class,
            StockTransaction::class,
            'barang_id',   // FK on stock_transactions
            'id',          // FK on warehouses
            'id',          // local key on items
            'gudang_id'    // local key on stock_transactions
        );
    }

    /**
     * Stock calculation logic using cached columns
     */
    public function getCurrentStockKecilAttribute()
    {
        return $this->stok_saat_ini_kecil;
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



    public function getQrCodeAttribute()
    {
        return QrCode::format('svg')->size(200)->generate($this->kode_barang);
    }
}

