@extends('layouts.app')

@section('title', 'Instrumentos de Gestión | CPIMuni')
@section('page-title', $tipo !== '' ? $tipo : 'Instrumentos de Gestión')

@section('content')
<section class="card">
    <div class="toolbar">
        <div>
            <div class="card-title">{{ $tipo !== '' ? $tipo : 'Catálogo de instrumentos' }}</div>
            <p class="muted">Consulta de instrumentos registrados por municipalidad y estado.</p>
        </div>
        <div class="filters">
            <a class="filter {{ $tipo === '' ? 'selected' : '' }}" href="{{ route('instrumentos.index') }}">Todos</a>
            <a class="filter {{ $tipo === 'ROF' ? 'selected' : '' }}" href="{{ route('instrumentos.index', ['tipo' => 'ROF']) }}">ROF</a>
            <a class="filter {{ $tipo === 'PEI' ? 'selected' : '' }}" href="{{ route('instrumentos.index', ['tipo' => 'PEI']) }}">PEI</a>
            <a class="filter {{ $tipo === 'POI' ? 'selected' : '' }}" href="{{ route('instrumentos.index', ['tipo' => 'POI']) }}">POI</a>
        </div>
    </div>

    <div class="table-wrap">
        <table>
            <thead><tr><th>Instrumento</th><th>Tipo</th><th>Municipalidad</th><th>Estado</th><th>Vigencia</th></tr></thead>
            <tbody>
            @forelse($instrumentos as $instrumento)
                <tr>
                    <td><strong>{{ $instrumento->nombre }}</strong><small>{{ $instrumento->codigo ?: 'Sin código' }}</small></td>
                    <td>{{ $instrumento->tipo }}</td>
                    <td>{{ $instrumento->municipalidad?->nombre ?: 'Global' }}</td>
                    <td><span class="status">{{ $instrumento->estado }}</span></td>
                    <td>{{ $instrumento->fecha_vigencia?->format('d/m/Y') ?: '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="empty">No hay instrumentos registrados para este filtro.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $instrumentos->links() }}
</section>
@endsection

@push('styles')
<style>
.toolbar{display:flex;justify-content:space-between;align-items:start;gap:20px;margin-bottom:18px}.muted{font-size:12px;color:#64748b}.filters{display:flex;gap:6px;flex-wrap:wrap}.filter{padding:8px 11px;border:1px solid #cbd5e1;border-radius:7px;color:#475569;text-decoration:none;font-size:11px;font-weight:700}.filter.selected{background:#2563eb;border-color:#2563eb;color:#fff}.table-wrap{overflow:auto}table{width:100%;border-collapse:collapse;font-size:12px}th,td{text-align:left;padding:13px 10px;border-bottom:1px solid #e2e8f0;white-space:nowrap}th{font-size:10px;text-transform:uppercase;color:#64748b}td small{display:block;color:#64748b;margin-top:3px}.status{padding:4px 8px;border-radius:999px;background:#eff6ff;color:#1d4ed8;font-size:10px;font-weight:700}.empty{padding:30px;text-align:center;color:#64748b}@media(max-width:700px){.toolbar{display:block}.filters{margin-top:14px}}
</style>
@endpush
