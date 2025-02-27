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
        Schema::create('transport_inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('planta_id')->constrained();
            $table->foreignId('product_id')->constrained();
            $table->foreignId('rm_reception_id')->constrained();
            $table->string('pilot_name');
            $table->string('truck_type');
            $table->string('plate');
            $table->string('date');
            $table->string('observations');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transport_inspections');
    }
};
