<?php

namespace App\Http\Controllers\Loyalty;

use App\Http\Controllers\ApiController;
use App\Models\LoyaltyTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LoyaltyController extends ApiController
{
    public function balance(Request $request): JsonResponse
    {
        $profile = $request->user()->customerProfile;

        $tiers = config('nexrun.loyalty.tiers');
        $currentTierConfig = $tiers[$profile?->loyalty_tier ?? 'bronze'];

        return $this->success([
            'points'       => $profile?->loyalty_points ?? 0,
            'tier'         => $profile?->loyalty_tier ?? 'bronze',
            'tier_discount' => $currentTierConfig['discount_pct'] ?? 0,
            'total_spent'  => (float) ($profile?->total_spent ?? 0),
            'redemption_rate' => config('nexrun.loyalty.redemption_rate', 100),
        ]);
    }

    public function transactions(Request $request): JsonResponse
    {
        $transactions = LoyaltyTransaction::where('customer_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->paginate($request->get('per_page', 20));

        $data = $transactions->through(fn($t) => [
            'type'          => $t->transaction_type,
            'points'        => $t->points,
            'balance_after' => $t->balance_after,
            'remarks'       => $t->remarks,
            'created_at'    => $t->created_at?->toIso8601String(),
        ]);

        return $this->paginated($data);
    }

    public function redeem(Request $request): JsonResponse
    {
        $request->validate(['points' => 'required|integer|min:100']);

        $profile = $request->user()->customerProfile;
        if (!$profile || $profile->loyalty_points < $request->points) {
            return $this->error('Insufficient loyalty points.', 422);
        }

        $redemptionRate = config('nexrun.loyalty.redemption_rate', 100);
        $discount = $request->points / $redemptionRate;

        return $this->success([
            'points_to_redeem' => $request->points,
            'discount_value'   => $discount,
            'message'          => "Redeem {$request->points} points for ৳{$discount} discount on your next order.",
        ]);
    }
}
