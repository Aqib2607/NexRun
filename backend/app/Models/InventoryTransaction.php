<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryTransaction extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'inventory_id', 'transaction_type', 'quantity',
        'previous_balance', 'new_balance', 'remarks', 'created_by',
    ];

    protected function casts(): array
    {
        return ['created_at' => 'datetime'];
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
