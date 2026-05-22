<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('capital_records', function (Blueprint $table) {
            $table->id();
            $table->string('report_type')->default('monthly');
            $table->date('period_start');
            $table->date('period_end');
            $table->decimal('starting_capital', 10, 2);
            $table->decimal('total_revenue', 10, 2);
            $table->decimal('total_expenses', 10, 2);
            $table->decimal('final_profit', 10, 2);
            $table->timestamps();

            $table->unique(['report_type', 'period_start', 'period_end']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('capital_records');
    }
};
