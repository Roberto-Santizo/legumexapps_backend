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
        Schema::table('task_production_plans', function (Blueprint $table) {
            $table->dropForeign('task_production_plans_sku_id_foreign');
            $table->dropColumn('sku_id');

            $table->foreignId('line_sku_id')->constrained()->on('line_stock_keeping_units');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_production_plans', function (Blueprint $table) {
            $table->foreignId('sku_id')->constrained()->on('stock_keeping_units');

            $table->dropForeign('task_production_plans_line_sku_id_foreign');
            $table->dropColumn('line_sku_id');
        });
    }
};
