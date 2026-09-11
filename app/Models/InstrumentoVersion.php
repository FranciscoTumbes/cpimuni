<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstrumentoVersion extends Model
{
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
}
