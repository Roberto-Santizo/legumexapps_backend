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
            $table->string('product_name');
            $table->float('presentation')->nullable();
            $table->float('boxes_pallet')->nullable();
            $table->float('config_box')->nullable();
            $table->float('config_bag')->nullable();
            $table->float('config_inner_bag')->nullable();
            $table->float('pallets_container')->nullable();
            $table->float('hours_container')->nullable();
            $table->string('client_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_keeping_units', function (Blueprint $table) {
            $table->string('name');
            $table->string('unit_mesurment');

            $table->dropColumn('product_name');
            $table->dropColumn('presentation');
            $table->dropColumn('boxes_pallet');
            $table->dropColumn('config_box');
            $table->dropColumn('config_bag');
            $table->dropColumn('config_inner_bag');
            $table->dropColumn('pallets_container');
            $table->dropColumn('hours_container');
            $table->dropColumn('client_name');
        });
    }
};
