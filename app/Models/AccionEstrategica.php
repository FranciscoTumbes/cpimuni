<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AccionEstrategica extends Model
{
    protected $table = 'acciones_estrategicas';
    protected $fillable = ['objetivo_estrategico_id', 'unidad_responsable_id', 'codigo', 'enunciado', 'descripcion', 'orden', 'estado'];
    protected $casts = ['orden' => 'integer'];

    public function objetivo(): BelongsTo { return $this->belongsTo(ObjetivoEstrategico::class, 'objetivo_estrategico_id'); }
    public function unidadResponsable(): BelongsTo { return $this->belongsTo(UnidadOrganica::class, 'unidad_responsable_id'); }
    public function actividades(): HasMany { return $this->hasMany(ActividadOperativa::class); }
    public function indicadores(): HasMany { return $this->hasMany(Indicador::class); }
}