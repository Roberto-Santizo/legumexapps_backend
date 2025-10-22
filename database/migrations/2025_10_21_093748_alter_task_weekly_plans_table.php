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
        Schema::table('task_weekly_plans', function (Blueprint $table) {
            $table->dropForeign('task_weekly_plans_lote_plantation_control_id_foreign');
            $table->dropColumn('lote_plantation_control_id');

            $table->foreignId('plantation_control_id')->default(1)->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_weekly_plans', function (Blueprint $table) {
            $table->foreignId('lote_plantation_control_id')->constrained();
            $table->dropForeign('task_weekly_plans_plantation_control_id_foreign');
            $table->dropColumn('plantation_control_id');
        });
    }
};
