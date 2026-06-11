<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'shipping_address_id' => ['required', 'exists:customer_addresses,id'],
            'billing_address_id'  => ['nullable', 'exists:customer_addresses,id'],
            'payment_method_id'   => ['required', 'exists:payment_methods,id'],
            'coupon_code'         => ['nullable', 'string', 'max:50'],
            'notes'               => ['nullable', 'string', 'max:500'],
            'redeem_points'       => ['nullable', 'integer', 'min:0'],
        ];
    }
}
