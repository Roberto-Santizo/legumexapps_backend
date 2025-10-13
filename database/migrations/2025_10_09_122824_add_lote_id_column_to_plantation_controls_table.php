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
        Schema::table('plantation_controls', function (Blueprint $table) {
            $table->foreignId('lote_id')->default(1)->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plantation_controls', function (Blueprint $table) {
            $table->dropForeign('plantation_controls_lote_id_foreign');
            $table->dropColumn('lote_id');
        });
    }
};
