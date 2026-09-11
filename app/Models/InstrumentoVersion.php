<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InstrumentoVersion extends Model
{
    use Auditable;

    protected $table = 'instrumento_versiones';

    protected $fillable = ['instrumento_id', 'version', 'motivo', 'usuario_id', 'fecha_version', 'estado', 'archivo_docx', 'archivo_pdf', 'observaciones'];

    protected $casts = ['fecha_version' => 'datetime'];

    public function instrumento(): BelongsTo
    {
        return $this->belongsTo(Instrumento::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class);
    }

    public function revisiones(): HasMany
    {
        return $this->hasMany(Revision::class);
    }

    public function aprobaciones(): HasMany
    {
        return $this->hasMany(Aprobacion::class);
    }
}
