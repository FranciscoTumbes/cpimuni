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
                'municipalidades.ver',
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

        foreach ($permisos as $permisoNombre) {

            $permiso = Permiso::where(
                'nombre',
                $permisoNombre
            )->first();

            if ($permiso) {

                $rol->permisos()->syncWithoutDetaching([
                    $permiso->id
                ]);

            }
        }
    }
}