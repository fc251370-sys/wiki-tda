<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectFile extends Model
{
    // Tabla donde se almacenan los archivos de los proyectos.
    protected $table = 'project_files';

    // Campos que podemos guardar.
    protected $fillable = [
        'project_id',
        'file_type',
        'original_name',
        'file_path',
        'file_size_kb',
        'extension',
    ];

    // Convierte file_size_kb automáticamente a número entero.
    protected $casts = [
        'file_size_kb' => 'integer',
    ];

    // Cada archivo pertenece a un proyecto.
    //
    // project_files.project_id
    //          ↓
    // projects.id
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }
}