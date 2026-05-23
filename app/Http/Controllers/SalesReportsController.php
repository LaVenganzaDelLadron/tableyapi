<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSalesReportsRequest;
use App\Http\Requests\UpdateSalesReportsRequest;
use App\Models\SalesReports;
use App\Services\FinancialService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesReportsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $reports = SalesReports::query()->paginate($request->integer('per_page', 15));

        return $this->success('Sales reports retrieved successfully.', $reports);
    }

    public function store(StoreSalesReportsRequest $request, FinancialService $financialReportService): JsonResponse
    {
        $report = DB::transaction(function () use ($request, $financialReportService) {
            $data = $request->validated();
            $paidRevenue = $financialReportService->paidSalesRevenue($data['period_start'], $data['period_end']);
            $paidOrdersCount = $financialReportService->paidOrdersCount($data['period_start'], $data['period_end']);

            return SalesReports::create([
                'report_type' => $data['report_type'] ?? 'daily',
                'period_start' => $data['period_start'],
                'period_end' => $data['period_end'],
                'total_sales' => $paidRevenue,
                'total_orders' => $paidOrdersCount,
                'total_revenue' => $paidRevenue,
            ]);
        });

        return $this->success('Sales report created successfully.', $report, 201);
    }

    public function show(SalesReports $salesReport): JsonResponse
    {
        return $this->success('Sales report retrieved successfully.', $salesReport);
    }

    public function update(UpdateSalesReportsRequest $request, SalesReports $salesReport): JsonResponse
    {
        $salesReport = DB::transaction(function () use ($request, $salesReport) {
            $salesReport->update($request->validated());

            return $salesReport;
        });

        return $this->success('Sales report updated successfully.', $salesReport);
    }

    public function destroy(SalesReports $salesReport): JsonResponse
    {
        $salesReport->delete();

        return $this->success('Sales report deleted successfully.');
    }
}
