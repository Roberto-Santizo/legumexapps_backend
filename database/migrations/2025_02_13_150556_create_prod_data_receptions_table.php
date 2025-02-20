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
        Schema::create('prod_data_receptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rm_reception_id')->constrained();
            $table->integer('total_baskets');
            $table->float('weight_baskets');
            $table->float('gross_weight');
            $table->float('net_weight');
            $table->string('receptor_signature');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prod_data_receptions');
    }
};