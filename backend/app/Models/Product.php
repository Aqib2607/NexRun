<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\Cacheable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes, Auditable, Cacheable;

    protected string $auditModule = 'Products';

    protected $fillable = [
        'sku', 'category_id', 'brand_id', 'product_name', 'slug',
        'short_description', 'description', 'base_price', 'sale_price',
        'status', 'featured', 'gender',
    ];

    protected function casts(): array
    {
        return [
            'base_price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'featured'   => 'boolean',
        ];
    }

    // ── Relationships ──────────────────────────────────────

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('image_order');
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function activeVariants()
    {
        return $this->variants()->where('status', 'active');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews()
    {
        return $this->reviews()->where('status', 'approved');
    }

    public function analytics()
    {
        return $this->hasOne(ProductAnalytics::class);
    }

    public function wishlistItems()
    {
        return $this->hasMany(WishlistItem::class);
    }

    // ── Accessors ──────────────────────────────────────────

    public function getCurrentPriceAttribute(): float
    {
        return $this->sale_price ?? $this->base_price;
    }

    public function getDiscountPercentageAttribute(): ?float
    {
        if ($this->sale_price && $this->base_price > 0) {
            return round((($this->base_price - $this->sale_price) / $this->base_price) * 100, 1);
        }
        return null;
    }

    public function getAverageRatingAttribute(): float
    {
        return $this->approvedReviews()->avg('rating') ?? 0;
    }

    // ── Scopes ─────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    public function scopeByCategory($query, int $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeByBrand($query, int $brandId)
    {
        return $query->where('brand_id', $brandId);
    }

    public function scopeByGender($query, string $gender)
    {
        return $query->where('gender', $gender);
    }

    public function scopeInPriceRange($query, float $min, float $max)
    {
        return $query->whereBetween('base_price', [$min, $max]);
    }

    public function scopeOnSale($query)
    {
        return $query->whereNotNull('sale_price')
                     ->whereColumn('sale_price', '<', 'base_price');
    }

    public function scopeSearch($query, string $term)
    {
        if (app()->environment('testing')) {
            return $query->where(function ($q) use ($term) {
                $q->where('product_name', 'like', "%{$term}%")
                  ->orWhere('short_description', 'like', "%{$term}%")
                  ->orWhere('description', 'like', "%{$term}%");
            });
        }

        return $query->whereRaw(
            "MATCH(product_name, short_description, description) AGAINST(? IN BOOLEAN MODE)",
            [$term . '*']
        );
    }

    public function scopeInStock($query)
    {
        return $query->whereHas('variants.inventory', function ($q) {
            $q->where('quantity_available', '>', 0);
        });
    }
}
