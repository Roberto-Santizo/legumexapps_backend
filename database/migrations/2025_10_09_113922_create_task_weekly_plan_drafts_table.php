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
        Schema::create('task_weekly_plan_drafts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_guideline_id')->constrained();
            $table->foreignId('draft_weekly_plan_id')->constrained();
            $table->foreignId('plantation_control_id')->constrained();
            $table->float('hours');
            $table->float('budget');
            $table->integer('slots');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_weekly_plan_drafts');
    }
};
