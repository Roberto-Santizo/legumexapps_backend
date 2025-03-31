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
        Schema::table('task_production_plans', function (Blueprint $table) {
            $table->float('total_lbs_bascula')->nullable();
            $table->boolean('is_minimum_require')->nullable();
            $table->boolean('is_justified')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_production_plans', function (Blueprint $table) {
            $table->dropColumn('total_lbs_bascula');
            $table->dropColumn('is_minimum_require');
            $table->dropColumn('is_justified');
        });
    }
};
