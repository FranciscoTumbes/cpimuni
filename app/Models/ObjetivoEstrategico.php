<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ObjetivoEstrategico extends Model
{
    protected $table = 'objetivos_estrategicos';
    protected $fillable = ['plan_estrategico_id', 'unidad_responsable_id', 'codigo', 'enunciado', 'descripcion', 'orden', 'estado'];
    protected $casts = ['orden' => 'integer'];

    public function plan(): BelongsTo { return $this->belongsTo(PlanEstrategico::class, 'plan_estrategico_id'); }
    public function unidadResponsable(): BelongsTo { return $this->belongsTo(UnidadOrganica::class, 'unidad_responsable_id'); }
    public function acciones(): HasMany { return $this->hasMany(AccionEstrategica::class); }
    public function indicadores(): HasMany { return $this->hasMany(Indicador::class); }
}