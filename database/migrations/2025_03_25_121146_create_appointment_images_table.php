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
        Schema::create('appointment_images', function (Blueprint $table) {
            $table->id(); // Chave primária autoincremento
            $table->uuid('user_id'); // UUID para user_id
            $table->unsignedBigInteger('location_id'); // ID do local

            // Removido o índice único para permitir múltiplos agendamentos por usuário e local

            // Chaves estrangeiras
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('location_id')->references('id')->on('locations')->onDelete('cascade');

            $table->dateTime('date_time'); // Data e hora do agendamento
            $table->timestamps(); // Timestamps de criação e atualização
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment_images');
    }
};
