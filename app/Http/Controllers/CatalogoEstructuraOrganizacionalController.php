<?php

namespace App\Http\Controllers;

use App\Models\CatalogoEstructuraOrganizacional;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogoEstructuraOrganizacionalController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->rol?->nombre === 'SUPERADMIN', 403);

        $query = CatalogoEstructuraOrganizacional::query();

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('codigo', 'like', "%{$search}%")
                  ->orWhere('nombre', 'like', "%{$search}%")
                  ->orWhere('codigo_padre', 'like', "%{$search}%");
            });
        }

        if ($request->filled('categoria')) {
            $query->where('categoria', $request->input('categoria'));
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->input('estado'));
        }

        $items = $query
            ->orderBy('categoria')
            ->orderBy('orden')
            ->orderBy('codigo')
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'total' => CatalogoEstructuraOrganizacional::count(),
            'activos' => CatalogoEstructuraOrganizacional::where('estado', 'ACTIVO')->count(),
            'inactivos' => CatalogoEstructuraOrganizacional::where('estado', 'INACTIVO')->count(),
            'niveles' => CatalogoEstructuraOrganizacional::distinct('nivel')->count('nivel'),
        ];

        return view('admin.catalogos.estructura-organizacional', [
            'items' => $items,
            'stats' => $stats,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->rol?->nombre === 'SUPERADMIN', 403);

        $data = $request->validate([
            'codigo' => ['required', 'string', 'max:50', 'unique:catalogo_estructura_organizacional,codigo'],
            'codigo_padre' => ['nullable', 'string', 'max:50'],
            'nombre' => ['required', 'string', 'max:255'],
            'categoria' => ['required', 'string', 'max:50'],
            'tipo' => ['required', 'string', 'max:100'],
            'nivel' => ['required', 'integer', 'min:1'],
            'descripcion' => ['nullable', 'string'],
            'permite_hijos' => ['nullable', 'boolean'],
            'orden' => ['nullable', 'integer', 'min:0'],
            'estado' => ['required', 'in:ACTIVO,INACTIVO'],
        ]);

        if (!empty($data['codigo_padre'])) {
            $data['parent_id'] = CatalogoEstructuraOrganizacional::where('codigo', $data['codigo_padre'])->value('id');
        }

        CatalogoEstructuraOrganizacional::create($data);

        return redirect()->route('catalogos.estructura.index')->with('success', 'Elemento del catálogo registrado.');
    }

    public function update(Request $request, CatalogoEstructuraOrganizacional $catalogo): RedirectResponse
    {
        abort_unless($request->user()->rol?->nombre === 'SUPERADMIN', 403);

        $data = $request->validate([
            'codigo' => ['required', 'string', 'max:50', 'unique:catalogo_estructura_organizacional,codigo,' . $catalogo->id],
            'codigo_padre' => ['nullable', 'string', 'max:50'],
            'nombre' => ['required', 'string', 'max:255'],
            'categoria' => ['required', 'string', 'max:50'],
            'tipo' => ['required', 'string', 'max:100'],
            'nivel' => ['required', 'integer', 'min:1'],
            'descripcion' => ['nullable', 'string'],
            'permite_hijos' => ['nullable', 'boolean'],
            'orden' => ['nullable', 'integer', 'min:0'],
            'estado' => ['required', 'in:ACTIVO,INACTIVO'],
        ]);

        if (!empty($data['codigo_padre'])) {
            $data['parent_id'] = CatalogoEstructuraOrganizacional::where('codigo', $data['codigo_padre'])->value('id');
        } else {
            $data['parent_id'] = null;
            $data['codigo_padre'] = null;
        }

        $catalogo->update($data);

        return redirect()->route('catalogos.estructura.index')->with('success', 'Elemento del catálogo actualizado.');
    }

    public function destroy(Request $request, CatalogoEstructuraOrganizacional $catalogo): RedirectResponse
    {
        abort_unless($request->user()->rol?->nombre === 'SUPERADMIN', 403);

        $catalogo->update(['estado' => 'INACTIVO']);

        return redirect()->route('catalogos.estructura.index')->with('success', 'Elemento del catálogo desactivado.');
    }
}
