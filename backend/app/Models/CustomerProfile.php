<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'gender', 'birth_date', 'loyalty_points',
        'loyalty_tier', 'total_spent', 'referral_code', 'referred_by',
    ];

    protected function casts(): array
    {
        return [
            'birth_date'     => 'date',
            'total_spent'    => 'decimal:2',
            'loyalty_points' => 'integer',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    public function scopeByTier($query, string $tier)
    {
        return $query->where('loyalty_tier', $tier);
    }
}
