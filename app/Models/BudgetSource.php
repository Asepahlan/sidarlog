<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BudgetSource extends Model
{
    protected $fillable = ['nama_sumber', 'tahun_anggaran', 'deskripsi'];
}
