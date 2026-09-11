<?php

namespace App\Policies;

use App\Models\Norma;
use App\Models\Usuario;

class NormaPolicy
{
    public function before(Usuario $usuario): ?bool
    {
        return $usuario->rol?->nombre === 'SUPERADMIN' ? true : null;
    }

    public function view(Usuario $usuario, Norma $norma): bool
    {
        return $norma->municipalidad_id === null || $norma->municipalidad_id === $usuario->municipalidad_id;
    }

    public function update(Usuario $usuario, Norma $norma): bool
    {
        return $this->view($usuario, $norma) && $usuario->tienePermiso('normativa.gestionar');
    }

    public function delete(Usuario $usuario, Norma $norma): bool
    {
        return $this->update($usuario, $norma);
    }
}
