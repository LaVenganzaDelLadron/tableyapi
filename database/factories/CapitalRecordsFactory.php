<?php

namespace Database\Factories;

use App\Models\CapitalRecords;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<CapitalRecords> */
class CapitalRecordsFactory extends Factory
{
    protected $model = CapitalRecords::class;

    public function definition(): array
    {
        $capital = fake()->randomFloat(2, 30000, 100000);
        $revenue = fake()->randomFloat(2, 20000, 80000);
        $expenses = fake()->randomFloat(2, 5000, 25000);

        return [
            'report_type' => 'monthly',
            'period_start' => now()->startOfMonth()->toDateString(),
            'period_end' => now()->endOfMonth()->toDateString(),
            'starting_capital' => $capital,
            'total_revenue' => $revenue,
            'total_expenses' => $expenses,
            'final_profit' => round($capital + $revenue - $expenses, 2),
        ];
    }
}
