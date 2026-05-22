<?php

namespace Database\Factories;

use App\Models\Employees;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Employees> */
class EmployeesFactory extends Factory
{
    protected $model = Employees::class;

    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Alma Bautista', 'Rico Pangan', 'Nora Villanueva', 'Benjie Ramos']),
            'position' => fake()->randomElement(['Roasting Worker', 'Packaging Worker', 'Sales Staff']),
            'payment_type' => fake()->randomElement(['daily', 'roasting_per_sack', 'commission_per_pack']),
            'rate' => fake()->randomElement([2.50, 5.00, 100.00, 500.00]),
            'phone' => '09'.fake()->numerify('#########'),
            'address' => 'Barangay '.fake()->randomElement(['Apokon', 'Mankilam', 'Magugpo']).', Tagum City',
            'is_active' => true,
        ];
    }
}
