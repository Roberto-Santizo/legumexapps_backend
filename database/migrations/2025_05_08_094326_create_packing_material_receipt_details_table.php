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
        Schema::create('packing_material_receipt_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('p_material_id')->constrained()->on('packing_materials');
            $table->foreignId('pm_receipt_id')->constrained()->on('packing_material_receipts');
            $table->string('lote');
            $table->float('quantity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packing_material_receipt_details');
    }
};
