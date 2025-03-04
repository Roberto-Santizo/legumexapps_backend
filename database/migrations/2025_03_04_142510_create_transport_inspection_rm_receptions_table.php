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
        Schema::create('transport_inspection_rm_receptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transport_id')->constrained()->on('transport_inspections');
            $table->foreignId('reception_id')->constrained()->on('rm_receptions');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transport_inspection_rm_receptions');
    }
};
