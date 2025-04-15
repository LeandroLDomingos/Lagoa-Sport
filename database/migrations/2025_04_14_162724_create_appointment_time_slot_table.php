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
            $table->foreignId('appointment_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->foreignId('time_slot_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('appointment_time_slot');
    }
}
