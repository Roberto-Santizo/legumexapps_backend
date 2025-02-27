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
            $table->foreignId('user_id')->constrained();
            $table->string('verify_by_signature');
            $table->string('quality_manager_signature');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transport_inspections', function (Blueprint $table) {
            $table->dropForeign('transport_inspections_user_id_foreign');
            $table->dropColumn('user_id');
            $table->dropColumn('quality_manager_signature');
            $table->dropColumn('verify_by_signature');
        });
    }
};
