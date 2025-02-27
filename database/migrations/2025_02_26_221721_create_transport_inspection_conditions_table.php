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
        Schema::create('transport_inspection_conditions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transport_condition_id')->constrained();
            $table->foreignId('transport_inspection_id')->constrained();
            $table->boolean('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transport_inspection_conditions');
    }
};
