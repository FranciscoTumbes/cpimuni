<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UnidadOrganica extends Model
{
    protected $table = 'unidades_organicas';

    protected $fillable = ['municipalidad_id', 'organo_id', 'codigo', 'nombre', 'tipo', 'nivel_jerarquico', 'unidad_padre_id', 'finalidad', 'estado'];

    public function municipalidad(): BelongsTo { return $this->belongsTo(Municipalidad::class); }
    public function organo(): BelongsTo { return $this->belongsTo(Organo::class); }
    public function padre(): BelongsTo { return $this->belongsTo(self::class, 'unidad_padre_id'); }
    public function hijos(): HasMany { return $this->hasMany(self::class, 'unidad_padre_id'); }
}
