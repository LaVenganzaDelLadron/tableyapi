<?php

namespace Database\Factories;

use App\Models\EmployeeAttendances;
use App\Models\Employees;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<EmployeeAttendances> */
class EmployeeAttendancesFactory extends Factory
{
    protected $model = EmployeeAttendances::class;

    public function definition(): array
    {
        $days = fake()->randomElement([0.5, 1]);
        $rate = 500;

        return [
            'employee_id' => Employees::factory(),
            'work_date' => now()->subDays(fake()->numberBetween(1, 20))->toDateString(),
            'hours_worked' => $days * 8,
            'days_worked' => $days,
            'salary_total' => round($days * $rate, 2),
        ];
    }
}
