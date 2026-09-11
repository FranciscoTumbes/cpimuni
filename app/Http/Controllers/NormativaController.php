<?php

namespace App\Http\Controllers;

use App\Models\Norma;
use App\Models\Municipalidad;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class NormativaController extends Controller
{
    public function index(Request $request): View
    {
        $query = Norma::with('municipalidad')->latest();
        if ($request->user()->rol?->nombre !== 'SUPERADMIN') $query->where('municipalidad_id', $request->user()->municipalidad_id);
        return view('normativa.index', ['normas' => $query->paginate(15), 'municipalidades' => Municipalidad::orderBy('nombre')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['municipalidad_id' => ['nullable', 'exists:municipalidades,id'], 'tipo' => ['required', 'string', 'max:100'], 'numero' => ['nullable', 'string', 'max:100'], 'titulo' => ['required', 'string', 'max:500'], 'fecha_emision' => ['nullable', 'date'], 'fecha_vigencia' => ['nullable', 'date'], 'enlace' => ['nullable', 'url', 'max:1000'], 'resumen' => ['nullable', 'string']]);
        $data['municipalidad_id'] = $request->user()->rol?->nombre === 'SUPERADMIN' ? ($data['municipalidad_id'] ?? null) : $request->user()->municipalidad_id;
        abort_unless($data['municipalidad_id'], 422, 'Seleccione una municipalidad.');
        $data['estado'] = 'VIGENTE';
        $norma = Norma::make($data);
        Gate::authorize('update', $norma);
        $norma->save();
        return back()->with('success', 'Norma registrada.');
    }
}
