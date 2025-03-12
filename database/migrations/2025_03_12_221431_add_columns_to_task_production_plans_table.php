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
            $table->float('tarimas');
            $table->foreignId('sku_id')->constrained()->on('stock_keeping_units');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_production_plans', function (Blueprint $table) {
            $table->dropColumn('tarimas');
        });
    }
};
