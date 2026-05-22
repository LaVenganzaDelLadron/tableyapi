<?php

namespace Database\Factories;

use App\Models\CacaoBatches;
use App\Models\CacaoPurchases;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<CacaoBatches> */
class CacaoBatchesFactory extends Factory
{
    protected $model = CacaoBatches::class;

    public function definition(): array
    {
        $raw = fake()->randomFloat(2, 12, 80);
        $sacks = fake()->numberBetween(2, 8);
        $rate = fake()->randomElement([90, 100, 120]);

        return [
            'cacao_purchase_id' => CacaoPurchases::factory(),
            'raw_kilogram' => $raw,
            'roasted_kilogram' => round($raw * 0.84, 2),
            'sack_count' => $sacks,
            'roasting_payment_per_sack' => $rate,
            'total_roasting_payment' => round($sacks * $rate, 2),
            'production_date' => now()->subDays(fake()->numberBetween(1, 45))->toDateString(),
            'notes' => 'Roasted cacao batch for tableya processing.',
        ];
    }
}
