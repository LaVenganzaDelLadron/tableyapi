<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('capital_records', function (Blueprint $table) {
            $table->decimal('sales_revenue', 12, 2)->default(0)->after('starting_capital');
            $table->decimal('cacao_costs', 12, 2)->default(0)->after('sales_revenue');
            $table->decimal('employee_costs', 12, 2)->default(0)->after('cacao_costs');
            $table->decimal('operational_expenses', 12, 2)->default(0)->after('employee_costs');
            $table->decimal('gross_profit', 12, 2)->default(0)->after('total_expenses');
            $table->decimal('net_profit', 12, 2)->default(0)->after('gross_profit');
            $table->decimal('remaining_capital', 12, 2)->default(0)->after('net_profit');
        });
    }

    public function down(): void
    {
        Schema::table('capital_records', function (Blueprint $table) {
            $table->dropColumn([
                'sales_revenue',
                'cacao_costs',
                'employee_costs',
                'operational_expenses',
                'gross_profit',
                'net_profit',
                'remaining_capital',
            ]);
        });
    }
};
