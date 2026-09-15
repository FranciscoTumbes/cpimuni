@extends('layouts.app')

@php
    $tipoTitles = [
        'organos' => ['title' => 'Editar Órgano', 'icon' => 'fa-landmark', 'tab' => 'organos', 'desc' => 'Actualice los atributos institucionales, naturaleza y jerarquía del órgano.'],
        'unidades' => ['title' => 'Editar Unidad Orgánica', 'icon' => 'fa-network-wired', 'tab' => 'unidades', 'desc' => 'Actualice la denominación, categoría, adscripción y funciones de la unidad.'],
        'puestos' => ['title' => 'Editar Puesto de Trabajo', 'icon' => 'fa-id-badge', 'tab' => 'puestos', 'desc' => 'Actualice la plaza, perfil, nivel ocupacional y unidad asignada.'],
        'funciones' => ['title' => 'Editar Función Institucional', 'icon' => 'fa-list-check', 'tab' => 'funciones', 'desc' => 'Actualice la descripción funcional, base legal y vigencia.'],
    ];
    $currentMeta = $tipoTitles[$tipo] ?? ['title' => 'Editar Registro', 'icon' => 'fa-pen-to-square', 'tab' => 'organos', 'desc' => 'Modificación de registro organizacional.'];
@endphp

@section('title', $currentMeta['title'] . ' | CPIMuni')
@section('page-title', $currentMeta['title'])

@section('content')
    @include('partials.form-feedback')

    {{-- Miga de pan / Navegación de retorno --}}
    <div class="edit-nav-bar">
        <a href="{{ route('organizacion.index', ['tab' => $currentMeta['tab']]) }}" class="btn-back">
            <i class="fa-solid fa-arrow-left"></i> Volver al Catálogo de Organización
        </a>
        <span class="nav-separator">/</span>
        <span class="nav-current">{{ $currentMeta['title'] }}</span>
    </div>

    <section class="card editor-card">
        <div class="card-header-flex">
            <div class="card-header-info">
                <h2 class="card-title">
                    <i class="fa-solid {{ $currentMeta['icon'] }} title-icon"></i>
                    {{ $currentMeta['title'] }}: <span class="highlight-title">{{ $model->nombre ?? ($model->denominacion ?? ($model->codigo ?: 'ID #' . $model->id)) }}</span>
                </h2>
                <p class="card-subtitle">{{ $currentMeta['desc'] }}</p>
            </div>
            <div class="header-status">
                <span class="status-pill {{ in_array($model->estado, ['ACTIVO', 'ACTIVA', 'VIGENTE']) ? 'active' : 'inactive' }}">
                    <span class="dot"></span> {{ $model->estado }}
                </span>
            </div>
        </div>

        <form method="POST" action="{{ route('organizacion.update', [$tipo, $model->id]) }}" class="modern-form">
            @csrf
            @method('PUT')

            {{-- 1. ÓRGANOS --}}
            @if($tipo === 'organos')
                <div class="form-row grid-3">
                    <div class="field-group">
                        <label for="codigo">Código del Órgano</label>
                        <input id="codigo" name="codigo" value="{{ old('codigo', $model->codigo) }}" placeholder="Ej: ALC, GM, OCI">
                    </div>
                    <div class="field-group span-2">
                        <label for="nombre">Nombre Oficial <span class="required">*</span></label>
                        <input id="nombre" name="nombre" required value="{{ old('nombre', $model->nombre) }}" placeholder="Ej: Alcaldía">
                    </div>
                </div>

                <div class="form-row grid-3">
                    <div class="field-group">
                        <label for="naturaleza">Naturaleza</label>
                        <input id="naturaleza" name="naturaleza" value="{{ old('naturaleza', $model->naturaleza) }}" placeholder="Ej: Político, Ejecutivo, Control">
                    </div>
                    <div class="field-group">
                        <label for="tipo_input">Tipo</label>
                        <input id="tipo_input" name="tipo" value="{{ old('tipo', $model->tipo) }}" placeholder="Ej: Dirección, Asesoría, Apoyo">
                    </div>
                    <div class="field-group">
                        <label for="nivel_jerarquico">Nivel Jerárquico</label>
                        <input id="nivel_jerarquico" type="number" name="nivel_jerarquico" min="0" value="{{ old('nivel_jerarquico', $model->nivel_jerarquico) }}">
                    </div>
                </div>

                <div class="form-row grid-2">
                    <div class="field-group">
                        <label for="organo_padre_id">Órgano Superior (Padre)</label>
                        <select id="organo_padre_id" name="organo_padre_id">
                            <option value="">Sin órgano padre (Raíz)</option>
                            @foreach($organos as $item)
                                @if($item->id != $model->id)
                                    <option value="{{ $item->id }}" @selected(old('organo_padre_id', $model->organo_padre_id) == $item->id)>
                                        {{ $item->nombre }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="field-group">
                        <label for="estado">Estado Operativo <span class="required">*</span></label>
                        <select id="estado" name="estado" required>
                            <option value="ACTIVO" @selected(old('estado', $model->estado) === 'ACTIVO')>🟢 ACTIVO</option>
                            <option value="INACTIVO" @selected(old('estado', $model->estado) === 'INACTIVO')>🔴 INACTIVO</option>
                        </select>
                    </div>
                </div>

            {{-- 2. UNIDADES ORGÁNICAS --}}
            @elseif($tipo === 'unidades')
                <div class="form-row grid-4">
                    <div class="field-group">
                        <label for="codigo">Código</label>
                        <input id="codigo" name="codigo" value="{{ old('codigo', $model->codigo) }}" placeholder="Ej: GAF, SGRH">
                    </div>
                    <div class="field-group">
                        <label for="abreviatura">Abreviatura</label>
                        <input id="abreviatura" name="abreviatura" maxlength="30" value="{{ old('abreviatura', $model->abreviatura) }}" placeholder="Ej: GAF">
                    </div>
                    <div class="field-group span-2">
                        <label for="nombre">Nombre de la Unidad <span class="required">*</span></label>
                        <input id="nombre" name="nombre" required value="{{ old('nombre', $model->nombre) }}" placeholder="Ej: Gerencia de Administración">
                    </div>
                </div>

                <div class="form-row grid-3">
                    <div class="field-group">
                        <label for="categoria_institucional">Categoría Institucional</label>
                        <select id="categoria_institucional" name="categoria_institucional">
                            <option value="">Sin categoría asignada</option>
                            @foreach(['GERENCIA' => 'Gerencia', 'SUBGERENCIA' => 'Subgerencia', 'OFICINA' => 'Oficina', 'ÁREA' => 'Área', 'OTRA' => 'Otra'] as $catKey => $catLabel)
                                <option value="{{ $catKey }}" @selected(old('categoria_institucional', $model->categoria_institucional) === $catKey)>
                                    {{ $catLabel }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field-group">
                        <label for="organo_id">Órgano al que pertenece</label>
                        <select id="organo_id" name="organo_id">
                            <option value="">Sin órgano asignado</option>
                            @foreach($organos as $item)
                                <option value="{{ $item->id }}" @selected(old('organo_id', $model->organo_id) == $item->id)>
                                    {{ $item->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field-group">
                        <label for="unidad_padre_id">Unidad Superior (Padre)</label>
                        <select id="unidad_padre_id" name="unidad_padre_id">
                            <option value="">Sin unidad padre</option>
                            @foreach($unidades as $item)
                                @if($item->id != $model->id)
                                    <option value="{{ $item->id }}" @selected(old('unidad_padre_id', $model->unidad_padre_id) == $item->id)>
                                        {{ $item->nombre }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-row grid-4">
                    <div class="field-group">
                        <label for="naturaleza">Naturaleza</label>
                        <input id="naturaleza" name="naturaleza" value="{{ old('naturaleza', $model->naturaleza) }}">
                    </div>
                    <div class="field-group">
                        <label for="tipo_input">Tipo</label>
                        <input id="tipo_input" name="tipo" value="{{ old('tipo', $model->tipo) }}">
                    </div>
                    <div class="field-group">
                        <label for="nivel_jerarquico">Nivel Jerárquico</label>
                        <input id="nivel_jerarquico" type="number" name="nivel_jerarquico" min="0" value="{{ old('nivel_jerarquico', $model->nivel_jerarquico) }}">
                    </div>
                    <div class="field-group">
                        <label for="orden">Orden</label>
                        <input id="orden" type="number" name="orden" min="0" value="{{ old('orden', $model->orden) }}">
                    </div>
                </div>

                <div class="form-row grid-2">
                    <div class="field-group">
                        <label for="finalidad">Finalidad Institucional</label>
                        <textarea id="finalidad" name="finalidad" rows="3">{{ old('finalidad', $model->finalidad) }}</textarea>
                    </div>
                    <div class="field-group">
                        <label for="descripcion">Descripción / Alcance</label>
                        <textarea id="descripcion" name="descripcion" rows="3">{{ old('descripcion', $model->descripcion) }}</textarea>
                    </div>
                </div>

                <div class="form-row grid-2">
                    <div class="field-group">
                        <label for="estado">Estado Operativo <span class="required">*</span></label>
                        <select id="estado" name="estado" required>
                            <option value="ACTIVA" @selected(old('estado', $model->estado) === 'ACTIVA')>🟢 ACTIVA</option>
                            <option value="INACTIVA" @selected(old('estado', $model->estado) === 'INACTIVA')>🔴 INACTIVA</option>
                        </select>
                    </div>
                </div>

            {{-- 3. PUESTOS --}}
            @elseif($tipo === 'puestos')
                <div class="form-row grid-3">
                    <div class="field-group span-2">
                        <label for="unidad_organica_id">Unidad Orgánica Asignada</label>
                        <select id="unidad_organica_id" name="unidad_organica_id">
                            <option value="">Sin unidad orgánica asignada</option>
                            @foreach($unidades as $item)
                                <option value="{{ $item->id }}" @selected(old('unidad_organica_id', $model->unidad_organica_id) == $item->id)>
                                    {{ $item->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field-group">
                        <label for="codigo">Código de Plaza</label>
                        <input id="codigo" name="codigo" value="{{ old('codigo', $model->codigo) }}">
                    </div>
                </div>

                <div class="form-row grid-3">
                    <div class="field-group span-2">
                        <label for="denominacion">Denominación del Puesto <span class="required">*</span></label>
                        <input id="denominacion" name="denominacion" required value="{{ old('denominacion', $model->denominacion) }}">
                    </div>
                    <div class="field-group">
                        <label for="nivel">Nivel Ocupacional</label>
                        <input id="nivel" name="nivel" value="{{ old('nivel', $model->nivel) }}" placeholder="Ej: Directivo, Profesional, Técnico">
                    </div>
                </div>

                <div class="form-row grid-1">
                    <div class="field-group">
                        <label for="finalidad">Finalidad del Puesto</label>
                        <textarea id="finalidad" name="finalidad" rows="2">{{ old('finalidad', $model->finalidad) }}</textarea>
                    </div>
                </div>

                <div class="form-row grid-2">
                    <div class="field-group">
                        <label for="requisitos">Requisitos del Perfil</label>
                        <textarea id="requisitos" name="requisitos" rows="2">{{ old('requisitos', $model->requisitos) }}</textarea>
                    </div>
                    <div class="field-group">
                        <label for="competencias">Competencias</label>
                        <textarea id="competencias" name="competencias" rows="2">{{ old('competencias', $model->competencias) }}</textarea>
                    </div>
                </div>

                <div class="form-row grid-2">
                    <div class="field-group">
                        <label for="estado">Estado <span class="required">*</span></label>
                        <select id="estado" name="estado" required>
                            <option value="ACTIVO" @selected(old('estado', $model->estado) === 'ACTIVO')>🟢 ACTIVO</option>
                            <option value="INACTIVO" @selected(old('estado', $model->estado) === 'INACTIVO')>🔴 INACTIVO</option>
                        </select>
                    </div>
                </div>

            {{-- 4. FUNCIONES --}}
            @else
                <div class="form-row grid-3">
                    <div class="field-group">
                        <label for="unidad_organica_id">Unidad Orgánica</label>
                        <select id="unidad_organica_id" name="unidad_organica_id">
                            <option value="">Sin unidad asignada</option>
                            @foreach($unidades as $item)
                                <option value="{{ $item->id }}" @selected(old('unidad_organica_id', $model->unidad_organica_id) == $item->id)>
                                    {{ $item->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field-group">
                        <label for="puesto_id">Puesto Asignado (Opcional)</label>
                        <select id="puesto_id" name="puesto_id">
                            <option value="">A nivel de unidad</option>
                            @foreach($puestos as $item)
                                <option value="{{ $item->id }}" @selected(old('puesto_id', $model->puesto_id) == $item->id)>
                                    {{ $item->denominacion }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field-group">
                        <label for="codigo">Código</label>
                        <input id="codigo" name="codigo" value="{{ old('codigo', $model->codigo) }}">
                    </div>
                </div>

                <div class="form-row grid-3">
                    <div class="field-group">
                        <label for="tipo_input">Tipo de Función</label>
                        <input id="tipo_input" name="tipo" value="{{ old('tipo', $model->tipo) }}">
                    </div>
                    <div class="field-group span-2">
                        <label for="fuente">Fuente Normativa</label>
                        <input id="fuente" name="fuente" value="{{ old('fuente', $model->fuente) }}" placeholder="Ej: ROF Art. 34">
                    </div>
                </div>

                <div class="form-row grid-1">
                    <div class="field-group">
                        <label for="descripcion">Descripción de la Función <span class="required">*</span></label>
                        <textarea id="descripcion" name="descripcion" required rows="4">{{ old('descripcion', $model->descripcion) }}</textarea>
                    </div>
                </div>

                <div class="form-row grid-3">
                    <div class="field-group">
                        <label for="fecha_inicio">Fecha de Inicio</label>
                        <input id="fecha_inicio" type="date" name="fecha_inicio" value="{{ old('fecha_inicio', optional($model->fecha_inicio)->format('Y-m-d')) }}">
                    </div>
                    <div class="field-group">
                        <label for="fecha_fin">Fecha de Fin</label>
                        <input id="fecha_fin" type="date" name="fecha_fin" value="{{ old('fecha_fin', optional($model->fecha_fin)->format('Y-m-d')) }}">
                    </div>
                    <div class="field-group">
                        <label for="estado">Estado <span class="required">*</span></label>
                        <select id="estado" name="estado" required>
                            <option value="VIGENTE" @selected(old('estado', $model->estado) === 'VIGENTE')>🟢 VIGENTE</option>
                            <option value="NO_VIGENTE" @selected(old('estado', $model->estado) === 'NO_VIGENTE')>🔴 NO VIGENTE</option>
                            <option value="EN_REVISION" @selected(old('estado', $model->estado) === 'EN_REVISION')>🟡 EN REVISIÓN</option>
                        </select>
                    </div>
                </div>
            @endif

            <div class="form-actions edit-actions">
                <a class="btn-secondary" href="{{ route('organizacion.index', ['tab' => $currentMeta['tab']]) }}">
                    <i class="fa-solid fa-arrow-left"></i> Cancelar y Volver
                </a>
                <button class="btn-primary" type="submit">
                    <i class="fa-solid fa-floppy-disk"></i> Guardar Cambios
                </button>
            </div>
        </form>
    </section>
@endsection

@push('styles')
<style>
    .edit-nav-bar {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 20px;
        font-size: 13px;
    }

    .btn-back {
        color: var(--secondary);
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: color 0.15s ease;
    }

    .btn-back:hover {
        color: #1d4ed8;
        text-decoration: underline;
    }

    .nav-separator {
        color: var(--muted);
    }

    .nav-current {
        color: #64748b;
        font-weight: 500;
    }

    .editor-card {
        max-width: 960px;
        margin: 0 auto 30px;
        background: #ffffff;
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 26px 30px;
        box-shadow: var(--shadow-sm);
    }

    .card-header-flex {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding-bottom: 18px;
        border-bottom: 1px solid var(--border);
        margin-bottom: 24px;
    }

    .card-title {
        font-size: 18px;
        font-weight: 800;
        color: var(--text);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .title-icon {
        color: var(--secondary);
    }

    .highlight-title {
        color: var(--secondary);
        font-weight: 700;
    }

    .card-subtitle {
        font-size: 12px;
        color: var(--muted);
        margin: 4px 0 0;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 11px;
        font-weight: 700;
        padding: 5px 12px;
        border-radius: 999px;
    }

    .status-pill.active { background: #dcfce7; color: #166534; }
    .status-pill.inactive { background: #fee2e2; color: #991b1b; }

    .status-pill .dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
    }

    .form-row {
        display: grid;
        gap: 16px;
        margin-bottom: 16px;
    }

    .grid-1 { grid-template-columns: 1fr; }
    .grid-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .grid-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    .grid-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }

    .span-2 { grid-column: span 2; }

    .field-group {
        display: flex;
        flex-direction: column;
    }

    .field-group label {
        font-size: 11px;
        font-weight: 700;
        color: #334155;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .field-group label .required {
        color: var(--danger);
    }

    .field-group input,
    .field-group select,
    .field-group textarea {
        width: 100%;
        padding: 10px 13px;
        border: 1px solid #cbd5e1;
        border-radius: var(--radius-md);
        background: #ffffff;
        font-family: inherit;
        font-size: 13px;
        color: var(--text);
        outline: none;
        transition: all 0.15s ease;
    }

    .field-group input:focus,
    .field-group select:focus,
    .field-group textarea:focus {
        border-color: var(--secondary);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .form-actions.edit-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 26px;
        padding-top: 20px;
        border-top: 1px solid var(--border);
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--secondary) 0%, #1d4ed8 100%);
        color: #ffffff;
        border: none;
        padding: 11px 22px;
        border-radius: var(--radius-md);
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 2px 6px rgba(37, 99, 235, 0.25);
        transition: all 0.15s ease;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
        box-shadow: 0 4px 10px rgba(37, 99, 235, 0.35);
        transform: translateY(-1px);
    }

    .btn-secondary {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid var(--border);
        padding: 11px 20px;
        border-radius: var(--radius-md);
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        transition: all 0.15s ease;
    }

    .btn-secondary:hover {
        background: #e2e8f0;
        color: var(--text);
    }

    @media (max-width: 768px) {
        .grid-4, .grid-3, .grid-2 {
            grid-template-columns: 1fr;
        }
        .span-2 {
            grid-column: auto;
        }
        .card-header-flex {
            flex-direction: column;
            align-items: flex-start;
        }
        .editor-card {
            padding: 20px 16px;
        }
        .form-actions.edit-actions {
            flex-direction: column-reverse;
            width: 100%;
        }
        .btn-primary, .btn-secondary {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endpush
