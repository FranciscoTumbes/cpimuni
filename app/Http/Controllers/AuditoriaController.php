<?php

namespace App\Http\Controllers;

use App\Models\Auditoria;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditoriaController extends Controller
{
    public function index(Request $request): View
    {
        $usuario = $request->user();
        $query = Auditoria::with(['usuario', 'municipalidad'])->latest('id');

        if ($usuario->rol?->nombre !== 'SUPERADMIN') {
            $query->where('municipalidad_id', $usuario->municipalidad_id);
        }

        $query->when($request->filled('tabla'), fn ($query) => $query->where('tabla', $request->string('tabla')))
            ->when($request->filled('accion'), fn ($query) => $query->where('accion', $request->string('accion')))
            ->when($request->filled('usuario_id'), fn ($query) => $query->where('usuario_id', $request->integer('usuario_id')))
            ->when($request->filled('desde'), fn ($query) => $query->whereDate('created_at', '>=', $request->date('desde')))
            ->when($request->filled('hasta'), fn ($query) => $query->whereDate('created_at', '<=', $request->date('hasta')));

        return view('admin.auditoria.index', [
            'auditorias' => $query->paginate(25)->withQueryString(),
            'usuarios' => $usuario->rol?->nombre === 'SUPERADMIN'
                ? Usuario::orderBy('apellido')->orderBy('nombre')->get()
                : Usuario::where('municipalidad_id', $usuario->municipalidad_id)->orderBy('apellido')->orderBy('nombre')->get(),
            'acciones' => ['CREAR', 'ACTUALIZAR', 'ELIMINAR', 'LOGIN', 'LOGOUT', 'EXPORTAR', 'APROBAR', 'OBSERVAR'],
        ]);
    }
}
