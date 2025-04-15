<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParticipantsTable extends Migration
{
    public function up()
    {
        Schema::create('participants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('cpf')->nullable();
            $table->string('rg')->nullable();
            $table->string('contact')->nullable();
            $table->string('email')->nullable();
            $table->timestamps();
        });

        // Pivot table para relacionar appointments ↔ participants
        Schema::create('appointment_participant', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->foreignId('participant_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('appointment_participant');
        Schema::dropIfExists('participants');
    }
}
