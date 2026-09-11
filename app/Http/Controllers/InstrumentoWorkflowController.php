<?php

namespace App\Http\Controllers;

use App\Models\Aprobacion;
use App\Models\Documento;
use App\Models\Instrumento;
use App\Models\InstrumentoVersion;
use App\Models\Observacion;
use App\Models\Revision;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class InstrumentoWorkflowController extends Controller
{
    public function show(Instrumento $instrumento): View
    {
        Gate::authorize('view', $instrumento);

        return view('instrumentos.show', [
            'instrumento' => $instrumento->load([
                'municipalidad',
                'versiones.usuario',
                'versiones.revisiones.usuario',
                'versiones.revisiones.observaciones.usuario',
                'versiones.aprobaciones.usuario',
                'documentos.usuario',
            ]),
        ]);
    }

    public function storeVersion(Request $request, Instrumento $instrumento): RedirectResponse
    {
        Gate::authorize('update', $instrumento);
        $data = $request->validate([
            'version' => ['required', 'string', 'max:30', Rule::unique('instrumento_versiones', 'version')->where('instrumento_id', $instrumento->id)],
            'motivo' => ['nullable', 'string'],
            'archivo_docx' => ['nullable', 'string', 'max:500'],
            'archivo_pdf' => ['nullable', 'string', 'max:500'],
        ]);

        $version = new InstrumentoVersion($data);
        $version->instrumento()->associate($instrumento);
        $version->usuario_id = $request->user()->id;
        $version->estado = 'BORRADOR';
        $version->save();

        return back()->with('success', 'Versión documental creada en estado BORRADOR.');
    }

    public function storeDocument(Request $request, Instrumento $instrumento): RedirectResponse
    {
        Gate::authorize('update', $instrumento);
        $data = $request->validate([
            'archivo' => ['required', 'file', 'max:20480', 'mimes:pdf,doc,docx,xls,xlsx'],
            'tipo' => ['nullable', 'string', 'max:100'],
        ]);
        $archivo = $data['archivo'];
        $ruta = $archivo->store('instrumentos/'.$instrumento->id, 'private');

        Documento::create([
            'municipalidad_id' => $instrumento->municipalidad_id,
            'instrumento_id' => $instrumento->id,
            'nombre' => $archivo->getClientOriginalName(),
            'tipo' => $data['tipo'] ?? $archivo->getClientMimeType(),
            'ruta' => $ruta,
            'hash_archivo' => hash_file('sha256', $archivo->getRealPath()),
            'tamano' => $archivo->getSize(),
            'usuario_id' => $request->user()->id,
        ]);

        return back()->with('success', 'Documento incorporado al expediente.');
    }

    public function submitRevision(Request $request, InstrumentoVersion $version): RedirectResponse
    {
        Gate::authorize('submit', $version);
        abort_unless(in_array($version->estado, ['BORRADOR', 'OBSERVADO'], true), 422, 'La versión no puede enviarse a revisión desde su estado actual.');

        DB::transaction(function () use ($request, $version): void {
            $version->update(['estado' => 'EN_REVISION']);
            Revision::create([
                'instrumento_version_id' => $version->id,
                'usuario_id' => $request->user()->id,
                'tipo' => 'TECNICA',
                'estado' => 'PENDIENTE',
                'fecha_inicio' => now(),
            ]);
        });

        return back()->with('success', 'Versión enviada a revisión.');
    }

    public function storeObservation(Request $request, InstrumentoVersion $version): RedirectResponse
    {
        Gate::authorize('observe', $version);
        abort_unless($version->estado === 'EN_REVISION', 422, 'Solo se pueden observar versiones en revisión.');
        $data = $request->validate(['observacion' => ['required', 'string']]);

        DB::transaction(function () use ($request, $version, $data): void {
            $revision = $version->revisiones()->where('estado', 'PENDIENTE')->latest('id')->firstOrFail();
            $revision->update(['estado' => 'OBSERVADA']);
            $observacion = $revision->observaciones()->create([
                'usuario_id' => $request->user()->id,
                'observacion' => $data['observacion'],
                'estado' => 'PENDIENTE',
            ]);
            $version->update(['estado' => 'OBSERVADO']);
            AuditService::event('observaciones', 'OBSERVAR', $observacion->id, $version->instrumento->municipalidad_id, null, ['observacion' => $data['observacion']], 'Observación registrada sobre la versión.');
        });

        return back()->with('success', 'Observación registrada.');
    }

    public function respondObservation(Request $request, Observacion $observacion): RedirectResponse
    {
        $observacion->load('revision.version.instrumento');
        $version = $observacion->revision->version;
        abort_unless($version->usuario_id === $request->user()->id, 403, 'Solo el responsable de la versión puede responder la observación.');
        abort_unless($observacion->estado === 'PENDIENTE', 422, 'La observación ya fue atendida.');
        $data = $request->validate(['respuesta' => ['required', 'string']]);

        DB::transaction(function () use ($observacion, $version, $data): void {
            $observacion->update(['respuesta' => $data['respuesta'], 'estado' => 'SUBSANADA']);
            $observacion->revision->update(['estado' => 'PENDIENTE']);
            $version->update(['estado' => 'EN_REVISION']);
        });

        return back()->with('success', 'Respuesta registrada y versión devuelta a revisión.');
    }

    public function decide(Request $request, InstrumentoVersion $version): RedirectResponse
    {
        Gate::authorize('approve', $version);
        abort_unless(in_array($version->estado, ['EN_REVISION', 'OBSERVADO'], true), 422, 'La versión no está lista para decisión.');
        $data = $request->validate([
            'resultado' => ['required', 'in:APROBADO,RECHAZADO'],
            'tipo' => ['required', 'string', 'max:100'],
            'comentario' => ['nullable', 'string'],
        ]);
        abort_if($version->revisiones()->whereHas('observaciones', fn ($query) => $query->where('estado', 'PENDIENTE'))->exists(), 422, 'No se puede decidir mientras existan observaciones pendientes.');

        DB::transaction(function () use ($request, $version, $data): void {
            Aprobacion::create([
                'instrumento_version_id' => $version->id,
                'usuario_id' => $request->user()->id,
                'tipo' => $data['tipo'],
                'resultado' => $data['resultado'],
                'comentario' => $data['comentario'] ?? null,
            ]);
            $nuevoEstado = $data['resultado'] === 'APROBADO' ? 'APROBADO' : 'OBSERVADO';
            $version->update(['estado' => $nuevoEstado]);
            $version->instrumento->update(['estado' => $nuevoEstado]);
            AuditService::event('aprobaciones', 'APROBAR', $version->id, $version->instrumento->municipalidad_id, null, ['resultado' => $data['resultado'], 'tipo' => $data['tipo']], 'Decisión registrada sobre la versión.');
        });

        return back()->with('success', 'Decisión registrada correctamente.');
    }
}
