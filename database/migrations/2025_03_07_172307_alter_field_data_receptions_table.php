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
            $table->dropColumn('transport');
            $table->dropColumn('pilot_name');
            $table->dropColumn('cdp');
            $table->dropColumn('transport_plate');
            $table->foreignId('driver_id')->constrained();
            $table->foreignId('cdp_id')->constrained()->on('productor_plantation_controls');
            $table->foreignId('plate_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('field_data_receptions', function (Blueprint $table) {
            $table->string('transport');
            $table->string('pilot_name');
            $table->string('cdp');
            $table->string('transport_plate');
            $table->dropForeign('field_data_receptions_driver_id_foreign');
            $table->dropForeign('field_data_receptions_plate_id_foreign');
            $table->dropForeign('field_data_receptions_cdp_id_foreign');

            $table->dropColumn('driver_id');
            $table->dropColumn('cdp_id');
            $table->dropColumn('plate_id');
        });
    }
};
