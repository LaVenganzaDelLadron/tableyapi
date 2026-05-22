<?php

namespace Database\Factories;

use App\Models\SalesReports;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<SalesReports> */
class SalesReportsFactory extends Factory
{
    protected $model = SalesReports::class;

    public function definition(): array
    {
        $orders = fake()->numberBetween(2, 12);
        $sales = fake()->randomFloat(2, 1000, 15000);

        return [
            'report_type' => 'daily',
            'period_start' => now()->toDateString(),
            'period_end' => now()->toDateString(),
            'total_sales' => $sales,
            'total_orders' => $orders,
            'total_revenue' => $sales,
        ];
    }
}
