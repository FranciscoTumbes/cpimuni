<?php

namespace Tests\Feature;

use App\Models\Auditoria;
use App\Models\CatalogoEstructuraOrganizacional;
use App\Models\Instrumento;
use App\Models\InstrumentoVersion;
use App\Models\Municipalidad;
use App\Models\Organo;
use App\Models\Permiso;
use App\Models\Puesto;
use App\Models\Rol;
use App\Models\UnidadOrganica;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;
use App\Models\Funcion;
use App\Models\AccionEstrategica;
use App\Models\ActividadOperativa;
use App\Models\Indicador;
use App\Models\ObjetivoEstrategico;
use App\Models\PlanEstrategico;
use App\Models\PlanOperativo;
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

    public function test_superadmin_manages_organizational_catalog_and_regular_admin_is_forbidden(): void
    {
        $this->get(route('catalogos.estructura.index'))->assertOk();

        $this->post(route('catalogos.estructura.store'), [
            'codigo' => 'CAT-001',
            'codigo_padre' => null,
            'nombre' => 'Gobierno',
            'categoria' => 'NATURALEZA',
            'tipo' => 'NATURALEZA',
            'nivel' => 1,
            'descripcion' => 'Naturaleza de gobierno',
            'permite_hijos' => true,
            'orden' => 1,
            'estado' => 'ACTIVO',
        ])->assertRedirect();

        $catalog = CatalogoEstructuraOrganizacional::where('codigo', 'CAT-001')->firstOrFail();

        $this->put(route('catalogos.estructura.update', $catalog), [
            'codigo' => 'CAT-001',
            'nombre' => 'Gobierno institucional',
            'categoria' => 'NATURALEZA',
            'tipo' => 'NATURALEZA',
            'nivel' => 1,
            'descripcion' => 'Naturaleza institucional de gobierno',
            'permite_hijos' => true,
            'orden' => 1,
            'estado' => 'ACTIVO',
        ])->assertRedirect();

        $this->delete(route('catalogos.estructura.destroy', $catalog))->assertRedirect();

        $admin = Usuario::where('email', 'municipalidad@cpimuni.test')->firstOrFail();
        $this->actingAs($admin)
            ->get(route('catalogos.estructura.index'))
            ->assertForbidden();
    }

    public function test_pei_poi_chain_links_strategy_to_responsible_activity_and_indicator(): void
    {
        $municipalidad = Municipalidad::firstOrFail();
        $unidad = UnidadOrganica::create([
            'municipalidad_id' => $municipalidad->id,
            'codigo' => 'PL-001',
            'nombre' => 'Gerencia de Planeamiento',
            'tipo' => 'GERENCIA',
            'estado' => 'ACTIVA',
        ]);
        $pei = PlanEstrategico::create([
            'municipalidad_id' => $municipalidad->id,
            'codigo' => 'PEI-2026-2030',
            'nombre' => 'Plan Estratégico Institucional',
            'anio_inicio' => 2026,
            'anio_fin' => 2030,
        ]);
        $objetivo = ObjetivoEstrategico::create([
            'plan_estrategico_id' => $pei->id,
            'unidad_responsable_id' => $unidad->id,
            'codigo' => 'OEI.01',
            'enunciado' => 'Mejorar los servicios públicos municipales.',
        ]);
        $accion = AccionEstrategica::create([
            'objetivo_estrategico_id' => $objetivo->id,
            'unidad_responsable_id' => $unidad->id,
            'codigo' => 'AEI.01.01',
            'enunciado' => 'Fortalecer la prestación de servicios.',
        ]);
        $poi = PlanOperativo::create([
            'municipalidad_id' => $municipalidad->id,
            'codigo' => 'POI-2026',
            'nombre' => 'Plan Operativo Institucional 2026',
            'anio' => 2026,
        ]);
        $actividad = ActividadOperativa::create([
            'plan_operativo_id' => $poi->id,
            'accion_estrategica_id' => $accion->id,
            'unidad_responsable_id' => $unidad->id,
            'codigo' => 'AOI.01.01.01',
            'denominacion' => 'Capacitar al personal municipal.',
            'unidad_medida' => 'Persona capacitada',
            'meta_programada' => 100,
        ]);
        $indicador = Indicador::create([
            'municipalidad_id' => $municipalidad->id,
            'actividad_operativa_id' => $actividad->id,
            'codigo' => 'IND.01',
            'nombre' => 'Porcentaje de personal capacitado',
            'unidad_medida' => 'Porcentaje',
            'meta' => 90,
        ]);

        $this->assertSame($objetivo->id, $accion->fresh()->objetivo->id);
        $this->assertSame($accion->id, $actividad->fresh()->accion->id);
        $this->assertSame($unidad->id, $actividad->fresh()->unidadResponsable->id);
        $this->assertSame($actividad->id, $indicador->fresh()->actividad->id);
        $this->assertSame($municipalidad->id, $poi->fresh()->municipalidad->id);
    }

    public function test_superadmin_can_duplicate_structure_without_users_or_documents(): void
    {
        $source = Municipalidad::firstOrFail();
        $organo = Organo::create([
            'municipalidad_id' => $source->id,
            'codigo' => '01',
            'nombre' => 'Órgano de prueba',
            'tipo' => 'ORGANO',
            'nivel_jerarquico' => 1,
        ]);
        $unidad = UnidadOrganica::create([
            'municipalidad_id' => $source->id,
            'organo_id' => $organo->id,
            'codigo' => '01.01',
            'nombre' => 'Unidad de prueba',
            'tipo' => 'GERENCIA',
            'nivel_jerarquico' => 2,
        ]);
        $puesto = Puesto::create([
            'municipalidad_id' => $source->id,
            'unidad_organica_id' => $unidad->id,
            'codigo' => 'P-001',
            'denominacion' => 'Puesto de prueba',
        ]);
        Funcion::create([
            'municipalidad_id' => $source->id,
            'unidad_organica_id' => $unidad->id,
            'puesto_id' => $puesto->id,
            'codigo' => 'F-001',
            'descripcion' => 'Función de prueba',
        ]);

        $this->post(route('admin.municipalidades.duplicate', $source), [
            'nombre' => 'Municipalidad clonada',
            'ruc' => '20977777777',
            'tipo' => 'DISTRITAL',
            'estructura' => '1',
            'puestos' => '1',
            'funciones' => '1',
        ])->assertRedirect()->assertSessionHasNoErrors();

        $destination = Municipalidad::where('ruc', '20977777777')->firstOrFail();
        $newOrgano = Organo::where('municipalidad_id', $destination->id)->firstOrFail();
        $newUnidad = UnidadOrganica::where('municipalidad_id', $destination->id)->firstOrFail();
        $newPuesto = Puesto::where('municipalidad_id', $destination->id)->firstOrFail();

        $this->assertNotSame($organo->id, $newOrgano->id);
        $this->assertSame($newOrgano->id, $newUnidad->organo_id);
        $this->assertSame($newUnidad->id, $newPuesto->unidad_organica_id);
        $this->assertDatabaseHas('funciones', ['municipalidad_id' => $destination->id, 'puesto_id' => $newPuesto->id]);
        $this->assertDatabaseMissing('usuarios', ['municipalidad_id' => $destination->id]);
        $this->assertDatabaseMissing('documentos', ['municipalidad_id' => $destination->id]);
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

    public function test_organizational_nature_is_stored_separately_from_type(): void
    {
        $municipalidad = Municipalidad::firstOrFail();

        $organo = Organo::create([
            'municipalidad_id' => $municipalidad->id,
            'codigo' => '07',
            'nombre' => 'Gerencia de Desarrollo Económico',
            'naturaleza' => 'LINEA',
            'tipo' => 'GERENCIA',
            'nivel_jerarquico' => 3,
            'estado' => 'ACTIVO',
        ]);

        $unidad = UnidadOrganica::create([
            'municipalidad_id' => $municipalidad->id,
            'organo_id' => $organo->id,
            'codigo' => '07.02',
            'nombre' => 'Subgerencia de Turismo',
            'naturaleza' => 'LINEA',
            'tipo' => 'SUBGERENCIA',
            'nivel_jerarquico' => 4,
            'estado' => 'ACTIVA',
        ]);

        $this->assertSame('LINEA', $organo->fresh()->naturaleza);
        $this->assertSame('GERENCIA', $organo->fresh()->tipo);
        $this->assertSame('LINEA', $unidad->fresh()->naturaleza);
        $this->assertSame('SUBGERENCIA', $unidad->fresh()->tipo);
        $this->assertDatabaseHas('organos', ['id' => $organo->id, 'naturaleza' => 'LINEA', 'tipo' => 'GERENCIA']);
        $this->assertDatabaseHas('unidades_organicas', ['id' => $unidad->id, 'naturaleza' => 'LINEA', 'tipo' => 'SUBGERENCIA']);
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

    public function test_unit_preserves_institutional_fields_for_nested_structure(): void
    {
        $municipalidad = Municipalidad::firstOrFail();

        $this->post(route('organizacion.unidades.store'), [
            'municipalidad_id' => $municipalidad->id,
            'nombre' => 'Subgerencia de Presupuesto',
            'abreviatura' => 'SGP',
            'tipo' => 'SUBGERENCIA',
            'categoria_institucional' => 'SUBGERENCIA',
            'nivel_jerarquico' => 4,
            'orden' => 2,
            'finalidad' => 'Gestionar el presupuesto institucional.',
            'descripcion' => 'Unidad orgánica dependiente de una gerencia.',
        ])->assertRedirect()->assertSessionHasNoErrors();

        $this->assertDatabaseHas('unidades_organicas', [
            'municipalidad_id' => $municipalidad->id,
            'nombre' => 'Subgerencia de Presupuesto',
            'abreviatura' => 'SGP',
            'categoria_institucional' => 'SUBGERENCIA',
            'nivel_jerarquico' => 4,
            'orden' => 2,
            'descripcion' => 'Unidad orgánica dependiente de una gerencia.',
        ]);
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
