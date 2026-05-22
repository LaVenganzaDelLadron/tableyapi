<?php

namespace Database\Factories;

use App\Models\OrderItems;
use App\Models\Orders;
use App\Models\Products;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<OrderItems> */
class OrderItemsFactory extends Factory
{
    protected $model = OrderItems::class;

    public function definition(): array
    {
        $quantity = fake()->numberBetween(1, 24);
        $price = fake()->randomFloat(2, 95, 320);

        return [
            'order_id' => Orders::factory(),
            'product_id' => Products::factory(),
            'product_name' => 'Pure Tableya Pack',
            'quantity' => $quantity,
            'price' => $price,
            'price_type' => fake()->randomElement(['retail', 'wholesale']),
            'sub_total' => round($quantity * $price, 2),
        ];
    }
}
