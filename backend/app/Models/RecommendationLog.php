<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecommendationLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'customer_id', 'product_id', 'recommendation_type',
        'was_clicked', 'was_purchased',
    ];

    protected function casts(): array
    {
        return [
            'was_clicked'   => 'boolean',
            'was_purchased' => 'boolean',
            'generated_at'  => 'datetime',
        ];
    }

    public function customer() { return $this->belongsTo(User::class, 'customer_id'); }
    public function product()  { return $this->belongsTo(Product::class); }
}
