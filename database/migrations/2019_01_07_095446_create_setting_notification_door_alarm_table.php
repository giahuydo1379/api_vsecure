<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingNotificationDoorAlarmTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('setting_notification_door_alarm', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('arm_opening_push_switch')->default(0);
            $table->boolean('arm_closed_push_switch')->default(0);
            $table->boolean('pass_change_push_switch')->nullable();
            $table->boolean('mode_change_push_switch')->nullable();
            $table->boolean('boot_push_switch')->nullable();
            $table->boolean('low_battery_push_switch')->nullable();
            $table->boolean('offline_push_switch')->default(0);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('setting_notification_door_alarm', function (Blueprint $table) {
            //
        });
    }
}
