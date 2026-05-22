<?php

namespace Database\Factories;

use App\Models\CartItems;
use App\Models\Carts;
use App\Models\Products;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<CartItems> */
class CartItemsFactory extends Factory
{
    protected $model = CartItems::class;

    public function definition(): array
    {
        $quantity = fake()->numberBetween(1, 12);
        $price = fake()->randomFloat(2, 95, 320);

        return [
            'cart_id' => Carts::factory(),
            'product_id' => Products::factory(),
            'quantity' => $quantity,
            'price' => $price,
            'price_type' => 'retail',
            'sub_total' => round($quantity * $price, 2),
        ];
    }
}
