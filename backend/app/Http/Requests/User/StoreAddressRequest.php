<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class StoreAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'address_type'   => 'required|in:shipping,billing',
            'recipient_name' => 'required|string|max:200',
            'phone'          => 'required|string|max:20',
            'district'       => 'required|string|max:100',
            'city'           => 'required|string|max:100',
            'postal_code'    => 'required|string|max:10',
            'address_line_1' => 'required|string|max:500',
            'address_line_2' => 'nullable|string|max:500',
            'is_default'     => 'boolean',
        ];
    }
}
