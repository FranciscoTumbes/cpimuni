<?php

namespace Tests\Feature;

use App\Models\Auditoria;
use App\Models\Municipalidad;
use App\Models\Rol;
use App\Models\Usuario;
use App\Support\ProductionConfigValidator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use RuntimeException;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_invalid_login_is_audited_without_revealing_account_existence(): void
    {
        $response = $this->from(route('login'))->post(route('login.process'), [
            'email' => 'no-existe@cpimuni.test',
            'password' => 'incorrecta',
        ]);

        $response->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');
        $this->assertSame('Las credenciales ingresadas no son válidas.', session('errors')->get('email')[0]);
        $this->assertDatabaseHas('auditoria', [
            'tabla' => 'usuarios',
            'accion' => 'LOGIN',
            'resultado' => 'FALLIDO',
        ]);
    }

    public function test_inactive_account_cannot_login(): void
    {
        $usuario = Usuario::where('email', 'municipalidad@cpimuni.test')->firstOrFail();
        $usuario->update(['estado' => 'INACTIVO']);

        $this->post(route('login.process'), [
            'email' => $usuario->email,
            'password' => 'password',
        ])->assertRedirect()->assertSessionHasErrors('email');

        $this->assertDatabaseHas('auditoria', [
            'usuario_id' => $usuario->id,
            'accion' => 'LOGIN',
            'resultado' => 'FALLIDO',
        ]);
    }

    public function test_login_is_rate_limited_after_five_invalid_passwords(): void
    {
        RateLimiter::clear('admin@cpimuni.test|127.0.0.1');

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post(route('login.process'), [
                'email' => 'admin@cpimuni.test',
                'password' => 'incorrecta',
            ])->assertRedirect();
        }

        $this->post(route('login.process'), [
            'email' => 'admin@cpimuni.test',
            'password' => 'incorrecta',
        ])->assertSessionHasErrors('email');
    }

    public function test_municipal_user_cannot_access_another_municipality_audit_log(): void
    {
        $municipalidad = Municipalidad::firstOrFail();
        $otraMunicipalidad = Municipalidad::create([
            'nombre' => 'Municipalidad Aislada',
            'ruc' => '20777777777',
            'tipo' => 'DISTRITAL',
            'estado' => 'ACTIVA',
        ]);
        $rol = Rol::where('nombre', 'ADMIN_MUNICIPAL')->firstOrFail();
        $usuario = Usuario::create([
            'municipalidad_id' => $municipalidad->id,
            'rol_id' => $rol->id,
            'nombre' => 'Administrador',
            'apellido' => 'Local',
            'email' => 'admin-local@cpimuni.test',
            'password' => Hash::make('password'),
            'estado' => 'ACTIVO',
        ]);
        Auditoria::create([
            'municipalidad_id' => $otraMunicipalidad->id,
            'usuario_id' => null,
            'tabla' => 'usuarios',
            'registro_id' => null,
            'accion' => 'LOGIN',
            'resultado' => 'EXITOSO',
            'descripcion' => 'Evento aislado',
        ]);

        $this->actingAs($usuario)->get(route('admin.auditoria'))
            ->assertOk()
            ->assertDontSee('Evento aislado');
    }

    public function test_production_validator_rejects_insecure_configuration(): void
    {
        app()->detectEnvironment(fn () => 'production');
        Config::set([
            'app.debug' => true,
            'app.key' => '',
            'app.url' => 'http://localhost',
            'session.secure' => false,
            'filesystems.default' => 'public',
        ]);

        $this->expectException(RuntimeException::class);
        ProductionConfigValidator::validate();
    }

    public function test_production_validator_accepts_secure_configuration(): void
    {
        app()->detectEnvironment(fn () => 'production');
        Config::set([
            'app.debug' => false,
            'app.key' => 'base64:'.base64_encode(random_bytes(32)),
            'app.url' => 'https://cpimuni.example.test',
            'session.secure' => true,
            'filesystems.default' => 'local',
        ]);

        ProductionConfigValidator::validate();
        $this->assertTrue(true);
    }
}
