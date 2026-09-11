<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Norma extends Model
{
    protected $table = 'normas';

    protected $fillable = ['municipalidad_id', 'tipo', 'numero', 'titulo', 'fecha_emision', 'fecha_vigencia', 'fecha_derogacion', 'estado', 'archivo', 'enlace', 'resumen'];
    protected $casts = ['fecha_emision' => 'date', 'fecha_vigencia' => 'date', 'fecha_derogacion' => 'date'];

    public function municipalidad(): BelongsTo { return $this->belongsTo(Municipalidad::class); }
    public function funciones(): BelongsToMany { return $this->belongsToMany(Funcion::class, 'funcion_norma'); }
}
