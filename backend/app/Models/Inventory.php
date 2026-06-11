<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $table = 'inventory';

    protected $fillable = [
        'warehouse_id', 'product_variant_id', 'quantity_available',
        'quantity_reserved', 'reorder_level',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function transactions()
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    public function getEffectiveQuantityAttribute(): int
    {
        return $this->quantity_available - $this->quantity_reserved;
    }

    public function isLowStock(): bool
    {
        return $this->quantity_available <= $this->reorder_level;
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('quantity_available', '<=', 'reorder_level');
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('quantity_available', 0);
    }
}
