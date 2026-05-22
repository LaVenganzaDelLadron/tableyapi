<?php

namespace Database\Seeders;

use App\Models\CapitalRecords;
use App\Models\EmployeePayRecords;
use App\Models\Expenses;
use App\Models\Orders;
use App\Models\RevenueReports;
use App\Models\SalesReports;
use Illuminate\Database\Seeder;

class FinanceReportsSeeder extends Seeder
{
    public function run(): void
    {
        $expenses = [
            ['title' => 'Roasting Gas Refill', 'category' => 'production', 'amount' => 850.00, 'payment_method' => 'cash', 'payee' => 'Tagum Gas Center', 'expense_date' => '2026-05-05', 'notes' => 'Fuel for cacao roasting.'],
            ['title' => 'Grinding Service', 'category' => 'production', 'amount' => 1200.00, 'payment_method' => 'cash', 'payee' => 'Local Milling Service', 'expense_date' => '2026-05-09', 'notes' => 'Grinding roasted cacao nibs.'],
            ['title' => 'Packaging Labels', 'category' => 'packaging', 'amount' => 1500.00, 'payment_method' => 'gcash', 'payee' => 'Davao Print Shop', 'expense_date' => '2026-05-12', 'notes' => 'Labels for retail packs.'],
            ['title' => 'Delivery Fare', 'category' => 'transportation', 'amount' => 650.00, 'payment_method' => 'cash', 'payee' => 'Local Courier', 'expense_date' => '2026-05-18', 'notes' => 'Delivery of reseller orders.'],
            ['title' => 'Electricity Share', 'category' => 'utilities', 'amount' => 2100.00, 'payment_method' => 'bank_transfer', 'payee' => 'Utility Provider', 'expense_date' => '2026-05-22', 'notes' => 'Production area utility expense.'],
        ];

        foreach ($expenses as $expense) {
            Expenses::updateOrCreate(
                ['title' => $expense['title'], 'expense_date' => $expense['expense_date']],
                $expense
            );
        }

        $periodStart = '2026-05-01';
        $periodEnd = '2026-05-31';
        $grossRevenue = (float) Orders::whereBetween('created_at', [$periodStart, $periodEnd.' 23:59:59'])->sum('total_price');
        if ($grossRevenue <= 0) {
            $grossRevenue = (float) Orders::sum('total_price');
        }

        $expenseTotal = (float) Expenses::whereBetween('expense_date', [$periodStart, $periodEnd])->sum('amount');
        $payrollTotal = (float) EmployeePayRecords::whereBetween('pay_date', [$periodStart, $periodEnd])->sum('total_amount');
        $totalExpenses = round($expenseTotal + $payrollTotal, 2);
        $orderCount = Orders::count();
        $startingCapital = 50000.00;

        SalesReports::updateOrCreate(
            ['report_type' => 'monthly', 'period_start' => $periodStart, 'period_end' => $periodEnd],
            [
                'total_sales' => $grossRevenue,
                'total_orders' => $orderCount,
                'total_revenue' => $grossRevenue,
            ]
        );

        RevenueReports::updateOrCreate(
            ['report_type' => 'monthly', 'period_start' => $periodStart, 'period_end' => $periodEnd],
            [
                'gross_revenue' => $grossRevenue,
                'total_expenses' => $totalExpenses,
                'net_income' => round($grossRevenue - $totalExpenses, 2),
            ]
        );

        CapitalRecords::updateOrCreate(
            ['report_type' => 'monthly', 'period_start' => $periodStart, 'period_end' => $periodEnd],
            [
                'starting_capital' => $startingCapital,
                'total_revenue' => $grossRevenue,
                'total_expenses' => $totalExpenses,
                'final_profit' => round($startingCapital + $grossRevenue - $totalExpenses, 2),
            ]
        );
    }
}
