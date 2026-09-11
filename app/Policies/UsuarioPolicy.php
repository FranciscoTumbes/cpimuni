<?php

namespace App\Policies;

use App\Models\Usuario;

class UsuarioPolicy
{
    public function before(Usuario $actor): ?bool
    {
        return $actor->rol?->nombre === 'SUPERADMIN' ? true : null;
    }

    public function view(Usuario $actor, Usuario $usuario): bool
    {
        return $actor->municipalidad_id === $usuario->municipalidad_id;
    }

    public function update(Usuario $actor, Usuario $usuario): bool
    {
        return $this->view($actor, $usuario) && $actor->tienePermiso('usuarios.gestionar');
    }

    public function delete(Usuario $actor, Usuario $usuario): bool
    {
        return $this->update($actor, $usuario) && ! $actor->is($usuario);
    }
}
