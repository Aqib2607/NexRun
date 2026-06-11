<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiController;
use App\Models\Coupon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminCouponController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = Coupon::query();
        if ($request->filled('status')) $query->where('status', $request->status);

        return $this->paginated($query->orderByDesc('created_at')->paginate($request->get('per_page', 20)));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'coupon_code'        => 'required|string|max:50|unique:coupons',
            'coupon_type'        => 'required|in:percentage,fixed,free_shipping',
            'value'              => 'required|numeric|min:0',
            'minimum_purchase'   => 'sometimes|numeric|min:0',
            'maximum_discount'   => 'nullable|numeric|min:0',
            'start_date'         => 'required|date',
            'end_date'           => 'required|date|after:start_date',
            'usage_limit'        => 'nullable|integer|min:1',
            'usage_per_customer' => 'sometimes|integer|min:1',
            'status'             => 'sometimes|in:active,inactive',
        ]);

        $data['coupon_code'] = strtoupper($data['coupon_code']);
        return $this->created(Coupon::create($data));
    }

    public function show(Coupon $coupon): JsonResponse
    {
        return $this->success($coupon->loadCount('usage'));
    }

    public function update(Request $request, Coupon $coupon): JsonResponse
    {
        $data = $request->validate([
            'coupon_code'        => 'sometimes|string|max:50|unique:coupons,coupon_code,' . $coupon->id,
            'coupon_type'        => 'sometimes|in:percentage,fixed,free_shipping',
            'value'              => 'sometimes|numeric|min:0',
            'minimum_purchase'   => 'sometimes|numeric|min:0',
            'maximum_discount'   => 'nullable|numeric|min:0',
            'start_date'         => 'sometimes|date',
            'end_date'           => 'sometimes|date',
            'usage_limit'        => 'nullable|integer|min:1',
            'usage_per_customer' => 'sometimes|integer|min:1',
            'status'             => 'sometimes|in:active,inactive,expired',
        ]);

        $coupon->update($data);
        return $this->success($coupon->fresh());
    }

    public function destroy(Coupon $coupon): JsonResponse
    {
        $coupon->delete();
        return $this->noContent('Coupon deleted.');
    }
}
