<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organo extends Model
{
    use Auditable;

    protected $table = 'organos';

    protected $fillable = ['municipalidad_id', 'codigo', 'nombre', 'naturaleza', 'tipo', 'nivel_jerarquico', 'organo_padre_id', 'estado'];

    public function municipalidad(): BelongsTo { return $this->belongsTo(Municipalidad::class); }
    public function padre(): BelongsTo { return $this->belongsTo(self::class, 'organo_padre_id'); }
    public function hijos(): HasMany { return $this->hasMany(self::class, 'organo_padre_id'); }
}
