<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    // Tabla de la base de datos que representa este modelo.
    protected $table = 'events';

    // Campos que podemos guardar mediante asignación masiva.
    protected $fillable = [
        'coordinador_id',
        'cicle_name',
        'start_date',
        'is_active',
    ];

    // Convierte automáticamente estos valores.
    //
    // start_date -> objeto de fecha
    // is_active  -> true/false
    protected $casts = [
        'start_date' => 'date',
        'is_active' => 'boolean',
    ];

    // Cada evento tiene un coordinador.
    //
    // events.coordinador_id
    //        ↓
    // users.id
    public function coordinador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coordinador_id');
    }

    // Un evento puede tener varias materias.
    //
    // events.id
    //        ↓
    // event_subjects.event_id
    public function eventSubjects(): HasMany
    {
        return $this->hasMany(EventSubject::class, 'event_id');
    }
}