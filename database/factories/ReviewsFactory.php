<?php

namespace Database\Factories;

use App\Models\Orders;
use App\Models\Products;
use App\Models\Reviews;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Reviews> */
class ReviewsFactory extends Factory
{
    protected $model = Reviews::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'product_id' => Products::factory(),
            'order_id' => Orders::factory(),
            'rating' => fake()->numberBetween(4, 5),
            'comment' => fake()->randomElement([
                'Sarap ng tableya, bagay kaayo pang sikwate.',
                'Mabango at pure ang lasa ng cacao.',
                'Sulit for family breakfast and merienda.',
            ]),
        ];
    }
}
