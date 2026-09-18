<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventSubject extends Model
{
    protected $table = 'event_subjects';

    protected $fillable = [
        'event_id',
        'teacher_id',
        'carrera_id',
        'subject_name',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function carrera(): BelongsTo
    {
        return $this->belongsTo(Carrera::class, 'carrera_id');
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'event_subject_id');
    }
}