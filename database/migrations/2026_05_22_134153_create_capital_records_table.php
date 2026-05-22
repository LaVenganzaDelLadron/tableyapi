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
            $table->decimal('starting_capital', 10, 2);
            $table->decimal('total_revenue', 10, 2);
            $table->decimal('total_expenses', 10, 2);
            $table->decimal('final_profit', 10, 2);
            $table->timestamps();
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
