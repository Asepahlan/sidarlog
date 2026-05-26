<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Jabatan extends Model
{
    use SoftDeletes;

    protected $fillable = ['nama_jabatan'];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
