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
        Schema::create('employee_payment_weekly_summaries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code');
            $table->string('emp_id');
            $table->float('hours');
            $table->float('amount');
            $table->foreignId('task_weekly_plan_id')->nullable()->constrained();
            $table->foreignId('daily_assignment_id')->nullable()->constrained();
            $table->foreignId('weekly_plan_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_payment_weekly_summaries');
    }
};
