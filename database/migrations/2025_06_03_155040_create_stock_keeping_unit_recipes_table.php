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
        Schema::create('stock_keeping_unit_recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sku_id')->constrained()->on('stock_keeping_units');
            $table->foreignId('item_id')->constrained()->on('packing_materials');
            $table->float('lbs_per_item');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_keeping_unit_recipes');
    }
};
