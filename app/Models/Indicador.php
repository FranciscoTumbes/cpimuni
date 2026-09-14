<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Indicador extends Model
{
    protected $table = 'indicadores';
    protected $fillable = ['municipalidad_id', 'objetivo_estrategico_id', 'accion_estrategica_id', 'actividad_operativa_id', 'codigo', 'nombre', 'formula', 'unidad_medida', 'fuente', 'frecuencia', 'linea_base', 'meta', 'estado'];
    protected $casts = ['linea_base' => 'decimal:4', 'meta' => 'decimal:4'];

    public function municipalidad(): BelongsTo { return $this->belongsTo(Municipalidad::class); }
    public function objetivo(): BelongsTo { return $this->belongsTo(ObjetivoEstrategico::class, 'objetivo_estrategico_id'); }
    public function accion(): BelongsTo { return $this->belongsTo(AccionEstrategica::class, 'accion_estrategica_id'); }
    public function actividad(): BelongsTo { return $this->belongsTo(ActividadOperativa::class, 'actividad_operativa_id'); }
}