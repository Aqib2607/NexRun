<?php

namespace App\Http\Controllers\Coupon;

use App\Http\Controllers\ApiController;
use App\Models\Coupon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CouponController extends ApiController
{
    public function validateCoupon(Request $request): JsonResponse
    {
        $request->validate([
            'coupon_code' => 'required|string',
            'subtotal'    => 'required|numeric|min:0',
        ]);

        $coupon = Coupon::byCode($request->coupon_code)->first();

        if (!$coupon) {
            return $this->error('Coupon not found.', 404);
        }

        if (!$coupon->isValid()) {
            return $this->error('Coupon is expired or inactive.', 422);
        }

        if ($coupon->hasBeenUsedByCustomer($request->user()->id)) {
            return $this->error('You have already used this coupon.', 422);
        }

        $discount = $coupon->calculateDiscount($request->subtotal);

        if ($discount <= 0 && $coupon->coupon_type !== 'free_shipping') {
            return $this->error("Minimum purchase of ৳{$coupon->minimum_purchase} required.", 422);
        }

        return $this->success([
            'coupon_code' => $coupon->coupon_code,
            'type'        => $coupon->coupon_type,
            'value'       => (float)$coupon->value,
            'discount'    => $discount,
            'message'     => $coupon->coupon_type === 'free_shipping'
                ? 'Free shipping applied!'
                : "৳{$discount} discount applied.",
        ]);
    }
}
