@extends('layouts.app')

@section('title', 'Estructura y Organización | CPIMuni')
@section('page-title', 'Estructura y Organización Municipal')

@section('content')
    @include('partials.form-feedback')

    {{-- Barra de Contexto Municipal --}}
    <div class="org-header-toolbar">
        <div class="header-info">
            <h1 class="page-main-title">
                <i class="fa-solid fa-sitemap title-icon"></i>
                Gestión Organizacional y Estructura
            </h1>
            <p class="page-subtitle">
                Administración integral de Órganos, Unidades Orgánicas, Puestos y Funciones institucionales.
            </p>
        </div>

        @if(auth()->user()->rol?->nombre === 'SUPERADMIN')
            <div class="superadmin-filter-box">
                <label for="filter_muni_global"><i class="fa-solid fa-filter"></i> Filtrar por Municipalidad:</label>
                <div class="filter-input-wrap">
                    <select id="filter_muni_global" onchange="filterByMunicipality(this.value)">
                        <option value="">Todas las Municipalidades</option>
                        @foreach($municipalidades as $muni)
                            <option value="{{ $muni->id }}" @selected($selectedMuniId == $muni->id)>
                                {{ $muni->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @if($selectedMuniId)
                        <a href="{{ route('organizacion.index') }}" class="btn-clear-muni" title="Ver todas">
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    @endif
                </div>
            </div>
        @else
            <div class="muni-badge-active">
                <i class="fa-solid fa-building-columns"></i>
                <div>
                    <span class="muni-badge-label">Municipalidad Asignada</span>
                    <strong class="muni-badge-name">{{ auth()->user()->municipalidad?->nombre ?: 'Sin municipalidad' }}</strong>
                </div>
            </div>
        @endif
    </div>

    {{-- Tarjetas de Estadísticas (KPIs) --}}
    <div class="stats-grid">
        <div class="stat-box" onclick="switchTab('organos')">
            <div class="stat-box-icon blue">
                <i class="fa-solid fa-landmark"></i>
            </div>
            <div class="stat-box-data">
                <span class="stat-box-value">{{ $organos->count() }}</span>
                <span class="stat-box-label">Órganos</span>
            </div>
        </div>

        <div class="stat-box" onclick="switchTab('unidades')">
            <div class="stat-box-icon emerald">
                <i class="fa-solid fa-network-wired"></i>
            </div>
            <div class="stat-box-data">
                <span class="stat-box-value">{{ $unidades->count() }}</span>
                <span class="stat-box-label">Unidades Orgánicas</span>
            </div>
        </div>

        <div class="stat-box" onclick="switchTab('puestos')">
            <div class="stat-box-icon indigo">
                <i class="fa-solid fa-id-badge"></i>
            </div>
            <div class="stat-box-data">
                <span class="stat-box-value">{{ $puestos->count() }}</span>
                <span class="stat-box-label">Puestos de Trabajo</span>
            </div>
        </div>

        <div class="stat-box" onclick="switchTab('funciones')">
            <div class="stat-box-icon amber">
                <i class="fa-solid fa-list-check"></i>
            </div>
            <div class="stat-box-data">
                <span class="stat-box-value">{{ $funciones->count() }}</span>
                <span class="stat-box-label">Funciones</span>
            </div>
        </div>
    </div>

    {{-- Navegación por Pestañas (Tabs) --}}
    <div class="tabs-nav-wrapper">
        <div class="tabs-nav">
            <button type="button" class="tab-btn active" id="tab-btn-organos" onclick="switchTab('organos')">
                <i class="fa-solid fa-landmark"></i>
                <span>Órganos</span>
                <span class="tab-badge">{{ $organos->count() }}</span>
            </button>
            <button type="button" class="tab-btn" id="tab-btn-unidades" onclick="switchTab('unidades')">
                <i class="fa-solid fa-network-wired"></i>
                <span>Unidades Orgánicas</span>
                <span class="tab-badge">{{ $unidades->count() }}</span>
            </button>
            <button type="button" class="tab-btn" id="tab-btn-puestos" onclick="switchTab('puestos')">
                <i class="fa-solid fa-id-badge"></i>
                <span>Puestos</span>
                <span class="tab-badge">{{ $puestos->count() }}</span>
            </button>
            <button type="button" class="tab-btn" id="tab-btn-funciones" onclick="switchTab('funciones')">
                <i class="fa-solid fa-list-check"></i>
                <span>Funciones</span>
                <span class="tab-badge">{{ $funciones->count() }}</span>
            </button>
            <button type="button" class="tab-btn" id="tab-btn-organigrama" onclick="switchTab('organigrama')">
                <i class="fa-solid fa-diagram-project"></i>
                <span>Vista Jerárquica</span>
            </button>
        </div>
    </div>

    {{-- =========================================================
         PESTAÑA 1: ÓRGANOS
    ========================================================= --}}
    <div class="tab-pane active" id="pane-organos">
        {{-- Formulario Registrar Órgano --}}
        <section class="card form-card">
            <div class="card-header-flex">
                <div class="card-header-info">
                    <h2 class="card-title">
                        <i class="fa-solid fa-circle-plus title-icon"></i> Registrar Nuevo Órgano
                    </h2>
                    <p class="card-subtitle">Alta de órganos de alta dirección, consultivos, control o asesoría.</p>
                </div>
                <button type="button" class="btn-toggle-form" onclick="toggleForm('form-organos-container', this)">
                    <i class="fa-solid fa-chevron-up"></i> <span>Ocultar</span>
                </button>
            </div>

            <div id="form-organos-container" class="form-container">
                <form method="POST" action="{{ route('organizacion.organos.store') }}" class="modern-form">
                    @csrf
                    <div class="form-row grid-4">
                        @include('organizacion.partials.municipalidad', ['prefix' => 'organos'])

                        <div class="field-group">
                            <label for="organo_codigo">Código</label>
                            <input id="organo_codigo" name="codigo" value="{{ old('codigo') }}" placeholder="Ej: ALC, GM, OCI" maxlength="50">
                            <small class="field-hint">Identificador interno</small>
                        </div>

                        <div class="field-group span-2">
                            <label for="organo_nombre">Nombre del Órgano <span class="required">*</span></label>
                            <input id="organo_nombre" name="nombre" required value="{{ old('nombre') }}" placeholder="Ej: Alcaldía, Gerencia Municipal">
                        </div>
                    </div>

                    <div class="form-row grid-4">
                        <div class="field-group">
                            <label for="organo_naturaleza">Naturaleza</label>
                            <input id="organo_naturaleza" name="naturaleza" value="{{ old('naturaleza') }}" placeholder="Ej: Político, Ejecutivo, Control">
                        </div>

                        <div class="field-group">
                            <label for="organo_tipo">Tipo de Órgano</label>
                            <input id="organo_tipo" name="tipo" value="{{ old('tipo') }}" placeholder="Ej: Dirección, Asesoría, Apoyo">
                        </div>

                        <div class="field-group">
                            <label for="organo_nivel">Nivel Jerárquico</label>
                            <input id="organo_nivel" type="number" name="nivel_jerarquico" min="1" value="{{ old('nivel_jerarquico', 1) }}">
                            <small class="field-hint">1 = Nivel superior</small>
                        </div>

                        <div class="field-group">
                            <label for="organo_padre">Órgano Padre / Superior</label>
                            <select id="organo_padre" name="organo_padre_id">
                                <option value="">Sin órgano padre (Raíz)</option>
                                @foreach($organos as $org)
                                    <option value="{{ $org->id }}" {{ old('organo_padre_id') == $org->id ? 'selected' : '' }}>
                                        {{ $org->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button class="btn-primary" type="submit">
                            <i class="fa-solid fa-plus"></i> Registrar Órgano
                        </button>
                        <button class="btn-secondary" type="reset">
                            <i class="fa-solid fa-rotate-left"></i> Limpiar
                        </button>
                    </div>
                </form>
            </div>
        </section>

        {{-- Tabla de Órganos --}}
        <section class="card table-card">
            <div class="table-card-header">
                <div class="card-header-info">
                    <h2 class="card-title">
                        <i class="fa-solid fa-landmark title-icon"></i> Órganos Registrados
                    </h2>
                    <p class="card-subtitle">Listado de órganos municipales activos e inactivos.</p>
                </div>
                <div class="table-filter-wrap">
                    <div class="search-box">
                        <i class="fa-solid fa-magnifying-glass search-icon"></i>
                        <input type="text" placeholder="Buscar en órganos..." onkeyup="filterTableLive(this, 'table-organos')">
                    </div>
                </div>
            </div>

            <div class="table-wrap">
                <table class="modern-table" id="table-organos">
                    <thead>
                        <tr>
                            <th style="width: 120px;">Código</th>
                            <th>Nombre del Órgano</th>
                            <th>Naturaleza</th>
                            <th>Tipo</th>
                            <th style="width: 130px;">Órgano Padre</th>
                            <th style="width: 90px; text-align: center;">Nivel</th>
                            <th style="width: 100px; text-align: center;">Estado</th>
                            <th style="width: 100px; text-align: center;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($organos as $organo)
                            <tr>
                                <td>
                                    <span class="code-badge">{{ $organo->codigo ?: '—' }}</span>
                                </td>
                                <td>
                                    <div class="unit-name-cell">
                                        <strong>{{ $organo->nombre }}</strong>
                                        @if(auth()->user()->rol?->nombre === 'SUPERADMIN' && $organo->municipalidad)
                                            <span class="muni-chip"><i class="fa-solid fa-city"></i> {{ $organo->municipalidad->nombre }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td>{{ $organo->naturaleza ?: '—' }}</td>
                                <td>
                                    @if($organo->tipo)
                                        <span class="badge-type">{{ $organo->tipo }}</span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    @if($organo->padre)
                                        <span class="parent-chip" title="{{ $organo->padre->nombre }}">
                                            <i class="fa-solid fa-arrow-turn-up fa-rotate-90"></i> {{ Str::limit($organo->padre->nombre, 22) }}
                                        </span>
                                    @else
                                        <span class="badge-pill gray">Raíz</span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    <span class="level-badge">N{{ $organo->nivel_jerarquico ?: 1 }}</span>
                                </td>
                                <td style="text-align: center;">
                                    <span class="status-pill {{ $organo->estado === 'ACTIVO' ? 'active' : 'inactive' }}">
                                        <span class="dot"></span>
                                        {{ $organo->estado }}
                                    </span>
                                </td>
                                <td class="actions-cell">
                                    <div class="action-buttons">
                                        <a class="btn-action edit" title="Editar órgano" href="{{ route('organizacion.edit', ['organos', $organo->id]) }}">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <form method="POST" action="{{ route('organizacion.destroy', ['organos', $organo->id]) }}" onsubmit="return confirm('¿Confirma dar de baja lógica este órgano?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn-action danger" type="submit" title="Dar de baja">
                                                <i class="fa-solid fa-ban"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    <div class="empty-state">
                                        <div class="empty-icon"><i class="fa-solid fa-landmark"></i></div>
                                        <div class="empty-title">Sin órganos registrados</div>
                                        <p class="empty-desc">No hay órganos configurados para la municipalidad seleccionada.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    {{-- =========================================================
         PESTAÑA 2: UNIDADES ORGÁNICAS
    ========================================================= --}}
    <div class="tab-pane" id="pane-unidades">
        {{-- Formulario Registrar Unidad --}}
        <section class="card form-card">
            <div class="card-header-flex">
                <div class="card-header-info">
                    <h2 class="card-title">
                        <i class="fa-solid fa-circle-plus title-icon"></i> Registrar Unidad Orgánica
                    </h2>
                    <p class="card-subtitle">Gerencias, subgerencias, oficinas y dependencias operativas.</p>
                </div>
                <button type="button" class="btn-toggle-form" onclick="toggleForm('form-unidades-container', this)">
                    <i class="fa-solid fa-chevron-up"></i> <span>Ocultar</span>
                </button>
            </div>

            <div id="form-unidades-container" class="form-container">
                <form method="POST" action="{{ route('organizacion.unidades.store') }}" class="modern-form">
                    @csrf
                    <div class="form-row grid-4">
                        @include('organizacion.partials.municipalidad', ['prefix' => 'unidades'])

                        <div class="field-group">
                            <label for="unidad_organo">Órgano Perteneciente</label>
                            <select id="unidad_organo" name="organo_id">
                                <option value="">Sin órgano asignado</option>
                                @foreach($organos as $org)
                                    <option value="{{ $org->id }}" {{ old('organo_id') == $org->id ? 'selected' : '' }}>
                                        {{ $org->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="field-group">
                            <label for="unidad_categoria">Categoría Institucional</label>
                            <select id="unidad_categoria" name="categoria_institucional">
                                <option value="">Seleccione categoría...</option>
                                @foreach(['GERENCIA' => 'Gerencia', 'SUBGERENCIA' => 'Subgerencia', 'OFICINA' => 'Oficina', 'ÁREA' => 'Área', 'OTRA' => 'Otra'] as $catVal => $catLabel)
                                    <option value="{{ $catVal }}" {{ old('categoria_institucional') === $catVal ? 'selected' : '' }}>
                                        {{ $catLabel }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="field-group">
                            <label for="unidad_padre">Unidad Padre (Superior)</label>
                            <select id="unidad_padre" name="unidad_padre_id">
                                <option value="">Sin unidad padre</option>
                                @foreach($unidades as $un)
                                    <option value="{{ $un->id }}" {{ old('unidad_padre_id') == $un->id ? 'selected' : '' }}>
                                        {{ $un->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-row grid-4">
                        <div class="field-group">
                            <label for="unidad_codigo">Código</label>
                            <input id="unidad_codigo" name="codigo" value="{{ old('codigo') }}" placeholder="Ej: GAF, SGRH">
                        </div>

                        <div class="field-group">
                            <label for="unidad_abreviatura">Abreviatura</label>
                            <input id="unidad_abreviatura" name="abreviatura" maxlength="30" value="{{ old('abreviatura') }}" placeholder="Ej: GAF">
                        </div>

                        <div class="field-group span-2">
                            <label for="unidad_nombre">Nombre de la Unidad Orgánica <span class="required">*</span></label>
                            <input id="unidad_nombre" name="nombre" required value="{{ old('nombre') }}" placeholder="Ej: Gerencia de Administración y Finanzas">
                        </div>
                    </div>

                    <div class="form-row grid-4">
                        <div class="field-group">
                            <label for="unidad_naturaleza">Naturaleza</label>
                            <input id="unidad_naturaleza" name="naturaleza" value="{{ old('naturaleza') }}" placeholder="Ej: Sustantiva, Adjetiva">
                        </div>

                        <div class="field-group">
                            <label for="unidad_tipo">Tipo</label>
                            <input id="unidad_tipo" name="tipo" value="{{ old('tipo') }}" placeholder="Ej: Línea, Asesoramiento">
                        </div>

                        <div class="field-group">
                            <label for="unidad_nivel">Nivel Jerárquico</label>
                            <input id="unidad_nivel" type="number" name="nivel_jerarquico" min="0" value="{{ old('nivel_jerarquico', 2) }}">
                        </div>

                        <div class="field-group">
                            <label for="unidad_orden">Orden</label>
                            <input id="unidad_orden" type="number" name="orden" min="0" value="{{ old('orden', 0) }}">
                        </div>
                    </div>

                    <div class="form-row grid-2">
                        <div class="field-group">
                            <label for="unidad_finalidad">Finalidad Institucional</label>
                            <textarea id="unidad_finalidad" name="finalidad" rows="2" placeholder="Propósito general de la unidad orgánica...">{{ old('finalidad') }}</textarea>
                        </div>

                        <div class="field-group">
                            <label for="unidad_descripcion">Descripción / Alcance</label>
                            <textarea id="unidad_descripcion" name="descripcion" rows="2" placeholder="Detalle funcional o marco operativo...">{{ old('descripcion') }}</textarea>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button class="btn-primary" type="submit">
                            <i class="fa-solid fa-plus"></i> Registrar Unidad Orgánica
                        </button>
                        <button class="btn-secondary" type="reset">
                            <i class="fa-solid fa-rotate-left"></i> Limpiar
                        </button>
                    </div>
                </form>
            </div>
        </section>

        {{-- Tabla de Unidades Orgánicas --}}
        <section class="card table-card">
            <div class="table-card-header">
                <div class="card-header-info">
                    <h2 class="card-title">
                        <i class="fa-solid fa-network-wired title-icon"></i> Unidades Orgánicas Registradas
                    </h2>
                    <p class="card-subtitle">Listado maestro con dependencias jerárquicas y categoría.</p>
                </div>
                <div class="table-filter-wrap">
                    <div class="search-box">
                        <i class="fa-solid fa-magnifying-glass search-icon"></i>
                        <input type="text" placeholder="Buscar en unidades..." onkeyup="filterTableLive(this, 'table-unidades')">
                    </div>
                </div>
            </div>

            <div class="table-wrap">
                <table class="modern-table" id="table-unidades">
                    <thead>
                        <tr>
                            <th style="width: 120px;">Código</th>
                            <th>Unidad Orgánica</th>
                            <th style="width: 140px;">Categoría</th>
                            <th>Órgano / Padre</th>
                            <th>Naturaleza / Tipo</th>
                            <th style="width: 90px; text-align: center;">Nivel / Ord.</th>
                            <th style="width: 90px; text-align: center;">Estado</th>
                            <th style="width: 100px; text-align: center;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($unidades as $unidad)
                            <tr>
                                <td>
                                    <div class="code-badge-wrap">
                                        <span class="code-badge">{{ $unidad->codigo ?: ($unidad->abreviatura ?: '—') }}</span>
                                        @if($unidad->abreviatura && $unidad->codigo && $unidad->abreviatura !== $unidad->codigo)
                                            <span class="parent-chip">{{ $unidad->abreviatura }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="unit-name-cell">
                                        <strong>{{ $unidad->nombre }}</strong>
                                        @if(auth()->user()->rol?->nombre === 'SUPERADMIN' && $unidad->municipalidad)
                                            <span class="muni-chip"><i class="fa-solid fa-city"></i> {{ $unidad->municipalidad->nombre }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $catU = strtoupper($unidad->categoria_institucional ?? '');
                                    @endphp
                                    @if($catU === 'GERENCIA')
                                        <span class="badge-category cat-gobierno">Gerencia</span>
                                    @elseif($catU === 'SUBGERENCIA')
                                        <span class="badge-category cat-linea">Subgerencia</span>
                                    @elseif($catU === 'OFICINA')
                                        <span class="badge-category cat-asesoria">Oficina</span>
                                    @elseif($catU === 'ÁREA' || $catU === 'AREA')
                                        <span class="badge-category cat-control">Área</span>
                                    @else
                                        <span class="badge-type">{{ $unidad->categoria_institucional ?: '—' }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="hierarchy-cell">
                                        @if($unidad->organo)
                                            <span class="chip-organo" title="Órgano"><i class="fa-solid fa-landmark"></i> {{ Str::limit($unidad->organo->nombre, 22) }}</span>
                                        @endif
                                        @if($unidad->padre)
                                            <span class="parent-chip" title="Unidad Padre"><i class="fa-solid fa-arrow-turn-up fa-rotate-90"></i> {{ Str::limit($unidad->padre->nombre, 20) }}</span>
                                        @endif
                                        @if(!$unidad->organo && !$unidad->padre)
                                            <span class="badge-pill gray">Sin asignar</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="types-cell">
                                        <span>{{ $unidad->naturaleza ?: '—' }}</span>
                                        @if($unidad->tipo)
                                            <small class="text-muted">({{ $unidad->tipo }})</small>
                                        @endif
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    <span class="level-badge">N{{ $unidad->nivel_jerarquico ?: 1 }} <small class="order-sub">#{{ $unidad->orden ?: 0 }}</small></span>
                                </td>
                                <td style="text-align: center;">
                                    <span class="status-pill {{ in_array($unidad->estado, ['ACTIVA', 'ACTIVO']) ? 'active' : 'inactive' }}">
                                        <span class="dot"></span>
                                        {{ $unidad->estado }}
                                    </span>
                                </td>
                                <td class="actions-cell">
                                    <div class="action-buttons">
                                        <a class="btn-action edit" title="Editar unidad" href="{{ route('organizacion.edit', ['unidades', $unidad->id]) }}">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <form method="POST" action="{{ route('organizacion.destroy', ['unidades', $unidad->id]) }}" onsubmit="return confirm('¿Confirma dar de baja lógica esta unidad orgánica?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn-action danger" type="submit" title="Dar de baja">
                                                <i class="fa-solid fa-ban"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    <div class="empty-state">
                                        <div class="empty-icon"><i class="fa-solid fa-network-wired"></i></div>
                                        <div class="empty-title">Sin unidades orgánicas</div>
                                        <p class="empty-desc">No hay unidades registradas para la municipalidad seleccionada.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    {{-- =========================================================
         PESTAÑA 3: PUESTOS
    ========================================================= --}}
    <div class="tab-pane" id="pane-puestos">
        {{-- Formulario Registrar Puesto --}}
        <section class="card form-card">
            <div class="card-header-flex">
                <div class="card-header-info">
                    <h2 class="card-title">
                        <i class="fa-solid fa-circle-plus title-icon"></i> Registrar Puesto de Trabajo
                    </h2>
                    <p class="card-subtitle">Plazas, cargos y puestos clasificados del cuadro de personal.</p>
                </div>
                <button type="button" class="btn-toggle-form" onclick="toggleForm('form-puestos-container', this)">
                    <i class="fa-solid fa-chevron-up"></i> <span>Ocultar</span>
                </button>
            </div>

            <div id="form-puestos-container" class="form-container">
                <form method="POST" action="{{ route('organizacion.puestos.store') }}" class="modern-form">
                    @csrf
                    <div class="form-row grid-4">
                        @include('organizacion.partials.municipalidad', ['prefix' => 'puestos'])

                        <div class="field-group span-2">
                            <label for="puesto_unidad">Unidad Orgánica Asignada</label>
                            <select id="puesto_unidad" name="unidad_organica_id">
                                <option value="">Sin unidad orgánica asignada</option>
                                @foreach($unidades as $un)
                                    <option value="{{ $un->id }}" {{ old('unidad_organica_id') == $un->id ? 'selected' : '' }}>
                                        {{ $un->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="field-group">
                            <label for="puesto_codigo">Código de Plaza / Puesto</label>
                            <input id="puesto_codigo" name="codigo" value="{{ old('codigo') }}" placeholder="Ej: P-014, GAF-01">
                        </div>
                    </div>

                    <div class="form-row grid-3">
                        <div class="field-group span-2">
                            <label for="puesto_denominacion">Denominación del Cargo / Puesto <span class="required">*</span></label>
                            <input id="puesto_denominacion" name="denominacion" required value="{{ old('denominacion') }}" placeholder="Ej: Subgerente de Recursos Humanos, Especialista en Presupuesto">
                        </div>

                        <div class="field-group">
                            <label for="puesto_nivel">Nivel / Grupo Ocupacional</label>
                            <input id="puesto_nivel" name="nivel" value="{{ old('nivel') }}" placeholder="Ej: Ejecutivo, Profesional, Técnico">
                        </div>
                    </div>

                    <div class="form-row grid-1">
                        <div class="field-group">
                            <label for="puesto_finalidad">Finalidad del Puesto</label>
                            <textarea id="puesto_finalidad" name="finalidad" rows="2" placeholder="Misión principal y contribución del puesto...">{{ old('finalidad') }}</textarea>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button class="btn-primary" type="submit">
                            <i class="fa-solid fa-plus"></i> Registrar Puesto
                        </button>
                        <button class="btn-secondary" type="reset">
                            <i class="fa-solid fa-rotate-left"></i> Limpiar
                        </button>
                    </div>
                </form>
            </div>
        </section>

        {{-- Tabla de Puestos --}}
        <section class="card table-card">
            <div class="table-card-header">
                <div class="card-header-info">
                    <h2 class="card-title">
                        <i class="fa-solid fa-id-badge title-icon"></i> Puestos de Trabajo Registrados
                    </h2>
                    <p class="card-subtitle">Cargos vinculados a unidades orgánicas.</p>
                </div>
                <div class="table-filter-wrap">
                    <div class="search-box">
                        <i class="fa-solid fa-magnifying-glass search-icon"></i>
                        <input type="text" placeholder="Buscar en puestos..." onkeyup="filterTableLive(this, 'table-puestos')">
                    </div>
                </div>
            </div>

            <div class="table-wrap">
                <table class="modern-table" id="table-puestos">
                    <thead>
                        <tr>
                            <th style="width: 120px;">Código</th>
                            <th>Denominación del Puesto</th>
                            <th>Unidad Orgánica</th>
                            <th style="width: 140px;">Nivel Ocupacional</th>
                            <th style="width: 100px; text-align: center;">Estado</th>
                            <th style="width: 100px; text-align: center;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($puestos as $puesto)
                            <tr>
                                <td>
                                    <span class="code-badge">{{ $puesto->codigo ?: '—' }}</span>
                                </td>
                                <td>
                                    <div class="unit-name-cell">
                                        <strong>{{ $puesto->denominacion }}</strong>
                                        @if($puesto->finalidad)
                                            <span class="unit-desc-text">{{ Str::limit($puesto->finalidad, 65) }}</span>
                                        @endif
                                        @if(auth()->user()->rol?->nombre === 'SUPERADMIN' && $puesto->municipalidad)
                                            <span class="muni-chip"><i class="fa-solid fa-city"></i> {{ $puesto->municipalidad->nombre }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($puesto->unidad)
                                        <span class="chip-unidad"><i class="fa-solid fa-network-wired"></i> {{ $puesto->unidad->nombre }}</span>
                                    @else
                                        <span class="badge-pill gray">Sin unidad asignada</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge-type">{{ $puesto->nivel ?: '—' }}</span>
                                </td>
                                <td style="text-align: center;">
                                    <span class="status-pill {{ $puesto->estado === 'ACTIVO' ? 'active' : 'inactive' }}">
                                        <span class="dot"></span>
                                        {{ $puesto->estado }}
                                    </span>
                                </td>
                                <td class="actions-cell">
                                    <div class="action-buttons">
                                        <a class="btn-action edit" title="Editar puesto" href="{{ route('organizacion.edit', ['puestos', $puesto->id]) }}">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <form method="POST" action="{{ route('organizacion.destroy', ['puestos', $puesto->id]) }}" onsubmit="return confirm('¿Confirma dar de baja lógica este puesto?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn-action danger" type="submit" title="Dar de baja">
                                                <i class="fa-solid fa-ban"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">
                                        <div class="empty-icon"><i class="fa-solid fa-id-badge"></i></div>
                                        <div class="empty-title">Sin puestos registrados</div>
                                        <p class="empty-desc">No hay puestos de trabajo registrados para la municipalidad.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    {{-- =========================================================
         PESTAÑA 4: FUNCIONES
    ========================================================= --}}
    <div class="tab-pane" id="pane-funciones">
        {{-- Formulario Registrar Función --}}
        <section class="card form-card">
            <div class="card-header-flex">
                <div class="card-header-info">
                    <h2 class="card-title">
                        <i class="fa-solid fa-circle-plus title-icon"></i> Registrar Función Institucional
                    </h2>
                    <p class="card-subtitle">Funciones generales y específicas según ROF o manual de organización.</p>
                </div>
                <button type="button" class="btn-toggle-form" onclick="toggleForm('form-funciones-container', this)">
                    <i class="fa-solid fa-chevron-up"></i> <span>Ocultar</span>
                </button>
            </div>

            <div id="form-funciones-container" class="form-container">
                <form method="POST" action="{{ route('organizacion.funciones.store') }}" class="modern-form">
                    @csrf
                    <div class="form-row grid-4">
                        @include('organizacion.partials.municipalidad', ['prefix' => 'funciones'])

                        <div class="field-group">
                            <label for="func_unidad">Unidad Orgánica</label>
                            <select id="func_unidad" name="unidad_organica_id">
                                <option value="">Seleccione unidad...</option>
                                @foreach($unidades as $un)
                                    <option value="{{ $un->id }}" {{ old('unidad_organica_id') == $un->id ? 'selected' : '' }}>
                                        {{ $un->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="field-group">
                            <label for="func_puesto">Puesto Asignado (Opcional)</label>
                            <select id="func_puesto" name="puesto_id">
                                <option value="">Función a nivel de unidad</option>
                                @foreach($puestos as $puesto)
                                    <option value="{{ $puesto->id }}" {{ old('puesto_id') == $puesto->id ? 'selected' : '' }}>
                                        {{ $puesto->denominacion }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="field-group">
                            <label for="func_codigo">Código de Función</label>
                            <input id="func_codigo" name="codigo" value="{{ old('codigo') }}" placeholder="Ej: F-01, GAF-F02">
                        </div>
                    </div>

                    <div class="form-row grid-4">
                        <div class="field-group">
                            <label for="func_tipo">Tipo de Función</label>
                            <input id="func_tipo" name="tipo" value="{{ old('tipo') }}" placeholder="Ej: General, Específica">
                        </div>

                        <div class="field-group">
                            <label for="func_fuente">Fuente Normativa</label>
                            <input id="func_fuente" name="fuente" value="{{ old('fuente') }}" placeholder="Ej: ROF Art. 45, MOF">
                        </div>

                        <div class="field-group">
                            <label for="func_inicio">Fecha de Inicio</label>
                            <input id="func_inicio" type="date" name="fecha_inicio" value="{{ old('fecha_inicio') }}">
                        </div>

                        <div class="field-group">
                            <label for="func_fin">Fecha de Fin (Vigencia)</label>
                            <input id="func_fin" type="date" name="fecha_fin" value="{{ old('fecha_fin') }}">
                        </div>
                    </div>

                    <div class="form-row grid-1">
                        <div class="field-group">
                            <label for="func_desc">Descripción Detallada de la Función <span class="required">*</span></label>
                            <textarea id="func_desc" name="descripcion" required rows="3" placeholder="Redacte el enunciado de la función con verbo en infinitivo (Ej: Formular, ejecutar y supervisar...)">{{ old('descripcion') }}</textarea>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button class="btn-primary" type="submit">
                            <i class="fa-solid fa-plus"></i> Registrar Función
                        </button>
                        <button class="btn-secondary" type="reset">
                            <i class="fa-solid fa-rotate-left"></i> Limpiar
                        </button>
                    </div>
                </form>
            </div>
        </section>

        {{-- Tabla de Funciones --}}
        <section class="card table-card">
            <div class="table-card-header">
                <div class="card-header-info">
                    <h2 class="card-title">
                        <i class="fa-solid fa-list-check title-icon"></i> Funciones Registradas
                    </h2>
                    <p class="card-subtitle">Catálogo de funciones operativas y normativas.</p>
                </div>
                <div class="table-filter-wrap">
                    <div class="search-box">
                        <i class="fa-solid fa-magnifying-glass search-icon"></i>
                        <input type="text" placeholder="Buscar en funciones..." onkeyup="filterTableLive(this, 'table-funciones')">
                    </div>
                </div>
            </div>

            <div class="table-wrap">
                <table class="modern-table" id="table-funciones">
                    <thead>
                        <tr>
                            <th style="width: 100px;">Código</th>
                            <th>Descripción de la Función</th>
                            <th style="width: 180px;">Unidad / Puesto</th>
                            <th style="width: 110px;">Tipo</th>
                            <th style="width: 120px;">Vigencia</th>
                            <th style="width: 110px; text-align: center;">Estado</th>
                            <th style="width: 100px; text-align: center;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($funciones as $funcion)
                            <tr>
                                <td>
                                    <span class="code-badge">{{ $funcion->codigo ?: '—' }}</span>
                                </td>
                                <td>
                                    <div class="unit-name-cell">
                                        <p class="function-text">{{ $funcion->descripcion }}</p>
                                        @if($funcion->fuente)
                                            <span class="fuente-tag"><i class="fa-solid fa-book"></i> {{ $funcion->fuente }}</span>
                                        @endif
                                        @if(auth()->user()->rol?->nombre === 'SUPERADMIN' && $funcion->municipalidad)
                                            <span class="muni-chip"><i class="fa-solid fa-city"></i> {{ $funcion->municipalidad->nombre }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="hierarchy-cell">
                                        @if($funcion->unidad)
                                            <span class="chip-unidad" title="Unidad Orgánica"><i class="fa-solid fa-network-wired"></i> {{ Str::limit($funcion->unidad->nombre, 22) }}</span>
                                        @endif
                                        @if($funcion->puesto)
                                            <span class="chip-puesto" title="Puesto"><i class="fa-solid fa-id-badge"></i> {{ Str::limit($funcion->puesto->denominacion, 22) }}</span>
                                        @endif
                                        @if(!$funcion->unidad && !$funcion->puesto)
                                            <span class="badge-pill gray">Institucional</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="badge-type">{{ $funcion->tipo ?: 'General' }}</span>
                                </td>
                                <td>
                                    <small class="date-info">
                                        {{ $funcion->fecha_inicio ? $funcion->fecha_inicio->format('d/m/Y') : 'Inicio indefinido' }}
                                        <br>
                                        <span class="text-muted">Hasta: {{ $funcion->fecha_fin ? $funcion->fecha_fin->format('d/m/Y') : 'Vigente' }}</span>
                                    </small>
                                </td>
                                <td style="text-align: center;">
                                    <span class="status-pill {{ $funcion->estado === 'VIGENTE' ? 'active' : ($funcion->estado === 'NO_VIGENTE' ? 'inactive' : 'warning') }}">
                                        <span class="dot"></span>
                                        {{ $funcion->estado }}
                                    </span>
                                </td>
                                <td class="actions-cell">
                                    <div class="action-buttons">
                                        <a class="btn-action edit" title="Editar función" href="{{ route('organizacion.edit', ['funciones', $funcion->id]) }}">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <form method="POST" action="{{ route('organizacion.destroy', ['funciones', $funcion->id]) }}" onsubmit="return confirm('¿Confirma dar de baja lógica esta función?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn-action danger" type="submit" title="Dar de baja">
                                                <i class="fa-solid fa-ban"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <div class="empty-icon"><i class="fa-solid fa-list-check"></i></div>
                                        <div class="empty-title">Sin funciones registradas</div>
                                        <p class="empty-desc">No hay funciones registradas para la municipalidad.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    {{-- =========================================================
         PESTAÑA 5: ORGANIGRAMA / VISTA JERÁRQUICA
    ========================================================= --}}
    <div class="tab-pane" id="pane-organigrama">
        <section class="card">
            <div class="card-header-flex">
                <div class="card-header-info">
                    <h2 class="card-title">
                        <i class="fa-solid fa-diagram-project title-icon"></i> Vista Jerárquica de la Organización
                    </h2>
                    <p class="card-subtitle">Árbol de dependencias entre Órganos, Unidades Orgánicas y Puestos.</p>
                </div>
            </div>

            <div class="tree-container">
                @php
                    $organosRaiz = $organos->whereNull('organo_padre_id');
                @endphp

                @forelse($organosRaiz as $orgRaiz)
                    <div class="tree-root-card">
                        <div class="tree-node root">
                            <div class="node-icon"><i class="fa-solid fa-landmark"></i></div>
                            <div class="node-info">
                                <span class="node-code">{{ $orgRaiz->codigo ?: 'ÓRGANO' }}</span>
                                <strong class="node-name">{{ $orgRaiz->nombre }}</strong>
                                <span class="node-meta">{{ $orgRaiz->tipo ?: 'Alta Dirección' }}</span>
                            </div>
                        </div>

                        {{-- Unidades dependientes de este órgano --}}
                        @php
                            $unidadesDirectas = $unidades->where('organo_id', $orgRaiz->id)->whereNull('unidad_padre_id');
                        @endphp

                        @if($unidadesDirectas->count() > 0)
                            <div class="tree-children">
                                @foreach($unidadesDirectas as $uDirecta)
                                    <div class="tree-branch">
                                        <div class="tree-node unit">
                                            <div class="node-icon green"><i class="fa-solid fa-network-wired"></i></div>
                                            <div class="node-info">
                                                <span class="node-code">{{ $uDirecta->codigo ?: ($uDirecta->abreviatura ?: 'UNIDAD') }}</span>
                                                <strong class="node-name">{{ $uDirecta->nombre }}</strong>
                                                <span class="node-meta">{{ $uDirecta->categoria_institucional ?: 'Unidad Orgánica' }}</span>
                                            </div>
                                        </div>

                                        {{-- Subunidades --}}
                                        @php
                                            $subUnidades = $unidades->where('unidad_padre_id', $uDirecta->id);
                                            $puestosUnidad = $puestos->where('unidad_organica_id', $uDirecta->id);
                                        @endphp

                                        @if($subUnidades->count() > 0 || $puestosUnidad->count() > 0)
                                            <div class="tree-subchildren">
                                                @foreach($subUnidades as $subU)
                                                    <div class="tree-node subunit">
                                                        <div class="node-icon amber"><i class="fa-solid fa-folder-tree"></i></div>
                                                        <div class="node-info">
                                                            <span class="node-code">{{ $subU->codigo ?: 'SUB' }}</span>
                                                            <span class="node-name-sm">{{ $subU->nombre }}</span>
                                                        </div>
                                                    </div>
                                                @endforeach

                                                @foreach($puestosUnidad as $pst)
                                                    <div class="tree-node post">
                                                        <div class="node-icon purple"><i class="fa-solid fa-id-badge"></i></div>
                                                        <div class="node-info">
                                                            <span class="node-code">{{ $pst->codigo ?: 'PUESTO' }}</span>
                                                            <span class="node-name-sm">{{ $pst->denominacion }}</span>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="empty-state">
                        <div class="empty-icon"><i class="fa-solid fa-diagram-project"></i></div>
                        <div class="empty-title">Sin estructura jerárquica para graficar</div>
                        <p class="empty-desc">Registra órganos y unidades para generar el árbol organizacional.</p>
                    </div>
                @endforelse
            </div>
        </section>
    </div>
@endsection

@push('styles')
<style>
    /* =========================================================
       BARRA DE CONTEXTO Y ENCABEZADO
    ========================================================= */
    .org-header-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 22px;
        background: #ffffff;
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 18px 24px;
        box-shadow: var(--shadow-sm);
    }

    .page-main-title {
        font-size: 20px;
        font-weight: 800;
        color: var(--text);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .page-subtitle {
        font-size: 12px;
        color: var(--muted);
        margin: 4px 0 0 0;
    }

    .title-icon {
        color: var(--secondary);
    }

    .superadmin-filter-box {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #f8fafc;
        border: 1px solid var(--border);
        padding: 8px 14px;
        border-radius: var(--radius-md);
    }

    .superadmin-filter-box label {
        font-size: 11px;
        font-weight: 700;
        color: #475569;
        display: flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
    }

    .filter-input-wrap {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .filter-input-wrap select {
        padding: 6px 10px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        font-size: 12px;
        background: #fff;
        color: var(--text);
        font-weight: 600;
        outline: none;
    }

    .btn-clear-muni {
        color: var(--danger);
        padding: 4px 8px;
        border-radius: 6px;
        background: #fee2e2;
        text-decoration: none;
        font-size: 12px;
    }

    .muni-badge-active {
        display: flex;
        align-items: center;
        gap: 12px;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        padding: 8px 16px;
        border-radius: var(--radius-md);
        color: var(--secondary);
    }

    .muni-badge-active i {
        font-size: 20px;
    }

    .muni-badge-label {
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 700;
        color: #3b82f6;
        display: block;
    }

    .muni-badge-name {
        font-size: 13px;
        color: #1e3a8a;
    }

    /* =========================================================
       KPIS / TARJETAS ESTADÍSTICAS
    ========================================================= */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 22px;
    }

    .stat-box {
        background: #ffffff;
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 16px 18px;
        display: flex;
        align-items: center;
        gap: 14px;
        box-shadow: var(--shadow-sm);
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .stat-box:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
        border-color: #cbd5e1;
    }

    .stat-box-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }

    .stat-box-icon.blue { background: #eff6ff; color: #2563eb; }
    .stat-box-icon.emerald { background: #dcfce7; color: #16a34a; }
    .stat-box-icon.indigo { background: #e0e7ff; color: #4338ca; }
    .stat-box-icon.amber { background: #fef3c7; color: #d97706; }

    .stat-box-data {
        display: flex;
        flex-direction: column;
    }

    .stat-box-value {
        font-size: 22px;
        font-weight: 800;
        color: var(--text);
        line-height: 1.1;
    }

    .stat-box-label {
        font-size: 11px;
        font-weight: 600;
        color: var(--muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 3px;
    }

    /* =========================================================
       SISTEMA DE PESTAÑAS (TABS)
    ========================================================= */
    .tabs-nav-wrapper {
        margin-bottom: 22px;
        border-bottom: 2px solid var(--border);
    }

    .tabs-nav {
        display: flex;
        gap: 8px;
        overflow-x: auto;
        padding-bottom: -2px;
    }

    .tab-btn {
        background: transparent;
        border: none;
        border-bottom: 3px solid transparent;
        padding: 12px 18px;
        font-size: 13px;
        font-weight: 600;
        color: #64748b;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
        transition: all 0.15s ease;
        margin-bottom: -2px;
    }

    .tab-btn:hover {
        color: var(--secondary);
        background: #f8fafc;
        border-radius: 8px 8px 0 0;
    }

    .tab-btn.active {
        color: var(--secondary);
        border-bottom-color: var(--secondary);
        background: #ffffff;
        border-radius: 8px 8px 0 0;
        font-weight: 700;
    }

    .tab-badge {
        font-size: 11px;
        font-weight: 700;
        padding: 2px 7px;
        border-radius: 999px;
        background: #f1f5f9;
        color: #475569;
    }

    .tab-btn.active .tab-badge {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .tab-pane {
        display: none;
        animation: fadeIn 0.15s ease-in;
    }

    .tab-pane.active {
        display: block;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(4px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* =========================================================
       TARJETAS Y FORMULARIOS
    ========================================================= */
    .card {
        background: #ffffff;
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 22px 24px;
        margin-bottom: 24px;
        box-shadow: var(--shadow-sm);
    }

    .card-header-flex {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding-bottom: 14px;
        border-bottom: 1px solid var(--border);
        margin-bottom: 18px;
    }

    .card-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--text);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .card-subtitle {
        font-size: 12px;
        color: var(--muted);
        margin: 3px 0 0 0;
    }

    .btn-toggle-form {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid var(--border);
        padding: 6px 12px;
        border-radius: var(--radius-md);
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.15s ease;
    }

    .btn-toggle-form:hover {
        background: #e2e8f0;
        color: var(--text);
    }

    .form-row {
        display: grid;
        gap: 14px;
        margin-bottom: 14px;
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
        margin-bottom: 5px;
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
        padding: 9px 12px;
        border: 1px solid #cbd5e1;
        border-radius: var(--radius-md);
        background: #ffffff;
        font-family: inherit;
        font-size: 12px;
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

    .field-hint {
        font-size: 10px;
        color: var(--muted);
        margin-top: 3px;
    }

    .readonly-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 12px;
        border: 1px solid #e2e8f0;
        border-radius: var(--radius-md);
        background: #f8fafc;
        color: #475569;
        font-size: 12px;
        font-weight: 600;
    }

    .form-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 18px;
        padding-top: 14px;
        border-top: 1px solid var(--border);
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--secondary) 0%, #1d4ed8 100%);
        color: #ffffff;
        border: none;
        padding: 9px 18px;
        border-radius: var(--radius-md);
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 2px 5px rgba(37, 99, 235, 0.25);
        transition: all 0.15s ease;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
        box-shadow: 0 4px 8px rgba(37, 99, 235, 0.35);
        transform: translateY(-1px);
    }

    .btn-secondary {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid var(--border);
        padding: 9px 16px;
        border-radius: var(--radius-md);
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
        transition: all 0.15s ease;
    }

    .btn-secondary:hover {
        background: #e2e8f0;
        color: var(--text);
    }

    /* =========================================================
       TABLAS MODERNAS
    ========================================================= */
    .table-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 14px;
        margin-bottom: 16px;
    }

    .table-filter-wrap {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .search-box {
        position: relative;
        min-width: 240px;
    }

    .search-box input {
        width: 100%;
        padding: 7px 12px 7px 32px;
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        font-size: 12px;
        background: #f8fafc;
        outline: none;
        transition: all 0.15s ease;
    }

    .search-box input:focus {
        background: #fff;
        border-color: var(--secondary);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .search-icon {
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--muted);
        font-size: 11px;
        pointer-events: none;
    }

    .table-wrap {
        overflow-x: auto;
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        background: #ffffff;
    }

    .modern-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
        text-align: left;
    }

    .modern-table thead {
        background: #f8fafc;
        border-bottom: 1px solid var(--border);
    }

    .modern-table th {
        padding: 11px 13px;
        font-size: 10px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        white-space: nowrap;
    }

    .modern-table td {
        padding: 12px 13px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        color: var(--text);
    }

    .modern-table tbody tr:hover {
        background-color: #f8fafc;
    }

    .modern-table tbody tr:last-child td {
        border-bottom: none;
    }

    /* Badges & Chips */
    .code-badge-wrap {
        display: flex;
        flex-direction: column;
        gap: 3px;
        align-items: flex-start;
    }

    .code-badge {
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-size: 11px;
        font-weight: 700;
        background: #f1f5f9;
        color: #0f172a;
        padding: 3px 7px;
        border-radius: 5px;
        border: 1px solid #cbd5e1;
    }

    .parent-chip {
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-size: 10px;
        font-weight: 600;
        color: #64748b;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: #f8fafc;
        padding: 2px 6px;
        border-radius: 4px;
        border: 1px dashed #cbd5e1;
    }

    .chip-organo {
        font-size: 11px;
        font-weight: 600;
        color: #1e40af;
        background: #eff6ff;
        padding: 2px 7px;
        border-radius: 4px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .chip-unidad {
        font-size: 11px;
        font-weight: 600;
        color: #065f46;
        background: #d1fae5;
        padding: 2px 7px;
        border-radius: 4px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .chip-puesto {
        font-size: 11px;
        font-weight: 600;
        color: #5b21b6;
        background: #ede9fe;
        padding: 2px 7px;
        border-radius: 4px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .muni-chip {
        font-size: 10px;
        font-weight: 600;
        color: #475569;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        margin-top: 2px;
    }

    .unit-name-cell {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .unit-name-cell strong {
        font-weight: 600;
        color: #0f172a;
        font-size: 13px;
    }

    .unit-desc-text {
        font-size: 11px;
        color: var(--muted);
    }

    .function-text {
        font-size: 12px;
        line-height: 1.4;
        margin: 0 0 4px 0;
        color: #1e293b;
    }

    .fuente-tag {
        font-size: 10px;
        color: #64748b;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .badge-category {
        display: inline-block;
        font-size: 10px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 999px;
        white-space: nowrap;
    }

    .badge-category.cat-gobierno { background: #ede9fe; color: #5b21b6; }
    .badge-category.cat-control { background: #fef3c7; color: #92400e; }
    .badge-category.cat-asesoria { background: #dbeafe; color: #1e40af; }
    .badge-category.cat-linea { background: #d1fae5; color: #065f46; }

    .badge-type {
        font-size: 10px;
        font-weight: 600;
        color: #475569;
        background: #f1f5f9;
        padding: 2px 7px;
        border-radius: 5px;
        white-space: nowrap;
    }

    .level-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        padding: 2px 7px;
        border-radius: 5px;
        font-size: 11px;
        font-weight: 700;
        color: #334155;
    }

    .level-badge .order-sub {
        font-size: 10px;
        color: var(--muted);
    }

    .badge-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 10px;
        font-weight: 600;
        padding: 2px 7px;
        border-radius: 999px;
    }

    .badge-pill.gray { background: #f1f5f9; color: #64748b; }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 10px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 999px;
    }

    .status-pill.active { background: #dcfce7; color: #166534; }
    .status-pill.inactive { background: #fee2e2; color: #991b1b; }
    .status-pill.warning { background: #fef3c7; color: #92400e; }

    .status-pill .dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
    }

    .hierarchy-cell {
        display: flex;
        flex-direction: column;
        gap: 4px;
        align-items: flex-start;
    }

    .types-cell {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .date-info {
        font-size: 11px;
        line-height: 1.3;
    }

    .text-muted {
        color: var(--muted);
    }

    /* Acciones */
    .actions-cell {
        text-align: center;
    }

    .action-buttons {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    .btn-action {
        border: none;
        width: 30px;
        height: 30px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.15s ease;
        font-size: 12px;
        text-decoration: none;
    }

    .btn-action.edit {
        background: #eff6ff;
        color: #2563eb;
    }

    .btn-action.edit:hover {
        background: #dbeafe;
        color: #1d4ed8;
        transform: scale(1.05);
    }

    .btn-action.danger {
        background: #fee2e2;
        color: #dc2626;
    }

    .btn-action.danger:hover {
        background: #fecaca;
        color: #b91c1c;
        transform: scale(1.05);
    }

    /* Empty State */
    .empty-state {
        padding: 36px 20px;
        text-align: center;
    }

    .empty-icon {
        font-size: 36px;
        color: #cbd5e1;
        margin-bottom: 10px;
    }

    .empty-title {
        font-size: 14px;
        font-weight: 700;
        color: #334155;
    }

    .empty-desc {
        font-size: 12px;
        color: var(--muted);
        margin-top: 4px;
    }

    /* =========================================================
       ÁRBOL JERÁRQUICO / ORGANIGRAMA
    ========================================================= */
    .tree-container {
        display: flex;
        flex-direction: column;
        gap: 20px;
        padding: 10px 0;
    }

    .tree-root-card {
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        padding: 16px;
        background: #fafafa;
    }

    .tree-node {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        background: #ffffff;
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        padding: 10px 14px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    }

    .tree-node.root {
        border-left: 4px solid var(--secondary);
        width: 100%;
        max-width: 420px;
        margin-bottom: 14px;
    }

    .tree-node.unit {
        border-left: 4px solid #16a34a;
        min-width: 280px;
    }

    .tree-node.subunit {
        border-left: 3px solid #d97706;
        padding: 6px 10px;
    }

    .tree-node.post {
        border-left: 3px solid #7c3aed;
        padding: 6px 10px;
    }

    .node-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #eff6ff;
        color: var(--secondary);
        font-size: 14px;
    }

    .node-icon.green { background: #dcfce7; color: #16a34a; }
    .node-icon.amber { background: #fef3c7; color: #d97706; width: 24px; height: 24px; font-size: 11px; }
    .node-icon.purple { background: #ede9fe; color: #7c3aed; width: 24px; height: 24px; font-size: 11px; }

    .node-info {
        display: flex;
        flex-direction: column;
    }

    .node-code {
        font-family: monospace;
        font-size: 10px;
        font-weight: 700;
        color: var(--muted);
    }

    .node-name {
        font-size: 13px;
        font-weight: 700;
        color: #0f172a;
    }

    .node-name-sm {
        font-size: 11px;
        font-weight: 600;
        color: #1e293b;
    }

    .node-meta {
        font-size: 10px;
        color: var(--muted);
    }

    .tree-children {
        margin-left: 24px;
        padding-left: 18px;
        border-left: 2px dashed #cbd5e1;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .tree-branch {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .tree-subchildren {
        margin-left: 20px;
        padding-left: 14px;
        border-left: 2px dotted #e2e8f0;
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    /* =========================================================
       RESPONSIVE
    ========================================================= */
    @media (max-width: 1100px) {
        .stats-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        .grid-4 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
        .grid-4, .grid-3, .grid-2 {
            grid-template-columns: 1fr;
        }
        .span-2 {
            grid-column: auto;
        }
        .org-header-toolbar {
            flex-direction: column;
            align-items: flex-start;
        }
        .superadmin-filter-box {
            width: 100%;
        }
        .table-card-header {
            flex-direction: column;
            align-items: flex-start;
        }
        .table-filter-wrap {
            width: 100%;
        }
        .search-box {
            width: 100%;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Cambio de pestañas (Tabs)
    function switchTab(tabKey) {
        // Desactivar todos los botones y paneles
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.remove('active'));

        // Activar el seleccionado
        const targetBtn = document.getElementById('tab-btn-' + tabKey);
        const targetPane = document.getElementById('pane-' + tabKey);

        if (targetBtn && targetPane) {
            targetBtn.classList.add('active');
            targetPane.classList.add('active');
            window.location.hash = tabKey;
        }
    }

    // Alternar visibilidad de formulario de registro
    function toggleForm(containerId, button) {
        const container = document.getElementById(containerId);
        const icon = button.querySelector('i');
        const span = button.querySelector('span');

        if (container.style.display === 'none') {
            container.style.display = 'block';
            icon.className = 'fa-solid fa-chevron-up';
            span.textContent = 'Ocultar';
        } else {
            container.style.display = 'none';
            icon.className = 'fa-solid fa-chevron-down';
            span.textContent = 'Mostrar';
        }
    }

    // Filtrar tabla en vivo por texto
    function filterTableLive(input, tableId) {
        const query = input.value.toLowerCase().trim();
        const table = document.getElementById(tableId);
        if (!table) return;

        const rows = table.querySelectorAll('tbody tr');
        rows.forEach(row => {
            if (row.querySelector('.empty-state')) return;
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(query) ? '' : 'none';
        });
    }

    // Filtro global por municipalidad para SUPERADMIN
    function filterByMunicipality(muniId) {
        const currentUrl = new URL(window.location.href);
        if (muniId) {
            currentUrl.searchParams.set('municipalidad_id', muniId);
        } else {
            currentUrl.searchParams.delete('municipalidad_id');
        }
        window.location.href = currentUrl.toString();
    }

    // Restaurar pestaña activa desde Session Flash, Parámetro URL o Hash
    document.addEventListener('DOMContentLoaded', function () {
        const flashTab = "{{ session('active_tab') }}";
        const urlParams = new URLSearchParams(window.location.search);
        const paramTab = urlParams.get('tab');
        const hashTab = window.location.hash ? window.location.hash.substring(1) : null;

        const activeTab = flashTab || paramTab || hashTab || 'organos';
        switchTab(activeTab);
    });
</script>
@endpush
