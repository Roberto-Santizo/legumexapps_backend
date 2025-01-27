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
        Schema::table('daily_assignments', function (Blueprint $table) {
            $table->float('plants')->nullable();
            $table->float('lbs_finca')->nullable();
            $table->float('lbs_planta')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_assignments', function (Blueprint $table) {
            //
        });
    }
};
