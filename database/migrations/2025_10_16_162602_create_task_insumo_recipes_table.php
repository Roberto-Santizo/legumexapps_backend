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
        Schema::create('task_insumo_recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_guideline_id')->constrained();
            $table->foreignId('insumo_id')->constrained();
            $table->float('quantity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_insumo_recipes');
    }
};
