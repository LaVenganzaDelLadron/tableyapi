<?php

namespace Tests\Feature;

use App\Models\CacaoBatches;
use App\Models\CacaoPurchases;
use App\Models\CapitalRecords;
use App\Models\Categories;
use App\Models\EmployeePayRecords;
use App\Models\Expenses;
use App\Models\Orders;
use App\Models\Products;
use App\Models\ProductionBatches;
use App\Models\Suppliers;
use App\Services\FinancialReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinancialReportServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_period_summary_uses_only_paid_orders_and_real_expenses(): void
    {
        Orders::create([
            'subtotal' => 100000,
            'shipping_fee' => 0,
            'total_price' => 100000,
            'payment_method' => 'gcash',
            'payment_status' => 'paid',
            'payment_reference' => 'PAID-001',
            'paid_at' => '2026-05-10 09:00:00',
            'shipping_address' => 'Tagum City',
            'status' => 'completed',
        ]);
        Orders::create([
            'subtotal' => 999999,
            'shipping_fee' => 0,
            'total_price' => 999999,
            'payment_method' => 'cash_on_delivery',
            'payment_status' => 'unpaid',
            'shipping_address' => 'Tagum City',
            'status' => 'pending',
        ]);
        CacaoPurchases::create([
            'kilogram' => 100,
            'price_per_kilogram' => 150,
            'total_amount' => 15000,
            'payment_status' => 'paid',
            'paid_at' => '2026-05-03 09:00:00',
            'purchase_date' => '2026-05-03',
        ]);
        EmployeePayRecords::create([
            'employee_id' => $this->employeeId(),
            'pay_type' => 'daily',
            'pay_date' => '2026-05-12',
            'quantity' => 1,
            'rate' => 5000,
            'total_amount' => 5000,
        ]);
        Expenses::create([
            'title' => 'Packaging',
            'category' => 'packaging',
            'amount' => 20000,
            'expense_date' => '2026-05-15',
        ]);

        $summary = app(FinancialReportService::class)->calculatePeriodSummary('2026-05-01', '2026-05-31', 50000);

        $this->assertSame(100000.0, $summary['sales_revenue']);
        $this->assertSame(40000.0, $summary['total_expenses']);
        $this->assertSame(60000.0, $summary['net_profit']);
        $this->assertSame(110000.0, $summary['remaining_capital']);
    }

    public function test_production_value_is_not_counted_as_revenue(): void
    {
        $product = $this->product();
        ProductionBatches::create([
            'product_id' => $product->id,
            'packs_produced' => 100,
            'price_per_pack' => 1000,
            'total_production_value' => 100000,
            'production_date' => '2026-05-05',
        ]);

        $summary = app(FinancialReportService::class)->calculatePeriodSummary('2026-05-01', '2026-05-31', 0);

        $this->assertSame(0.0, $summary['sales_revenue']);
    }

    public function test_next_period_inherits_previous_remaining_capital(): void
    {
        CapitalRecords::create([
            'report_type' => 'monthly',
            'period_start' => '2026-05-01',
            'period_end' => '2026-05-31',
            'starting_capital' => 50000,
            'sales_revenue' => 100000,
            'cacao_costs' => 15000,
            'employee_costs' => 5000,
            'operational_expenses' => 20000,
            'total_revenue' => 100000,
            'total_expenses' => 40000,
            'gross_profit' => 85000,
            'net_profit' => 60000,
            'remaining_capital' => 110000,
            'final_profit' => 60000,
        ]);

        $summary = app(FinancialReportService::class)->calculatePeriodSummary('2026-06-01', '2026-06-30');

        $this->assertSame(110000.0, $summary['starting_capital']);
    }

    public function test_inventory_value_uses_cost_per_pack_not_selling_price(): void
    {
        $product = $this->product(['stock' => 10, 'price' => 999]);
        ProductionBatches::create([
            'product_id' => $product->id,
            'packs_produced' => 10,
            'price_per_pack' => 999,
            'total_production_value' => 9990,
            'total_production_cost' => 250,
            'cost_per_pack' => 25,
            'production_date' => '2026-05-05',
        ]);

        $inventory = app(FinancialReportService::class)->calculateInventoryValue($product);

        $this->assertSame(250.0, $inventory['total_inventory_value']);
    }

    private function product(array $overrides = []): Products
    {
        $category = Categories::create(['name' => 'Tableya Packs']);

        return Products::create(array_merge([
            'category_id' => $category->id,
            'name' => 'Pure Tableya Pack',
            'description' => 'Demo product',
            'price' => 120,
            'wholesale_price' => 95,
            'minimum_wholesale_quantity' => 20,
            'stock' => 100,
            'is_available' => true,
        ], $overrides));
    }

    private function employeeId(): int
    {
        return \App\Models\Employees::create([
            'name' => 'Nora Villanueva',
            'position' => 'Sales Staff',
            'payment_type' => 'daily',
            'rate' => 500,
            'is_active' => true,
        ])->id;
    }
}
