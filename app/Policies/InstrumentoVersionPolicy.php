<?php

namespace App\Policies;

use App\Models\Auditoria;
use App\Models\InstrumentoVersion;
use App\Models\Usuario;

class InstrumentoVersionPolicy
{
    public function approve(Usuario $usuario, InstrumentoVersion $version): bool
    {
        $instrumento = $version->instrumento;

        if ($version->usuario_id === $usuario->id) {
            return false;
        }

        if (! $instrumento || $instrumento->municipalidad_id !== $usuario->municipalidad_id) {
            return $usuario->rol?->nombre === 'SUPERADMIN' && (bool) $instrumento;
        }

        if ($usuario->rol?->nombre !== 'SUPERADMIN' && ! $usuario->tienePermiso('instrumentos.aprobar')) {
            return false;
        }

        return ! Auditoria::query()
            ->where('usuario_id', $usuario->id)
            ->whereIn('tabla', ['instrumentos', 'instrumento_versiones'])
            ->whereIn('accion', ['CREAR', 'ACTUALIZAR'])
            ->where(function ($query) use ($version, $instrumento): void {
                $query->where(function ($query) use ($version): void {
                    $query->where('tabla', 'instrumento_versiones')->where('registro_id', $version->id);
                })->orWhere(function ($query) use ($instrumento): void {
                    $query->where('tabla', 'instrumentos')->where('registro_id', $instrumento->id);
                });
            })
            ->exists();
    }
}
