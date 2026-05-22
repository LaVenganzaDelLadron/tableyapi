<?php

namespace App\Http\Requests;

use App\Models\CartItems;
use App\Models\Products;
use Illuminate\Validation\Rule;

class AddToCartRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'cart_id' => ['required', 'integer', 'exists:carts,id'],
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'price' => ['nullable', 'numeric', 'decimal:0,2', 'min:0'],
            'price_type' => ['sometimes', Rule::in(['retail', 'wholesale'])],
        ];
    }

    public function after(): array
    {
        return [
            function ($validator): void {
                $product = Products::find($this->input('product_id'));

                if (! $product) {
                    return;
                }

                $quantity = (int) $this->input('quantity', 0);

                $existingQuantity = (int) CartItems::where('cart_id', $this->input('cart_id'))
                    ->where('product_id', $product->id)
                    ->value('quantity');

                if (($existingQuantity + $quantity) > (int) $product->stock) {
                    $validator->errors()->add('quantity', 'The selected product does not have enough stock.');
                }

                if ($this->input('price_type', 'retail') === 'wholesale') {
                    $minimum = (int) ($product->minimum_wholesale_quantity ?? 0);

                    if ($minimum > 0 && $quantity < $minimum) {
                        $validator->errors()->add('quantity', 'Wholesale orders must meet the minimum wholesale quantity.');
                    }

                    if ($product->wholesale_price === null && ! $this->filled('price')) {
                        $validator->errors()->add('price_type', 'Wholesale pricing is not available for this product.');
                    }
                }
            },
        ];
    }
}
