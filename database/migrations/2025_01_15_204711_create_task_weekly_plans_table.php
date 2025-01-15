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
        Schema::create('task_weekly_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('weekly_plan_id')->constrained();
            $table->foreignId('lote_plantation_control_id')->constrained();
            $table->foreignId('tarea_id')->constrained();
            $table->integer('workers_quantity');
            $table->float('budget');
            $table->float('hours');
            $table->integer('slots');
            $table->boolean('extraordinary');
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
        Schema::dropIfExists('task_weekly_plans');
    }
};
