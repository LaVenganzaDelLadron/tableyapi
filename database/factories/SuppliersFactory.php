<?php

namespace Database\Factories;

use App\Models\Suppliers;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Suppliers> */
class SuppliersFactory extends Factory
{
    protected $model = Suppliers::class;

    public function definition(): array
    {
        return [
            'email' => fake()->unique()->safeEmail(),
            'name' => fake()->randomElement(['Dela Cruz Cacao Farm', 'Mendoza Family Cacao', 'Davao Cacao Growers']),
            'phone' => '09'.fake()->numerify('#########'),
            'address' => 'Barangay '.fake()->randomElement(['Talandang', 'Bincungan', 'New Corella']).', Davao Region',
        ];
    }
}
