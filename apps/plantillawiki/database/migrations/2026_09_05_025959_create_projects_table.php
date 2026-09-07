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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_subject_id')->constrained('event_subjects')->cascadeOnDelete();
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->string('repository_url', 255)->nullable();
            $table->string('status', 20)->default('pending');
            $table->timestamps();
        });
    }

    //event_subject_id con cascadeOnDelete si se borra la materia de un evento, tiene sentido que los proyectos ligados a ella se borren tambien

    //description y repository_url como nullable un proyecto puede crearse antes de tener repositorio o descripción complet

    //estatus pendiente solo por que si es como los roles de estudiante por defecto






    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
