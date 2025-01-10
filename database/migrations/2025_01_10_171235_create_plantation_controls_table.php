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
        Schema::create('plantation_controls', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('crop_id')->constrained();
            $table->foreignId('recipe_id')->constrained();
            $table->float('density');
            $table->string('size');
            $table->datetime('start_date');
            $table->datetime('end_data');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plantation_controls');
    }
};
