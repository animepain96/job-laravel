<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Job extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];
    protected $casts = [
        'paid' => 'boolean',
        'price' => 'integer',
        'price_yen' => 'integer',
        'start_date' => 'date:Y-m-d',
        'pay_date' => 'date:Y-m-d',
        'deadline' => 'date:Y-m-d',
        'finish_date' => 'date:Y-m-d',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id')
            ->withTrashed();
    }

    public function method()
    {
        return $this->belongsTo(Method::class, 'method_id', 'id')
            ->withTrashed();
    }

    public function type()
    {
        return $this->belongsTo(Type::class, 'type_id', 'id')
            ->withTrashed();
    }
}
