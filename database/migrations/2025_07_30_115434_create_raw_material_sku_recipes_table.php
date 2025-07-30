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
        Schema::create('raw_material_sku_recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_keeping_unit_id')->constrained()->on('stock_keeping_units');
            $table->foreignId('raw_material_item_id')->constrained()->on('raw_material_items');
            $table->float('percentage', 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('raw_material_sku_recipes');
    }
};
