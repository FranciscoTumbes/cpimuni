@extends('layouts.app')

@section('title', 'Municipalidades | CPIMuni')
@section('page-title', 'Municipalidades')

@section('content')
@if(auth()->user()->rol?->nombre === 'SUPERADMIN')
<section class="card clone-card" id="clone-municipality">
    <div class="card-title">Duplicar municipalidad</div>
    <p class="muted">Crea una municipalidad nueva copiando solo los datos estructurales seleccionados. No se copian usuarios, documentos, auditoría ni revisiones.</p>
    @include('partials.form-feedback')
    <form method="POST" action="{{ url('/administracion/municipalidades/0/duplicar') }}" class="form-grid">
        @csrf
        <label>Municipalidad origen<select required data-base-url="{{ url('/administracion/municipalidades') }}" onchange="this.form.action = this.dataset.baseUrl + '/' + this.value + '/duplicar'"><option value="">Seleccione origen</option>@foreach($municipalidades as $origen)<option value="{{ $origen->id }}">{{ $origen->nombre }}</option>@endforeach</select></label>
        <label>Nombre de la nueva municipalidad<input name="nombre" required value="{{ old('nombre') }}"></label>
        <label>RUC<input name="ruc" maxlength="20" value="{{ old('ruc') }}"></label>
        <label>Código de entidad<input name="codigo_entidad" maxlength="20" value="{{ old('codigo_entidad') }}"></label>
        <label>Tipo<select name="tipo"><option value="DISTRITAL">Distrital</option><option value="PROVINCIAL">Provincial</option></select></label>
        <label>Departamento<input name="departamento" value="{{ old('departamento') }}"></label>
        <label>Provincia<input name="provincia" value="{{ old('provincia') }}"></label>
        <label>Distrito<input name="distrito" value="{{ old('distrito') }}"></label>
        <fieldset class="clone-options"><legend>Datos a copiar</legend><label><input type="checkbox" name="estructura" value="1" checked> Órganos y unidades orgánicas</label><label><input type="checkbox" name="puestos" value="1" checked> Puestos</label><label><input type="checkbox" name="funciones" value="1" checked> Funciones</label><label><input type="checkbox" name="normativa" value="1"> Normativa base</label><label><input type="checkbox" name="instrumentos" value="1"> Instrumentos base</label></fieldset>
        <div><button class="button" type="submit"><i class="fa-solid fa-copy"></i> Duplicar</button></div>
    </form>
</section>
@endif
<div class="page-grid">
    <section class="card">
        <div class="card-title">Registrar municipalidad</div>
        @include('partials.form-feedback')
        <form method="POST" action="{{ route('admin.municipalidades.store') }}" class="form-grid">
            @csrf
            <label>Nombre<input name="nombre" required value="{{ old('nombre') }}"></label>
            <label>RUC<input name="ruc" maxlength="20" value="{{ old('ruc') }}"></label>
            <label>Código de entidad<input name="codigo_entidad" maxlength="20" value="{{ old('codigo_entidad') }}"></label>
            <label>Tipo<select name="tipo"><option value="DISTRITAL">Distrital</option><option value="PROVINCIAL">Provincial</option></select></label>
            <label>Departamento<input name="departamento" value="{{ old('departamento') }}"></label>
            <label>Provincia<input name="provincia" value="{{ old('provincia') }}"></label>
            <label>Distrito<input name="distrito" value="{{ old('distrito') }}"></label>
            <div><button class="button" type="submit"><i class="fa-solid fa-plus"></i> Registrar</button></div>
        </form>
    </section>
    <section class="card">
        <div class="card-title">Municipalidades registradas</div>
        <div class="table-wrap"><table><thead><tr><th>Nombre</th><th>RUC</th><th>Tipo</th><th>Usuarios</th><th>Estado</th><th>Acciones</th></tr></thead><tbody>
        @forelse($municipalidades as $municipalidad)
            <tr><td><strong>{{ $municipalidad->nombre }}</strong><small>{{ $municipalidad->codigo_entidad }}</small></td><td>{{ $municipalidad->ruc ?: '—' }}</td><td>{{ $municipalidad->tipo }}</td><td>{{ $municipalidad->usuarios_count }}</td><td><span class="status {{ $municipalidad->estado === 'ACTIVA' ? 'active' : 'inactive' }}">{{ $municipalidad->estado }}</span></td><td class="actions"><a class="icon-button" title="Editar" href="{{ route('admin.municipalidades.edit', $municipalidad) }}"><i class="fa-solid fa-pen"></i></a>@if(auth()->user()->rol?->nombre === 'SUPERADMIN')<a class="icon-button" title="Duplicar" href="#clone-municipality"><i class="fa-solid fa-copy"></i></a>@endif<form method="POST" action="{{ route('admin.municipalidades.destroy', $municipalidad) }}" onsubmit="return confirm('¿Eliminar esta municipalidad?')">@csrf @method('DELETE')<button class="icon-button danger" title="Eliminar" type="submit"><i class="fa-solid fa-trash"></i></button></form></td></tr>
        @empty <tr><td colspan="6" class="empty">No hay municipalidades registradas.</td></tr> @endforelse
        </tbody></table></div>
        {{ $municipalidades->links() }}
    </section>
</div>
@endsection

@push('styles')
<style>
.clone-card{margin-bottom:20px}.muted{color:#64748b;font-size:12px}.clone-options{grid-column:span 2;display:flex;gap:14px;flex-wrap:wrap;border:1px solid #e2e8f0;padding:12px}.clone-options legend{font-size:11px;font-weight:700;color:#475569}.clone-options label{font-weight:600}.clone-options input{display:inline;width:auto;margin:0}.page-grid{display:grid;grid-template-columns:minmax(300px,.8fr) minmax(500px,1.6fr);gap:20px}.form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}label{font-size:11px;font-weight:700;color:#475569}input,select,textarea{display:block;width:100%;margin-top:6px;padding:10px 11px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;font:inherit;font-size:13px}label:first-child{grid-column:span 2}.button{margin-top:20px;border:0;border-radius:8px;padding:11px 15px;background:#2563eb;color:white;font-weight:700;cursor:pointer}.table-wrap{overflow:auto}table{width:100%;border-collapse:collapse;font-size:12px}th,td{text-align:left;padding:13px 10px;border-bottom:1px solid #e2e8f0;white-space:nowrap}th{font-size:10px;text-transform:uppercase;color:#64748b}td small{display:block;color:#64748b;margin-top:3px}.status{padding:4px 8px;border-radius:999px;font-size:10px;font-weight:700}.status.active{background:#dcfce7;color:#166534}.status.inactive{background:#fee2e2;color:#991b1b}.actions{display:flex;gap:6px;align-items:center}.icon-button{border:0;background:#eff6ff;color:#2563eb;border-radius:7px;padding:7px 9px;cursor:pointer;text-decoration:none}.icon-button.danger{background:#fee2e2;color:#b91c1c}.empty{padding:28px;text-align:center;color:#64748b}.alert{padding:10px 12px;border-radius:8px;margin-bottom:14px;font-size:12px}.alert.success{background:#dcfce7;color:#166534}.alert.error{background:#fee2e2;color:#991b1b}@media(max-width:900px){.page-grid{grid-template-columns:1fr}.form-grid{grid-template-columns:1fr}label:first-child{grid-column:auto}}
</style>
@endpush
