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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            //se agrego desde aca
            $table->foreignId('coordinador_id')->constrained('users')->restrictOnDelete();
            $table->string('cicle_name', 50);
            $table->date('start_date');
            $table->boolean('is_active')->default(true);
            // hasta aca
            $table->timestamps();
        });
    }

    //coordinator_id con restrictOnDelete no deja borrar a un usuario si es coordinador de algún evento existente  un evento sin coordinador no tendria  responsable




    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
