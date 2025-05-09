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
        Schema::create('insumos_receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('supplier_id')->constrained()->on('supplier_packing_materials');
            $table->string('supervisor_name');
            $table->string('invoice');
            $table->datetime('received_date');
            $table->datetime('invoice_date') ;
            $table->string('user_signature');
            $table->string('supervisor_signature');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insumos_receipts');
    }
};
