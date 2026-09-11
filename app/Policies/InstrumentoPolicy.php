<?php

namespace App\Policies;

use App\Models\Instrumento;
use App\Models\Usuario;

class InstrumentoPolicy
{
    public function before(Usuario $usuario): ?bool
    {
        return $usuario->rol?->nombre === 'SUPERADMIN' ? true : null;
    }

    public function view(Usuario $usuario, Instrumento $instrumento): bool
    {
        return $instrumento->municipalidad_id === $usuario->municipalidad_id;
    }

    public function update(Usuario $usuario, Instrumento $instrumento): bool
    {
        return $this->view($usuario, $instrumento) && $usuario->tienePermiso('instrumentos.gestionar');
    }

    public function approve(Usuario $usuario, Instrumento $instrumento): bool
    {
        return $this->view($usuario, $instrumento) && $usuario->tienePermiso('instrumentos.aprobar');
    }
}
