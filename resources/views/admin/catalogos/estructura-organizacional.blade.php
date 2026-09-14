@extends('layouts.app')

@section('title', 'Catálogo de estructura organizacional | CPIMuni')
@section('page-title', 'Catálogo maestro de estructura organizacional')

@section('content')
    @include('partials.form-feedback')

    <section class="card">
        <div class="card-title">Registrar elemento</div>
        <form method="POST" action="{{ route('catalogos.estructura.store') }}" class="form-grid">
            @csrf
            <label>Código<input name="codigo" required value="{{ old('codigo') }}"></label>
            <label>Código padre<input name="codigo_padre" value="{{ old('codigo_padre') }}"></label>
            <label>Nombre<input name="nombre" required value="{{ old('nombre') }}"></label>
            <label>Categoría<select name="categoria" required>
                <option value="">Seleccione</option>
                <option value="GOBIERNO" {{ old('categoria') === 'GOBIERNO' ? 'selected' : '' }}>Gobierno</option>
                <option value="CONTROL" {{ old('categoria') === 'CONTROL' ? 'selected' : '' }}>Control</option>
                <option value="ASESORIA" {{ old('categoria') === 'ASESORIA' ? 'selected' : '' }}>Asesoría</option>
                <option value="LINEA" {{ old('categoria') === 'LINEA' ? 'selected' : '' }}>Línea</option>
                <option value="COORDINACION" {{ old('categoria') === 'COORDINACION' ? 'selected' : '' }}>Coordinación</option>
                <option value="CONSULTIVO" {{ old('categoria') === 'CONSULTIVO' ? 'selected' : '' }}>Consultivo</option>
            </select></label>
            <label>Tipo<select name="tipo" required>
                <option value="">Seleccione</option>
                <option value="NATURALEZA" {{ old('tipo') === 'NATURALEZA' ? 'selected' : '' }}>Naturaleza</option>
                <option value="TIPO" {{ old('tipo') === 'TIPO' ? 'selected' : '' }}>Tipo</option>
                <option value="UNIDAD" {{ old('tipo') === 'UNIDAD' ? 'selected' : '' }}>Unidad</option>
                <option value="PUESTO" {{ old('tipo') === 'PUESTO' ? 'selected' : '' }}>Puesto</option>
            </select></label>
            <label>Nivel<input type="number" name="nivel" min="1" value="{{ old('nivel', 1) }}" required></label>
            <label>Orden<input type="number" name="orden" min="0" value="{{ old('orden', 0) }}"></label>
            <label>Permite hijos<input type="checkbox" name="permite_hijos" value="1" {{ old('permite_hijos') ? 'checked' : '' }}></label>
            <label>Estado<select name="estado" required>
                <option value="ACTIVO" {{ old('estado', 'ACTIVO') === 'ACTIVO' ? 'selected' : '' }}>ACTIVO</option>
                <option value="INACTIVO" {{ old('estado') === 'INACTIVO' ? 'selected' : '' }}>INACTIVO</option>
            </select></label>
            <label class="full-width">Descripción<textarea name="descripcion" rows="3">{{ old('descripcion') }}</textarea></label>
            <div><button class="button" type="submit"><i class="fa-solid fa-plus"></i> Registrar</button></div>
        </form>
    </section>

    <section class="card">
        <div class="card-title">Catálogo registrado</div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Tipo</th>
                        <th>Nivel</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td>{{ $item->codigo }}</td>
                            <td>{{ $item->nombre }}</td>
                            <td>{{ $item->categoria }}</td>
                            <td>{{ $item->tipo }}</td>
                            <td>{{ $item->nivel }}</td>
                            <td><span class="status {{ $item->estado === 'ACTIVO' ? 'active' : 'inactive' }}">{{ $item->estado }}</span></td>
                            <td class="actions">
                                <form method="POST" action="{{ route('catalogos.estructura.destroy', $item) }}" onsubmit="return confirm('¿Desactivar este elemento?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="icon-button danger" type="submit" title="Desactivar"><i class="fa-solid fa-ban"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="empty">No hay elementos en el catálogo.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $items->links() }}
    </section>
@endsection

@push('styles')
<style>
    .form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}.full-width{grid-column:span 2}.card{margin-bottom:20px}.button{margin-top:8px;border:0;border-radius:8px;padding:10px 14px;background:#2563eb;color:#fff;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:8px}.button i,.button svg,.icon-button i,.icon-button svg{width:14px !important;height:14px !important;font-size:14px !important;display:inline-flex;align-items:center;justify-content:center}.table-wrap{overflow:auto}table{width:100%;border-collapse:collapse;font-size:12px}th,td{text-align:left;padding:12px 10px;border-bottom:1px solid #e2e8f0}th{font-size:10px;text-transform:uppercase;color:#64748b}.status{padding:4px 8px;border-radius:999px;font-size:10px;font-weight:700}.status.active{background:#dcfce7;color:#166534}.status.inactive{background:#fee2e2;color:#991b1b}.actions{display:flex;gap:6px;align-items:center}.icon-button{border:0;background:#eff6ff;color:#2563eb;border-radius:7px;padding:7px 9px;cursor:pointer;display:inline-flex;align-items:center;justify-content:center}.icon-button.danger{background:#fee2e2;color:#b91c1c}.empty{padding:28px;text-align:center;color:#64748b}label{display:block;font-size:11px;font-weight:700;color:#475569}input,select,textarea{display:block;width:100%;margin-top:6px;padding:10px 11px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;font:inherit;font-size:13px}textarea{resize:vertical}.alert{padding:10px 12px;border-radius:8px;margin-bottom:14px;font-size:12px}.alert.success{background:#dcfce7;color:#166534}.alert.error{background:#fee2e2;color:#991b1b}@media(max-width:900px){.form-grid{grid-template-columns:1fr}.full-width{grid-column:auto}}
</style>
@endpush
