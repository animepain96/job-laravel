<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Method extends Model
{
    use SoftDeletes;
    protected $table = 'JMethod';
    protected $primaryKey = 'ID';
    protected $guarded = [];

    public function jobs() {
        return $this->hasMany(Job::class, 'MethodID', 'ID');
    }
}
