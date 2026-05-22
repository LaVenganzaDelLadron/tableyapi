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
        Schema::create('cacao_batches', function (Blueprint $table) {
            $table->id();
            $table->decimal('raw_kilogram', 10, 2);
            $table->decimal('roasted_kilogram', 10, 2);
            $table->date('production_date');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cacao_batches');
    }
};
