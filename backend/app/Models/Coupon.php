<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected string $auditModule = 'Coupons';

    protected $fillable = [
        'coupon_code', 'coupon_type', 'value', 'minimum_purchase',
        'maximum_discount', 'start_date', 'end_date', 'usage_limit',
        'usage_per_customer', 'times_used', 'status',
    ];

    protected function casts(): array
    {
        return [
            'value'            => 'decimal:2',
            'minimum_purchase' => 'decimal:2',
            'maximum_discount' => 'decimal:2',
            'start_date'       => 'date',
            'end_date'         => 'date',
        ];
    }

    public function usage()
    {
        return $this->hasMany(CouponUsage::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function isValid(): bool
    {
        return $this->status === 'active'
            && now()->between($this->start_date, $this->end_date)
            && ($this->usage_limit === null || $this->times_used < $this->usage_limit);
    }

    public function hasBeenUsedByCustomer(int $customerId): bool
    {
        $count = $this->usage()->where('customer_id', $customerId)->count();
        return $count >= $this->usage_per_customer;
    }

    public function calculateDiscount(float $subtotal): float
    {
        if ($subtotal < $this->minimum_purchase) {
            return 0;
        }

        $discount = match ($this->coupon_type) {
            'percentage'    => $subtotal * ($this->value / 100),
            'fixed'         => $this->value,
            'free_shipping' => 0, // handled in shipping calculation
        };

        if ($this->coupon_type === 'percentage' && $this->maximum_discount) {
            $discount = min($discount, $this->maximum_discount);
        }

        return min($discount, $subtotal);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                     ->where('start_date', '<=', now())
                     ->where('end_date', '>=', now());
    }

    public function scopeByCode($query, string $code)
    {
        return $query->where('coupon_code', strtoupper($code));
    }
}
