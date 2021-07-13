<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;
    protected $table = 'Customer';
    protected $primaryKey = 'ID';
    protected $guarded = [];

    public function jobs()
    {
        return $this->hasMany(Job::class, 'CustomerID', 'ID');
    }
}
