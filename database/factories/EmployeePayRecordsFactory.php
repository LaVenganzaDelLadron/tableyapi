<?php

namespace Database\Factories;

use App\Models\EmployeePayRecords;
use App\Models\Employees;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<EmployeePayRecords> */
class EmployeePayRecordsFactory extends Factory
{
    protected $model = EmployeePayRecords::class;

    public function definition(): array
    {
        $quantity = fake()->randomFloat(2, 1, 20);
        $rate = fake()->randomElement([5, 100, 500]);

        return [
            'employee_id' => Employees::factory(),
            'employee_attendance_id' => null,
            'cacao_batch_id' => null,
            'production_batch_id' => null,
            'pay_type' => fake()->randomElement(['daily', 'roasting_per_sack', 'commission_per_pack']),
            'pay_date' => now()->subDays(fake()->numberBetween(1, 20))->toDateString(),
            'quantity' => $quantity,
            'rate' => $rate,
            'total_amount' => round($quantity * $rate, 2),
            'notes' => 'Demo payroll record.',
        ];
    }
}
