<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// Permite indicar que una relación devuelve varios registros.
use Illuminate\Database\Eloquent\Relations\HasMany;

// Modelo que representa la tabla carreras.
class Carrera extends Model
{
    // Indica explícitamente qué tabla utiliza este modelo.
    protected $table = 'carreras';

    // Estos campos pueden ser asignados mediante Carrera::create()
    // o $carrera->fill().
    protected $fillable = [
        'name',
        'code',
    ];

    // Una carrera puede tener muchos usuarios.
    //
    // Ejemplo:
    // Carrera 1
    // ├── Usuario Sandra
    // ├── Usuario Juan
    // └── Usuario Pedro
    //
    // Se conecta mediante:
    // carreras.id -> users.carrera_id
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'carrera_id');
    }

    // Una carrera puede aparecer en muchas materias/eventos.
    // Se conecta mediante:
    // carreras.id -> event_subjects.carrera_id
    public function eventSubjects(): HasMany
    {
        return $this->hasMany(EventSubject::class, 'carrera_id');
    }
}