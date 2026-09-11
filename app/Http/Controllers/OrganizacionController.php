<?php

namespace App\Http\Controllers;

use App\Models\Funcion;
use App\Models\Municipalidad;
use App\Models\Organo;
use App\Models\Puesto;
use App\Models\UnidadOrganica;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

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
        $data = $request->validate(['municipalidad_id' => ['nullable', 'exists:municipalidades,id'], 'codigo' => ['nullable', 'string', 'max:50'], 'nombre' => ['required', 'string', 'max:255'], 'tipo' => ['nullable', 'string', 'max:100'], 'nivel_jerarquico' => ['nullable', 'integer'], 'organo_padre_id' => ['nullable', 'exists:organos,id']]);
        $data['municipalidad_id'] = $this->municipalidadId($request, $data['municipalidad_id'] ?? null);
        Organo::create($data);
        return back()->with('success', 'Órgano registrado.');
    }

    public function storeUnidad(Request $request): RedirectResponse
    {
        $data = $request->validate(['municipalidad_id' => ['nullable', 'exists:municipalidades,id'], 'organo_id' => ['nullable', 'exists:organos,id'], 'codigo' => ['nullable', 'string', 'max:50'], 'nombre' => ['required', 'string', 'max:255'], 'tipo' => ['nullable', 'string', 'max:100'], 'unidad_padre_id' => ['nullable', 'exists:unidades_organicas,id'], 'finalidad' => ['nullable', 'string']]);
        $data['municipalidad_id'] = $this->municipalidadId($request, $data['municipalidad_id'] ?? null);
        UnidadOrganica::create($data);
        return back()->with('success', 'Unidad orgánica registrada.');
    }

    public function storePuesto(Request $request): RedirectResponse
    {
        $data = $request->validate(['municipalidad_id' => ['nullable', 'exists:municipalidades,id'], 'unidad_organica_id' => ['nullable', 'exists:unidades_organicas,id'], 'codigo' => ['nullable', 'string', 'max:50'], 'denominacion' => ['required', 'string', 'max:255'], 'nivel' => ['nullable', 'string', 'max:100'], 'finalidad' => ['nullable', 'string']]);
        $data['municipalidad_id'] = $this->municipalidadId($request, $data['municipalidad_id'] ?? null);
        Puesto::create($data);
        return back()->with('success', 'Puesto registrado.');
    }

    public function storeFuncion(Request $request): RedirectResponse
    {
        $data = $request->validate(['municipalidad_id' => ['nullable', 'exists:municipalidades,id'], 'unidad_organica_id' => ['nullable', 'exists:unidades_organicas,id'], 'puesto_id' => ['nullable', 'exists:puestos,id'], 'codigo' => ['nullable', 'string', 'max:50'], 'descripcion' => ['required', 'string'], 'tipo' => ['nullable', 'string', 'max:100'], 'fuente' => ['nullable', 'string', 'max:100'], 'fecha_inicio' => ['nullable', 'date'], 'fecha_fin' => ['nullable', 'date', 'after_or_equal:fecha_inicio']]);
        $data['municipalidad_id'] = $this->municipalidadId($request, $data['municipalidad_id'] ?? null);
        Funcion::create($data);
        return back()->with('success', 'Función registrada.');
    }

    private function municipalidadId(Request $request, ?int $selected): int
    {
        $user = $request->user();
        if ($user->rol?->nombre !== 'SUPERADMIN') return (int) $user->municipalidad_id;
        abort_unless($selected, 422, 'Seleccione una municipalidad.');
        return $selected;
    }
}
