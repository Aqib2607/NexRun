<?php

namespace App\Services;

use App\Models\CustomerProfile;
use App\Models\LoyaltyTransaction;
use App\Models\Order;
use App\Models\User;

class LoyaltyService
{
    public function awardOrderPoints(Order $order): void
    {
        $profile = CustomerProfile::where('user_id', $order->customer_id)->first();
        if (!$profile) return;

        $pointsPerCurrency = config('nexrun.loyalty.points_per_currency', 1);
        $points = (int) floor($order->total_amount * $pointsPerCurrency);

        if ($points <= 0) return;

        $newBalance = $profile->loyalty_points + $points;

        $profile->update([
            'loyalty_points' => $newBalance,
            'total_spent'    => $profile->total_spent + $order->total_amount,
        ]);

        LoyaltyTransaction::create([
            'customer_id'      => $order->customer_id,
            'transaction_type' => 'earn',
            'points'           => $points,
            'balance_after'    => $newBalance,
            'remarks'          => "Earned from order #{$order->order_number}",
            'source_type'      => Order::class,
            'source_id'        => $order->id,
        ]);

        $this->evaluateTierUpgrade($profile->fresh());
    }

    public function redeemPoints(User $user, int $points, Order $order): float
    {
        $profile = CustomerProfile::where('user_id', $user->id)->firstOrFail();

        if ($profile->loyalty_points < $points) {
            abort(422, 'Insufficient loyalty points.');
        }

        $redemptionRate = config('nexrun.loyalty.redemption_rate', 100);
        $discount = $points / $redemptionRate;

        $newBalance = $profile->loyalty_points - $points;
        $profile->update(['loyalty_points' => $newBalance]);

        LoyaltyTransaction::create([
            'customer_id'      => $user->id,
            'transaction_type' => 'redeem',
            'points'           => -$points,
            'balance_after'    => $newBalance,
            'remarks'          => "Redeemed for order #{$order->order_number}",
            'source_type'      => Order::class,
            'source_id'        => $order->id,
        ]);

        return $discount;
    }

    public function calculatePointsDiscount(User $user, int $points): float
    {
        $profile = CustomerProfile::where('user_id', $user->id)->first();
        if (!$profile || $profile->loyalty_points < $points) {
            return 0;
        }

        $redemptionRate = config('nexrun.loyalty.redemption_rate', 100);
        return $points / $redemptionRate;
    }

    public function awardReferralBonus(int $referrerId, int $referredId): void
    {
        $referrerProfile = CustomerProfile::where('user_id', $referrerId)->first();
        if (!$referrerProfile) return;

        $referrerPoints = config('nexrun.referral.referrer_points', 500);
        $referredPoints = config('nexrun.referral.referred_points', 200);

        // Award referrer
        $newBalance = $referrerProfile->loyalty_points + $referrerPoints;
        $referrerProfile->update(['loyalty_points' => $newBalance]);
        LoyaltyTransaction::create([
            'customer_id'      => $referrerId,
            'transaction_type' => 'referral_bonus',
            'points'           => $referrerPoints,
            'balance_after'    => $newBalance,
            'remarks'          => 'Referral bonus',
            'source_type'      => User::class,
            'source_id'        => $referredId,
        ]);

        // Award referred
        $referredProfile = CustomerProfile::where('user_id', $referredId)->first();
        if ($referredProfile) {
            $referredNewBalance = $referredProfile->loyalty_points + $referredPoints;
            $referredProfile->update(['loyalty_points' => $referredNewBalance]);
            LoyaltyTransaction::create([
                'customer_id'      => $referredId,
                'transaction_type' => 'referral_bonus',
                'points'           => $referredPoints,
                'balance_after'    => $referredNewBalance,
                'remarks'          => 'Welcome referral bonus',
                'source_type'      => User::class,
                'source_id'        => $referrerId,
            ]);
        }
    }

    private function evaluateTierUpgrade(CustomerProfile $profile): void
    {
        $tiers = config('nexrun.loyalty.tiers');
        $currentTier = 'bronze';

        foreach (['platinum', 'gold', 'silver', 'bronze'] as $tier) {
            $config = $tiers[$tier];
            if ($profile->total_spent >= $config['min_spent']) {
                $currentTier = $tier;
                break;
            }
        }

        if ($profile->loyalty_tier !== $currentTier) {
            $profile->update(['loyalty_tier' => $currentTier]);
        }
    }
}
