<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WikiCategory extends Model
{
    // Tabla de categorías.
    protected $table = 'wiki_categories';

    // Campos permitidos para crear/modificar categorías.
    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    // Una categoría puede tener muchos artículos.
    //
    // wiki_categories.id
    //       ↓
    // wiki_articles.category_id
    public function articles(): HasMany
    {
        return $this->hasMany(WikiArticle::class, 'category_id');
    }
}