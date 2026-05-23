<?php

namespace App\Http\Controllers;

use App\Http\Requests\FinancialSummaryRequest;
use App\Http\Requests\GenerateMonthlyFinancialReportRequest;
use App\Http\Requests\StoreCapitalRecordsRequest;
use App\Http\Requests\UpdateCapitalRecordsRequest;
use App\Models\CapitalRecords;
use App\Services\FinancialService;
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

    public function store(StoreCapitalRecordsRequest $request, FinancialService $financialReportService): JsonResponse
    {
        $record = DB::transaction(function () use ($request, $financialReportService) {
            $data = $request->validated();
            return $financialReportService->generateCapitalRecord(
                $data['period_start'],
                $data['period_end'],
                $data['report_type'] ?? 'monthly',
                $data['starting_capital'] ?? null
            );
        });

        return $this->success('Capital record created successfully.', $record, 201);
    }

    public function summary(FinancialSummaryRequest $request, FinancialService $financialReportService): JsonResponse
    {
        $data = $request->validated();
        [$periodStart, $periodEnd] = $this->periodFromRequest($data);
        $summary = $financialReportService->calculatePeriodSummary(
            $periodStart,
            $periodEnd,
            $data['starting_capital'] ?? null
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

    public function update(
        UpdateCapitalRecordsRequest $request,
        CapitalRecords $capitalRecord,
        FinancialService $financialReportService
    ): JsonResponse
    {
        $capitalRecord = DB::transaction(function () use ($request, $capitalRecord, $financialReportService) {
            $data = $request->validated();
            $periodStart = $data['period_start'] ?? $capitalRecord->period_start->toDateString();
            $periodEnd = $data['period_end'] ?? $capitalRecord->period_end->toDateString();
            $reportType = $data['report_type'] ?? $capitalRecord->report_type;
            $startingCapital = $data['starting_capital'] ?? $capitalRecord->starting_capital;

            $attributes = $financialReportService->calculateCapitalRecordAttributes(
                $periodStart,
                $periodEnd,
                $reportType,
                $startingCapital
            );

            $capitalRecord->update($attributes);

            return $capitalRecord;
        });

        return $this->success('Capital record updated successfully.', $capitalRecord);
    }

    public function destroy(CapitalRecords $capitalRecord): JsonResponse
    {
        $capitalRecord->delete();

        return $this->success('Capital record deleted successfully.');
    }

    public function generateMonthly(GenerateMonthlyFinancialReportRequest $request, FinancialService $financialReportService): JsonResponse
    {
        $data = $request->validated();
        $reports = $financialReportService->generateMonthlyReports(
            (int) $data['year'],
            (int) $data['month'],
            $data['starting_capital'] ?? null
        );

        return $this->success('Monthly financial reports generated successfully.', $reports, 201);
    }

    private function periodFromRequest(array $data): array
    {
        $periodStart = $data['period_start'] ?? CarbonImmutable::now()->startOfMonth()->toDateString();
        $periodEnd = $data['period_end'] ?? CarbonImmutable::parse($periodStart)->endOfMonth()->toDateString();

        return [$periodStart, $periodEnd];
    }
}
