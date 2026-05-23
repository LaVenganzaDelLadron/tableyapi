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
        $cacaoCosts = fake()->randomFloat(2, 3000, 15000);
        $employeeCosts = fake()->randomFloat(2, 2000, 10000);
        $operationalExpenses = fake()->randomFloat(2, 1000, 8000);
        $expenses = round($cacaoCosts + $employeeCosts + $operationalExpenses, 2);
        $netProfit = round($revenue - $expenses, 2);

        return [
            'report_type' => 'monthly',
            'period_start' => now()->startOfMonth()->toDateString(),
            'period_end' => now()->endOfMonth()->toDateString(),
            'starting_capital' => $capital,
            'sales_revenue' => $revenue,
            'cacao_costs' => $cacaoCosts,
            'employee_costs' => $employeeCosts,
            'operational_expenses' => $operationalExpenses,
            'total_expenses' => $expenses,
            'net_profit' => $netProfit,
            'remaining_capital' => round($capital + $netProfit, 2),
        ];
    }
}
