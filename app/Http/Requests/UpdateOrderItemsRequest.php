<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderItemsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'order_id' => ['sometimes', 'integer', 'exists:orders,id'],
            'product_id' => ['sometimes', 'nullable', 'integer', 'exists:products,id'],
            'product_name' => ['sometimes', 'string', 'max:255'],
            'quantity' => ['sometimes', 'integer', 'min:0'],
            'price' => ['sometimes', 'numeric', 'decimal:0,2', 'min:0'],
            'price_type' => ['sometimes', 'string', 'max:255'],
            'sub_total' => ['sometimes', 'numeric', 'decimal:0,2', 'min:0'],
        ];
    }
}
