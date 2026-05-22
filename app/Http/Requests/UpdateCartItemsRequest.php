<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ResolvesRouteIds;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCartItemsRequest extends FormRequest
{
    use ResolvesRouteIds;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cart_id' => ['sometimes', 'integer', 'exists:carts,id'],
            'product_id' => [
                'sometimes',
                'integer',
                'exists:products,id',
                Rule::unique('cart_items', 'product_id')
                    ->where(fn ($query) => $query->where('cart_id', $this->input('cart_id')))
                    ->ignore($this->routeId('cart_item')),
            ],
            'quantity' => ['sometimes', 'integer', 'min:0'],
            'price' => ['sometimes', 'numeric', 'decimal:0,2', 'min:0'],
            'price_type' => ['sometimes', 'string', 'max:255'],
            'sub_total' => ['sometimes', 'numeric', 'decimal:0,2', 'min:0'],
        ];
    }
}
