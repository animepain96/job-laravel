<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Type extends Model
{
    use SoftDeletes;
    protected $table = 'JType';
    protected $primaryKey = 'ID';
    protected $guarded = [];

    public function jobs() {
        return $this->hasMany(Job::class, 'TypeID', 'ID');
    }
}
