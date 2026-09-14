<?php

namespace App\Http\Controllers;

use App\Models\Municipalidad;
use App\Models\Permiso;
use App\Models\Rol;
use App\Models\Usuario;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use App\Services\MunicipalidadCloneService;

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

    public function duplicateMunicipalidad(Request $request, Municipalidad $municipalidad, MunicipalidadCloneService $cloneService): RedirectResponse
    {
        abort_unless($request->user()->rol?->nombre === 'SUPERADMIN', 403);

        $data = $request->validate([
            'codigo_entidad' => ['nullable', 'string', 'max:20'],
            'ruc' => ['nullable', 'string', 'max:20', 'unique:municipalidades,ruc'],
            'nombre' => ['required', 'string', 'max:255'],
            'tipo' => ['required', 'in:PROVINCIAL,DISTRITAL'],
            'departamento' => ['nullable', 'string', 'max:100'],
            'provincia' => ['nullable', 'string', 'max:100'],
            'distrito' => ['nullable', 'string', 'max:100'],
            'estructura' => ['sometimes', 'boolean'],
            'puestos' => ['sometimes', 'boolean'],
            'funciones' => ['sometimes', 'boolean'],
            'normativa' => ['sometimes', 'boolean'],
            'instrumentos' => ['sometimes', 'boolean'],
        ]);

        $options = collect(['estructura', 'puestos', 'funciones', 'normativa', 'instrumentos'])
            ->mapWithKeys(fn (string $option) => [$option => (bool) ($data[$option] ?? false)])
            ->all();
        abort_unless(!($options['puestos'] || $options['funciones']) || $options['estructura'], 422, 'Puestos y funciones requieren copiar la estructura.');

        unset($data['estructura'], $data['puestos'], $data['funciones'], $data['normativa'], $data['instrumentos']);
        $cloneService->clone($municipalidad, $data, $options);

        return redirect()->route('admin.municipalidades')->with('success', 'Municipalidad duplicada con la estructura seleccionada.');
    }

    public function editMunicipalidad(Municipalidad $municipalidad): View
    {
        Gate::authorize('view', $municipalidad);
        return view('admin.municipalidades.edit', compact('municipalidad'));
    }

    public function updateMunicipalidad(Request $request, Municipalidad $municipalidad): RedirectResponse
    {
        Gate::authorize('update', $municipalidad);
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
        Gate::authorize('delete', $municipalidad);
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
        $this->ensureActiveMunicipality($data['municipalidad_id'] ?? null);
        $data['password'] = Hash::make($data['password']);
        Usuario::create($data);
        return back()->with('success', 'Usuario registrado correctamente.');
    }

    public function editUsuario(Request $request, Usuario $usuario): View
    {
        Gate::authorize('view', $usuario);
        return view('admin.usuarios.edit', ['usuario' => $usuario, 'roles' => Rol::orderBy('nombre')->get(), 'municipalidades' => $request->user()->rol?->nombre === 'SUPERADMIN' ? Municipalidad::orderBy('nombre')->get() : collect()]);
    }

    public function updateUsuario(Request $request, Usuario $usuario): RedirectResponse
    {
        Gate::authorize('update', $usuario);
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
        $this->ensureActiveMunicipality($data['municipalidad_id'] ?? null);
        $this->ensureAccountProtection($actor, $usuario, $data);
        if (blank($data['password'] ?? null)) unset($data['password']); else $data['password'] = Hash::make($data['password']);
        $usuario->update($data);
        return redirect()->route('admin.usuarios')->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroyUsuario(Request $request, Usuario $usuario): RedirectResponse
    {
        Gate::authorize('delete', $usuario);
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
        Gate::authorize('update', $rol);
        $data = $request->validate(['descripcion' => ['nullable', 'string', 'max:255'], 'permisos' => ['array'], 'permisos.*' => ['integer', 'exists:permisos,id']]);
        if ($rol->nombre === 'ADMIN_MUNICIPAL') {
            $permisos = Permiso::whereIn('nombre', ['usuarios.gestionar', 'auditoria.ver'])->pluck('id');
            abort_unless($permisos->diff($data['permisos'] ?? [])->isEmpty(), 422, 'El rol ADMIN_MUNICIPAL debe conservar sus permisos críticos.');
        }
        $permisosAnteriores = $rol->permisos()->pluck('nombre')->sort()->values()->all();
        $rol->update(['descripcion' => $data['descripcion'] ?? null]);
        $rol->permisos()->sync($data['permisos'] ?? []);
        $permisosNuevos = $rol->permisos()->pluck('nombre')->sort()->values()->all();
        AuditService::event(
            'roles',
            'ACTUALIZAR',
            $rol->id,
            null,
            ['permisos' => $permisosAnteriores],
            ['descripcion' => $rol->descripcion, 'permisos' => $permisosNuevos],
            'Actualización de permisos del rol.'
        );
        return back()->with('success', 'Permisos del rol actualizados.');
    }

    public function storeRol(Request $request): RedirectResponse
    {
        Gate::authorize('create', Rol::class);
        $data = $request->validate(['nombre' => ['required', 'string', 'max:100', 'unique:roles,nombre'], 'descripcion' => ['nullable', 'string', 'max:255']]);
        Rol::create($data);
        return back()->with('success', 'Rol creado correctamente.');
    }

    public function destroyRol(Rol $rol): RedirectResponse
    {
        Gate::authorize('delete', $rol);
        if ($rol->nombre === 'SUPERADMIN' || $rol->usuarios()->exists()) return back()->withErrors(['rol' => 'No se puede eliminar este rol porque está protegido o tiene usuarios asociados.']);
        $rol->delete();
        return back()->with('success', 'Rol eliminado.');
    }

    public function storePermiso(Request $request): RedirectResponse
    {
        Gate::authorize('create', Permiso::class);
        $data = $request->validate(['nombre' => ['required', 'string', 'max:150', 'unique:permisos,nombre'], 'modulo' => ['nullable', 'string', 'max:100'], 'descripcion' => ['nullable', 'string', 'max:255']]);
        Permiso::create($data);
        return back()->with('success', 'Permiso creado correctamente.');
    }

    public function updatePermiso(Request $request, Permiso $permiso): RedirectResponse
    {
        Gate::authorize('update', $permiso);
        $data = $request->validate(['nombre' => ['required', 'string', 'max:150', 'unique:permisos,nombre,'.$permiso->id], 'modulo' => ['nullable', 'string', 'max:100'], 'descripcion' => ['nullable', 'string', 'max:255']]);
        $permiso->update($data);
        return back()->with('success', 'Permiso actualizado correctamente.');
    }

    public function destroyPermiso(Permiso $permiso): RedirectResponse
    {
        Gate::authorize('delete', $permiso);
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

    private function ensureActiveMunicipality(?int $municipalidadId): void
    {
        if ($municipalidadId && Municipalidad::whereKey($municipalidadId)->where('estado', 'ACTIVA')->doesntExist()) {
            abort(422, 'La municipalidad seleccionada no está activa.');
        }
    }

    private function ensureAccountProtection(Usuario $actor, Usuario $target, array $data): void
    {
        if ($actor->is($target) && (($data['rol_id'] ?? $target->rol_id) !== $target->rol_id || ($data['estado'] ?? $target->estado) !== $target->estado)) {
            abort(403, 'No puede cambiar su propio rol o estado.');
        }

        $isActiveMunicipalAdmin = $target->rol?->nombre === 'ADMIN_MUNICIPAL' && $target->estado === 'ACTIVO';
        $remainsActiveMunicipalAdmin = Rol::whereKey($data['rol_id'])->where('nombre', 'ADMIN_MUNICIPAL')->exists() && ($data['estado'] ?? $target->estado) === 'ACTIVO';
        if ($isActiveMunicipalAdmin && ! $remainsActiveMunicipalAdmin && $target->municipalidad_id && Usuario::where('municipalidad_id', $target->municipalidad_id)->where('rol_id', $target->rol_id)->where('estado', 'ACTIVO')->where('id', '!=', $target->id)->doesntExist()) {
            abort(422, 'No puede dejar la municipalidad sin un administrador activo.');
        }
    }
}
