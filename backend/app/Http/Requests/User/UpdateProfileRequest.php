<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => 'sometimes|string|max:100',
            'last_name'  => 'sometimes|string|max:100',
            'phone'      => 'sometimes|string|max:20|unique:users,phone,' . $this->user()->id,
            'gender'     => 'sometimes|in:male,female,other',
            'birth_date' => 'sometimes|date|before:today',
        ];
    }
}
