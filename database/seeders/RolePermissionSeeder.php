<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rol;
use App\Models\Permiso;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | SUPERADMIN
        |--------------------------------------------------------------------------
        */

        $superadmin = Rol::where(
            'nombre',
            'SUPERADMIN'
        )->first();

        if ($superadmin) {

            $permisos = Permiso::pluck('id');

            $superadmin->permisos()->syncWithoutDetaching(
                $permisos
            );
        }


        /*
        |--------------------------------------------------------------------------
        | ADMIN_MUNICIPAL
        |--------------------------------------------------------------------------
        */

        $this->asignarPermisos(
            'ADMIN_MUNICIPAL',
            [
                'dashboard.ver',
                'municipalidades.ver',
                'municipalidades.gestionar',
                'usuarios.gestionar',
                'organizacion.ver',
                'organizacion.gestionar',
                'funciones.ver',
                'funciones.gestionar',
                'normativa.ver',
                'normativa.gestionar',
                'instrumentos.ver',
                'instrumentos.gestionar',
                'instrumentos.aprobar',
                'documentos.gestionar',
                'reportes.ver',
                'auditoria.ver',
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | PLANEAMIENTO
        |--------------------------------------------------------------------------
        */

        $this->asignarPermisos(
            'PLANEAMIENTO',
            [
                'dashboard.ver',
                'organizacion.ver',
                'funciones.ver',
                'normativa.ver',
                'normativa.gestionar',
                'instrumentos.ver',
                'instrumentos.gestionar',
                'reportes.ver',
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | RECURSOS HUMANOS
        |--------------------------------------------------------------------------
        */

        $this->asignarPermisos(
            'RECURSOS_HUMANOS',
            [
                'dashboard.ver',
                'organizacion.ver',
                'organizacion.gestionar',
                'funciones.ver',
                'funciones.gestionar',
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | ASESORIA JURIDICA
        |--------------------------------------------------------------------------
        */

        $this->asignarPermisos(
            'ASESORIA_JURIDICA',
            [
                'dashboard.ver',
                'normativa.ver',
                'normativa.gestionar',
                'instrumentos.ver',
                'instrumentos.aprobar',
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | USUARIO
        |--------------------------------------------------------------------------
        */

        $this->asignarPermisos(
            'USUARIO',
            [
                'dashboard.ver',
                'organizacion.ver',
                'funciones.ver',
                'normativa.ver',
                'instrumentos.ver',
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | CONSULTA
        |--------------------------------------------------------------------------
        */

        $this->asignarPermisos(
            'CONSULTA',
            [
                'dashboard.ver',
                'municipalidades.ver',
                'organizacion.ver',
                'funciones.ver',
                'normativa.ver',
                'instrumentos.ver',
                'reportes.ver',
            ]
        );
    }


    private function asignarPermisos(
        string $rolNombre,
        array $permisos
    ): void {

        $rol = Rol::where(
            'nombre',
            $rolNombre
        )->first();

        if (!$rol) {
            return;
        }

        $ids = Permiso::whereIn('nombre', $permisos)->pluck('id');
        $rol->permisos()->sync($ids);
    }
}