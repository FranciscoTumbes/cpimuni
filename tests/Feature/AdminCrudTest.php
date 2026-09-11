<?php

namespace Tests\Feature;

use App\Models\Municipalidad;
use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->actingAs(Usuario::where('email', 'admin@cpimuni.test')->firstOrFail());
    }

    public function test_superadmin_can_create_update_and_delete_municipality(): void
    {
        $this->post(route('admin.municipalidades.store'), [
            'nombre' => 'Municipalidad de Prueba',
            'ruc' => '20999999999',
            'tipo' => 'PROVINCIAL',
        ])->assertRedirect();

        $municipalidad = Municipalidad::where('ruc', '20999999999')->firstOrFail();

        $this->put(route('admin.municipalidades.update', $municipalidad), [
            'nombre' => 'Municipalidad Actualizada',
            'ruc' => '20999999999',
            'tipo' => 'PROVINCIAL',
            'estado' => 'ACTIVA',
        ])->assertRedirect();

        $this->delete(route('admin.municipalidades.destroy', $municipalidad))->assertRedirect();
        $this->assertDatabaseMissing('municipalidades', ['id' => $municipalidad->id]);
    }

    public function test_superadmin_can_create_update_and_delete_user(): void
    {
        $rol = Rol::where('nombre', 'CONSULTA')->firstOrFail();
        $municipalidad = Municipalidad::firstOrFail();

        $this->post(route('admin.usuarios.store'), [
            'municipalidad_id' => $municipalidad->id,
            'rol_id' => $rol->id,
            'nombre' => 'Usuario',
            'apellido' => 'CRUD',
            'email' => 'crud@cpimuni.test',
            'password' => 'password123',
            'estado' => 'ACTIVO',
        ])->assertRedirect();

        $usuario = Usuario::where('email', 'crud@cpimuni.test')->firstOrFail();

        $this->put(route('admin.usuarios.update', $usuario), [
            'municipalidad_id' => $municipalidad->id,
            'rol_id' => $rol->id,
            'nombre' => 'Usuario Editado',
            'apellido' => 'CRUD',
            'email' => 'crud-editado@cpimuni.test',
            'estado' => 'INACTIVO',
        ])->assertRedirect();

        $this->delete(route('admin.usuarios.destroy', $usuario))->assertRedirect();
        $this->assertDatabaseMissing('usuarios', ['id' => $usuario->id]);
    }

    public function test_municipality_with_users_cannot_be_deleted(): void
    {
        $municipalidad = Municipalidad::firstOrFail();

        $this->delete(route('admin.municipalidades.destroy', $municipalidad))
            ->assertSessionHasErrors('municipalidad');

        $this->assertDatabaseHas('municipalidades', ['id' => $municipalidad->id]);
    }
}
