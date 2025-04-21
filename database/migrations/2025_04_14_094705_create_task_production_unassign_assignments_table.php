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
        Schema::create('task_production_unassign_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_p_unassign_id')->constrained()->on('task_production_unassigns');
            $table->foreignId('assignment_id')->constrained()->on('task_production_employees'); 
            $table->float('hours',2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_production_unassign_assignments');
    }
};
