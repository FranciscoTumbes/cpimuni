<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UnidadOrganica extends Model
{
    use Auditable;

    protected $table = 'unidades_organicas';

    protected $fillable = ['municipalidad_id', 'organo_id', 'codigo', 'nombre', 'abreviatura', 'naturaleza', 'tipo', 'categoria_institucional', 'nivel_jerarquico', 'orden', 'unidad_padre_id', 'finalidad', 'descripcion', 'estado'];

    public function municipalidad(): BelongsTo { return $this->belongsTo(Municipalidad::class); }
    public function organo(): BelongsTo { return $this->belongsTo(Organo::class); }
    public function padre(): BelongsTo { return $this->belongsTo(self::class, 'unidad_padre_id'); }
    public function hijos(): HasMany { return $this->hasMany(self::class, 'unidad_padre_id'); }
}
