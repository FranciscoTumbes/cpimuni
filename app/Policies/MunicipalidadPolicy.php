<?php

namespace App\Policies;

use App\Models\Municipalidad;
use App\Models\Usuario;

class MunicipalidadPolicy
{
    public function before(Usuario $usuario): ?bool
    {
        return $usuario->rol?->nombre === 'SUPERADMIN' ? true : null;
    }

    public function view(Usuario $usuario, Municipalidad $municipalidad): bool
    {
        return $usuario->municipalidad_id === $municipalidad->id;
    }

    public function update(Usuario $usuario, Municipalidad $municipalidad): bool
    {
        return $usuario->municipalidad_id === $municipalidad->id && $usuario->tienePermiso('municipalidades.gestionar');
    }

    public function delete(Usuario $usuario, Municipalidad $municipalidad): bool
    {
        return $this->update($usuario, $municipalidad);
    }
}
