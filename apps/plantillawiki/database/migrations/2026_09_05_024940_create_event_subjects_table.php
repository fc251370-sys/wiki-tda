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
        Schema::create('event_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('carrera_id')->constrained('carreras')->restrictOnDelete();
            $table->string('subject_name', 150);
            $table->timestamps();
        });
    }

    //event_id con cascadeOnDelete si se borra un evento completo que se borren las materias asociadas a ese ciclo

    //teacher_id con restrictOnDelete no deja borrar a un profesor si tiene materias asignadas en algún evento — evita dejar una materia sin docente responsable igual que con coordinador

    //carrera_id con restrictOnDelete lo mism no deja borrar una carrera si tiene materias de evento 



    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_subjects');
    }
};
