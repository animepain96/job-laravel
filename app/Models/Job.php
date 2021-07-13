<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Job extends Model
{
    use SoftDeletes;
    protected $table = 'Job';
    protected $primaryKey = 'ID';
    protected $guarded = [];
    protected $casts = [
        'StartDate' => 'date:Y-m-d',
        'Deadline' => 'date:Y-m-d',
        'Price' => 'integer',
        'PriceYen' => 'integer',
        'Paydate' => 'date:Y-m-d',
        'FinishDate' => 'date:Y-m-d',
        'Paid' => 'boolean',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'CustomerID', 'ID')
            ->withTrashed();
    }

    public function type()
    {
        return $this->belongsTo(Type::class, 'TypeID', 'ID')
            ->withTrashed();
    }

    public function method()
    {
        return $this->belongsTo(Method::class, 'MethodID', 'ID')
            ->withTrashed();
    }
}
