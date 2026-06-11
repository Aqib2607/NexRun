<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    use HasFactory;

    protected $fillable = ['customer_id'];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function items()
    {
        return $this->hasMany(WishlistItem::class);
    }

    public function products()
    {
        return $this->hasManyThrough(Product::class, WishlistItem::class, 'wishlist_id', 'id', 'id', 'product_id');
    }

    public function getItemCountAttribute(): int
    {
        return $this->items()->count();
    }
}
