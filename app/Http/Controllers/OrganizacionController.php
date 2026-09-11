<?php

namespace App\Http\Controllers;

use App\Models\Funcion;
use App\Models\Municipalidad;
use App\Models\Organo;
use App\Models\Puesto;
use App\Models\UnidadOrganica;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;
use Illuminate\View\View;
use Illuminate\Database\Eloquent\Model;

class OrganizacionController extends Controller
{
    public function index(Request $request): View
    {
        $municipalidadId = $request->user()->municipalidad_id;
        $scope = fn ($query) => $request->user()->rol?->nombre === 'SUPERADMIN' ? $query : $query->where('municipalidad_id', $municipalidadId);
        return view('organizacion.index', [
            'organos' => $scope(Organo::query())->with('padre')->orderBy('nombre')->get(),
            'unidades' => $scope(UnidadOrganica::query())->with('organo', 'padre')->orderBy('nombre')->get(),
            'puestos' => $scope(Puesto::query())->with('unidad')->orderBy('denominacion')->get(),
            'funciones' => $scope(Funcion::query())->with('unidad', 'puesto')->orderBy('codigo')->get(),
            'municipalidades' => Municipalidad::orderBy('nombre')->get(),
        ]);
    }

    public function storeOrgano(Request $request): RedirectResponse
    {
        $municipalidadId = $this->municipalidadId($request, $request->input('municipalidad_id'));
        $data = $request->validate(['municipalidad_id' => ['required', 'exists:municipalidades,id'], 'codigo' => ['nullable', 'string', 'max:50'], 'nombre' => ['required', 'string', 'max:255'], 'tipo' => ['nullable', 'string', 'max:100'], 'nivel_jerarquico' => ['nullable', 'integer'], 'organo_padre_id' => ['nullable', $this->belongsToMunicipality('organos', $municipalidadId)]]);
        $data['municipalidad_id'] = $municipalidadId;
        Organo::create($data);
        return back()->with('success', 'Órgano registrado.');
    }

    public function storeUnidad(Request $request): RedirectResponse
    {
        $municipalidadId = $this->municipalidadId($request, $request->input('municipalidad_id'));
        $data = $request->validate(['municipalidad_id' => ['required', 'exists:municipalidades,id'], 'organo_id' => ['nullable', $this->belongsToMunicipality('organos', $municipalidadId)], 'codigo' => ['nullable', 'string', 'max:50'], 'nombre' => ['required', 'string', 'max:255'], 'tipo' => ['nullable', 'string', 'max:100'], 'unidad_padre_id' => ['nullable', $this->belongsToMunicipality('unidades_organicas', $municipalidadId)], 'finalidad' => ['nullable', 'string']]);
        $data['municipalidad_id'] = $municipalidadId;
        UnidadOrganica::create($data);
        return back()->with('success', 'Unidad orgánica registrada.');
    }

    public function storePuesto(Request $request): RedirectResponse
    {
        $municipalidadId = $this->municipalidadId($request, $request->input('municipalidad_id'));
        $data = $request->validate(['municipalidad_id' => ['required', 'exists:municipalidades,id'], 'unidad_organica_id' => ['nullable', $this->belongsToMunicipality('unidades_organicas', $municipalidadId)], 'codigo' => ['nullable', 'string', 'max:50'], 'denominacion' => ['required', 'string', 'max:255'], 'nivel' => ['nullable', 'string', 'max:100'], 'finalidad' => ['nullable', 'string']]);
        $data['municipalidad_id'] = $municipalidadId;
        Puesto::create($data);
        return back()->with('success', 'Puesto registrado.');
    }

    public function storeFuncion(Request $request): RedirectResponse
    {
        $municipalidadId = $this->municipalidadId($request, $request->input('municipalidad_id'));
        $data = $request->validate(['municipalidad_id' => ['required', 'exists:municipalidades,id'], 'unidad_organica_id' => ['nullable', $this->belongsToMunicipality('unidades_organicas', $municipalidadId)], 'puesto_id' => ['nullable', $this->belongsToMunicipality('puestos', $municipalidadId)], 'codigo' => ['nullable', 'string', 'max:50'], 'descripcion' => ['required', 'string'], 'tipo' => ['nullable', 'string', 'max:100'], 'fuente' => ['nullable', 'string', 'max:100'], 'fecha_inicio' => ['nullable', 'date'], 'fecha_fin' => ['nullable', 'date', 'after_or_equal:fecha_inicio']]);
        $data['municipalidad_id'] = $municipalidadId;
        Funcion::create($data);
        return back()->with('success', 'Función registrada.');
    }

    public function edit(Request $request, string $tipo, int $id): View
    {
        $model = $this->findModel($request, $tipo, $id);
        return view('organizacion.edit', [
            'tipo' => $tipo,
            'model' => $model,
            'municipalidades' => Municipalidad::orderBy('nombre')->get(),
            'organos' => $this->scope($request, Organo::query())->orderBy('nombre')->get(),
            'unidades' => $this->scope($request, UnidadOrganica::query())->orderBy('nombre')->get(),
            'puestos' => $this->scope($request, Puesto::query())->orderBy('denominacion')->get(),
        ]);
    }

    public function update(Request $request, string $tipo, int $id): RedirectResponse
    {
        $model = $this->findModel($request, $tipo, $id);
        $municipalidadId = (int) $model->municipalidad_id;
        $data = $this->validatedData($request, $tipo, $municipalidadId, $model->id);
        $this->validateHierarchy($tipo, $model, $data);
        $model->update($data);
        return redirect()->route('organizacion.index')->with('success', 'Registro organizacional actualizado.');
    }

    public function destroy(Request $request, string $tipo, int $id): RedirectResponse
    {
        $model = $this->findModel($request, $tipo, $id);
        $this->ensureCanDeactivate($tipo, $model);
        $model->update(['estado' => $this->inactiveState($tipo)]);
        return back()->with('success', 'Registro organizacional dado de baja lógica.');
    }

    private function findModel(Request $request, string $tipo, int $id): Model
    {
        $class = $this->modelClass($tipo);
        $model = $this->scope($request, $class::query())->whereKey($id)->firstOrFail();
        abort_unless($model->municipalidad_id, 404);
        return $model;
    }

    private function modelClass(string $tipo): string
    {
        return match ($tipo) {
            'organos' => Organo::class,
            'unidades' => UnidadOrganica::class,
            'puestos' => Puesto::class,
            'funciones' => Funcion::class,
            default => abort(404),
        };
    }

    private function scope(Request $request, $query)
    {
        return $request->user()->rol?->nombre === 'SUPERADMIN'
            ? $query
            : $query->where('municipalidad_id', $request->user()->municipalidad_id);
    }

    private function validatedData(Request $request, string $tipo, int $municipalidadId, int $id): array
    {
        $common = [
            'codigo' => ['nullable', 'string', 'max:50'],
            'nombre' => ['required', 'string', 'max:255'],
            'tipo' => ['nullable', 'string', 'max:100'],
        ];

        return match ($tipo) {
            'organos' => $request->validate($common + [
                'nivel_jerarquico' => ['nullable', 'integer', 'min:0'],
                'organo_padre_id' => ['nullable', $this->belongsToMunicipality('organos', $municipalidadId)],
                'estado' => ['required', 'in:ACTIVO,INACTIVO'],
            ]),
            'unidades' => $request->validate($common + [
                'organo_id' => ['nullable', $this->belongsToMunicipality('organos', $municipalidadId)],
                'unidad_padre_id' => ['nullable', $this->belongsToMunicipality('unidades_organicas', $municipalidadId)],
                'nivel_jerarquico' => ['nullable', 'integer', 'min:0'],
                'finalidad' => ['nullable', 'string'],
                'estado' => ['required', 'in:ACTIVA,INACTIVA'],
            ]),
            'puestos' => $request->validate([
                'unidad_organica_id' => ['nullable', $this->belongsToMunicipality('unidades_organicas', $municipalidadId)],
                'codigo' => ['nullable', 'string', 'max:50'],
                'denominacion' => ['required', 'string', 'max:255'],
                'nivel' => ['nullable', 'string', 'max:100'],
                'finalidad' => ['nullable', 'string'],
                'requisitos' => ['nullable', 'string'],
                'competencias' => ['nullable', 'string'],
                'estado' => ['required', 'in:ACTIVO,INACTIVO'],
            ]),
            'funciones' => $request->validate([
                'unidad_organica_id' => ['nullable', $this->belongsToMunicipality('unidades_organicas', $municipalidadId)],
                'puesto_id' => ['nullable', $this->belongsToMunicipality('puestos', $municipalidadId)],
                'codigo' => ['nullable', 'string', 'max:50'],
                'descripcion' => ['required', 'string'],
                'tipo' => ['nullable', 'string', 'max:100'],
                'fuente' => ['nullable', 'string', 'max:100'],
                'fecha_inicio' => ['nullable', 'date'],
                'fecha_fin' => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
                'estado' => ['required', 'in:VIGENTE,NO_VIGENTE,EN_REVISION'],
            ]),
            default => abort(404),
        };
    }

    private function validateHierarchy(string $tipo, Model $model, array $data): void
    {
        $parentId = $tipo === 'organos' ? ($data['organo_padre_id'] ?? null) : ($data['unidad_padre_id'] ?? null);
        if (!$parentId) return;
        abort_if((int) $parentId === (int) $model->id, 422, 'Un registro no puede ser su propio padre.');

        $parent = $tipo === 'organos' ? Organo::findOrFail($parentId) : UnidadOrganica::findOrFail($parentId);
        $visited = [$model->id];
        while ($parent) {
            abort_if(in_array($parent->id, $visited, true), 422, 'La jerarquía contiene un ciclo.');
            $visited[] = $parent->id;
            $parent = $tipo === 'organos' ? $parent->padre : $parent->padre;
        }
    }

    private function ensureCanDeactivate(string $tipo, Model $model): void
    {
        $hasChildren = match ($tipo) {
            'organos' => $model->hijos()->exists() || $model->hasMany(UnidadOrganica::class, 'organo_id')->exists(),
            'unidades' => $model->hijos()->exists() || $model->hasMany(Puesto::class, 'unidad_organica_id')->exists() || $model->hasMany(Funcion::class, 'unidad_organica_id')->exists(),
            'puestos' => $model->hasMany(Funcion::class, 'puesto_id')->exists(),
            'funciones' => false,
            default => false,
        };
        abort_if($hasChildren, 422, 'No se puede dar de baja un registro con dependencias activas.');
    }

    private function inactiveState(string $tipo): string
    {
        return match ($tipo) {
            'organos', 'puestos' => 'INACTIVO',
            'unidades' => 'INACTIVA',
            'funciones' => 'NO_VIGENTE',
            default => abort(404),
        };
    }

    private function belongsToMunicipality(string $table, int $municipalidadId): Exists
    {
        return Rule::exists($table, 'id')->where(fn ($query) => $query->where('municipalidad_id', $municipalidadId));
    }

    private function municipalidadId(Request $request, ?int $selected): int
    {
        $user = $request->user();
        if ($user->rol?->nombre !== 'SUPERADMIN') return (int) $user->municipalidad_id;
        abort_unless($selected, 422, 'Seleccione una municipalidad.');
        return $selected;
    }
}
