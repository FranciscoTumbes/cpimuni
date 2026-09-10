<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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

        $request->session()->regenerate();

        /*
        |--------------------------------------------------------------------------
        | Registrar último acceso
        |--------------------------------------------------------------------------
        */

        $usuario->update([
            'ultimo_acceso' => now(),
        ]);

        return redirect()->intended(
            route('dashboard')
        );
    }

    /**
     * Cerrar sesión
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('success', 'Sesión cerrada correctamente.');
    }
}