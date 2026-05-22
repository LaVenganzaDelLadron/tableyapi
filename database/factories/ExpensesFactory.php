<?php

namespace Database\Factories;

use App\Models\Expenses;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Expenses> */
class ExpensesFactory extends Factory
{
    protected $model = Expenses::class;

    public function definition(): array
    {
        return [
            'title' => fake()->randomElement(['Roasting Gas Refill', 'Packaging Labels', 'Grinding Fee', 'Delivery Fare']),
            'category' => fake()->randomElement(['production', 'packaging', 'transportation', 'utilities']),
            'amount' => fake()->randomFloat(2, 150, 3500),
            'payment_method' => fake()->randomElement(['cash', 'gcash', 'bank_transfer']),
            'payee' => fake()->company(),
            'expense_date' => now()->subDays(fake()->numberBetween(1, 30))->toDateString(),
            'notes' => 'Demo operating expense.',
        ];
    }
}
