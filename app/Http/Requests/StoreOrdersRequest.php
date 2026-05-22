<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrdersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'subtotal' => ['sometimes', 'numeric', 'decimal:0,2', 'min:0'],
            'shipping_fee' => ['sometimes', 'numeric', 'decimal:0,2', 'min:0'],
            'total_price' => ['sometimes', 'numeric', 'decimal:0,2', 'min:0'],
            'payment_method' => ['sometimes', 'string', 'max:255'],
            'payment_status' => ['sometimes', 'string', 'max:255'],
            'payment_reference' => ['nullable', 'string', 'max:255'],
            'paid_at' => ['nullable', 'date'],
            'shipping_address' => ['required', 'string', 'max:255'],
            'status' => ['sometimes', 'string', 'max:255'],
        ];
    }
}
