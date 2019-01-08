<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDoorAlarmTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('door_alarm', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('password');
            $table->string('location');
            $table->macAddress('mac')->nullable();
            $table->float('version')->nullable();
            $table->integer('volume')->default(3)->comment('level 1-3');
            $table->integer('arm_delay')->default(5)->comment('5-255');
            $table->integer('alarm_delay')->default(5)->comment('5-255');
            $table->integer('alarm_duration')->default(60)->comment('5-300');
            $table->enum('self_test_mode', [0, 1, 2])->default(0)->comment('0: normal, 1: power saving, 2: fast');
            $table->integer('timing_arm_disarm')->nullable();
            $table->boolean('is_arm')->default(0)->comment('0: dis arm,1: arm');
            $table->boolean('is_home')->default(0)->comment('0: home, 1: away');
            $table->boolean('is_alarm')->default(0)->comment('0: alarm,1: door bell');
            $table->boolean('door_status')->default(0)->comment('0: close, 1: open');
            $table->enum('battery_capacity_remaining', [0, 1, 2, 3])->default(3)->comment('0: <= 25%,...');
            $table->boolean('is_deleted')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('door_alarm', function (Blueprint $table) {
            //
        });
    }
}
