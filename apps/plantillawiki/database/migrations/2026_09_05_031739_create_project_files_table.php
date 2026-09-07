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
        Schema::create('project_files', function (Blueprint $table) {
            $table->id();
            //se hiso de aca 
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string('file_type', 20);
            $table->string('original_name', 255);
            $table->string('file_path', 255);
            $table->integer('file_size_kb');
            $table->string('extension', 10);
            $table->timestamps(); //hast aca
        });
    }
    //project_id con cascadeOnDelete si se borra el proyecto se borra todo lo de el obio









    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_files');
    }
};
