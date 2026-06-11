<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerAnalytics extends Model
{
    protected $primaryKey = 'customer_id';
    public $incrementing = false;

    protected $fillable = [
        'customer_id', 'total_orders', 'lifetime_value',
        'average_order_value', 'first_order_date', 'last_order_date',
    ];

    protected function casts(): array
    {
        return [
            'lifetime_value'      => 'decimal:2',
            'average_order_value' => 'decimal:2',
            'first_order_date'    => 'date',
            'last_order_date'     => 'date',
        ];
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }
}
