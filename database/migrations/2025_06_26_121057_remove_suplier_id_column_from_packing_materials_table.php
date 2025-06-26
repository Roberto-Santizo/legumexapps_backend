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
        Schema::table('packing_materials', function (Blueprint $table) {
            $table->dropForeign('packing_materials_supplier_id_foreign');
            $table->dropColumn('supplier_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packing_materials', function (Blueprint $table) {
            $table->foreignId('supplier_id')->constrained()->on('supplier_packing_materials');
        });
    }
};
