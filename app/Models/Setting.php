<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function scopeGetValue($query, $key)
    {
        return $query->where('key', $key)
            ->select('value')
            ->first();
    }
}
