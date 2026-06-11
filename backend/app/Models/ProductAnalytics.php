<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAnalytics extends Model
{
    protected $primaryKey = 'product_id';
    public $incrementing = false;

    protected $fillable = [
        'product_id', 'views', 'purchases', 'revenue_generated',
        'average_rating', 'review_count', 'conversion_rate',
    ];

    protected function casts(): array
    {
        return [
            'revenue_generated' => 'decimal:2',
            'average_rating'    => 'decimal:2',
            'conversion_rate'   => 'decimal:2',
        ];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
