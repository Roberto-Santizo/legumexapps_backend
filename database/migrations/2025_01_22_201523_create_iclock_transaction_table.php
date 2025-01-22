<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIclockTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('iclock_transaction', function (Blueprint $table) {
            $table->id(); 
            $table->string('emp_code', 20);
            $table->dateTime('punch_time');
            $table->string('punch_state', 5)->nullable();
            $table->integer('verify_type')->nullable();
            $table->string('work_code', 20)->nullable();
            $table->string('terminal_sn', 50)->nullable();
            $table->string('terminal_alias', 50)->nullable();
            $table->string('area_alias', 100)->nullable();
            $table->float('longitude')->nullable();
            $table->float('latitude')->nullable();
            $table->text('gps_location')->nullable();
            $table->string('mobile', 50)->nullable();
            $table->smallInteger('source')->nullable();
            $table->smallInteger('purpose')->nullable();
            $table->string('crc', 100)->nullable();
            $table->smallInteger('is_attendance')->nullable();
            $table->string('reserved', 100)->nullable();
            $table->dateTime('upload_time')->nullable();
            $table->smallInteger('sync_status')->nullable();
            $table->dateTime('sync_time')->nullable();
            $table->smallInteger('is_mask')->nullable();
            $table->decimal('temperature', 4, 1)->nullable();
            $table->unsignedBigInteger('emp_id')->nullable(); // Sin clave foránea
            $table->unsignedBigInteger('terminal_id')->nullable(); // Sin clave foránea

            // Clave primaria
            $table->primary('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('iclock_transaction');
    }
}
