<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Instrumento extends Model
{
    use Auditable;

    protected $table = 'instrumentos';

    protected $fillable = [
        'municipalidad_id',
        'tipo',
        'codigo',
        'nombre',
        'descripcion',
        'estado',
        'fecha_aprobacion',
        'fecha_vigencia',
        'fecha_derogacion',
    ];

    protected $casts = [
        'fecha_aprobacion' => 'date',
        'fecha_vigencia' => 'date',
        'fecha_derogacion' => 'date',
    ];

    public function municipalidad(): BelongsTo
    {
        return $this->belongsTo(Municipalidad::class);
    }

    public function versiones(): HasMany
    {
        return $this->hasMany(InstrumentoVersion::class);
    }

    public function documentos(): HasMany
    {
        return $this->hasMany(Documento::class);
    }

    public function planEstrategico(): HasOne
    {
        return $this->hasOne(PlanEstrategico::class);
    }

    public function planOperativo(): HasOne
    {
        return $this->hasOne(PlanOperativo::class);
    }
}
