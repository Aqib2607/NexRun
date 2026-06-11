<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory, Auditable;

    protected string $auditModule = 'Orders';

    protected $fillable = [
        'order_number', 'customer_id', 'shipping_address_id', 'billing_address_id',
        'subtotal', 'discount_amount', 'shipping_amount', 'tax_amount', 'total_amount',
        'payment_status', 'order_status', 'coupon_id', 'notes', 'placed_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal'        => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'shipping_amount' => 'decimal:2',
            'tax_amount'      => 'decimal:2',
            'total_amount'    => 'decimal:2',
            'placed_at'       => 'datetime',
        ];
    }

    // ── Relationships ──────────────────────────────────────

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function shippingAddress()
    {
        return $this->belongsTo(CustomerAddress::class, 'shipping_address_id');
    }

    public function billingAddress()
    {
        return $this->belongsTo(CustomerAddress::class, 'billing_address_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function latestPayment()
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    public function statusHistory()
    {
        return $this->hasMany(OrderStatusHistory::class)->orderByDesc('changed_at');
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // ── Helpers ────────────────────────────────────────────

    public function canTransitionTo(string $newStatus): bool
    {
        $transitions = config('nexrun.order_transitions');
        $allowed = $transitions[$this->order_status] ?? [];
        return in_array($newStatus, $allowed);
    }

    public static function generateOrderNumber(): string
    {
        $prefix = 'NR';
        $date = now()->format('ymd');
        $random = strtoupper(substr(uniqid(), -5));
        return "{$prefix}-{$date}-{$random}";
    }

    // ── Scopes ─────────────────────────────────────────────

    public function scopeByStatus($query, string $status)
    {
        return $query->where('order_status', $status);
    }

    public function scopeForCustomer($query, int $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'completed');
    }

    public function scopeRecent($query)
    {
        return $query->orderByDesc('placed_at');
    }

    public function scopePlacedBetween($query, $from, $to)
    {
        return $query->whereBetween('placed_at', [$from, $to]);
    }
}
