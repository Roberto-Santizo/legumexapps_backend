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
        Schema::create('raw_material_items', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('product_name');
            $table->enum('type', ['imported', 'legumex'])->default('legumex');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('raw_material_items');
    }
};
