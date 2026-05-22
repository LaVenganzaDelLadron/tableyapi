<?php

namespace Database\Factories;

use App\Models\InventoryLogs;
use App\Models\Products;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<InventoryLogs> */
class InventoryLogsFactory extends Factory
{
    protected $model = InventoryLogs::class;

    public function definition(): array
    {
        return [
            'product_id' => Products::factory(),
            'order_id' => null,
            'production_batch_id' => null,
            'type' => fake()->randomElement(['production_added', 'manual_adjustment']),
            'quantity_change' => fake()->numberBetween(10, 120),
            'remaining_stock' => fake()->numberBetween(50, 300),
            'notes' => 'Demo inventory movement.',
        ];
    }
}
