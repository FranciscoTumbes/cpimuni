<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Observacion extends Model
{
    use Auditable;

    protected $table = 'observaciones';
    protected $fillable = ['revision_id', 'usuario_id', 'observacion', 'respuesta', 'estado'];

    public function revision(): BelongsTo { return $this->belongsTo(Revision::class); }
    public function usuario(): BelongsTo { return $this->belongsTo(Usuario::class); }
}
