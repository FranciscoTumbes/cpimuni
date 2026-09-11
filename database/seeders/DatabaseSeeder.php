<?php

namespace Database\Seeders;

use App\Models\Municipalidad;
use App\Models\Permiso;
use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $municipalidad = Municipalidad::updateOrCreate(
            ['ruc' => '20123456789'],
            [
                'codigo_entidad' => 'MUNI-DEMO',
                'nombre' => 'Municipalidad Demo',
                'tipo' => 'DISTRITAL',
                'departamento' => 'Lima',
                'provincia' => 'Lima',
                'distrito' => 'Demo',
                'estado' => 'ACTIVA',
            ]
        );

        $roles = [
            ['nombre' => 'SUPERADMIN', 'descripcion' => 'Administrador general de CPIMuni'],
            ['nombre' => 'ADMIN_MUNICIPAL', 'descripcion' => 'Administrador de una municipalidad'],
            ['nombre' => 'PLANEAMIENTO', 'descripcion' => 'Usuario del área de planeamiento'],
            ['nombre' => 'RECURSOS_HUMANOS', 'descripcion' => 'Usuario de recursos humanos'],
            ['nombre' => 'ASESORIA_JURIDICA', 'descripcion' => 'Usuario de asesoría jurídica'],
            ['nombre' => 'USUARIO', 'descripcion' => 'Usuario municipal'],
            ['nombre' => 'CONSULTA', 'descripcion' => 'Usuario con acceso de consulta'],
        ];

        foreach ($roles as $role) {
            Rol::updateOrCreate(['nombre' => $role['nombre']], $role);
        }

        $permissions = [
            ['nombre' => 'dashboard.ver', 'modulo' => 'dashboard', 'descripcion' => 'Acceder al dashboard'],
            ['nombre' => 'municipalidades.ver', 'modulo' => 'municipalidades', 'descripcion' => 'Ver municipalidades'],
            ['nombre' => 'municipalidades.gestionar', 'modulo' => 'municipalidades', 'descripcion' => 'Gestionar municipalidades'],
            ['nombre' => 'usuarios.gestionar', 'modulo' => 'usuarios', 'descripcion' => 'Gestionar usuarios'],
            ['nombre' => 'roles.gestionar', 'modulo' => 'roles', 'descripcion' => 'Gestionar roles y permisos'],
            ['nombre' => 'organizacion.ver', 'modulo' => 'organizacion', 'descripcion' => 'Ver estructura organizacional'],
            ['nombre' => 'organizacion.gestionar', 'modulo' => 'organizacion', 'descripcion' => 'Gestionar estructura organizacional'],
            ['nombre' => 'funciones.ver', 'modulo' => 'funciones', 'descripcion' => 'Ver funciones'],
            ['nombre' => 'funciones.gestionar', 'modulo' => 'funciones', 'descripcion' => 'Gestionar funciones'],
            ['nombre' => 'normativa.ver', 'modulo' => 'normativa', 'descripcion' => 'Ver normativa'],
            ['nombre' => 'normativa.gestionar', 'modulo' => 'normativa', 'descripcion' => 'Gestionar normativa'],
            ['nombre' => 'instrumentos.ver', 'modulo' => 'instrumentos', 'descripcion' => 'Ver instrumentos'],
            ['nombre' => 'instrumentos.gestionar', 'modulo' => 'instrumentos', 'descripcion' => 'Gestionar instrumentos'],
            ['nombre' => 'instrumentos.revisar', 'modulo' => 'instrumentos', 'descripcion' => 'Revisar instrumentos y registrar observaciones'],
            ['nombre' => 'instrumentos.aprobar', 'modulo' => 'instrumentos', 'descripcion' => 'Aprobar instrumentos'],
            ['nombre' => 'documentos.gestionar', 'modulo' => 'documentos', 'descripcion' => 'Gestionar documentos'],
            ['nombre' => 'reportes.ver', 'modulo' => 'reportes', 'descripcion' => 'Ver reportes'],
            ['nombre' => 'auditoria.ver', 'modulo' => 'auditoria', 'descripcion' => 'Ver auditoría'],
        ];

        foreach ($permissions as $permission) {
            Permiso::updateOrCreate(['nombre' => $permission['nombre']], $permission);
        }

        $this->call(RolePermissionSeeder::class);

        $superadmin = Rol::where('nombre', 'SUPERADMIN')->firstOrFail();
        $admin = Rol::where('nombre', 'ADMIN_MUNICIPAL')->firstOrFail();

        if (app()->environment('local', 'testing')) {
            Usuario::updateOrCreate(
                ['email' => 'admin@cpimuni.test'],
                [
                    'municipalidad_id' => null,
                    'rol_id' => $superadmin->id,
                    'nombre' => 'Administrador',
                    'apellido' => 'CPIMuni',
                    'password' => Hash::make('password'),
                    'estado' => 'ACTIVO',
                ]
            );

            Usuario::updateOrCreate(
                ['email' => 'municipalidad@cpimuni.test'],
                [
                    'municipalidad_id' => $municipalidad->id,
                    'rol_id' => $admin->id,
                    'nombre' => 'Administrador',
                    'apellido' => 'Municipal',
                    'password' => Hash::make('password'),
                    'estado' => 'ACTIVO',
                ]
            );
        }
    }
}
