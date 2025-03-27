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
        Schema::table('stock_keeping_units', function (Blueprint $table) {
            $table->dropColumn('unit_mesurment');
            $table->dropColumn('name');
            $table->foreignId('product_id')->constrained()->on('stock_keeping_units_products');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_keeping_units', function (Blueprint $table) {
            $table->string('name');
            $table->dropForeign('stock_keeping_units_product_id_foreign');
            $table->dropColumn('product_id');
        });
    }
};
