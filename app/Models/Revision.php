<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Observacion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Revision extends Model
{
    use Auditable;

    protected $table = 'revisiones';
    protected $fillable = ['instrumento_version_id', 'usuario_id', 'tipo', 'estado', 'comentario', 'fecha_inicio', 'fecha_fin'];
    protected $casts = ['fecha_inicio' => 'datetime', 'fecha_fin' => 'datetime'];

    public function version(): BelongsTo { return $this->belongsTo(InstrumentoVersion::class, 'instrumento_version_id'); }
    public function usuario(): BelongsTo { return $this->belongsTo(Usuario::class); }
    public function observaciones(): HasMany { return $this->hasMany(Observacion::class); }
}
