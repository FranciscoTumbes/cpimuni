<?php

namespace App\Http\Controllers;

use App\Models\Instrumento;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InstrumentoController extends Controller
{
    public function index(Request $request): View
    {
        $tipo = $request->string('tipo')->trim()->value();
        $query = Instrumento::with('municipalidad')->latest();

        if ($request->user()->rol?->nombre !== 'SUPERADMIN') {
            $query->where('municipalidad_id', $request->user()->municipalidad_id);
        }

        $tipos = $tipo === '' ? [] : array_filter(array_map('trim', explode(',', $tipo)));
        if ($tipos !== []) {
            $query->whereIn('tipo', $tipos);
        }

        return view('instrumentos.index', [
            'instrumentos' => $query->paginate(15)->withQueryString(),
            'tipo' => $tipo,
        ]);
    }
}
