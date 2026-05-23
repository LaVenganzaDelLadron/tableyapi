<?php

namespace Database\Seeders;

use App\Models\CacaoBatches;
use App\Models\EmployeeAttendances;
use App\Models\EmployeePayRecords;
use App\Models\Employees;
use App\Models\ProductionBatches;
use App\Services\FinancialService;
use Illuminate\Database\Seeder;

class EmployeePayrollSeeder extends Seeder
{
    public function run(): void
    {
        $financialReportService = app(FinancialService::class);
        $employees = collect([
            ['name' => 'Alma Bautista', 'position' => 'Roasting Worker', 'payment_type' => 'roasting_per_sack', 'rate' => 100.00, 'phone' => '09976660001', 'address' => 'Barangay Apokon, Tagum City'],
            ['name' => 'Rico Pangan', 'position' => 'Packaging Worker', 'payment_type' => 'commission_per_pack', 'rate' => 2.50, 'phone' => '09976660002', 'address' => 'Barangay Mankilam, Tagum City'],
            ['name' => 'Nora Villanueva', 'position' => 'Sales Staff', 'payment_type' => 'daily', 'rate' => 500.00, 'phone' => '09976660003', 'address' => 'Barangay Magugpo, Tagum City'],
        ])->mapWithKeys(fn (array $employee) => [
            $employee['name'] => Employees::updateOrCreate(
                ['name' => $employee['name']],
                $employee + ['is_active' => true]
            ),
        ]);

        $workDate = '2026-05-20';
        foreach ($employees as $employee) {
            $attendance = EmployeeAttendances::updateOrCreate(
                ['employee_id' => $employee->id, 'work_date' => $workDate],
                [
                    'hours_worked' => 8.00,
                    'days_worked' => 1.00,
                    'salary_total' => $employee->payment_type === 'daily' ? (float) $employee->rate : 0.00,
                ]
            );

            if ($employee->payment_type === 'daily') {
                EmployeePayRecords::updateOrCreate(
                    ['employee_id' => $employee->id, 'employee_attendance_id' => $attendance->id, 'pay_type' => 'daily'],
                    [
                        'cacao_batch_id' => null,
                        'production_batch_id' => null,
                        'pay_date' => $workDate,
                        'quantity' => 1.00,
                        'rate' => (float) $employee->rate,
                        'total_amount' => (float) $employee->rate,
                        'notes' => 'Daily wage for sales and store support.',
                    ]
                );
            }
        }

        $cacaoBatch = CacaoBatches::query()->first();
        if ($cacaoBatch) {
            $roaster = $employees['Alma Bautista'];
            EmployeePayRecords::updateOrCreate(
                ['employee_id' => $roaster->id, 'cacao_batch_id' => $cacaoBatch->id, 'pay_type' => 'roasting_per_sack'],
                [
                    'employee_attendance_id' => null,
                    'production_batch_id' => null,
                    'pay_date' => $cacaoBatch->production_date,
                    'quantity' => (float) $cacaoBatch->sack_count,
                    'rate' => (float) $cacaoBatch->roasting_payment_per_sack,
                    'total_amount' => (float) $cacaoBatch->total_roasting_payment,
                    'notes' => 'Roasting payment based on sack count.',
                ]
            );
            ProductionBatches::query()
                ->where('cacao_batch_id', $cacaoBatch->id)
                ->get()
                ->each(fn (ProductionBatches $batch) => $financialReportService->syncProductionCost($batch));
        }

        $productionBatch = ProductionBatches::query()->first();
        if ($productionBatch) {
            $packer = $employees['Rico Pangan'];
            $rate = (float) $packer->rate;
            EmployeePayRecords::updateOrCreate(
                ['employee_id' => $packer->id, 'production_batch_id' => $productionBatch->id, 'pay_type' => 'commission_per_pack'],
                [
                    'employee_attendance_id' => null,
                    'cacao_batch_id' => null,
                    'pay_date' => $productionBatch->production_date,
                    'quantity' => (float) $productionBatch->packs_produced,
                    'rate' => $rate,
                    'total_amount' => round((float) $productionBatch->packs_produced * $rate, 2),
                    'notes' => 'Packaging commission based on packs produced.',
                ]
            );
            $financialReportService->syncProductionCost($productionBatch);
        }
    }
}
