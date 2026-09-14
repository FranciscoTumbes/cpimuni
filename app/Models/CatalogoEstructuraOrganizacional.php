<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CatalogoEstructuraOrganizacional extends Model
{
    protected $table = 'catalogo_estructura_organizacional';

    protected $fillable = [
        'codigo', 'codigo_padre', 'parent_id', 'nombre', 'categoria', 'tipo',
        'nivel', 'descripcion', 'permite_hijos', 'orden', 'estado',
    ];

    protected function casts(): array
    {
        return [
            'permite_hijos' => 'boolean',
            'nivel' => 'integer',
            'orden' => 'integer',
        ];
    }

    public function padre(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function hijos(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('orden')->orderBy('codigo');
    }
}