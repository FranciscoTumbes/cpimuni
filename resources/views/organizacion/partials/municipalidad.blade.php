@if(auth()->user()->rol?->nombre === 'SUPERADMIN')
<select name="municipalidad_id" required>
    <option value="">Municipalidad</option>
    @foreach($municipalidades as $municipalidad)
        <option value="{{ $municipalidad->id }}">{{ $municipalidad->nombre }}</option>
    @endforeach
</select>
@endif
