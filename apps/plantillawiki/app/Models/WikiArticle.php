<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class WikiArticle extends Model
{
    // Tabla de artículos.
    protected $table = 'wiki_articles';

    // Campos que pueden ser asignados.
    protected $fillable = [
        'category_id',
        'author_id',
        'title',
        'slug',
        'content',
        'is_published',
    ];

    // Convierte is_published a true/false.
    protected $casts = [
        'is_published' => 'boolean',
    ];

    // Cada artículo pertenece a una categoría.
    //
    // wiki_articles.category_id
    //          ↓
    // wiki_categories.id
    public function category(): BelongsTo
    {
        return $this->belongsTo(WikiCategory::class, 'category_id');
    }

    // Cada artículo tiene un autor.
    //
    // wiki_articles.author_id
    //          ↓
    // users.id
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    // Un artículo puede tener muchas etiquetas.
    //
    // wiki_articles
    //      ↕
    // article_tag
    //      ↕
    // wiki_tags
    //
    // Es una relación muchos a muchos.
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(
            WikiTag::class,
            'article_tag',
            'article_id',
            'tag_id'
        );
    }
}