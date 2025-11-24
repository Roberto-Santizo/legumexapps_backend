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
            $table->foreignId('recipe_id')->constrained();
            $table->foreignId('crop_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plantation_controls', function (Blueprint $table) {
            $table->dropForeign('plantation_controls_recipe_id_foreign');
            $table->dropForeign('plantation_controls_crop_id_foreign');
            $table->dropColumn('recipe_id');
            $table->dropColumn('crop_id');
        });
    }
};
