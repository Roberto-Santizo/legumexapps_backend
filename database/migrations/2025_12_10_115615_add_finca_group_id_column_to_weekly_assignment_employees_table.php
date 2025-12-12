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
        Schema::table('weekly_assignment_employees', function (Blueprint $table) {
            $table->foreignId('finca_group_id')->nullable()->constrained();
            $table->dropForeign('weekly_assignment_employees_lote_id_foreign');
            $table->dropColumn('lote_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('weekly_assignment_employees', function (Blueprint $table) {
            $table->dropForeign('weekly_assignment_employees_finca_group_id_foreign');
            $table->dropColumn('finca_group_id');
            $table->foreignId('lote_id')->nullable()->constrained();
        });
    }
};
