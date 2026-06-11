<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoyaltyTransaction extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'customer_id', 'transaction_type', 'points', 'balance_after',
        'remarks', 'source_type', 'source_id',
    ];

    protected function casts(): array
    {
        return ['created_at' => 'datetime'];
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function source()
    {
        return $this->morphTo();
    }
}
