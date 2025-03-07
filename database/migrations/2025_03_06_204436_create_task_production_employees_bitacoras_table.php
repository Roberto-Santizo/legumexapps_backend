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
        Schema::create('task_production_employees_bitacoras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained()->on('task_production_employees');
            $table->string('original_name');
            $table->string('original_code');
            $table->string('original_position');
            $table->string('new_name');
            $table->string('new_code');
            $table->string('new_position');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_production_employees_bitacoras');
    }
};
