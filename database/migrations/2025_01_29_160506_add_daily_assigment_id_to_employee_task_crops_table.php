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
        Schema::table('employee_task_crops', function (Blueprint $table) {
            $table->foreignId('daily_assignment_id')->nullable()->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_task_crops', function (Blueprint $table) {
            $table->dropForeign('employee_task_crops_daily_assignment_id_foreign');
            $table->dropColumn('daily_assignment_id');
        });
    }
};
