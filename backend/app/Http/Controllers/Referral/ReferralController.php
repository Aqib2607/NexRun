<?php

namespace App\Http\Controllers\Referral;

use App\Http\Controllers\ApiController;
use App\Models\CustomerProfile;
use App\Models\Referral;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReferralController extends ApiController
{
    public function getCode(Request $request): JsonResponse
    {
        $profile = $request->user()->customerProfile;

        return $this->success([
            'referral_code' => $profile?->referral_code,
            'share_link'    => config('nexrun.frontend_url') . '/register?ref=' . ($profile?->referral_code ?? ''),
        ]);
    }

    public function stats(Request $request): JsonResponse
    {
        $referrals = Referral::where('referrer_id', $request->user()->id)
            ->with('referredCustomer:id,first_name,last_name,created_at')
            ->get();

        return $this->success([
            'total_referrals'  => $referrals->count(),
            'earned_points'    => $referrals->where('reward_status', 'earned')->sum('reward_points'),
            'pending_points'   => $referrals->where('reward_status', 'pending')->sum('reward_points'),
            'referrals'        => $referrals->map(fn($r) => [
                'customer_name' => $r->referredCustomer?->full_name,
                'reward_points' => $r->reward_points,
                'status'        => $r->reward_status,
                'date'          => $r->created_at?->toIso8601String(),
            ]),
        ]);
    }
}
