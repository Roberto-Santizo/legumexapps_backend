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
        Schema::create('task_production_drafts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('draft_weekly_production_plan_id')->constrained();
            $table->foreignId('line_id')->constrained()->nullable();
            $table->foreignId('stock_keeping_unit_id')->nullable();
            $table->float('total_boxes');
            $table->string('destination');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_production_drafts');
    }
};
