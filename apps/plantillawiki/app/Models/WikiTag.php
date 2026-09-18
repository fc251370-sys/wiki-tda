<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class WikiTag extends Model
{
    protected $table = 'wiki_tags';

    protected $fillable = [
        'name',
        'slug',
    ];

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(
            WikiArticle::class,
            'article_tag',
            'tag_id',
            'article_id'
        );
    }
}