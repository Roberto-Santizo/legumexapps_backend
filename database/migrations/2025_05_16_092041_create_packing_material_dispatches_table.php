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
        Schema::create('packing_material_dispatches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_production_plan_id')->nullable()->constrained();
            $table->foreignId('user_id')->constrained();
            $table->string('reference');
            $table->string('responsable_bags')->nullable();
            $table->string('responsable_boxes')->nullable();
            $table->string('signature_responsable_bags')->nullable();
            $table->string('signature_responsable_boxes')->nullable();
            $table->string('user_signature');
            $table->string('observations')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packing_material_dispatches');
    }
};
