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
            $table->renameColumn('calidad_signature','driver_signature');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('field_data_receptions', function (Blueprint $table) {
            $table->renameColumn('driver_signature','calidad_signature');
        });
    }
};
