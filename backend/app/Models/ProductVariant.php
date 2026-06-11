<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 'size_id', 'color_id', 'variant_sku',
        'barcode', 'price_adjustment', 'status',
    ];

    protected function casts(): array
    {
        return ['price_adjustment' => 'decimal:2'];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function size()
    {
        return $this->belongsTo(Size::class);
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    public function inventory()
    {
        return $this->hasMany(Inventory::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getFinalPriceAttribute(): float
    {
        $base = $this->product->current_price;
        return $base + $this->price_adjustment;
    }

    public function getTotalStockAttribute(): int
    {
        return $this->inventory->sum('quantity_available');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInStock($query)
    {
        return $query->whereHas('inventory', fn($q) => $q->where('quantity_available', '>', 0));
    }
}
