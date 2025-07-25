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
        Schema::create('task_operation_date_bitacoras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_production_plan_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->datetime('original_date');
            $table->datetime('new_date');
            $table->string('reason');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_operation_date_bitacoras');
    }
};
