<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FirstParty extends Model
{
    protected $fillable = ['nama_pihak', 'nip', 'jabatan', 'instansi'];
}
