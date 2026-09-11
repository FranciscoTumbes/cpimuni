<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Aprobacion extends Model
{
    use Auditable;

    protected $table = 'aprobaciones';
    protected $fillable = ['instrumento_version_id', 'usuario_id', 'tipo', 'resultado', 'documento_respaldo', 'comentario', 'fecha'];
    protected $casts = ['fecha' => 'datetime'];

    public function version(): BelongsTo { return $this->belongsTo(InstrumentoVersion::class, 'instrumento_version_id'); }
    public function usuario(): BelongsTo { return $this->belongsTo(Usuario::class); }
}
