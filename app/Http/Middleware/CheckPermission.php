<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(
        Request $request,
        Closure $next,
        string $permiso
    ): Response {
        $usuario = $request->user();

        if (!$usuario) {
            return redirect()->route('login');
        }

        /*
        |--------------------------------------------------------------------------
        | SUPERADMIN
        |--------------------------------------------------------------------------
        | El SUPERADMIN tiene acceso total a CPIMuni.
        */
        if (
            $usuario->rol &&
            $usuario->rol->nombre === 'SUPERADMIN'
        ) {
            return $next($request);
        }

        /*
        |--------------------------------------------------------------------------
        | Verificar permiso
        |--------------------------------------------------------------------------
        */
        if (!$usuario->tienePermiso($permiso)) {
            abort(403, 'No tiene permisos para acceder a este módulo.');
        }

        return $next($request);
    }
}