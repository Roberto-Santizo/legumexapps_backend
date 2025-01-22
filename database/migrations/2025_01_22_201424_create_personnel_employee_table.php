<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersonnelEmployeeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('personnel_employee', function (Blueprint $table) {
            $table->id(); 
            $table->dateTime('create_time')->nullable();
            $table->string('create_user', 150)->nullable();
            $table->dateTime('change_time')->nullable();
            $table->string('change_user', 150)->nullable();
            $table->smallInteger('status')->nullable();
            $table->bigInteger('emp_code')->nullable();
            $table->string('first_name', 50)->nullable();
            $table->string('last_name', 25)->nullable();
            $table->string('nickname', 25)->nullable();
            $table->string('passport', 30)->nullable();
            $table->string('driver_license_automobile', 30)->nullable();
            $table->string('driver_license_motorcycle', 30)->nullable();
            $table->string('photo', 200)->nullable();
            $table->string('self_password', 128)->nullable();
            $table->string('device_password', 20)->nullable();
            $table->integer('dev_privilege')->nullable();
            $table->string('card_no', 20)->nullable();
            $table->string('acc_group', 5)->nullable();
            $table->string('acc_timezone', 20)->nullable();
            $table->string('gender', 1)->nullable();
            $table->dateTime('birthday')->nullable();
            $table->string('address', 200)->nullable();
            $table->string('postcode', 10)->nullable();
            $table->string('office_tel', 20)->nullable();
            $table->string('contact_tel', 20)->nullable();
            $table->string('mobile', 30)->nullable();
            $table->string('national_num', 50)->nullable();
            $table->string('payroll_num', 50)->nullable();
            $table->string('internal_emp_num', 50)->nullable();
            $table->string('national', 50)->nullable();
            $table->string('religion', 20)->nullable();
            $table->string('title', 20)->nullable();
            $table->string('enroll_sn', 20)->nullable();
            $table->string('ssn', 20)->nullable();
            $table->dateTime('update_time')->nullable();
            $table->dateTime('hire_date')->nullable();
            $table->integer('verify_mode')->nullable();
            $table->string('city', 20)->nullable();
            $table->boolean('is_admin')->default(false);
            $table->integer('emp_type')->nullable();
            $table->boolean('enable_att')->default(false);
            $table->boolean('enable_payroll')->default(false);
            $table->boolean('enable_overtime')->default(false);
            $table->boolean('enable_holiday')->default(false);
            $table->boolean('deleted')->default(false);
            $table->integer('reserved')->nullable();
            $table->integer('del_tag')->nullable();
            $table->smallInteger('app_status')->nullable();
            $table->smallInteger('app_role')->nullable();
            $table->string('email', 50)->nullable();
            $table->dateTime('last_login')->nullable();
            $table->boolean('is_active')->default(true);
            $table->smallInteger('vacation_rule')->nullable();
            $table->integer('company_id')->nullable();
            $table->integer('department_id')->nullable();
            $table->integer('position_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('personnel_employee');
    }
}
