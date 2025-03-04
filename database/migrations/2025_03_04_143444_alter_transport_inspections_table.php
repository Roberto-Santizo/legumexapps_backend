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
        Schema::table('transport_inspections', function (Blueprint $table) {
            $table->dropForeign('transport_inspections_rm_reception_id_foreign');
            $table->dropColumn('rm_reception_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transport_inspections', function (Blueprint $table) {
            $table->foreignId('rm_reception_id');
        });
    }
};
