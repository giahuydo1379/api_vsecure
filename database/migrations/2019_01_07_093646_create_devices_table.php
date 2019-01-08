<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('door_alarm_id');
            $table->unsignedInteger('customer_id');
            $table->string('token');
            $table->timestamps();
            $table->foreign('door_alarm_id')->references('id')->on('door_alarm')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('devices', function (Blueprint $table) {
            $table->dropForeign('door_alarm_id');
            $table->dropForeign('customer_id');
        });
    }
}
