<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferenceBap extends Model
{
    protected $fillable = ['nomor_ba', 'judul_ba', 'tgl_ba', 'keterangan'];
}
