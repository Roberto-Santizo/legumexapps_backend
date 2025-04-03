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
        Schema::table('line_stock_keeping_units', function (Blueprint $table) {
            $table->integer('payment_method')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('line_stock_keeping_units', function (Blueprint $table) {
            $table->dropColumn('payment_method');
        });
    }
};
