<?php

namespace App\Http\Requests;

use App\Models\Products;
use Illuminate\Validation\Rule;

class CheckoutRequest extends StoreOrdersRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'items' => ['sometimes', 'array'],
            'items.*.product_id' => ['required_with:items', 'integer', 'exists:products,id'],
            'items.*.product_name' => ['sometimes', 'string', 'max:255'],
            'items.*.quantity' => ['required_with:items', 'integer', 'min:1'],
            'items.*.price' => ['sometimes', 'numeric', 'decimal:0,2', 'min:0'],
            'items.*.price_type' => ['sometimes', Rule::in(['retail', 'wholesale'])],
            'items.*.sub_total' => ['sometimes', 'numeric', 'decimal:0,2', 'min:0'],
            'cart_id' => ['sometimes', 'integer', 'exists:carts,id'],
        ]);
    }

    public function after(): array
    {
        return [
            function ($validator): void {
                foreach ($this->input('items', []) as $index => $item) {
                    $productId = $item['product_id'] ?? null;
                    $product = $productId ? Products::find($productId) : null;

                    if (! $product) {
                        continue;
                    }

                    $quantity = (int) ($item['quantity'] ?? 0);

                    if ($quantity > (int) $product->stock) {
                        $validator->errors()->add("items.$index.quantity", "The selected product {$product->name} does not have enough stock.");
                    }

                    if (($item['price_type'] ?? 'retail') === 'wholesale') {
                        $minimum = (int) ($product->minimum_wholesale_quantity ?? 0);

                        if ($minimum > 0 && $quantity < $minimum) {
                            $validator->errors()->add("items.$index.quantity", 'Wholesale orders must meet the minimum wholesale quantity.');
                        }

                        if ($product->wholesale_price === null && ! array_key_exists('price', $item)) {
                            $validator->errors()->add("items.$index.price_type", 'Wholesale pricing is not available for this product.');
                        }
                    }
                }
            },
        ];
    }
}
