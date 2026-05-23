<?php

namespace Database\Factories;

use App\Models\CacaoBatches;
use App\Models\ProductionBatches;
use App\Models\Products;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ProductionBatches> */
class ProductionBatchesFactory extends Factory
{
    protected $model = ProductionBatches::class;

    public function definition(): array
    {
        $packs = fake()->numberBetween(80, 400);
        $price = fake()->randomFloat(2, 95, 180);
        $costPerPack = fake()->randomFloat(2, 35, 95);

        return [
            'cacao_batch_id' => CacaoBatches::factory(),
            'product_id' => Products::factory(),
            'packs_produced' => $packs,
            'price_per_pack' => $price,
            'total_production_value' => round($packs * $price, 2),
            'total_production_cost' => round($packs * $costPerPack, 2),
            'cost_per_pack' => $costPerPack,
            'production_date' => now()->subDays(fake()->numberBetween(1, 30))->toDateString(),
        ];
    }
}
