<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Puesto extends Model
{
    protected $table = 'puestos';

    protected $fillable = ['municipalidad_id', 'unidad_organica_id', 'codigo', 'denominacion', 'nivel', 'finalidad', 'requisitos', 'competencias', 'estado'];

    public function municipalidad(): BelongsTo { return $this->belongsTo(Municipalidad::class); }
    public function unidad(): BelongsTo { return $this->belongsTo(UnidadOrganica::class, 'unidad_organica_id'); }
}
