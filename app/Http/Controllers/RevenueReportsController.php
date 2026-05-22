<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRevenueReportsRequest;
use App\Http\Requests\UpdateRevenueReportsRequest;
use App\Models\EmployeePayRecords;
use App\Models\Expenses;
use App\Models\Orders;
use App\Models\RevenueReports;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RevenueReportsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $reports = RevenueReports::query()->paginate($request->integer('per_page', 15));

        return $this->success('Revenue reports retrieved successfully.', $reports);
    }

    public function store(StoreRevenueReportsRequest $request): JsonResponse
    {
        $report = DB::transaction(fn () => RevenueReports::create($request->validated()));

        return $this->success('Revenue report created successfully.', $report, 201);
    }

    public function summary(Request $request): JsonResponse
    {
        $orders = Orders::query();
        $expenses = Expenses::query();
        $payroll = EmployeePayRecords::query();

        if ($request->filled('period_start')) {
            $orders->whereDate('created_at', '>=', $request->date('period_start'));
            $expenses->whereDate('expense_date', '>=', $request->date('period_start'));
            $payroll->whereDate('pay_date', '>=', $request->date('period_start'));
        }

        if ($request->filled('period_end')) {
            $orders->whereDate('created_at', '<=', $request->date('period_end'));
            $expenses->whereDate('expense_date', '<=', $request->date('period_end'));
            $payroll->whereDate('pay_date', '<=', $request->date('period_end'));
        }

        $grossRevenue = (float) $orders->sum('total_price');
        $totalExpenses = (float) $expenses->sum('amount') + (float) $payroll->sum('total_amount');

        return $this->success('Revenue summary computed successfully.', [
            'gross_revenue' => round($grossRevenue, 2),
            'total_expenses' => round($totalExpenses, 2),
            'net_income' => round($grossRevenue - $totalExpenses, 2),
        ]);
    }

    public function show(RevenueReports $revenueReport): JsonResponse
    {
        return $this->success('Revenue report retrieved successfully.', $revenueReport);
    }

    public function update(UpdateRevenueReportsRequest $request, RevenueReports $revenueReport): JsonResponse
    {
        $revenueReport = DB::transaction(function () use ($request, $revenueReport) {
            $revenueReport->update($request->validated());

            return $revenueReport;
        });

        return $this->success('Revenue report updated successfully.', $revenueReport);
    }

    public function destroy(RevenueReports $revenueReport): JsonResponse
    {
        $revenueReport->delete();

        return $this->success('Revenue report deleted successfully.');
    }
}
