<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Funcion extends Model
{
    protected $table = 'funciones';

    protected $fillable = ['municipalidad_id', 'unidad_organica_id', 'puesto_id', 'codigo', 'descripcion', 'tipo', 'fuente', 'estado', 'fecha_inicio', 'fecha_fin'];
    protected $casts = ['fecha_inicio' => 'date', 'fecha_fin' => 'date'];

    public function municipalidad(): BelongsTo { return $this->belongsTo(Municipalidad::class); }
    public function unidad(): BelongsTo { return $this->belongsTo(UnidadOrganica::class, 'unidad_organica_id'); }
    public function puesto(): BelongsTo { return $this->belongsTo(Puesto::class); }
    public function normas(): BelongsToMany { return $this->belongsToMany(Norma::class, 'funcion_norma'); }
}
