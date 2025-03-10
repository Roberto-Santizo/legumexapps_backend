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
        Schema::table('field_data_receptions', function (Blueprint $table) {
            $table->string('ref_doc');
            $table->foreignId('carrier_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('field_data_receptions', function (Blueprint $table) {
            $table->dropColumn('ref_doc');
            $table->dropForeign('field_data_receptions_carrier_id_foreign');
            $table->dropColumn('carrier_id');
        });
    }
};
