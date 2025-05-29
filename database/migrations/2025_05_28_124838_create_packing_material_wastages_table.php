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
        Schema::create('packing_material_wastages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_p_id')->constrained()->on('task_production_plans');
            $table->foreignId('packing_material_id')->constrained();
            $table->float('quantity');
            $table->string('lote');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packing_material_wastages');
    }
};
