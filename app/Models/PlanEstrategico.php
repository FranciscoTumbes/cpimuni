<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlanEstrategico extends Model
{
    protected $table = 'planes_estrategicos';
    protected $fillable = ['municipalidad_id', 'instrumento_id', 'codigo', 'nombre', 'anio_inicio', 'anio_fin', 'estado'];
    protected $casts = ['anio_inicio' => 'integer', 'anio_fin' => 'integer'];

    public function municipalidad(): BelongsTo { return $this->belongsTo(Municipalidad::class); }
    public function instrumento(): BelongsTo { return $this->belongsTo(Instrumento::class); }
    public function objetivos(): HasMany { return $this->hasMany(ObjetivoEstrategico::class); }
}