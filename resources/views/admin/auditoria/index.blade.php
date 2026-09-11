@extends('layouts.app')

@section('title', 'Auditoría | CPIMuni')
@section('page-title', 'Auditoría de operaciones')

@section('content')
<section class="card">
    <div class="card-title">Filtros de consulta</div>
    <form method="GET" class="filters">
        <label>Entidad<input name="tabla" value="{{ request('tabla') }}" placeholder="usuarios, normas..."></label>
        <label>Acción<select name="accion"><option value="">Todas</option>@foreach($acciones as $accion)<option value="{{ $accion }}" @selected(request('accion') === $accion)>{{ $accion }}</option>@endforeach</select></label>
        <label>Usuario<select name="usuario_id"><option value="">Todos</option>@foreach($usuarios as $usuario)<option value="{{ $usuario->id }}" @selected((string) request('usuario_id') === (string) $usuario->id)>{{ $usuario->nombre }} {{ $usuario->apellido }}</option>@endforeach</select></label>
        <label>Desde<input type="date" name="desde" value="{{ request('desde') }}"></label>
        <label>Hasta<input type="date" name="hasta" value="{{ request('hasta') }}"></label>
        <div><button class="button" type="submit"><i class="fa-solid fa-filter"></i> Filtrar</button></div>
    </form>
</section>
<section class="card">
    <div class="card-title">Registro inmutable de operaciones</div>
    <div class="table-wrap"><table><thead><tr><th>Fecha</th><th>Actor</th><th>Entidad</th><th>Acción</th><th>Resultado</th><th>Detalle</th></tr></thead><tbody>
    @forelse($auditorias as $auditoria)
        <tr><td>{{ $auditoria->created_at?->format('d/m/Y H:i:s') }}</td><td>{{ $auditoria->usuario?->nombre }} {{ $auditoria->usuario?->apellido ?: 'Sistema' }}<small>{{ $auditoria->ip }}</small></td><td>{{ $auditoria->tabla }} #{{ $auditoria->registro_id ?: '—' }}<small>{{ $auditoria->municipalidad?->nombre ?: 'Global' }}</small></td><td><span class="action">{{ $auditoria->accion }}</span></td><td>{{ $auditoria->resultado }}</td><td>{{ $auditoria->descripcion }}@if($auditoria->motivo)<small>Motivo: {{ $auditoria->motivo }}</small>@endif</td></tr>
    @empty <tr><td colspan="6" class="empty">No hay operaciones para los filtros seleccionados.</td></tr> @endforelse
    </tbody></table></div>{{ $auditorias->links() }}
</section>
@endsection

@push('styles')<style>
.card{margin-bottom:20px}.filters{display:grid;grid-template-columns:repeat(5,minmax(120px,1fr)) auto;gap:14px;align-items:end}label{font-size:11px;font-weight:700;color:#475569}input,select{display:block;width:100%;margin-top:6px;padding:10px 11px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;font:inherit;font-size:13px}.button{border:0;border-radius:8px;padding:11px 15px;background:#2563eb;color:white;font-weight:700;cursor:pointer;white-space:nowrap}.table-wrap{overflow:auto}table{width:100%;border-collapse:collapse;font-size:12px}th,td{text-align:left;padding:13px 10px;border-bottom:1px solid #e2e8f0;vertical-align:top}th{font-size:10px;text-transform:uppercase;color:#64748b;white-space:nowrap}td small{display:block;color:#64748b;margin-top:3px}.action{font-weight:700;color:#0f766e}.empty{padding:28px;text-align:center;color:#64748b}@media(max-width:1000px){.filters{grid-template-columns:repeat(2,minmax(140px,1fr))}}
</style>
@endpush
