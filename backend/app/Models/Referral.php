<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    protected $fillable = ['referrer_id', 'referred_customer_id', 'reward_points', 'reward_status'];

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    public function referredCustomer()
    {
        return $this->belongsTo(User::class, 'referred_customer_id');
    }

    public function scopePending($query)
    {
        return $query->where('reward_status', 'pending');
    }

    public function scopeEarned($query)
    {
        return $query->where('reward_status', 'earned');
    }
}
