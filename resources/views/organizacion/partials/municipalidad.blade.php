@if(auth()->user()->rol?->nombre === 'SUPERADMIN')
    <div class="field-group municipalidad-field">
        <label for="muni_select_{{ $prefix ?? 'default' }}">
            <i class="fa-solid fa-building-columns"></i> Municipalidad <span class="required">*</span>
        </label>
        <select name="municipalidad_id" id="muni_select_{{ $prefix ?? 'default' }}" required class="form-select">
            <option value="">Seleccione municipalidad...</option>
            @foreach($municipalidades as $municipalidad)
                <option value="{{ $municipalidad->id }}" @selected(old('municipalidad_id', $selectedMuniId ?? null) == $municipalidad->id)>
                    {{ $municipalidad->nombre }}
                </option>
            @endforeach
        </select>
        <small class="field-hint">Entidad titular a la que pertenece el registro</small>
    </div>
@else
    <div class="field-group municipalidad-field">
        <label>
            <i class="fa-solid fa-building-columns"></i> Municipalidad
        </label>
        <div class="readonly-badge" title="Municipalidad asignada a su usuario">
            <i class="fa-solid fa-city"></i>
            <span>{{ auth()->user()->municipalidad?->nombre ?: 'Municipalidad asignada' }}</span>
        </div>
        <input type="hidden" name="municipalidad_id" value="{{ auth()->user()->municipalidad_id }}">
    </div>
@endif
