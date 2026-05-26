<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'pengguna_id',
        'activity',
        'module',
        'data',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'data' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'pengguna_id');
    }

    public function pengguna()
    {
        return $this->user();
    }

    public static function log($activity, $module, $data = null)
    {
        return self::create([
            'pengguna_id' => auth()->id(),
            'activity' => $activity,
            'module' => $module,
            'data' => $data,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }
}
