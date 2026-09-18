<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectMember extends Model
{
    // Tabla intermedia entre proyectos y estudiantes.
    protected $table = 'project_members';

    // Campos que pueden ser asignados.
    protected $fillable = [
        'project_id',
        'student_id',
        'is_leader',
    ];

    // Convierte 0/1 en false/true.
    protected $casts = [
        'is_leader' => 'boolean',
    ];

    // Indica a qué proyecto pertenece este registro.
    //
    // project_members.project_id
    //          ↓
    // projects.id
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    // Indica qué usuario/estudiante pertenece al proyecto.
    //
    // project_members.student_id
    //          ↓
    // users.id
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}