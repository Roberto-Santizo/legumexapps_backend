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
        Schema::create('partial_production_closures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_p_sku_id')->constrained()->on('task_production_stock_keeping_units');
            $table->datetime('start_date');
            $table->datetime('end_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partial_production_closures');
    }
};
