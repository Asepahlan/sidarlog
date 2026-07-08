<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use SoftDeletes;

    protected $fillable = ['nama_satuan', 'simbol'];

    public function itemsKecil()
    {
        return $this->hasMany(Item::class, 'satuan_kecil_id');
    }
}

