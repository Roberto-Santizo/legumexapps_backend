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
        Schema::create('quality_control_defects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quality_control_doc_id')->constrained();
            $table->foreignId('defect_id')->constrained();
            $table->float('input');
            $table->float('result');
            $table->float('tolerance_percentage');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quality_control_defects');
    }
};
