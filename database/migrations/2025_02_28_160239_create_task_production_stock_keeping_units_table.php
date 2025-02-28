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
        Schema::create('task_production_stock_keeping_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_p_id')->constrained()->on('task_production_plans');
            $table->foreignId('sku_id')->constrained()->on('stock_keeping_units');
            $table->datetime('start_date')->nullable();
            $table->datetime('end_date')->nullable();
            $table->integer('tarimas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_production_stock_keeping_units');
    }
};
