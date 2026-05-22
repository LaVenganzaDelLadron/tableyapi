<?php

namespace App\Http\Controllers;

use App\Http\Requests\FinancialSummaryRequest;
use App\Http\Requests\StoreCapitalRecordsRequest;
use App\Http\Requests\UpdateCapitalRecordsRequest;
use App\Models\CapitalRecords;
use App\Models\EmployeePayRecords;
use App\Models\Expenses;
use App\Models\Orders;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CapitalRecordsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $records = CapitalRecords::query()->paginate($request->integer('per_page', 15));

        return $this->success('Capital records retrieved successfully.', $records);
    }

    public function store(StoreCapitalRecordsRequest $request): JsonResponse
    {
        $record = DB::transaction(fn () => CapitalRecords::create($request->validated()));

        return $this->success('Capital record created successfully.', $record, 201);
    }

    public function summary(FinancialSummaryRequest $request): JsonResponse
    {
        $data = $request->validated();
        $orders = Orders::query();
        $expenses = Expenses::query();
        $payroll = EmployeePayRecords::query();

        if (isset($data['period_start'])) {
            $orders->whereDate('created_at', '>=', $request->date('period_start'));
            $expenses->whereDate('expense_date', '>=', $request->date('period_start'));
            $payroll->whereDate('pay_date', '>=', $request->date('period_start'));
        }

        if (isset($data['period_end'])) {
            $orders->whereDate('created_at', '<=', $request->date('period_end'));
            $expenses->whereDate('expense_date', '<=', $request->date('period_end'));
            $payroll->whereDate('pay_date', '<=', $request->date('period_end'));
        }

        $totalRevenue = (float) $orders->sum('total_price');
        $totalExpenses = (float) $expenses->sum('amount') + (float) $payroll->sum('total_amount');
        $startingCapital = (float) ($data['starting_capital'] ?? 0);

        return $this->success('Capital summary computed successfully.', [
            'starting_capital' => round($startingCapital, 2),
            'total_revenue' => round($totalRevenue, 2),
            'total_expenses' => round($totalExpenses, 2),
            'final_profit' => round($startingCapital + $totalRevenue - $totalExpenses, 2),
        ]);
    }

    public function show(CapitalRecords $capitalRecord): JsonResponse
    {
        return $this->success('Capital record retrieved successfully.', $capitalRecord);
    }

    public function update(UpdateCapitalRecordsRequest $request, CapitalRecords $capitalRecord): JsonResponse
    {
        $capitalRecord = DB::transaction(function () use ($request, $capitalRecord) {
            $capitalRecord->update($request->validated());

            return $capitalRecord;
        });

        return $this->success('Capital record updated successfully.', $capitalRecord);
    }

    public function destroy(CapitalRecords $capitalRecord): JsonResponse
    {
        $capitalRecord->delete();

        return $this->success('Capital record deleted successfully.');
    }
}
