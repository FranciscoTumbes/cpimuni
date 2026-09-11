<?php

namespace App\Http\Controllers;

use App\Models\Municipalidad;
use App\Models\Permiso;
use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function municipalidades(Request $request): View
    {
        $query = Municipalidad::withCount('usuarios')->orderBy('nombre');
        if ($request->user()->rol?->nombre !== 'SUPERADMIN') {
            $query->whereKey($request->user()->municipalidad_id);
        }
        return view('admin.municipalidades.index', ['municipalidades' => $query->paginate(15)]);
    }

    public function storeMunicipalidad(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'codigo_entidad' => ['nullable', 'string', 'max:20'], 'ruc' => ['nullable', 'string', 'max:20', 'unique:municipalidades,ruc'],
            'nombre' => ['required', 'string', 'max:255'], 'tipo' => ['required', 'in:PROVINCIAL,DISTRITAL'],
            'departamento' => ['nullable', 'string', 'max:100'], 'provincia' => ['nullable', 'string', 'max:100'], 'distrito' => ['nullable', 'string', 'max:100'],
        ]);
        $data['estado'] = 'ACTIVA';
        Municipalidad::create($data);
        return back()->with('success', 'Municipalidad registrada correctamente.');
    }

    public function editMunicipalidad(Municipalidad $municipalidad): View
    {
        return view('admin.municipalidades.edit', compact('municipalidad'));
    }

    public function updateMunicipalidad(Request $request, Municipalidad $municipalidad): RedirectResponse
    {
        $data = $request->validate([
            'codigo_entidad' => ['nullable', 'string', 'max:20'], 'ruc' => ['nullable', 'string', 'max:20', 'unique:municipalidades,ruc,'.$municipalidad->id],
            'nombre' => ['required', 'string', 'max:255'], 'tipo' => ['required', 'in:PROVINCIAL,DISTRITAL'],
            'departamento' => ['nullable', 'string', 'max:100'], 'provincia' => ['nullable', 'string', 'max:100'], 'distrito' => ['nullable', 'string', 'max:100'],
            'estado' => ['required', 'in:ACTIVA,INACTIVA'],
        ]);
        $municipalidad->update($data);
        return redirect()->route('admin.municipalidades')->with('success', 'Municipalidad actualizada correctamente.');
    }

    public function destroyMunicipalidad(Municipalidad $municipalidad): RedirectResponse
    {
        if ($municipalidad->usuarios()->exists()) {
            return back()->withErrors(['municipalidad' => 'No se puede eliminar una municipalidad que tiene usuarios asociados.']);
        }
        $municipalidad->delete();
        return back()->with('success', 'Municipalidad eliminada.');
    }

    public function usuarios(Request $request): View
    {
        $usuario = $request->user();
        $query = Usuario::with(['rol', 'municipalidad'])->orderBy('apellido')->orderBy('nombre');
        if ($usuario->rol?->nombre !== 'SUPERADMIN') $query->where('municipalidad_id', $usuario->municipalidad_id);
        return view('admin.usuarios.index', ['usuarios' => $query->paginate(15), 'roles' => Rol::orderBy('nombre')->get(), 'municipalidades' => $usuario->rol?->nombre === 'SUPERADMIN' ? Municipalidad::orderBy('nombre')->get() : collect()]);
    }

    public function storeUsuario(Request $request): RedirectResponse
    {
        $actor = $request->user();
        $data = $request->validate([
            'municipalidad_id' => ['nullable', 'integer', 'exists:municipalidades,id'], 'rol_id' => ['required', 'exists:roles,id'],
            'nombre' => ['required', 'string', 'max:150'], 'apellido' => ['nullable', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:255', 'unique:usuarios,email'], 'password' => ['required', 'string', 'min:8'],
            'estado' => ['required', 'in:ACTIVO,INACTIVO,BLOQUEADO'],
        ]);
        if ($actor->rol?->nombre !== 'SUPERADMIN') $data['municipalidad_id'] = $actor->municipalidad_id;
        if ($actor->rol?->nombre !== 'SUPERADMIN' && Rol::findOrFail($data['rol_id'])->nombre === 'SUPERADMIN') {
            abort(403, 'No puede asignar el rol SUPERADMIN.');
        }
        $data['password'] = Hash::make($data['password']);
        Usuario::create($data);
        return back()->with('success', 'Usuario registrado correctamente.');
    }

    public function editUsuario(Request $request, Usuario $usuario): View
    {
        $this->ensureUserScope($request, $usuario);
        return view('admin.usuarios.edit', ['usuario' => $usuario, 'roles' => Rol::orderBy('nombre')->get(), 'municipalidades' => $request->user()->rol?->nombre === 'SUPERADMIN' ? Municipalidad::orderBy('nombre')->get() : collect()]);
    }

    public function updateUsuario(Request $request, Usuario $usuario): RedirectResponse
    {
        $this->ensureUserScope($request, $usuario);
        $actor = $request->user();
        $data = $request->validate([
            'municipalidad_id' => ['nullable', 'integer', 'exists:municipalidades,id'], 'rol_id' => ['required', 'exists:roles,id'],
            'nombre' => ['required', 'string', 'max:150'], 'apellido' => ['nullable', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:255', 'unique:usuarios,email,'.$usuario->id], 'password' => ['nullable', 'string', 'min:8'],
            'estado' => ['required', 'in:ACTIVO,INACTIVO,BLOQUEADO'],
        ]);
        if ($actor->rol?->nombre !== 'SUPERADMIN') $data['municipalidad_id'] = $actor->municipalidad_id;
        if ($actor->rol?->nombre !== 'SUPERADMIN' && Rol::findOrFail($data['rol_id'])->nombre === 'SUPERADMIN') {
            abort(403, 'No puede asignar el rol SUPERADMIN.');
        }
        if (blank($data['password'] ?? null)) unset($data['password']); else $data['password'] = Hash::make($data['password']);
        $usuario->update($data);
        return redirect()->route('admin.usuarios')->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroyUsuario(Request $request, Usuario $usuario): RedirectResponse
    {
        $this->ensureUserScope($request, $usuario);
        if ($request->user()->is($usuario)) return back()->withErrors(['usuario' => 'No puede eliminar su propia cuenta.']);
        $usuario->delete();
        return back()->with('success', 'Usuario eliminado.');
    }

    public function roles(): View
    {
        return view('admin.roles.index', ['roles' => Rol::withCount('usuarios')->with('permisos')->orderBy('nombre')->get(), 'permisos' => Permiso::orderBy('modulo')->orderBy('nombre')->get()]);
    }

    public function updateRol(Request $request, Rol $rol): RedirectResponse
    {
        $data = $request->validate(['descripcion' => ['nullable', 'string', 'max:255'], 'permisos' => ['array'], 'permisos.*' => ['integer', 'exists:permisos,id']]);
        $rol->update(['descripcion' => $data['descripcion'] ?? null]);
        $rol->permisos()->sync($data['permisos'] ?? []);
        return back()->with('success', 'Permisos del rol actualizados.');
    }

    public function storeRol(Request $request): RedirectResponse
    {
        $data = $request->validate(['nombre' => ['required', 'string', 'max:100', 'unique:roles,nombre'], 'descripcion' => ['nullable', 'string', 'max:255']]);
        Rol::create($data);
        return back()->with('success', 'Rol creado correctamente.');
    }

    public function destroyRol(Rol $rol): RedirectResponse
    {
        if ($rol->nombre === 'SUPERADMIN' || $rol->usuarios()->exists()) return back()->withErrors(['rol' => 'No se puede eliminar este rol porque está protegido o tiene usuarios asociados.']);
        $rol->delete();
        return back()->with('success', 'Rol eliminado.');
    }

    public function storePermiso(Request $request): RedirectResponse
    {
        $data = $request->validate(['nombre' => ['required', 'string', 'max:150', 'unique:permisos,nombre'], 'modulo' => ['nullable', 'string', 'max:100'], 'descripcion' => ['nullable', 'string', 'max:255']]);
        Permiso::create($data);
        return back()->with('success', 'Permiso creado correctamente.');
    }

    public function updatePermiso(Request $request, Permiso $permiso): RedirectResponse
    {
        $data = $request->validate(['nombre' => ['required', 'string', 'max:150', 'unique:permisos,nombre,'.$permiso->id], 'modulo' => ['nullable', 'string', 'max:100'], 'descripcion' => ['nullable', 'string', 'max:255']]);
        $permiso->update($data);
        return back()->with('success', 'Permiso actualizado correctamente.');
    }

    public function destroyPermiso(Permiso $permiso): RedirectResponse
    {
        if ($permiso->roles()->exists()) return back()->withErrors(['permiso' => 'No se puede eliminar un permiso asignado a roles.']);
        $permiso->delete();
        return back()->with('success', 'Permiso eliminado.');
    }

    private function ensureUserScope(Request $request, Usuario $usuario): void
    {
        abort_unless(
            $request->user()->rol?->nombre === 'SUPERADMIN' || $usuario->municipalidad_id === $request->user()->municipalidad_id,
            403,
            'No puede administrar usuarios de otra municipalidad.'
        );
    }
}
