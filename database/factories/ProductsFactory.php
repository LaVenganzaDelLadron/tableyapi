<?php

namespace Database\Factories;

use App\Models\Categories;
use App\Models\Products;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Products> */
class ProductsFactory extends Factory
{
    protected $model = Products::class;

    public function definition(): array
    {
        $price = fake()->randomFloat(2, 95, 320);
        $stock = fake()->numberBetween(25, 250);

        return [
            'category_id' => Categories::factory(),
            'name' => fake()->randomElement(['Pure Tableya Pack', '10-Piece Tableya Pack', 'Premium Unsweetened Tableya', 'Tableya Gift Pack']),
            'description' => 'Locally made tableya using roasted cacao beans from Davao Region.',
            'price' => $price,
            'wholesale_price' => round($price * 0.82, 2),
            'minimum_wholesale_quantity' => fake()->numberBetween(12, 30),
            'stock' => $stock,
            'image' => 'products/tableya-'.fake()->numberBetween(1, 5).'.jpg',
            'is_available' => true,
        ];
    }
}
