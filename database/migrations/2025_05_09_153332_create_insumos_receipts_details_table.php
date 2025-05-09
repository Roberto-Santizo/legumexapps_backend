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
        Schema::create('insumos_receipts_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('insumos_receipt_id')->constrained();
            $table->foreignId('insumo_id')->constrained();
            $table->float('units');
            $table->float('total');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insumos_receipts_details');
    }
};
