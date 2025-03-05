<?php

use Carbon\Carbon;
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
        Schema::create('quality_control_docs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rm_reception_id')->constrained();
            $table->foreignId('producer_id')->constrained();
            $table->float('net_weight');
            $table->string('no_doc_cosechero')->nullable();
            $table->string('sample_units');
            $table->integer('total_baskets');
            $table->float('ph')->nullable();
            $table->float('brix')->nullable();
            $table->float('percentage');
            $table->float('valid_pounds');
            $table->foreignId('user_id')->constrained();
            $table->datetime('doc_date');
            $table->string('observations')->nullable();
            $table->string('inspector_signature');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quality_control_docs');
    }
};
