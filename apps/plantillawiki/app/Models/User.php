<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, HasRoles, Notifiable;

    // Spatie utilizará el guard api.
    protected $guard_name = 'api';

    // Campos que pueden ser asignados.
    protected $fillable = [
        'carrera_id',
        'name',
        'email',
        'password',
        'role',
        'carnet_or_code',
        'email_verified_at',
    ];

    // Conversión automática de datos.
    protected $casts = [
        'carrera_id' => 'integer',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Oculta información sensible cuando se convierte
    // el usuario a JSON.
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // El usuario pertenece a una carrera.
    //
    // users.carrera_id
    //       ↓
    // carreras.id
    public function carrera(): BelongsTo
    {
        return $this->belongsTo(Carrera::class, 'carrera_id');
    }

    // Un usuario puede crear muchos artículos.
    //
    // wiki_articles.author_id
    //       ↓
    // users.id
    public function wikiArticles(): HasMany
    {
        return $this->hasMany(WikiArticle::class, 'author_id');
    }

    // Un usuario puede coordinar varios eventos.
    //
    // events.coordinador_id
    //       ↓
    // users.id
    public function coordinatedEvents(): HasMany
    {
        return $this->hasMany(Event::class, 'coordinador_id');
    }

    // Un usuario puede ser docente de varias materias.
    //
    // event_subjects.teacher_id
    //       ↓
    // users.id
    public function taughtSubjects(): HasMany
    {
        return $this->hasMany(EventSubject::class, 'teacher_id');
    }

    // Un usuario puede pertenecer a varios proyectos.
    //
    // project_members.student_id
    //       ↓
    // users.id
    public function projectMemberships(): HasMany
    {
        return $this->hasMany(ProjectMember::class, 'student_id');
    }

    // Identificador utilizado por JWT.
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    // Información adicional que se guarda dentro del JWT.
    public function getJWTCustomClaims()
    {
        return [
            'roles' => $this->roles->pluck('name'),
            'permissions' => $this->getAllPermissions()->pluck('name'),
        ];
    }
}