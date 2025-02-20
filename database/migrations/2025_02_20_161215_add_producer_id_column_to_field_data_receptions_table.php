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
            $table->dropColumn('coordinator_name');
            $table->foreignId('producer_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('field_data_receptions', function (Blueprint $table) {
            $table->string('coordinator_name');
            $table->dropForeign('field_data_receptions_producer_id_foreign');
            $table->dropColumn('producer_id');
        });
    }
};
