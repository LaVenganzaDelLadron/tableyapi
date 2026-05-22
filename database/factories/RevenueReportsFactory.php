<?php

namespace Database\Factories;

use App\Models\RevenueReports;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<RevenueReports> */
class RevenueReportsFactory extends Factory
{
    protected $model = RevenueReports::class;

    public function definition(): array
    {
        $gross = fake()->randomFloat(2, 20000, 80000);
        $expenses = fake()->randomFloat(2, 5000, 25000);

        return [
            'report_type' => 'monthly',
            'period_start' => now()->startOfMonth()->toDateString(),
            'period_end' => now()->endOfMonth()->toDateString(),
            'gross_revenue' => $gross,
            'total_expenses' => $expenses,
            'net_income' => round($gross - $expenses, 2),
        ];
    }
}
