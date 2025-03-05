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
        Schema::table('rm_receptions', function (Blueprint $table) {
            $table->foreignId('quality_status_id')->constrained();
            $table->dropColumn('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rm_receptions', function (Blueprint $table) {
            $table->dropForeign('rm_receptions_quality_status_id_foreign')->constrained();
            $table->boolean('status');
            
        });
    }
};
