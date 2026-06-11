<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\ApiController;
use App\Models\PaymentMethod;
use Illuminate\Http\JsonResponse;

class PaymentMethodController extends ApiController
{
    public function index(): JsonResponse
    {
        $methods = PaymentMethod::active()->ordered()->get()
            ->map(fn($m) => [
                'id'           => $m->id,
                'name'         => $m->method_name,
                'slug'         => $m->slug,
                'display_name' => $m->display_name,
                'logo'         => $m->logo,
            ]);

        return $this->success($methods);
    }
}
