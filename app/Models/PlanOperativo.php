<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlanOperativo extends Model
{
    protected $table = 'planes_operativos';
    protected $fillable = ['municipalidad_id', 'instrumento_id', 'codigo', 'nombre', 'anio', 'estado'];
    protected $casts = ['anio' => 'integer'];

    public function municipalidad(): BelongsTo { return $this->belongsTo(Municipalidad::class); }
    public function instrumento(): BelongsTo { return $this->belongsTo(Instrumento::class); }
    public function actividades(): HasMany { return $this->hasMany(ActividadOperativa::class); }
}