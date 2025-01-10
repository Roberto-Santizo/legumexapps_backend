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
        Schema::create('lote_plantation_controls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lote_id')->constrained();
            $table->foreignId('plantation_controls_id')->constrained();
            $table->boolean('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lote_plantation_controls');
    }
};
