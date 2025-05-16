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
            $table->foreignId('task_production_plan_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->string('reference');
            $table->string('received_by_boxes');
            $table->string('received_by_signature_boxes');
            $table->string('received_by_bags');
            $table->string('received_by_signature_bags');
            $table->integer('quantity_boxes')->nullable();
            $table->integer('quantity_bags')->nullable();
            $table->integer('quantity_inner_bags')->nullable();
            $table->string('observations')->nullable();
            $table->string('user_signature');
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
