<?php

namespace Tests\Feature;

use App\Models\Auditoria;
use App\Models\Instrumento;
use App\Models\InstrumentoVersion;
use App\Models\Municipalidad;
use App\Models\Organo;
use App\Models\Rol;
use App\Models\UnidadOrganica;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
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

    public function test_municipal_admin_cannot_reference_an_organ_from_another_municipality(): void
    {
        $municipalidadAjena = Municipalidad::create([
            'nombre' => 'Municipalidad Ajena',
            'ruc' => '20988888888',
            'tipo' => 'DISTRITAL',
            'estado' => 'ACTIVA',
        ]);
        $organoAjeno = Organo::create([
            'municipalidad_id' => $municipalidadAjena->id,
            'nombre' => 'Órgano Ajeno',
        ]);
        $administradorMunicipal = Usuario::where('email', 'municipalidad@cpimuni.test')->firstOrFail();

        $this->actingAs($administradorMunicipal)
            ->post(route('organizacion.unidades.store'), [
                'municipalidad_id' => $municipalidadAjena->id,
                'organo_id' => $organoAjeno->id,
                'nombre' => 'Unidad Cruzada',
            ])
            ->assertSessionHasErrors('organo_id');

        $this->assertDatabaseMissing('unidades_organicas', ['nombre' => 'Unidad Cruzada']);
    }

    public function test_user_changes_are_recorded_with_before_and_after_values(): void
    {
        $rol = Rol::where('nombre', 'CONSULTA')->firstOrFail();
        $municipalidad = Municipalidad::firstOrFail();

        $this->post(route('admin.usuarios.store'), [
            'municipalidad_id' => $municipalidad->id,
            'rol_id' => $rol->id,
            'nombre' => 'Auditado',
            'apellido' => 'Inicial',
            'email' => 'auditado@cpimuni.test',
            'password' => 'password123',
            'estado' => 'ACTIVO',
        ])->assertRedirect();

        $usuario = Usuario::where('email', 'auditado@cpimuni.test')->firstOrFail();

        $this->put(route('admin.usuarios.update', $usuario), [
            'municipalidad_id' => $municipalidad->id,
            'rol_id' => $rol->id,
            'nombre' => 'Auditado Editado',
            'apellido' => 'Final',
            'email' => 'auditado@cpimuni.test',
            'estado' => 'INACTIVO',
        ])->assertRedirect();

        $auditoria = Auditoria::where('tabla', 'usuarios')
            ->where('registro_id', $usuario->id)
            ->where('accion', 'ACTUALIZAR')
            ->latest('id')
            ->firstOrFail();

        $this->assertSame(Usuario::where('email', 'admin@cpimuni.test')->value('id'), $auditoria->usuario_id);
        $this->assertSame('Inicial', $auditoria->datos_anteriores['apellido']);
        $this->assertSame('Auditado Editado', $auditoria->datos_nuevos['nombre']);
        $this->assertSame('EXITOSO', $auditoria->resultado);
        $this->assertSame($municipalidad->id, $auditoria->municipalidad_id);
    }

    public function test_creator_cannot_approve_own_instrument_version(): void
    {
        $municipalidad = Municipalidad::firstOrFail();
        $instrumento = Instrumento::create([
            'municipalidad_id' => $municipalidad->id,
            'tipo' => 'ROF',
            'nombre' => 'Instrumento para segregación',
            'estado' => 'EN_REVISION',
        ]);
        $version = InstrumentoVersion::create([
            'instrumento_id' => $instrumento->id,
            'version' => '1.0',
            'estado' => 'EN_REVISION',
            'usuario_id' => Auth::id(),
        ]);

        $this->assertFalse(Gate::forUser(Usuario::findOrFail(Auth::id()))->allows('approve', $version));

        $rol = Rol::where('nombre', 'ASESORIA_JURIDICA')->firstOrFail();
        $revisor = Usuario::create([
            'municipalidad_id' => $municipalidad->id,
            'rol_id' => $rol->id,
            'nombre' => 'Revisor',
            'apellido' => 'Independiente',
            'email' => 'revisor@cpimuni.test',
            'password' => 'password123',
            'estado' => 'ACTIVO',
        ]);

        $this->assertTrue(Gate::forUser($revisor)->allows('approve', $version));
    }
}
