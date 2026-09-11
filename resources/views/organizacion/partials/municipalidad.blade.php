@if(auth()->user()->rol?->nombre === 'SUPERADMIN')
<select name="municipalidad_id" required>
    <option value="">Municipalidad</option>
    @foreach($municipalidades as $municipalidad)
        <option value="{{ $municipalidad->id }}">{{ $municipalidad->nombre }}</option>
    @endforeach
</select>
@else
<span class="municipalidad-context" title="Municipalidad asignada a su usuario">
    <i class="fa-solid fa-city"></i>
    {{ auth()->user()->municipalidad?->nombre ?: 'Municipalidad no asignada' }}
</span>
@endif
