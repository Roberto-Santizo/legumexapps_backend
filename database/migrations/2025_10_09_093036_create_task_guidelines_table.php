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
        Schema::create('task_guidelines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->on('tareas');
            $table->foreignId('recipe_id')->constrained();
            $table->foreignId('crop_id')->constrained();
            $table->float('budget');
            $table->float('hours');
            $table->integer('week');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_guidelines');
    }
};
