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
        Schema::create('task_production_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('weekly_production_plan_id')->constrained();
            $table->foreignId('line_id')->constrained();
            $table->datetime('operation_date');
            $table->float('total_hours')->nullab();
            $table->datetime('start_date')->nullable();
            $table->datetime('end_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_production_plans');
    }
};
