<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemType extends Model
{
    protected $fillable = ['kategori_id', 'nama_jenis', 'deskripsi'];

    public function category()
    {
        return $this->belongsTo(Category::class, 'kategori_id');
    }

    public function kategori()
    {
        return $this->category();
    }

    public function classifications()
    {
        return $this->hasMany(ItemClassification::class);
    }
}
