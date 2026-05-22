<?php

namespace Database\Factories;

use App\Models\CacaoPurchases;
use App\Models\Suppliers;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<CacaoPurchases> */
class CacaoPurchasesFactory extends Factory
{
    protected $model = CacaoPurchases::class;

    public function definition(): array
    {
        $kilogram = fake()->randomFloat(2, 12, 80);
        $price = fake()->randomFloat(2, 135, 180);
        $paid = fake()->boolean(80);

        return [
            'supplier_id' => Suppliers::factory(),
            'kilogram' => $kilogram,
            'price_per_kilogram' => $price,
            'total_amount' => round($kilogram * $price, 2),
            'payment_status' => $paid ? 'paid' : 'unpaid',
            'paid_at' => $paid ? now()->subDays(fake()->numberBetween(1, 60)) : null,
            'purchase_date' => now()->subDays(fake()->numberBetween(1, 60))->toDateString(),
            'notes' => 'Fermented cacao beans purchased for tableya production.',
        ];
    }
}
