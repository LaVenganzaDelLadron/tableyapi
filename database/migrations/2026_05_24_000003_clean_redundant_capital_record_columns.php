<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('capital_records', function (Blueprint $table) {
            $table->dropColumn(['total_revenue', 'gross_profit', 'final_profit']);
        });
    }

    public function down(): void
    {
        Schema::table('capital_records', function (Blueprint $table) {
            $table->decimal('total_revenue', 10, 2)->default(0)->after('operational_expenses');
            $table->decimal('gross_profit', 12, 2)->default(0)->after('total_expenses');
            $table->decimal('final_profit', 10, 2)->default(0)->after('remaining_capital');
        });
    }
};
