<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Mostrar formulario de login
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Procesar autenticación
     */
    public function login(Request $request)
    {
        $key = strtolower($request->input('email', '')) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages([
                'email' => 'Demasiados intentos. Espere unos minutos e inténtelo nuevamente.',
            ]);
        }

        $credenciales = $request->validate([
            'email' => [
                'required',
                'email',
            ],
            'password' => [
                'required',
                'string',
            ],
        ], [
            'email.required' => 'Ingrese su correo electrónico.',
            'email.email' => 'Ingrese un correo electrónico válido.',
            'password.required' => 'Ingrese su contraseña.',
        ]);

        $usuario = Usuario::with(['rol', 'municipalidad'])
            ->where('email', $credenciales['email'])
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Verificar existencia
        |--------------------------------------------------------------------------
        */

        if (!$usuario) {
            RateLimiter::hit($key, 60);
            AuditService::event('usuarios', 'LOGIN', null, null, null, null, 'Intento de inicio de sesión rechazado.', 'FALLIDO', 'Usuario no encontrado', $request);

            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'Las credenciales ingresadas no son válidas.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Verificar estado
        |--------------------------------------------------------------------------
        */

        if ($usuario->estado !== 'ACTIVO') {

            $mensaje = match ($usuario->estado) {
                'INACTIVO' => 'Su cuenta se encuentra inactiva.',
                'BLOQUEADO' => 'Su cuenta se encuentra bloqueada.',
                default => 'Su cuenta no está habilitada para ingresar.',
            };

            AuditService::event('usuarios', 'LOGIN', $usuario->id, $usuario->municipalidad_id, null, null, 'Intento de inicio de sesión rechazado.', 'FALLIDO', $mensaje, $request, $usuario->id);

            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => $mensaje,
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Verificar contraseña
        |--------------------------------------------------------------------------
        */

        if (!Hash::check(
            $credenciales['password'],
            $usuario->password
        )) {
            RateLimiter::hit($key, 60);
            AuditService::event('usuarios', 'LOGIN', $usuario->id, $usuario->municipalidad_id, null, null, 'Intento de inicio de sesión rechazado.', 'FALLIDO', 'Contraseña inválida', $request, $usuario->id);

            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'Las credenciales ingresadas no son válidas.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Iniciar sesión
        |--------------------------------------------------------------------------
        */

        Auth::login($usuario);
        RateLimiter::clear($key);

        $request->session()->regenerate();

        /*
        |--------------------------------------------------------------------------
        | Registrar último acceso
        |--------------------------------------------------------------------------
        */

        $usuario->update([
            'ultimo_acceso' => now(),
        ]);

        AuditService::event('usuarios', 'LOGIN', $usuario->id, $usuario->municipalidad_id, null, null, 'Inicio de sesión exitoso.', 'EXITOSO', null, $request);

        return redirect()->intended(
            route('dashboard')
        );
    }

    /**
     * Cerrar sesión
     */
    public function logout(Request $request)
    {
        $usuario = $request->user();

        if ($usuario) {
            AuditService::event('usuarios', 'LOGOUT', $usuario->id, $usuario->municipalidad_id, null, null, 'Cierre de sesión.', 'EXITOSO', null, $request);
        }

        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('success', 'Sesión cerrada correctamente.');
    }
}