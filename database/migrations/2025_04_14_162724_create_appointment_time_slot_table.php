<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppointmentTimeSlotTable extends Migration
{
    public function up()
    {
        Schema::create('appointment_time_slot', function (Blueprint $table) {
            $table->id();
            $table->foreign('appointment_id')->references('id')->on('appointments')->onDelete('cascade');
            $table->foreign('time_slots_id')->references('id')->on('time_slots')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('appointment_time_slot');
    }
}
