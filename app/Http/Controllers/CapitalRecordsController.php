<?php

namespace App\Http\Controllers;

use App\Http\Requests\FinancialSummaryRequest;
use App\Http\Requests\GenerateMonthlyFinancialReportRequest;
use App\Http\Requests\StoreCapitalRecordsRequest;
use App\Http\Requests\UpdateCapitalRecordsRequest;
use App\Models\CapitalRecords;
use App\Services\FinancialReportService;
use Carbon\CarbonImmutable;
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

    public function store(StoreCapitalRecordsRequest $request, FinancialReportService $financialReportService): JsonResponse
    {
        $record = DB::transaction(function () use ($request, $financialReportService) {
            $data = $request->validated();
            $summary = $financialReportService->calculatePeriodSummary(
                $data['period_start'],
                $data['period_end'],
                isset($data['starting_capital']) ? (float) $data['starting_capital'] : null
            );

            return CapitalRecords::create(array_merge([
                'report_type' => $data['report_type'] ?? 'monthly',
                'period_start' => $data['period_start'],
                'period_end' => $data['period_end'],
            ], $summary));
        });

        return $this->success('Capital record created successfully.', $record, 201);
    }

    public function summary(FinancialSummaryRequest $request, FinancialReportService $financialReportService): JsonResponse
    {
        $data = $request->validated();
        [$periodStart, $periodEnd] = $this->periodFromRequest($data);
        $summary = $financialReportService->calculatePeriodSummary(
            $periodStart,
            $periodEnd,
            isset($data['starting_capital']) ? (float) $data['starting_capital'] : null
        );

        return $this->success('Capital summary computed successfully.', [
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
            ...$summary,
            'inventory_value' => $financialReportService->calculateInventoryValue(),
        ]);
    }

    public function show(CapitalRecords $capitalRecord): JsonResponse
    {
        return $this->success('Capital record retrieved successfully.', $capitalRecord);
    }

    public function update(UpdateCapitalRecordsRequest $request, CapitalRecords $capitalRecord): JsonResponse
    {
        $capitalRecord = DB::transaction(function () use ($request, $capitalRecord) {
            $capitalRecord->update($this->withCompatibilityValues($request->validated()));

            return $capitalRecord;
        });

        return $this->success('Capital record updated successfully.', $capitalRecord);
    }

    public function destroy(CapitalRecords $capitalRecord): JsonResponse
    {
        $capitalRecord->delete();

        return $this->success('Capital record deleted successfully.');
    }

    public function generateMonthly(GenerateMonthlyFinancialReportRequest $request, FinancialReportService $financialReportService): JsonResponse
    {
        $data = $request->validated();
        $reports = $financialReportService->generateMonthlyReports(
            (int) $data['year'],
            (int) $data['month'],
            isset($data['starting_capital']) ? (float) $data['starting_capital'] : null
        );

        return $this->success('Monthly financial reports generated successfully.', $reports, 201);
    }

    private function periodFromRequest(array $data): array
    {
        $periodStart = $data['period_start'] ?? CarbonImmutable::now()->startOfMonth()->toDateString();
        $periodEnd = $data['period_end'] ?? CarbonImmutable::parse($periodStart)->endOfMonth()->toDateString();

        return [$periodStart, $periodEnd];
    }

    private function withCompatibilityValues(array $data): array
    {
        if (array_key_exists('sales_revenue', $data)) {
            $data['total_revenue'] = $data['sales_revenue'];
        }

        if (array_key_exists('cacao_costs', $data) || array_key_exists('employee_costs', $data) || array_key_exists('operational_expenses', $data)) {
            $data['total_expenses'] = round(
                (float) ($data['cacao_costs'] ?? 0)
                + (float) ($data['employee_costs'] ?? 0)
                + (float) ($data['operational_expenses'] ?? 0),
                2
            );
        }

        if (array_key_exists('net_profit', $data)) {
            $data['final_profit'] = $data['net_profit'];
        }

        return $data;
    }
}
