<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCartItemsRequest extends FormRequest
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
            'quantity' => ['required', 'integer', 'min:0'],
            'price' => ['required', 'numeric', 'decimal:0,2', 'min:0'],
            'price_type' => ['sometimes', 'string', 'max:255'],
            'sub_total' => ['required', 'numeric', 'decimal:0,2', 'min:0'],
        ];
    }
}
