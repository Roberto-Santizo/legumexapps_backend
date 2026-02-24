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
        Schema::create('lote_checklist_conditions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lote_checklist_id')->constrained();
            $table->foreignId('crop_disease_syptom_id')->constrained();
            $table->boolean('exists');
            $table->enum('level', ['NULL','LOW', 'MEDIUM', 'HIGH']);
            $table->string('observations')->nullable(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lote_checklist_conditions');
    }
};
