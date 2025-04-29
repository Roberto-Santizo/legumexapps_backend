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
            $table->boolean('prepared_insumos')->default(null)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_weekly_plans', function (Blueprint $table) {
            $table->dropColumn('prepared_insumos');
        });
    }
};
