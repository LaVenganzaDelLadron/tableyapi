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
        Schema::create('employee_pay_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->restrictOnDelete();
            $table->foreignId('employee_attendance_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('cacao_batch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('production_batch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('pay_type');
            $table->date('pay_date');
            $table->decimal('quantity', 10, 2)->default(0);
            $table->decimal('rate', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['employee_id', 'pay_date']);
            $table->index('pay_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_pay_records');
    }
};
