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
        Schema::create('wiki_articles', function (Blueprint $table) {
            $table->id();
            //se agrego de aca
            $table->foreignId('category_id')->constrained('wiki_categories')->restrictOnDelete();
            $table->foreignId('author_id')->constrained('users')->cascadeOnDelete();
            $table->string('title', 255);
            $table->string('slug', 280)->unique();
            $table->text('content');
            $table->boolean('is_published')->default(false);  //hast aca
            $table->timestamps();
        });
    }

    //restrictOnDelete no deja borrar una categoría si tiene artículos asociados en ves de cascadeondelete por q borrar una categoria no significa borrar articulos completos 

    //author_id con cascadeOnDelete si uso cascade, porque si se borra el usuario autor, sus artículos se van con él.



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wiki_articles');
    }
};
