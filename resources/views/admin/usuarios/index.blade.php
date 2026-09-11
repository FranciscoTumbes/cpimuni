@extends('layouts.app')

@section('title', 'Usuarios | CPIMuni')
@section('page-title', 'Usuarios')

@section('content')
<div class="page-grid">
<section class="card"><div class="card-title">Registrar usuario</div>@include('partials.form-feedback')
<form method="POST" action="{{ route('admin.usuarios.store') }}" class="form-grid">@csrf
<label>Nombre<input name="nombre" required value="{{ old('nombre') }}"></label><label>Apellido<input name="apellido" value="{{ old('apellido') }}"></label>
<label>Correo electrónico<input type="email" name="email" required value="{{ old('email') }}"></label><label>Contraseña<input type="password" name="password" required minlength="8"></label>
<label>Rol<select name="rol_id" required>@foreach($roles as $rol)<option value="{{ $rol->id }}">{{ $rol->nombre }}</option>@endforeach</select></label>
@if(auth()->user()->rol?->nombre === 'SUPERADMIN')<label>Municipalidad<select name="municipalidad_id"><option value="">Cuenta global</option>@foreach($municipalidades as $municipalidad)<option value="{{ $municipalidad->id }}">{{ $municipalidad->nombre }}</option>@endforeach</select></label>@endif
<label>Estado<select name="estado"><option>ACTIVO</option><option>INACTIVO</option><option>BLOQUEADO</option></select></label>
<div><button class="button" type="submit"><i class="fa-solid fa-user-plus"></i> Registrar</button></div></form></section>
<section class="card"><div class="card-title">Usuarios registrados</div><div class="table-wrap"><table><thead><tr><th>Usuario</th><th>Municipalidad</th><th>Rol</th><th>Estado</th><th>Último acceso</th><th>Acciones</th></tr></thead><tbody>
@forelse($usuarios as $usuario)<tr><td><strong>{{ $usuario->nombre }} {{ $usuario->apellido }}</strong><small>{{ $usuario->email }}</small></td><td>{{ $usuario->municipalidad?->nombre ?: 'Global' }}</td><td>{{ $usuario->rol?->nombre }}</td><td><span class="status {{ $usuario->estado === 'ACTIVO' ? 'active' : 'inactive' }}">{{ $usuario->estado }}</span></td><td>{{ $usuario->ultimo_acceso?->format('d/m/Y H:i') ?: 'Nunca' }}</td><td class="actions"><a class="icon-button" title="Editar" href="{{ route('admin.usuarios.edit', $usuario) }}"><i class="fa-solid fa-pen"></i></a>@if(!auth()->user()->is($usuario))<form method="POST" action="{{ route('admin.usuarios.destroy', $usuario) }}" onsubmit="return confirm('¿Eliminar este usuario?')">@csrf @method('DELETE')<button class="icon-button danger" title="Eliminar" type="submit"><i class="fa-solid fa-trash"></i></button></form>@endif</td></tr>@empty<tr><td colspan="6" class="empty">No hay usuarios.</td></tr>@endforelse
</tbody></table></div>{{ $usuarios->links() }}</section></div>
@endsection

@push('styles')<style>
.page-grid{display:grid;grid-template-columns:minmax(300px,.8fr) minmax(500px,1.6fr);gap:20px}.form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}label{font-size:11px;font-weight:700;color:#475569}input,select{display:block;width:100%;margin-top:6px;padding:10px 11px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;font:inherit;font-size:13px}.button{margin-top:20px;border:0;border-radius:8px;padding:11px 15px;background:#2563eb;color:white;font-weight:700;cursor:pointer}.table-wrap{overflow:auto}table{width:100%;border-collapse:collapse;font-size:12px}th,td{text-align:left;padding:13px 10px;border-bottom:1px solid #e2e8f0;white-space:nowrap}th{font-size:10px;text-transform:uppercase;color:#64748b}td small{display:block;color:#64748b;margin-top:3px}.status{padding:4px 8px;border-radius:999px;font-size:10px;font-weight:700}.active{background:#dcfce7;color:#166534}.inactive{background:#fee2e2;color:#991b1b}.actions{display:flex;gap:6px;align-items:center}.icon-button{border:0;background:#eff6ff;color:#2563eb;border-radius:7px;padding:7px 9px;cursor:pointer;text-decoration:none}.icon-button.danger{background:#fee2e2;color:#b91c1c}.empty{padding:28px;text-align:center;color:#64748b}.alert{padding:10px 12px;border-radius:8px;margin-bottom:14px;font-size:12px}.alert.success{background:#dcfce7;color:#166534}.alert.error{background:#fee2e2;color:#991b1b}@media(max-width:900px){.page-grid{grid-template-columns:1fr}.form-grid{grid-template-columns:1fr}}
</style>@endpush
