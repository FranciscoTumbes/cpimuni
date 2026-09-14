<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Municipalidad extends Model
{
    use Auditable;

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

    public function organos(): HasMany
    {
        return $this->hasMany(Organo::class, 'municipalidad_id');
    }

    public function unidadesOrganicas(): HasMany
    {
        return $this->hasMany(UnidadOrganica::class, 'municipalidad_id');
    }

    public function puestos(): HasMany
    {
        return $this->hasMany(Puesto::class, 'municipalidad_id');
    }

    public function funciones(): HasMany
    {
        return $this->hasMany(Funcion::class, 'municipalidad_id');
    }

    public function normas(): HasMany
    {
        return $this->hasMany(Norma::class, 'municipalidad_id');
    }

    public function instrumentos(): HasMany
    {
        return $this->hasMany(Instrumento::class, 'municipalidad_id');
    }
}