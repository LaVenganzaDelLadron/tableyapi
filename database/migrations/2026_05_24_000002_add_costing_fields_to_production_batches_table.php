<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('production_batches', function (Blueprint $table) {
            $table->decimal('total_production_cost', 12, 2)->default(0)->after('total_production_value');
            $table->decimal('cost_per_pack', 12, 2)->default(0)->after('total_production_cost');
        });
    }

    public function down(): void
    {
        Schema::table('production_batches', function (Blueprint $table) {
            $table->dropColumn(['total_production_cost', 'cost_per_pack']);
        });
    }
};
