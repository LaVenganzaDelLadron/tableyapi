<?php

namespace App\Http\Controllers;

use App\Http\Requests\FinancialSummaryRequest;
use App\Http\Requests\StoreRevenueReportsRequest;
use App\Http\Requests\UpdateRevenueReportsRequest;
use App\Models\RevenueReports;
use App\Services\FinancialService;
use Carbon\CarbonImmutable;
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

    public function store(StoreRevenueReportsRequest $request, FinancialService $financialReportService): JsonResponse
    {
        $report = DB::transaction(function () use ($request, $financialReportService) {
            $data = $request->validated();
            $summary = $financialReportService->calculatePeriodSummary($data['period_start'], $data['period_end']);

            return RevenueReports::create([
                'report_type' => $data['report_type'] ?? 'monthly',
                'period_start' => $data['period_start'],
                'period_end' => $data['period_end'],
                'gross_revenue' => $summary['sales_revenue'],
                'total_expenses' => $summary['total_expenses'],
                'net_income' => $summary['net_profit'],
            ]);
        });

        return $this->success('Revenue report created successfully.', $report, 201);
    }

    public function summary(FinancialSummaryRequest $request, FinancialService $financialReportService): JsonResponse
    {
        $data = $request->validated();
        $periodStart = $data['period_start'] ?? CarbonImmutable::now()->startOfMonth()->toDateString();
        $periodEnd = $data['period_end'] ?? CarbonImmutable::parse($periodStart)->endOfMonth()->toDateString();
        $summary = $financialReportService->calculatePeriodSummary($periodStart, $periodEnd);

        return $this->success('Revenue summary computed successfully.', [
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
            'gross_revenue' => $summary['sales_revenue'],
            'cacao_costs' => $summary['cacao_costs'],
            'employee_costs' => $summary['employee_costs'],
            'operational_expenses' => $summary['operational_expenses'],
            'total_expenses' => $summary['total_expenses'],
            'net_income' => $summary['net_profit'],
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
