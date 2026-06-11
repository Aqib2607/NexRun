<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->route('address')->customer_id === $this->user()->id;
    }

    public function rules(): array
    {
        return [
            'address_type'   => 'sometimes|in:shipping,billing',
            'recipient_name' => 'sometimes|string|max:200',
            'phone'          => 'sometimes|string|max:20',
            'district'       => 'sometimes|string|max:100',
            'city'           => 'sometimes|string|max:100',
            'postal_code'    => 'sometimes|string|max:10',
            'address_line_1' => 'sometimes|string|max:500',
            'address_line_2' => 'nullable|string|max:500',
        ];
    }
}
