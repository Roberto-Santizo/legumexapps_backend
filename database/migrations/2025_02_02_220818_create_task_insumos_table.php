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
        Schema::create('task_insumos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_weekly_plan_id')->constrained();
            $table->foreignId('insumo_id')->constrained();
            $table->float('assigned_quantity')->nullable(false);
            $table->float('used_quantity')->nullable(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_insumos');
    }
};
