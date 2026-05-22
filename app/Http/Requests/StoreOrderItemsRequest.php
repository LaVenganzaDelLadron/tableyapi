<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class StoreOrderItemsRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'order_id' => ['required', 'integer', 'exists:orders,id'],
            'product_id' => ['nullable', 'integer', 'exists:products,id'],
            'product_name' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:1'],
            'price' => ['required', 'numeric', 'decimal:0,2', 'min:0'],
            'price_type' => ['sometimes', Rule::in(['retail', 'wholesale'])],
            'sub_total' => ['required', 'numeric', 'decimal:0,2', 'min:0'],
        ];
    }
}
