<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Municipalidad extends Model
{
    protected $table = 'municipalidades';

    protected $fillable = [
        'codigo_entidad',
        'ruc',
        'nombre',
        'tipo',
        'departamento',
        'provincia',
        'distrito',
        'logo',
        'estado',
    ];

    public function usuarios(): HasMany
    {
        return $this->hasMany(Usuario::class, 'municipalidad_id');
    }
}