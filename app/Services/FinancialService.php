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

class FinancialService
{
    public function calculateSalesRevenue(string $periodStart, string $periodEnd): string
    {
        return $this->calculateRevenue($periodStart, $periodEnd);
    }

    public function calculateRevenue(string $periodStart, string $periodEnd): string
    {
        return $this->money($this->paidOrdersQuery($periodStart, $periodEnd)->sum('total_price'));
    }

    public function paidSalesRevenue(string $periodStart, string $periodEnd): string
    {
        return $this->calculateRevenue($periodStart, $periodEnd);
    }

    public function paidOrdersCount(string $periodStart, string $periodEnd): int
    {
        return (int) $this->paidOrdersQuery($periodStart, $periodEnd)->count();
    }

    public function calculateExpenses(string $periodStart, string $periodEnd): array
    {
        $cacaoCosts = $this->cacaoCosts($periodStart, $periodEnd);
        $employeeCosts = $this->employeeCosts($periodStart, $periodEnd);
        $operationalExpenses = $this->operationalExpenses($periodStart, $periodEnd);

        return [
            'cacao_costs' => $cacaoCosts,
            'employee_costs' => $employeeCosts,
            'operational_expenses' => $operationalExpenses,
            'total_expenses' => $this->add($this->add($cacaoCosts, $employeeCosts), $operationalExpenses),
        ];
    }

    public function calculateTotalExpenses(string $periodStart, string $periodEnd): string
    {
        return $this->calculateExpenses($periodStart, $periodEnd)['total_expenses'];
    }

    public function calculateNetProfit(string $salesRevenue, string $totalExpenses): string
    {
        return $this->sub($salesRevenue, $totalExpenses);
    }

    public function calculateCapital(string $startingCapital, string $netProfit): string
    {
        return $this->add($startingCapital, $netProfit);
    }

    public function calculateRemainingCapital(string $startingCapital, string $netProfit): string
    {
        return $this->calculateCapital($startingCapital, $netProfit);
    }

    public function cacaoCosts(string $periodStart, string $periodEnd): string
    {
        return $this->money(CacaoPurchases::query()
            ->whereBetween('purchase_date', [$periodStart, $periodEnd])
            ->sum('total_amount'));
    }

    public function employeeCosts(string $periodStart, string $periodEnd): string
    {
        return $this->money(EmployeePayRecords::query()
            ->whereBetween('pay_date', [$periodStart, $periodEnd])
            ->sum('total_amount'));
    }

    public function operationalExpenses(string $periodStart, string $periodEnd): string
    {
        return $this->money(Expenses::query()
            ->whereBetween('expense_date', [$periodStart, $periodEnd])
            ->sum('amount'));
    }

    public function calculatePeriodSummary(string $periodStart, string $periodEnd, int|float|string|null $startingCapital = null): array
    {
        $salesRevenue = $this->calculateRevenue($periodStart, $periodEnd);
        $expenses = $this->calculateExpenses($periodStart, $periodEnd);
        $netProfit = $this->calculateNetProfit($salesRevenue, $expenses['total_expenses']);
        $startingCapital = $this->money($startingCapital ?? $this->startingCapitalForPeriod($periodStart));
        $remainingCapital = $this->calculateCapital($startingCapital, $netProfit);

        return [
            'starting_capital' => $startingCapital,
            'sales_revenue' => $salesRevenue,
            'cacao_costs' => $expenses['cacao_costs'],
            'employee_costs' => $expenses['employee_costs'],
            'operational_expenses' => $expenses['operational_expenses'],
            'total_expenses' => $expenses['total_expenses'],
            'net_profit' => $netProfit,
            'remaining_capital' => $remainingCapital,
        ];
    }

    public function startingCapitalForPeriod(string $periodStart): string
    {
        $previous = CapitalRecords::query()
            ->whereDate('period_end', '<', $periodStart)
            ->orderByDesc('period_end')
            ->first();

        if (! $previous) {
            return '0.00';
        }

        return $this->money($previous->remaining_capital);
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

        $cacaoPurchaseCost = $this->money($productionBatch->cacaoBatch?->cacaoPurchase?->total_amount ?? 0);
        $roastingBatchCost = $this->money($productionBatch->cacaoBatch?->total_roasting_payment ?? 0);
        $cacaoBatchPayroll = $this->money($productionBatch->cacaoBatch?->employeePayRecords?->sum('total_amount') ?? 0);
        $roastingCost = $this->compare($cacaoBatchPayroll, '0.00') > 0 ? $cacaoBatchPayroll : $roastingBatchCost;
        $productionPayroll = $this->money($productionBatch->employeePayRecords->sum('total_amount'));

        $allocatedCacaoCost = $this->mul($cacaoPurchaseCost, (string) $allocationRatio);
        $allocatedRoastingCost = $this->mul($roastingCost, (string) $allocationRatio);
        $totalProductionCost = $this->add($this->add($allocatedCacaoCost, $allocatedRoastingCost), $productionPayroll);
        $costPerPack = $this->div($totalProductionCost, (string) $packsProduced);

        return [
            'cacao_cost' => $allocatedCacaoCost,
            'roasting_cost' => $allocatedRoastingCost,
            'employee_cost' => $productionPayroll,
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
            $totalCost = '0.00';
            foreach ($batches as $batch) {
                if ($this->compare((string) $batch->total_production_cost, '0.00') > 0) {
                    $totalCost = $this->add($totalCost, (string) $batch->total_production_cost);
                } else {
                    $totalCost = $this->add($totalCost, $this->calculateProductionCost($batch)['total_production_cost']);
                }
            }

            $averageCostPerPack = $this->div($totalCost, (string) $totalPacks);
            $inventoryValue = $this->mul((string) (int) $item->stock, $averageCostPerPack);

            return [
                'product_id' => $item->id,
                'product_name' => $item->name,
                'remaining_stock' => (int) $item->stock,
                'average_cost_per_pack' => $averageCostPerPack,
                'inventory_value' => $inventoryValue,
            ];
        });

        $totalInventoryValue = $items->reduce(fn (string $carry, array $item): string => $this->add($carry, $item['inventory_value']), '0.00');

        return [
            'total_inventory_value' => $totalInventoryValue,
            'items' => $items->values(),
        ];
    }

    public function generateMonthlyReports(int $year, int $month, int|float|string|null $startingCapital = null): array
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

    public function generateCapitalRecord(
        string $periodStart,
        string $periodEnd,
        string $reportType = 'monthly',
        int|float|string|null $startingCapital = null
    ): CapitalRecords {
        return DB::transaction(function () use ($periodStart, $periodEnd, $reportType, $startingCapital): CapitalRecords {
            $summary = $this->calculatePeriodSummary($periodStart, $periodEnd, $startingCapital);

            return CapitalRecords::updateOrCreate(
                ['report_type' => $reportType, 'period_start' => $periodStart, 'period_end' => $periodEnd],
                $summary
            );
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

    private function money(mixed $value): string
    {
        return number_format((float) ($value ?? 0), 2, '.', '');
    }

    private function add(string $left, string $right): string
    {
        if (function_exists('bcadd')) {
            return $this->money(bcadd($left, $right, 4));
        }

        return $this->money((float) $left + (float) $right);
    }

    private function sub(string $left, string $right): string
    {
        if (function_exists('bcsub')) {
            return $this->money(bcsub($left, $right, 4));
        }

        return $this->money((float) $left - (float) $right);
    }

    private function mul(string $left, string $right): string
    {
        if (function_exists('bcmul')) {
            return $this->money(bcmul($left, $right, 4));
        }

        return $this->money((float) $left * (float) $right);
    }

    private function div(string $left, string $right): string
    {
        if ($this->compare($right, '0.00') === 0) {
            return '0.00';
        }

        if (function_exists('bcdiv')) {
            return $this->money(bcdiv($left, $right, 4));
        }

        return $this->money((float) $left / (float) $right);
    }

    private function compare(string $left, string $right): int
    {
        if (function_exists('bccomp')) {
            return bccomp($left, $right, 4);
        }

        return (float) $left <=> (float) $right;
    }
}
