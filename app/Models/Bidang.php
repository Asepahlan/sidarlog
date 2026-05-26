<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bidang extends Model
{
    use SoftDeletes;

    protected $fillable = ['nama_bidang'];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
