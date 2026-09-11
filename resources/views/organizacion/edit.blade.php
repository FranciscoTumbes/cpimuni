@extends('layouts.app')

@section('title', 'Editar estructura | CPIMuni')
@section('page-title', 'Editar registro organizacional')

@section('content')
@include('partials.form-feedback')
<section class="card editor">
    <div class="card-title">Editar {{ $tipo === 'organos' ? 'órgano' : ($tipo === 'unidades' ? 'unidad orgánica' : ($tipo === 'puestos' ? 'puesto' : 'función')) }}</div>
    <form method="POST" action="{{ route('organizacion.update', [$tipo, $model->id]) }}" class="form-grid">
        @csrf @method('PUT')
        @if($tipo === 'organos')
            <label>Código<input name="codigo" value="{{ old('codigo', $model->codigo) }}"></label>
            <label>Nombre<input name="nombre" required value="{{ old('nombre', $model->nombre) }}"></label>
            <label>Tipo<input name="tipo" value="{{ old('tipo', $model->tipo) }}"></label>
            <label>Nivel jerárquico<input type="number" name="nivel_jerarquico" min="0" value="{{ old('nivel_jerarquico', $model->nivel_jerarquico) }}"></label>
            <label>Órgano padre<select name="organo_padre_id"><option value="">Sin padre</option>@foreach($organos as $item)<option value="{{ $item->id }}" @selected(old('organo_padre_id', $model->organo_padre_id) == $item->id)>{{ $item->nombre }}</option>@endforeach</select></label>
            <label>Estado<select name="estado"><option value="ACTIVO" @selected($model->estado === 'ACTIVO')>ACTIVO</option><option value="INACTIVO" @selected($model->estado === 'INACTIVO')>INACTIVO</option></select></label>
        @elseif($tipo === 'unidades')
            <label>Código<input name="codigo" value="{{ old('codigo', $model->codigo) }}"></label>
            <label>Nombre<input name="nombre" required value="{{ old('nombre', $model->nombre) }}"></label>
            <label>Tipo<input name="tipo" value="{{ old('tipo', $model->tipo) }}"></label>
            <label>Órgano<select name="organo_id"><option value="">Sin órgano</option>@foreach($organos as $item)<option value="{{ $item->id }}" @selected(old('organo_id', $model->organo_id) == $item->id)>{{ $item->nombre }}</option>@endforeach</select></label>
            <label>Unidad padre<select name="unidad_padre_id"><option value="">Sin padre</option>@foreach($unidades as $item)<option value="{{ $item->id }}" @selected(old('unidad_padre_id', $model->unidad_padre_id) == $item->id)>{{ $item->nombre }}</option>@endforeach</select></label>
            <label>Nivel jerárquico<input type="number" name="nivel_jerarquico" min="0" value="{{ old('nivel_jerarquico', $model->nivel_jerarquico) }}"></label>
            <label class="wide">Finalidad<textarea name="finalidad">{{ old('finalidad', $model->finalidad) }}</textarea></label>
            <label>Estado<select name="estado"><option value="ACTIVA" @selected($model->estado === 'ACTIVA')>ACTIVA</option><option value="INACTIVA" @selected($model->estado === 'INACTIVA')>INACTIVA</option></select></label>
        @elseif($tipo === 'puestos')
            <label>Unidad<select name="unidad_organica_id"><option value="">Sin unidad</option>@foreach($unidades as $item)<option value="{{ $item->id }}" @selected(old('unidad_organica_id', $model->unidad_organica_id) == $item->id)>{{ $item->nombre }}</option>@endforeach</select></label>
            <label>Código<input name="codigo" value="{{ old('codigo', $model->codigo) }}"></label>
            <label>Denominación<input name="denominacion" required value="{{ old('denominacion', $model->denominacion) }}"></label>
            <label>Nivel<input name="nivel" value="{{ old('nivel', $model->nivel) }}"></label>
            <label class="wide">Finalidad<textarea name="finalidad">{{ old('finalidad', $model->finalidad) }}</textarea></label>
            <label class="wide">Requisitos<textarea name="requisitos">{{ old('requisitos', $model->requisitos) }}</textarea></label>
            <label class="wide">Competencias<textarea name="competencias">{{ old('competencias', $model->competencias) }}</textarea></label>
            <label>Estado<select name="estado"><option value="ACTIVO" @selected($model->estado === 'ACTIVO')>ACTIVO</option><option value="INACTIVO" @selected($model->estado === 'INACTIVO')>INACTIVO</option></select></label>
        @else
            <label>Unidad<select name="unidad_organica_id"><option value="">Sin unidad</option>@foreach($unidades as $item)<option value="{{ $item->id }}" @selected(old('unidad_organica_id', $model->unidad_organica_id) == $item->id)>{{ $item->nombre }}</option>@endforeach</select></label>
            <label>Puesto<select name="puesto_id"><option value="">Sin puesto</option>@foreach($puestos as $item)<option value="{{ $item->id }}" @selected(old('puesto_id', $model->puesto_id) == $item->id)>{{ $item->denominacion }}</option>@endforeach</select></label>
            <label>Código<input name="codigo" value="{{ old('codigo', $model->codigo) }}"></label>
            <label>Tipo<input name="tipo" value="{{ old('tipo', $model->tipo) }}"></label>
            <label class="wide">Descripción<textarea name="descripcion" required>{{ old('descripcion', $model->descripcion) }}</textarea></label>
            <label>Fuente<input name="fuente" value="{{ old('fuente', $model->fuente) }}"></label>
            <label>Fecha inicio<input type="date" name="fecha_inicio" value="{{ old('fecha_inicio', optional($model->fecha_inicio)->format('Y-m-d')) }}"></label>
            <label>Fecha fin<input type="date" name="fecha_fin" value="{{ old('fecha_fin', optional($model->fecha_fin)->format('Y-m-d')) }}"></label>
            <label>Estado<select name="estado"><option value="VIGENTE" @selected($model->estado === 'VIGENTE')>VIGENTE</option><option value="NO_VIGENTE" @selected($model->estado === 'NO_VIGENTE')>NO_VIGENTE</option><option value="EN_REVISION" @selected($model->estado === 'EN_REVISION')>EN_REVISION</option></select></label>
        @endif
        <div class="actions"><a class="button secondary" href="{{ route('organizacion.index') }}">Cancelar</a><button class="button" type="submit">Guardar cambios</button></div>
    </form>
</section>
@endsection

@push('styles')<style>
.editor{max-width:900px}.card-title{font-weight:800;margin-bottom:18px}.form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}label{font-size:11px;font-weight:700;color:#475569}input,select,textarea{display:block;width:100%;margin-top:6px;padding:10px 11px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;font:inherit;font-size:13px}textarea{min-height:80px;resize:vertical}.wide{grid-column:span 2}.actions{grid-column:span 2;display:flex;gap:8px;justify-content:flex-end}.button{border:0;border-radius:8px;padding:10px 14px;background:#2563eb;color:#fff;font-weight:700;text-decoration:none;cursor:pointer}.button.secondary{background:#e2e8f0;color:#334155}.alert{padding:10px 12px;border-radius:8px;margin-bottom:14px;font-size:12px}.alert.error{background:#fee2e2;color:#991b1b}@media(max-width:700px){.form-grid{grid-template-columns:1fr}.wide,.actions{grid-column:auto}}
</style>
@endpush
