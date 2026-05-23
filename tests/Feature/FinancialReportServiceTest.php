<?php

namespace Tests\Feature;

use App\Models\CacaoPurchases;
use App\Models\CapitalRecords;
use App\Models\Categories;
use App\Models\EmployeePayRecords;
use App\Models\Expenses;
use App\Models\InventoryLogs;
use App\Models\Orders;
use App\Models\Products;
use App\Models\ProductionBatches;
use App\Services\FinancialService;
use App\Services\InventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
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
        Orders::create([
            'subtotal' => 888888,
            'shipping_fee' => 0,
            'total_price' => 888888,
            'payment_method' => 'gcash',
            'payment_status' => 'paid',
            'shipping_address' => 'Tagum City',
            'status' => 'processing',
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

        $summary = app(FinancialService::class)->calculatePeriodSummary('2026-05-01', '2026-05-31', 50000);

        $this->assertSame('100000.00', $summary['sales_revenue']);
        $this->assertSame('85000.00', $summary['gross_profit']);
        $this->assertSame('40000.00', $summary['total_expenses']);
        $this->assertSame('60000.00', $summary['net_profit']);
        $this->assertSame('110000.00', $summary['remaining_capital']);
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

        $summary = app(FinancialService::class)->calculatePeriodSummary('2026-05-01', '2026-05-31', 0);

        $this->assertSame('0.00', $summary['sales_revenue']);
    }

    public function test_capital_records_table_does_not_contain_redundant_columns(): void
    {
        $this->assertFalse(Schema::hasColumn('capital_records', 'total_revenue'));
        $this->assertFalse(Schema::hasColumn('capital_records', 'final_profit'));
        $this->assertFalse(Schema::hasColumn('capital_records', 'gross_profit'));
    }

    public function test_generated_capital_record_stores_only_accounting_snapshot_columns(): void
    {
        Orders::create([
            'subtotal' => 5000,
            'shipping_fee' => 0,
            'total_price' => 5000,
            'payment_method' => 'gcash',
            'payment_status' => 'paid',
            'payment_reference' => 'PAID-002',
            'paid_at' => '2026-05-08 09:00:00',
            'shipping_address' => 'Tagum City',
            'status' => 'completed',
        ]);
        CacaoPurchases::create([
            'kilogram' => 10,
            'price_per_kilogram' => 100,
            'total_amount' => 1000,
            'payment_status' => 'paid',
            'paid_at' => '2026-05-03 09:00:00',
            'purchase_date' => '2026-05-03',
        ]);

        $record = app(FinancialService::class)->generateCapitalRecord('2026-05-01', '2026-05-31', 'monthly', 10000);

        $this->assertSame('5000.00', (string) $record->sales_revenue);
        $this->assertSame('1000.00', (string) $record->cacao_costs);
        $this->assertSame('4000.00', (string) $record->net_profit);
        $this->assertSame('14000.00', (string) $record->remaining_capital);
        $this->assertArrayNotHasKey('gross_profit', $record->getAttributes());
    }

    public function test_capital_record_requests_reject_manually_submitted_computed_totals(): void
    {
        $payload = [
            'report_type' => 'monthly',
            'period_start' => '2026-05-01',
            'period_end' => '2026-05-31',
            'starting_capital' => '10000.00',
            'sales_revenue' => '999999.00',
            'gross_profit' => '999999.00',
            'net_profit' => '999999.00',
        ];
        $request = \App\Http\Requests\StoreCapitalRecordsRequest::create('/api/capital-records', 'POST', $payload);
        $validator = Validator::make($payload, $request->rules(), $request->messages());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('sales_revenue', $validator->errors()->messages());
        $this->assertArrayHasKey('gross_profit', $validator->errors()->messages());
        $this->assertArrayHasKey('net_profit', $validator->errors()->messages());
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
            'total_expenses' => 40000,
            'net_profit' => 60000,
            'remaining_capital' => 110000,
        ]);

        $summary = app(FinancialService::class)->calculatePeriodSummary('2026-06-01', '2026-06-30');

        $this->assertSame('110000.00', $summary['starting_capital']);
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

        $inventory = app(FinancialService::class)->calculateInventoryValue($product);

        $this->assertSame('250.00', $inventory['total_inventory_value']);
    }

    public function test_production_stock_increase_creates_accurate_inventory_log(): void
    {
        $product = $this->product(['stock' => 10]);
        $batch = ProductionBatches::create([
            'product_id' => $product->id,
            'packs_produced' => 25,
            'price_per_pack' => 120,
            'total_production_value' => 3000,
            'total_production_cost' => 1250,
            'cost_per_pack' => 50,
            'production_date' => '2026-05-05',
        ]);

        $log = app(InventoryService::class)->recordProductionIncrease($batch);

        $this->assertSame(35, (int) $product->fresh()->stock);
        $this->assertSame('production_added', $log->type);
        $this->assertSame(25, (int) $log->quantity_change);
        $this->assertSame(35, (int) $log->remaining_stock);
    }

    public function test_order_stock_deduction_creates_accurate_inventory_log(): void
    {
        $product = $this->product(['stock' => 30]);
        $order = $this->order();

        $log = app(InventoryService::class)->recordOrderDeduction($order, $product, 12);

        $this->assertSame(18, (int) $product->fresh()->stock);
        $this->assertSame('order_deduction', $log->type);
        $this->assertSame(-12, (int) $log->quantity_change);
        $this->assertSame(18, (int) $log->remaining_stock);
    }

    public function test_insufficient_stock_fails_before_order_deduction(): void
    {
        $product = $this->product(['stock' => 5]);
        $order = $this->order();

        $this->expectException(ValidationException::class);

        try {
            app(InventoryService::class)->recordOrderDeduction($order, $product, 6);
        } finally {
            $this->assertSame(5, (int) $product->fresh()->stock);
            $this->assertSame(0, InventoryLogs::query()->where('order_id', $order->id)->count());
        }
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

    private function order(array $overrides = []): Orders
    {
        return Orders::create(array_merge([
            'subtotal' => 1200,
            'shipping_fee' => 0,
            'total_price' => 1200,
            'payment_method' => 'gcash',
            'payment_status' => 'paid',
            'payment_reference' => 'TEST-ORDER',
            'paid_at' => '2026-05-10 09:00:00',
            'shipping_address' => 'Tagum City',
            'status' => 'completed',
        ], $overrides));
    }
}
