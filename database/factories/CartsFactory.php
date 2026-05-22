<?php

namespace Database\Factories;

use App\Models\Carts;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Carts> */
class CartsFactory extends Factory
{
    protected $model = Carts::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'status' => fake()->randomElement(['active', 'checked_out']),
        ];
    }
}
