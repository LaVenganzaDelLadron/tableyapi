<?php

namespace App\Services;

use App\Models\CacaoPurchases;
use App\Models\CapitalRecords;
use App\Models\EmployeePayRecords;
use App\Models\Expenses;
use App\Models\Orders;
use App\Models\Products;
use App\Models\ProductionBatches;
use App\Models\RevenueReports;
use App\Models\SalesReports;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class FinancialReportService
{
    public function paidSalesRevenue(string $periodStart, string $periodEnd): float
    {
        return round((float) $this->paidOrdersQuery($periodStart, $periodEnd)->sum('total_price'), 2);
    }

    public function paidOrdersCount(string $periodStart, string $periodEnd): int
    {
        return (int) $this->paidOrdersQuery($periodStart, $periodEnd)->count();
    }

    public function cacaoCosts(string $periodStart, string $periodEnd): float
    {
        return round((float) CacaoPurchases::query()
            ->whereBetween('purchase_date', [$periodStart, $periodEnd])
            ->sum('total_amount'), 2);
    }

    public function employeeCosts(string $periodStart, string $periodEnd): float
    {
        return round((float) EmployeePayRecords::query()
            ->whereBetween('pay_date', [$periodStart, $periodEnd])
            ->sum('total_amount'), 2);
    }

    public function operationalExpenses(string $periodStart, string $periodEnd): float
    {
        return round((float) Expenses::query()
            ->whereBetween('expense_date', [$periodStart, $periodEnd])
            ->sum('amount'), 2);
    }

    public function calculatePeriodSummary(string $periodStart, string $periodEnd, ?float $startingCapital = null): array
    {
        $salesRevenue = $this->paidSalesRevenue($periodStart, $periodEnd);
        $cacaoCosts = $this->cacaoCosts($periodStart, $periodEnd);
        $employeeCosts = $this->employeeCosts($periodStart, $periodEnd);
        $operationalExpenses = $this->operationalExpenses($periodStart, $periodEnd);
        $totalExpenses = round($cacaoCosts + $employeeCosts + $operationalExpenses, 2);
        $grossProfit = round($salesRevenue - $cacaoCosts, 2);
        $netProfit = round($salesRevenue - $totalExpenses, 2);
        $startingCapital = $startingCapital ?? $this->startingCapitalForPeriod($periodStart);
        $remainingCapital = round($startingCapital + $netProfit, 2);

        return [
            'starting_capital' => round($startingCapital, 2),
            'sales_revenue' => $salesRevenue,
            'cacao_costs' => $cacaoCosts,
            'employee_costs' => $employeeCosts,
            'operational_expenses' => $operationalExpenses,
            'total_expenses' => $totalExpenses,
            'gross_profit' => $grossProfit,
            'net_profit' => $netProfit,
            'remaining_capital' => $remainingCapital,
            'total_revenue' => $salesRevenue,
            'final_profit' => $netProfit,
        ];
    }

    public function startingCapitalForPeriod(string $periodStart): float
    {
        $previous = CapitalRecords::query()
            ->whereDate('period_end', '<', $periodStart)
            ->orderByDesc('period_end')
            ->first();

        if (! $previous) {
            return 0.00;
        }

        return round((float) ($previous->remaining_capital ?: $previous->final_profit), 2);
    }

    public function calculateProductionCost(ProductionBatches $productionBatch): array
    {
        $productionBatch->loadMissing([
            'cacaoBatch.cacaoPurchase',
            'cacaoBatch.employeePayRecords',
            'employeePayRecords',
        ]);

        $packsProduced = max(1, (int) $productionBatch->packs_produced);
        $totalPacksForCacaoBatch = $productionBatch->cacao_batch_id
            ? max(1, (int) ProductionBatches::query()
                ->where('cacao_batch_id', $productionBatch->cacao_batch_id)
                ->sum('packs_produced'))
            : $packsProduced;
        $allocationRatio = $productionBatch->cacao_batch_id
            ? $packsProduced / $totalPacksForCacaoBatch
            : 1;

        $cacaoPurchaseCost = (float) ($productionBatch->cacaoBatch?->cacaoPurchase?->total_amount ?? 0);
        $roastingBatchCost = (float) ($productionBatch->cacaoBatch?->total_roasting_payment ?? 0);
        $cacaoBatchPayroll = (float) ($productionBatch->cacaoBatch?->employeePayRecords?->sum('total_amount') ?? 0);
        $roastingCost = $cacaoBatchPayroll > 0 ? $cacaoBatchPayroll : $roastingBatchCost;
        $productionPayroll = (float) $productionBatch->employeePayRecords->sum('total_amount');

        $allocatedCacaoCost = round($cacaoPurchaseCost * $allocationRatio, 2);
        $allocatedRoastingCost = round($roastingCost * $allocationRatio, 2);
        $totalProductionCost = round($allocatedCacaoCost + $allocatedRoastingCost + $productionPayroll, 2);
        $costPerPack = round($totalProductionCost / $packsProduced, 2);

        return [
            'cacao_cost' => $allocatedCacaoCost,
            'roasting_cost' => $allocatedRoastingCost,
            'employee_cost' => round($productionPayroll, 2),
            'total_production_cost' => $totalProductionCost,
            'cost_per_pack' => $costPerPack,
        ];
    }

    public function syncProductionCost(ProductionBatches $productionBatch): ProductionBatches
    {
        $cost = $this->calculateProductionCost($productionBatch);
        $productionBatch->forceFill([
            'total_production_cost' => $cost['total_production_cost'],
            'cost_per_pack' => $cost['cost_per_pack'],
        ])->save();

        return $productionBatch->refresh();
    }

    public function calculateInventoryValue(?Products $product = null): array
    {
        $products = $product ? collect([$product]) : Products::query()->get();

        $items = $products->map(function (Products $item): array {
            $batches = ProductionBatches::query()
                ->where('product_id', $item->id)
                ->get();
            $totalPacks = max(1, (int) $batches->sum('packs_produced'));
            $totalCost = $batches->sum(function (ProductionBatches $batch): float {
                if ((float) $batch->total_production_cost > 0) {
                    return (float) $batch->total_production_cost;
                }

                return $this->calculateProductionCost($batch)['total_production_cost'];
            });
            $averageCostPerPack = round($totalCost / $totalPacks, 2);
            $inventoryValue = round((int) $item->stock * $averageCostPerPack, 2);

            return [
                'product_id' => $item->id,
                'product_name' => $item->name,
                'remaining_stock' => (int) $item->stock,
                'average_cost_per_pack' => $averageCostPerPack,
                'inventory_value' => $inventoryValue,
            ];
        });

        return [
            'total_inventory_value' => round((float) $items->sum('inventory_value'), 2),
            'items' => $items->values(),
        ];
    }

    public function generateMonthlyReports(int $year, int $month, ?float $startingCapital = null): array
    {
        $periodStart = CarbonImmutable::create($year, $month, 1)->startOfMonth()->toDateString();
        $periodEnd = CarbonImmutable::create($year, $month, 1)->endOfMonth()->toDateString();

        return DB::transaction(function () use ($periodStart, $periodEnd, $startingCapital): array {
            $summary = $this->calculatePeriodSummary($periodStart, $periodEnd, $startingCapital);
            $paidOrdersCount = $this->paidOrdersCount($periodStart, $periodEnd);

            $salesReport = SalesReports::updateOrCreate(
                ['report_type' => 'monthly', 'period_start' => $periodStart, 'period_end' => $periodEnd],
                [
                    'total_sales' => $summary['sales_revenue'],
                    'total_orders' => $paidOrdersCount,
                    'total_revenue' => $summary['sales_revenue'],
                ]
            );

            $revenueReport = RevenueReports::updateOrCreate(
                ['report_type' => 'monthly', 'period_start' => $periodStart, 'period_end' => $periodEnd],
                [
                    'gross_revenue' => $summary['sales_revenue'],
                    'total_expenses' => $summary['total_expenses'],
                    'net_income' => $summary['net_profit'],
                ]
            );

            $capitalRecord = CapitalRecords::updateOrCreate(
                ['report_type' => 'monthly', 'period_start' => $periodStart, 'period_end' => $periodEnd],
                $summary
            );

            return [
                'period_start' => $periodStart,
                'period_end' => $periodEnd,
                'summary' => $summary,
                'sales_report' => $salesReport,
                'revenue_report' => $revenueReport,
                'capital_record' => $capitalRecord,
                'inventory_value' => $this->calculateInventoryValue(),
            ];
        });
    }

    private function paidOrdersQuery(string $periodStart, string $periodEnd): Builder
    {
        return Orders::query()
            ->where('payment_status', 'paid')
            ->whereNotNull('paid_at')
            ->whereDate('paid_at', '>=', $periodStart)
            ->whereDate('paid_at', '<=', $periodEnd);
    }
}
