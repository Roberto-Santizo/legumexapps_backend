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
        Schema::create('bitacora_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('line_id')->constrained();
            $table->string('old_code');
            $table->string('new_code');
            $table->string('old_total_persons');
            $table->string('new_total_persons');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bitacora_lines');
    }
};
