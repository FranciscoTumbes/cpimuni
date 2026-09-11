<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Documento extends Model
{
    use Auditable;

    protected $table = 'documentos';
    protected $fillable = ['municipalidad_id', 'instrumento_id', 'nombre', 'tipo', 'ruta', 'hash_archivo', 'tamano', 'usuario_id'];

    public function municipalidad(): BelongsTo { return $this->belongsTo(Municipalidad::class); }
    public function instrumento(): BelongsTo { return $this->belongsTo(Instrumento::class); }
    public function usuario(): BelongsTo { return $this->belongsTo(Usuario::class); }
}
