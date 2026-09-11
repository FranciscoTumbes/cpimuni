<?php

namespace Tests\Feature;

use App\Models\Auditoria;
use App\Models\Instrumento;
use App\Models\InstrumentoVersion;
use App\Models\Municipalidad;
use App\Models\Organo;
use App\Models\Permiso;
use App\Models\Rol;
use App\Models\UnidadOrganica;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
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

    public function test_municipal_admin_can_register_a_position_without_sending_municipality_id(): void
    {
        $administradorMunicipal = Usuario::where('email', 'municipalidad@cpimuni.test')->firstOrFail();

        $this->actingAs($administradorMunicipal)
            ->get(route('organizacion.index'))
            ->assertOk()
            ->assertSee($administradorMunicipal->municipalidad->nombre);

        $this->post(route('organizacion.puestos.store'), [
            'denominacion' => 'Especialista en Planeamiento',
            'codigo' => 'PUESTO-001',
        ])->assertRedirect()->assertSessionHasNoErrors();

        $this->assertDatabaseHas('puestos', [
            'municipalidad_id' => $administradorMunicipal->municipalidad_id,
            'denominacion' => 'Especialista en Planeamiento',
        ]);
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

    public function test_instrument_version_completes_review_observation_and_approval_flow(): void
    {
        $municipalidad = Municipalidad::firstOrFail();
        $creador = Usuario::where('email', 'municipalidad@cpimuni.test')->firstOrFail();
        $rolRevisor = Rol::where('nombre', 'ASESORIA_JURIDICA')->firstOrFail();
        $revisor = Usuario::create([
            'municipalidad_id' => $municipalidad->id,
            'rol_id' => $rolRevisor->id,
            'nombre' => 'Revisor',
            'apellido' => 'Documental',
            'email' => 'flujo-revisor@cpimuni.test',
            'password' => 'password123',
            'estado' => 'ACTIVO',
        ]);
        $instrumento = Instrumento::create([
            'municipalidad_id' => $municipalidad->id,
            'tipo' => 'ROF',
            'nombre' => 'ROF con flujo completo',
            'estado' => 'BORRADOR',
        ]);
        Storage::fake('private');
        $this->actingAs($creador)
            ->post(route('instrumentos.documentos.store', $instrumento), [
                'archivo' => UploadedFile::fake()->create('informe.pdf', 12, 'application/pdf'),
                'tipo' => 'Informe técnico',
            ])->assertRedirect();
        $this->assertDatabaseHas('documentos', ['instrumento_id' => $instrumento->id, 'tipo' => 'Informe técnico']);

        $this->actingAs($creador)
            ->post(route('instrumentos.versiones.store', $instrumento), [
                'version' => '1.0',
                'motivo' => 'Versión inicial',
            ])->assertRedirect();
        $version = InstrumentoVersion::where('instrumento_id', $instrumento->id)->firstOrFail();

        $this->post(route('instrumentos.versiones.submit', $version))->assertRedirect();
        $this->actingAs($revisor)
            ->post(route('instrumentos.versiones.observations.store', $version), [
                'observacion' => 'Completar el fundamento legal.',
            ])->assertRedirect();
        $observacion = $version->fresh()->revisiones()->firstOrFail()->observaciones()->firstOrFail();

        $this->actingAs($creador)
            ->post(route('instrumentos.observaciones.respond', $observacion), [
                'respuesta' => 'Fundamento legal incorporado.',
            ])->assertRedirect();
        $aprobador = Usuario::create([
            'municipalidad_id' => $municipalidad->id,
            'rol_id' => Rol::where('nombre', 'ADMIN_MUNICIPAL')->firstOrFail()->id,
            'nombre' => 'Aprobador',
            'apellido' => 'Independiente',
            'email' => 'flujo-aprobador@cpimuni.test',
            'password' => 'password123',
            'estado' => 'ACTIVO',
        ]);
        $this->assertTrue($aprobador->tienePermiso('instrumentos.aprobar'));
        $this->assertNotSame($aprobador->id, $version->usuario_id);
        $this->assertSame($municipalidad->id, $version->fresh()->instrumento->municipalidad_id);
        $this->assertSame($municipalidad->id, $aprobador->municipalidad_id);
        $this->assertSame(0, Auditoria::where('usuario_id', $aprobador->id)->whereIn('tabla', ['instrumentos', 'instrumento_versiones'])->count());
        $this->assertTrue(Gate::forUser($aprobador)->allows('approve', $version->fresh()));
        $this->actingAs($aprobador)
            ->post(route('instrumentos.versiones.decide', $version), [
                'resultado' => 'APROBADO',
                'tipo' => 'APROBACION_FORMAL',
                'comentario' => 'Revisión conforme.',
            ])->assertRedirect();

        $this->assertDatabaseHas('instrumento_versiones', ['id' => $version->id, 'estado' => 'APROBADO']);
        $this->assertDatabaseHas('instrumentos', ['id' => $instrumento->id, 'estado' => 'APROBADO']);
        $this->assertDatabaseHas('aprobaciones', ['instrumento_version_id' => $version->id, 'resultado' => 'APROBADO', 'usuario_id' => $aprobador->id]);
    }

    public function test_critical_role_cannot_be_modified(): void
    {
        $superadmin = Rol::where('nombre', 'SUPERADMIN')->firstOrFail();

        $this->put(route('admin.roles.update', $superadmin), [
            'descripcion' => 'Intento de modificación',
            'permisos' => [],
        ])->assertForbidden();
    }

    public function test_last_municipal_admin_cannot_be_deactivated(): void
    {
        $administrador = Usuario::where('email', 'municipalidad@cpimuni.test')->firstOrFail();
        $rolConsulta = Rol::where('nombre', 'CONSULTA')->firstOrFail();

        $this->put(route('admin.usuarios.update', $administrador), [
            'municipalidad_id' => $administrador->municipalidad_id,
            'rol_id' => $rolConsulta->id,
            'nombre' => $administrador->nombre,
            'apellido' => $administrador->apellido,
            'email' => $administrador->email,
            'estado' => 'INACTIVO',
        ])->assertStatus(422);
    }

    public function test_user_cannot_change_own_role_or_state(): void
    {
        $administrador = Usuario::where('email', 'municipalidad@cpimuni.test')->firstOrFail();
        $rolConsulta = Rol::where('nombre', 'CONSULTA')->firstOrFail();

        $this->actingAs($administrador)
            ->put(route('admin.usuarios.update', $administrador), [
                'municipalidad_id' => $administrador->municipalidad_id,
                'rol_id' => $rolConsulta->id,
                'nombre' => $administrador->nombre,
                'apellido' => $administrador->apellido,
                'email' => $administrador->email,
                'estado' => 'INACTIVO',
            ])->assertForbidden();
    }

    public function test_users_cannot_be_created_in_inactive_municipality(): void
    {
        $municipalidad = Municipalidad::firstOrFail();
        $municipalidad->update(['estado' => 'INACTIVA']);
        $rol = Rol::where('nombre', 'CONSULTA')->firstOrFail();

        $this->post(route('admin.usuarios.store'), [
            'municipalidad_id' => $municipalidad->id,
            'rol_id' => $rol->id,
            'nombre' => 'Usuario',
            'apellido' => 'Inactivo',
            'email' => 'inactivo@cpimuni.test',
            'password' => 'password123',
            'estado' => 'ACTIVO',
        ])->assertStatus(422);
    }

    public function test_municipal_admin_role_cannot_lose_critical_permissions(): void
    {
        $rol = Rol::where('nombre', 'ADMIN_MUNICIPAL')->firstOrFail();
        $permisos = Permiso::whereNotIn('nombre', ['usuarios.gestionar', 'auditoria.ver'])->pluck('id')->all();

        $this->put(route('admin.roles.update', $rol), [
            'descripcion' => $rol->descripcion,
            'permisos' => $permisos,
        ])->assertStatus(422);
    }

    public function test_organizational_records_can_be_updated_and_deactivated(): void
    {
        $municipalidad = Municipalidad::firstOrFail();
        $organo = Organo::create([
            'municipalidad_id' => $municipalidad->id,
            'codigo' => 'ORG-001',
            'nombre' => 'Órgano original',
            'estado' => 'ACTIVO',
        ]);

        $this->put(route('organizacion.update', ['organos', $organo->id]), [
            'codigo' => 'ORG-001',
            'nombre' => 'Órgano actualizado',
            'tipo' => 'Órgano de línea',
            'nivel_jerarquico' => 1,
            'organo_padre_id' => null,
            'estado' => 'ACTIVO',
        ])->assertRedirect();

        $this->delete(route('organizacion.destroy', ['organos', $organo->id]))
            ->assertRedirect();

        $this->assertDatabaseHas('organos', ['id' => $organo->id, 'nombre' => 'Órgano actualizado', 'estado' => 'INACTIVO']);
    }

    public function test_organizational_hierarchy_rejects_cycles_and_cross_municipality_updates(): void
    {
        $municipalidad = Municipalidad::firstOrFail();
        $organo = Organo::create(['municipalidad_id' => $municipalidad->id, 'nombre' => 'Órgano base', 'estado' => 'ACTIVO']);
        $hijo = Organo::create(['municipalidad_id' => $municipalidad->id, 'nombre' => 'Órgano hijo', 'estado' => 'ACTIVO', 'organo_padre_id' => $organo->id]);

        $this->put(route('organizacion.update', ['organos', $organo->id]), [
            'nombre' => 'Órgano base',
            'organo_padre_id' => $hijo->id,
            'estado' => 'ACTIVO',
        ])->assertStatus(422);

        $otraMunicipalidad = Municipalidad::create(['nombre' => 'Municipalidad externa', 'ruc' => '20666666666', 'tipo' => 'DISTRITAL', 'estado' => 'ACTIVA']);
        $organoExterno = Organo::create(['municipalidad_id' => $otraMunicipalidad->id, 'nombre' => 'Órgano externo', 'estado' => 'ACTIVO']);
        $this->actingAs(Usuario::where('email', 'municipalidad@cpimuni.test')->firstOrFail())
            ->get(route('organizacion.edit', ['organos', $organoExterno->id]))
            ->assertNotFound();
    }
}
