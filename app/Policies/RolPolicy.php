<?php

namespace App\Policies;

use App\Models\Rol;
use App\Models\Usuario;

class RolPolicy
{
    public function before(Usuario $usuario): ?bool
    {
        return null;
    }

    public function update(Usuario $usuario, Rol $rol): bool
    {
        return ($usuario->rol?->nombre === 'SUPERADMIN' || $usuario->tienePermiso('roles.gestionar')) && $rol->nombre !== 'SUPERADMIN';
    }

    public function create(Usuario $usuario): bool
    {
        return $usuario->rol?->nombre === 'SUPERADMIN' || $usuario->tienePermiso('roles.gestionar');
    }

    public function delete(Usuario $usuario, Rol $rol): bool
    {
        return $this->update($usuario, $rol) && $rol->nombre !== 'ADMIN_MUNICIPAL';
    }
}
