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
        Schema::create('field_data_receptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rm_reception_id')->constrained();
            $table->string('coordinator_name');
            $table->foreignId('product_id')->constrained();
            $table->string('transport');
            $table->string('pilot_name');
            $table->string('inspector_name');
            $table->string('cdp');
            $table->string('transport_plate');
            $table->float('weight');
            $table->foreignId('basket_id')->constrained();
            $table->integer('total_baskets');
            $table->float('weight_baskets');
            $table->float('quality_percentage');
            $table->string('inspector_signature')->nullable();
            $table->string('prod_signature')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('field_data_receptions');
    }
};
