<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Usuario;

class DashboardController extends Controller
{
    public function index()
    {
        $usuario = Auth::user();

        $municipalidad = $usuario->municipalidad;

        /*
        |--------------------------------------------------------------------------
        | Estadísticas básicas
        |--------------------------------------------------------------------------
        */

        $usuarios = 0;

        if ($municipalidad) {
            $usuarios = Usuario::where(
                'municipalidad_id',
                $municipalidad->id
            )->count();
        }

        return view('dashboard', [
            'usuario' => $usuario,
            'municipalidad' => $municipalidad,
            'usuarios' => $usuarios,
        ]);
    }
}