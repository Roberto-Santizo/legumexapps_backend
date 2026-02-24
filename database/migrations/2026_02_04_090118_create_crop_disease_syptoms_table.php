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
        Schema::create('crop_disease_syptoms', function (Blueprint $table) {
            $table->id();
            $table->string('symptom');
            $table->foreignId('crop_disease_id')->constrained();
            $table->foreignId('crop_part_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crop_disease_syptoms');
    }
};
