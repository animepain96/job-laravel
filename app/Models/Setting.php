<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function scopeGetValue($query, $key)
    {
        return $query->where('key', $key)
            ->select('value')
            ->first();
    }
}
