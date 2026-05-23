<?php

namespace Database\Seeders;

use App\Models\Expenses;
use App\Services\FinancialService;
use Illuminate\Database\Seeder;

class FinanceReportsSeeder extends Seeder
{
    public function run(): void
    {
        $financialReportService = app(FinancialService::class);
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
        $financialReportService->generateMonthlyReports(2026, 5, 50000.00);
    }
}
