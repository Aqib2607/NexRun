<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name'    => ['required', 'string', 'max:100'],
            'last_name'     => ['required', 'string', 'max:100'],
            'email'         => ['required', 'email:rfc', 'unique:users,email', 'max:255'],
            'phone'         => ['nullable', 'string', 'max:20', 'unique:users,phone'],
            'password'      => [
                'required', 'string', 'min:8', 'confirmed',
                'regex:/[A-Z]/',      // at least 1 uppercase
                'regex:/[0-9]/',      // at least 1 number
                'regex:/[@$!%*?&#]/', // at least 1 special char
            ],
            'referral_code' => ['nullable', 'string', 'max:20'],
        ];
    }

    public function messages(): array
    {
        return [
            'password.regex' => 'Password must contain at least 1 uppercase letter, 1 number, and 1 special character.',
        ];
    }
}
