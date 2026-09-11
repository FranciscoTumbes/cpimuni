@extends('layouts.app')

@section('title', $instrumento->nombre . ' | CPIMuni')
@section('page-title', 'Expediente documental')

@section('content')
@include('partials.form-feedback')
<section class="card summary">
    <div><span class="eyebrow">{{ $instrumento->tipo }}</span><h2>{{ $instrumento->nombre }}</h2><p>{{ $instrumento->descripcion ?: 'Sin descripción registrada.' }}</p></div>
    <div class="facts"><strong>{{ $instrumento->estado }}</strong><span>{{ $instrumento->municipalidad->nombre }}</span><span>Código: {{ $instrumento->codigo ?: 'Sin código' }}</span></div>
</section>

@can('update', $instrumento)
<section class="card">
    <div class="card-title">Crear nueva versión</div>
    <form method="POST" action="{{ route('instrumentos.versiones.store', $instrumento) }}" class="form-grid">@csrf
        <label>Versión<input name="version" required maxlength="30" placeholder="1.0"></label>
        <label>Motivo<input name="motivo" maxlength="500" placeholder="Actualización normativa"></label>
        <label>Referencia DOCX<input name="archivo_docx" maxlength="500"></label>
        <label>Referencia PDF<input name="archivo_pdf" maxlength="500"></label>
        <div><button class="button" type="submit"><i class="fa-solid fa-code-branch"></i> Crear borrador</button></div>
    </form>
</section>
<section class="card">
    <div class="card-title">Incorporar documento al expediente</div>
    <form method="POST" action="{{ route('instrumentos.documentos.store', $instrumento) }}" enctype="multipart/form-data" class="form-grid document-form">@csrf
        <label>Archivo<input type="file" name="archivo" required accept=".pdf,.doc,.docx,.xls,.xlsx"></label>
        <label>Tipo documental<input name="tipo" maxlength="100" placeholder="Versión aprobada, informe técnico..."></label>
        <div><button class="button" type="submit"><i class="fa-solid fa-upload"></i> Incorporar documento</button></div>
    </form>
</section>
@endcan

<section class="versions">
@forelse($instrumento->versiones->sortByDesc('id') as $version)
    <article class="card version">
        <header><div><span class="eyebrow">Versión {{ $version->version }}</span><h3>{{ $version->estado }}</h3><p>Responsable: {{ $version->usuario?->nombre }} {{ $version->usuario?->apellido }} · {{ $version->created_at?->format('d/m/Y H:i') }}</p></div><span class="status">{{ $version->estado }}</span></header>
        @if($version->motivo)<p class="detail"><strong>Motivo:</strong> {{ $version->motivo }}</p>@endif
        <div class="workflow">
            @can('submit', $version)
                @if(in_array($version->estado, ['BORRADOR', 'OBSERVADO'], true))<form method="POST" action="{{ route('instrumentos.versiones.submit', $version) }}">@csrf<button class="button" type="submit"><i class="fa-solid fa-paper-plane"></i> Enviar a revisión</button></form>@endif
            @endcan
            @can('observe', $version)
                @if($version->estado === 'EN_REVISION')<form method="POST" action="{{ route('instrumentos.versiones.observations.store', $version) }}" class="inline-form">@csrf<input name="observacion" required placeholder="Describa la observación"><button class="button warning" type="submit"><i class="fa-solid fa-comment"></i> Observar</button></form>@endif
            @endcan
            @can('approve', $version)
                @if(in_array($version->estado, ['EN_REVISION', 'OBSERVADO'], true))<form method="POST" action="{{ route('instrumentos.versiones.decide', $version) }}" class="decision">@csrf<input type="hidden" name="tipo" value="APROBACION_FORMAL"><select name="resultado"><option value="APROBADO">Aprobar</option><option value="RECHAZADO">Rechazar</option></select><input name="comentario" placeholder="Comentario de decisión"><button class="button success" type="submit"><i class="fa-solid fa-stamp"></i> Registrar decisión</button></form>@endif
            @endcan
        </div>
        @foreach($version->revisiones as $revision)
            <div class="review"><strong>Revisión {{ $revision->tipo }}: {{ $revision->estado }}</strong>
                @foreach($revision->observaciones as $observacion)
                    <div class="observation"><p>{{ $observacion->observacion }}</p><small>{{ $observacion->usuario?->nombre }} · {{ $observacion->estado }}</small>
                    @if($observacion->estado === 'PENDIENTE' && $version->usuario_id === auth()->id())<form method="POST" action="{{ route('instrumentos.observaciones.respond', $observacion) }}" class="inline-form">@csrf<input name="respuesta" required placeholder="Respuesta y acción correctiva"><button class="button" type="submit">Subsanar</button></form>@elseif($observacion->respuesta)<p class="response"><strong>Respuesta:</strong> {{ $observacion->respuesta }}</p>@endif</div>
                @endforeach
            </div>
        @endforeach
        @foreach($version->aprobaciones as $aprobacion)<div class="approval"><strong>{{ $aprobacion->resultado }}</strong> · {{ $aprobacion->usuario?->nombre }} · {{ $aprobacion->comentario }}</div>@endforeach
    </article>
@empty
    <section class="card empty">Aún no existen versiones documentales.</section>
@endforelse
</section>
<section class="card"><div class="card-title">Documentos del expediente</div><div class="documents">@forelse($instrumento->documentos as $documento)<div class="document"><i class="fa-solid fa-file-lines"></i><div><strong>{{ $documento->nombre }}</strong><small>{{ $documento->tipo }} · {{ number_format($documento->tamano / 1024, 1) }} KB · Hash SHA-256: {{ $documento->hash_archivo }}</small></div></div>@empty<p class="empty">No hay documentos incorporados.</p>@endforelse</div></section>
@endsection

@push('styles')<style>
.summary{display:flex;justify-content:space-between;gap:24px}.summary h2{font-size:24px;margin:6px 0}.summary p,.version p{color:#64748b;font-size:12px}.facts{display:grid;gap:6px;text-align:right;color:#64748b;font-size:12px}.facts strong{color:#2563eb;font-size:15px}.eyebrow{font-size:10px;text-transform:uppercase;letter-spacing:.08em;color:#64748b;font-weight:800}.form-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;align-items:end}label{font-size:11px;font-weight:700;color:#475569}input,select{display:block;width:100%;margin-top:6px;padding:10px 11px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;font:inherit;font-size:13px}.button{border:0;border-radius:8px;padding:10px 14px;background:#2563eb;color:#fff;font-weight:700;cursor:pointer}.button.warning{background:#d97706}.button.success{background:#15803d}.version{margin-bottom:16px}.version header{display:flex;justify-content:space-between;gap:16px}.version h3{margin:5px 0;font-size:16px}.status{padding:5px 9px;border-radius:999px;background:#eff6ff;color:#1d4ed8;font-size:10px;font-weight:800;height:max-content}.detail{margin:16px 0}.workflow{display:flex;gap:10px;flex-wrap:wrap;padding:15px 0;border-top:1px solid #e2e8f0;border-bottom:1px solid #e2e8f0}.inline-form,.decision{display:flex;gap:8px;align-items:end;flex:1}.inline-form input,.decision input{min-width:180px}.review,.approval{margin-top:14px;padding:12px;background:#f8fafc;border-left:3px solid #2563eb;font-size:12px}.observation{margin-top:10px;padding:10px;background:#fff;border:1px solid #e2e8f0}.observation small{color:#64748b}.response{margin-top:8px}.empty{text-align:center;color:#64748b}@media(max-width:850px){.summary{display:block}.facts{text-align:left;margin-top:15px}.form-grid{grid-template-columns:1fr 1fr}.inline-form,.decision{flex-wrap:wrap}}
</style>
@endpush
