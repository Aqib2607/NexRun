<?php

namespace App\Http\Requests\Support;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreSupportTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subject'  => 'required|string|max:200',
            'category' => 'required|string|max:50',
            'priority' => 'required|in:low,medium,high,urgent',
            'message'  => 'required|string|max:2000',
        ];
    }
}
