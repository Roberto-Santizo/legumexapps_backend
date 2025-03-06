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
        Schema::create('task_production_timeouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('timeout_id')->constrained();
            $table->foreignId('task_p_id')->constrained()->on('task_production_plans');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_production_timeouts');
    }
};
