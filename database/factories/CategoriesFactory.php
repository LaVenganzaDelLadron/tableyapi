<?php

namespace Database\Factories;

use App\Models\Categories;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Categories> */
class CategoriesFactory extends Factory
{
    protected $model = Categories::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement(['Tableya Packs', 'Wholesale Bundles', 'Gift Sets', 'Unsweetened Cacao']),
        ];
    }
}
