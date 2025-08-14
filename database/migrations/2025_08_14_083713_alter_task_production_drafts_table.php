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
        Schema::table('task_production_drafts', function (Blueprint $table) {
            $table->dropForeign('task_production_drafts_line_id_foreign');
            $table->dropColumn('line_id');
            $table->foreignId('line_id')->nullable()->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_production_drafts', function (Blueprint $table) {
            $table->dropForeign('task_production_drafts_line_id_foreign');
            $table->dropColumn('line_id');
            $table->foreignId('line_id')->constrained();
        });
    }
};
