<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Usuario extends Authenticatable
{
    use Notifiable;

    protected $table = 'usuarios';

    protected $fillable = [
        'municipalidad_id',
        'rol_id',
        'nombre',
        'apellido',
        'email',
        'password',
        'estado',
        'ultimo_acceso',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'ultimo_acceso' => 'datetime',
        'password' => 'hashed',
    ];

    protected $rememberTokenName = null;

    public function municipalidad(): BelongsTo
    {
        return $this->belongsTo(
            Municipalidad::class,
            'municipalidad_id'
        );
    }

    public function rol(): BelongsTo
    {
        return $this->belongsTo(
            Rol::class,
            'rol_id'
        );
    }

    public function tienePermiso(string $permiso): bool
    {
        return $this->rol
            ? $this->rol->permisos()->where('nombre', $permiso)->exists()
            : false;
    }
}