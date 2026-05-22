<?php

namespace App\Http\Controllers;

use App\Http\Requests\PayrollSummaryRequest;
use App\Http\Requests\StoreEmployeePayRecordsRequest;
use App\Http\Requests\UpdateEmployeePayRecordsRequest;
use App\Models\EmployeePayRecords;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeePayRecordsController extends Controller
{
    private const RELATIONS = ['employee', 'employeeAttendance', 'cacaoBatch', 'productionBatch'];

    public function index(Request $request): JsonResponse
    {
        $records = EmployeePayRecords::with(self::RELATIONS)->paginate($request->integer('per_page', 15));

        return $this->success('Employee pay records retrieved successfully.', $records);
    }

    public function store(StoreEmployeePayRecordsRequest $request): JsonResponse
    {
        $record = DB::transaction(fn () => EmployeePayRecords::create($request->validated())->load(self::RELATIONS));

        return $this->success('Employee pay record created successfully.', $record, 201);
    }

    public function computePayroll(PayrollSummaryRequest $request): JsonResponse
    {
        $data = $request->validated();
        $query = EmployeePayRecords::query()->with('employee');

        if (isset($data['employee_id'])) {
            $query->where('employee_id', $data['employee_id']);
        }

        if (isset($data['date_from'])) {
            $query->whereDate('pay_date', '>=', $request->date('date_from'));
        }

        if (isset($data['date_to'])) {
            $query->whereDate('pay_date', '<=', $request->date('date_to'));
        }

        $records = $query->get();

        return $this->success('Payroll summary computed successfully.', [
            'total_amount' => round((float) $records->sum('total_amount'), 2),
            'records_count' => $records->count(),
            'by_employee' => $records
                ->groupBy('employee_id')
                ->map(fn ($items) => [
                    'employee' => $items->first()->employee,
                    'total_amount' => round((float) $items->sum('total_amount'), 2),
                    'records_count' => $items->count(),
                ])
                ->values(),
        ]);
    }

    public function show(EmployeePayRecords $employeePayRecord): JsonResponse
    {
        return $this->success('Employee pay record retrieved successfully.', $employeePayRecord->load(self::RELATIONS));
    }

    public function update(UpdateEmployeePayRecordsRequest $request, EmployeePayRecords $employeePayRecord): JsonResponse
    {
        $employeePayRecord = DB::transaction(function () use ($request, $employeePayRecord) {
            $employeePayRecord->update($request->validated());

            return $employeePayRecord->load(self::RELATIONS);
        });

        return $this->success('Employee pay record updated successfully.', $employeePayRecord);
    }

    public function destroy(EmployeePayRecords $employeePayRecord): JsonResponse
    {
        $employeePayRecord->delete();

        return $this->success('Employee pay record deleted successfully.');
    }
}
