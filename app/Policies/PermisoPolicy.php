<?php

namespace App\Policies;

use App\Models\Permiso;
use App\Models\Usuario;

class PermisoPolicy
{
    public function before(Usuario $usuario): ?bool
    {
        return null;
    }

    public function update(Usuario $usuario, Permiso $permiso): bool
    {
        return ($usuario->rol?->nombre === 'SUPERADMIN' || $usuario->tienePermiso('roles.gestionar')) && ! in_array($permiso->nombre, [
            'roles.gestionar',
            'auditoria.ver',
            'instrumentos.aprobar',
        ], true);
    }

    public function create(Usuario $usuario): bool
    {
        return $usuario->rol?->nombre === 'SUPERADMIN' || $usuario->tienePermiso('roles.gestionar');
    }

    public function delete(Usuario $usuario, Permiso $permiso): bool
    {
        return $this->update($usuario, $permiso) && ! $permiso->roles()->exists();
    }
}
