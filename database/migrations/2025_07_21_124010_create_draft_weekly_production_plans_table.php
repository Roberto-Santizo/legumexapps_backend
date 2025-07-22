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
        Schema::create('draft_weekly_production_plans', function (Blueprint $table) {
            $table->id();
            $table->integer('year');
            $table->integer('week');
            $table->boolean('production_confirmation')->default(false);
            $table->boolean('bodega_confirmation')->default(false);
            $table->boolean('logistics_confirmation')->default(false);
            $table->timestamp('confirmation_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('draft_weekly_production_plans');
    }
};
