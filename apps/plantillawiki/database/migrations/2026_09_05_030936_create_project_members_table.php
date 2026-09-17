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
        Schema::create('project_members', function (Blueprint $table) {
            $table->id();
            //se agrego de aca 
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->boolean('is_leader')->default(false);
            $table->primary(['project_id', 'student_id']); //hasta aca
            $table->timestamps();
        });
    }

    //project_id con cascadeOnDelete si se borra el proyecto, no tiene sentido conservar sus filas 

    //tudent_id con cascadeOnDelete si se borra el estudiante, se elimina  todo lo de el 

    //primary(['project_id', 'student_id']): igual que en article_tag son tablas pivote
    //esto también evita que se pueda agregar al mismo estudiante dos veces al mismo proyecto




    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_members');
    }
};
