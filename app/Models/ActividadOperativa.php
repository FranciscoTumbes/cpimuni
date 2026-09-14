<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ActividadOperativa extends Model
{
    protected $table = 'actividades_operativas';
    protected $fillable = ['plan_operativo_id', 'accion_estrategica_id', 'unidad_responsable_id', 'puesto_responsable_id', 'codigo', 'denominacion', 'descripcion', 'unidad_medida', 'meta_programada', 'meta_ejecutada', 'fecha_inicio', 'fecha_fin', 'estado'];
    protected $casts = ['meta_programada' => 'decimal:4', 'meta_ejecutada' => 'decimal:4', 'fecha_inicio' => 'date', 'fecha_fin' => 'date'];

    public function plan(): BelongsTo { return $this->belongsTo(PlanOperativo::class, 'plan_operativo_id'); }
    public function accion(): BelongsTo { return $this->belongsTo(AccionEstrategica::class, 'accion_estrategica_id'); }
    public function unidadResponsable(): BelongsTo { return $this->belongsTo(UnidadOrganica::class, 'unidad_responsable_id'); }
    public function puestoResponsable(): BelongsTo { return $this->belongsTo(Puesto::class, 'puesto_responsable_id'); }
    public function indicadores(): HasMany { return $this->hasMany(Indicador::class); }
}