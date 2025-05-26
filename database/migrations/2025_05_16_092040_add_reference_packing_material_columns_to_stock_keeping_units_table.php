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
            $table->foreignId('box_id')->nullable()->constrained()->on('packing_materials');
            $table->foreignId('bag_id')->nullable()->constrained()->on('packing_materials');;
            $table->foreignId('bag_inner_id')->nullable()->constrained()->on('packing_materials');;
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_keeping_units', function (Blueprint $table) {
            $table->dropForeign('stock_keeping_units_bag_id_foreign');
            $table->dropForeign('stock_keeping_units_box_id_foreign');
            $table->dropForeign('stock_keeping_units_bag_inner_id_foreign');

            $table->dropColumn('box_id');
            $table->dropColumn('bag_id');
            $table->dropColumn('bag_inner_id');
        });
    }
};
