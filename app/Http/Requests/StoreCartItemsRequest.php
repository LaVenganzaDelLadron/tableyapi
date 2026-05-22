<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class StoreCartItemsRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cart_id' => ['required', 'integer', 'exists:carts,id'],
            'product_id' => [
                'required',
                'integer',
                'exists:products,id',
                Rule::unique('cart_items', 'product_id')->where(fn ($query) => $query->where('cart_id', $this->input('cart_id'))),
            ],
            'quantity' => ['required', 'integer', 'min:1'],
            'price' => ['required', 'numeric', 'decimal:0,2', 'min:0'],
            'price_type' => ['sometimes', Rule::in(['retail', 'wholesale'])],
            'sub_total' => ['required', 'numeric', 'decimal:0,2', 'min:0'],
        ];
    }
}
