@extends('layouts.app')

@section('title', 'Catálogo de Estructura Organizacional | CPIMuni')
@section('page-title', 'Catálogo Maestro de Estructura Organizacional')

@section('content')
    @include('partials.form-feedback')

    {{-- Estadísticas Rápidas --}}
    <div class="stats-grid">
        <div class="stat-box">
            <div class="stat-box-icon blue">
                <i class="fa-solid fa-sitemap"></i>
            </div>
            <div class="stat-box-data">
                <span class="stat-box-value">{{ $stats['total'] ?? $items->total() }}</span>
                <span class="stat-box-label">Total Elementos</span>
            </div>
        </div>
        <div class="stat-box">
            <div class="stat-box-icon green">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="stat-box-data">
                <span class="stat-box-value">{{ $stats['activos'] ?? '—' }}</span>
                <span class="stat-box-label">Unidades Activas</span>
            </div>
        </div>
        <div class="stat-box">
            <div class="stat-box-icon amber">
                <i class="fa-solid fa-layer-group"></i>
            </div>
            <div class="stat-box-data">
                <span class="stat-box-value">{{ $stats['niveles'] ?? '—' }}</span>
                <span class="stat-box-label">Niveles Jerárquicos</span>
            </div>
        </div>
        <div class="stat-box">
            <div class="stat-box-icon red">
                <i class="fa-solid fa-ban"></i>
            </div>
            <div class="stat-box-data">
                <span class="stat-box-value">{{ $stats['inactivos'] ?? '—' }}</span>
                <span class="stat-box-label">Inactivos / Bajas</span>
            </div>
        </div>
    </div>

    {{-- Formulario de Registro --}}
    <section class="card form-card">
        <div class="card-header-flex">
            <div class="card-header-info">
                <h2 class="card-title">
                    <i class="fa-solid fa-circle-plus title-icon"></i>
                    Registrar Elemento Organizacional
                </h2>
                <p class="card-subtitle">Define una nueva unidad, órgano o puesto en el catálogo de organigrama.</p>
            </div>
            <button type="button" class="btn-toggle-form" id="btnToggleForm" onclick="toggleFormCard()">
                <i class="fa-solid fa-chevron-up" id="toggleIcon"></i>
                <span id="toggleText">Ocultar</span>
            </button>
        </div>

        <div id="formContainer" class="form-container">
            <form method="POST" action="{{ route('catalogos.estructura.store') }}" class="modern-form">
                @csrf

                <div class="form-section-title">
                    <i class="fa-solid fa-tag"></i> 1. Identificación y Jerarquía
                </div>
                <div class="form-row grid-3">
                    <div class="field-group">
                        <label for="codigo">Código <span class="required">*</span></label>
                        <input id="codigo" name="codigo" required value="{{ old('codigo') }}" placeholder="Ej: GM, GAF, SGRH" maxlength="50" autocomplete="off">
                        <small class="field-hint">Código único de la unidad</small>
                    </div>

                    <div class="field-group">
                        <label for="codigo_padre">Código Padre (Dependencia)</label>
                        <input id="codigo_padre" name="codigo_padre" value="{{ old('codigo_padre') }}" placeholder="Ej: ALC, GM (Opcional)" maxlength="50">
                        <small class="field-hint">Código de la entidad superior directa</small>
                    </div>

                    <div class="field-group">
                        <label for="nombre">Nombre de la Unidad <span class="required">*</span></label>
                        <input id="nombre" name="nombre" required value="{{ old('nombre') }}" placeholder="Ej: Gerencia de Administración y Finanzas">
                        <small class="field-hint">Denominación oficial completa</small>
                    </div>
                </div>

                <div class="form-section-title">
                    <i class="fa-solid fa-sliders"></i> 2. Clasificación y Nivel
                </div>
                <div class="form-row grid-4">
                    <div class="field-group">
                        <label for="categoria">Categoría <span class="required">*</span></label>
                        <select id="categoria" name="categoria" required>
                            <option value="">Seleccione categoría...</option>
                            <option value="GOBIERNO" {{ old('categoria') === 'GOBIERNO' ? 'selected' : '' }}>🏛️ Gobierno</option>
                            <option value="CONTROL" {{ old('categoria') === 'CONTROL' ? 'selected' : '' }}>🛡️ Control</option>
                            <option value="ASESORIA" {{ old('categoria') === 'ASESORIA' ? 'selected' : '' }}>⚖️ Asesoría</option>
                            <option value="LINEA" {{ old('categoria') === 'LINEA' ? 'selected' : '' }}>⚡ Línea</option>
                            <option value="COORDINACION" {{ old('categoria') === 'COORDINACION' ? 'selected' : '' }}>🔄 Coordinación</option>
                            <option value="CONSULTIVO" {{ old('categoria') === 'CONSULTIVO' ? 'selected' : '' }}>👥 Consultivo</option>
                        </select>
                    </div>

                    <div class="field-group">
                        <label for="tipo">Tipo de Elemento <span class="required">*</span></label>
                        <select id="tipo" name="tipo" required>
                            <option value="">Seleccione tipo...</option>
                            <option value="NATURALEZA" {{ old('tipo') === 'NATURALEZA' ? 'selected' : '' }}>Naturaleza</option>
                            <option value="TIPO" {{ old('tipo') === 'TIPO' ? 'selected' : '' }}>Tipo</option>
                            <option value="UNIDAD" {{ old('tipo', 'UNIDAD') === 'UNIDAD' ? 'selected' : '' }}>Unidad Orgánica</option>
                            <option value="PUESTO" {{ old('tipo') === 'PUESTO' ? 'selected' : '' }}>Puesto de Trabajo</option>
                        </select>
                    </div>

                    <div class="field-group">
                        <label for="nivel">Nivel Jerárquico <span class="required">*</span></label>
                        <input id="nivel" type="number" name="nivel" min="1" max="20" value="{{ old('nivel', 1) }}" required>
                        <small class="field-hint">1 = Órgano de Gobierno / Superior</small>
                    </div>

                    <div class="field-group">
                        <label for="orden">Orden de Prelación</label>
                        <input id="orden" type="number" name="orden" min="0" value="{{ old('orden', 0) }}">
                        <small class="field-hint">Posición dentro del mismo nivel</small>
                    </div>
                </div>

                <div class="form-section-title">
                    <i class="fa-solid fa-circle-info"></i> 3. Configuración y Estado
                </div>
                <div class="form-row grid-3">
                    <div class="field-group checkbox-card">
                        <label class="toggle-label" for="permite_hijos">
                            <input type="checkbox" id="permite_hijos" name="permite_hijos" value="1" {{ old('permite_hijos', 1) ? 'checked' : '' }}>
                            <div class="toggle-content">
                                <span class="toggle-title">Permite Subunidades / Hijos</span>
                                <span class="toggle-desc">Habilita asignar unidades dependientes bajo este nodo</span>
                            </div>
                        </label>
                    </div>

                    <div class="field-group">
                        <label for="estado">Estado Operativo <span class="required">*</span></label>
                        <select id="estado" name="estado" required>
                            <option value="ACTIVO" {{ old('estado', 'ACTIVO') === 'ACTIVO' ? 'selected' : '' }}>🟢 ACTIVO</option>
                            <option value="INACTIVO" {{ old('estado') === 'INACTIVO' ? 'selected' : '' }}>🔴 INACTIVO</option>
                        </select>
                    </div>

                    <div class="field-group full-width-sm">
                        <label for="descripcion">Descripción / Finalidad</label>
                        <textarea id="descripcion" name="descripcion" rows="2" placeholder="Breve detalle de las funciones o alcance...">{{ old('descripcion') }}</textarea>
                    </div>
                </div>

                <div class="form-actions">
                    <button class="btn-primary" type="submit">
                        <i class="fa-solid fa-floppy-disk"></i> Guardar Elemento
                    </button>
                    <button class="btn-secondary" type="reset">
                        <i class="fa-solid fa-rotate-left"></i> Limpiar Campos
                    </button>
                </div>
            </form>
        </div>
    </section>

    {{-- Listado de Catálogo --}}
    <section class="card table-card">
        <div class="table-card-header">
            <div class="card-header-info">
                <h2 class="card-title">
                    <i class="fa-solid fa-network-wired title-icon"></i>
                    Catálogo de Estructura Registrado
                </h2>
                <p class="card-subtitle">Listado maestro paginado con jerarquía y estado actual.</p>
            </div>

            {{-- Filtros y Búsqueda --}}
            <form method="GET" action="{{ route('catalogos.estructura.index') }}" class="search-filter-form">
                <div class="search-box">
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por código, nombre o padre..." autocomplete="off">
                    @if(request('search'))
                        <a href="{{ route('catalogos.estructura.index', array_filter(['categoria' => request('categoria'), 'estado' => request('estado')])) }}" class="clear-search" title="Borrar búsqueda">
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    @endif
                </div>

                <select name="categoria" onchange="this.form.submit()" class="filter-select">
                    <option value="">Todas las categorías</option>
                    <option value="GOBIERNO" {{ request('categoria') === 'GOBIERNO' ? 'selected' : '' }}>Gobierno</option>
                    <option value="CONTROL" {{ request('categoria') === 'CONTROL' ? 'selected' : '' }}>Control</option>
                    <option value="ASESORIA" {{ request('categoria') === 'ASESORIA' ? 'selected' : '' }}>Asesoría</option>
                    <option value="LINEA" {{ request('categoria') === 'LINEA' ? 'selected' : '' }}>Línea</option>
                    <option value="COORDINACION" {{ request('categoria') === 'COORDINACION' ? 'selected' : '' }}>Coordinación</option>
                    <option value="CONSULTIVO" {{ request('categoria') === 'CONSULTIVO' ? 'selected' : '' }}>Consultivo</option>
                </select>

                <select name="estado" onchange="this.form.submit()" class="filter-select">
                    <option value="">Todos los estados</option>
                    <option value="ACTIVO" {{ request('estado') === 'ACTIVO' ? 'selected' : '' }}>Activos</option>
                    <option value="INACTIVO" {{ request('estado') === 'INACTIVO' ? 'selected' : '' }}>Inactivos</option>
                </select>

                @if(request()->hasAny(['search', 'categoria', 'estado']))
                    <a href="{{ route('catalogos.estructura.index') }}" class="btn-clear-all" title="Restablecer filtros">
                        <i class="fa-solid fa-filter-circle-xmark"></i> Limpiar
                    </a>
                @endif
            </form>
        </div>

        <div class="table-wrap">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th style="width: 140px;">Código</th>
                        <th>Nombre de la Unidad</th>
                        <th style="width: 130px;">Categoría</th>
                        <th style="width: 110px;">Tipo</th>
                        <th style="width: 90px; text-align: center;">Nivel / Ord.</th>
                        <th style="width: 100px; text-align: center;">Subunidades</th>
                        <th style="width: 100px; text-align: center;">Estado</th>
                        <th style="width: 100px; text-align: center;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td>
                                <div class="code-badge-wrap">
                                    <span class="code-badge">{{ $item->codigo }}</span>
                                    @if($item->codigo_padre)
                                        <span class="parent-chip" title="Dependencia superior directa">
                                            <i class="fa-solid fa-arrow-turn-up fa-rotate-90"></i> {{ $item->codigo_padre }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="unit-name-cell">
                                    <strong>{{ $item->nombre }}</strong>
                                    @if($item->descripcion)
                                        <span class="unit-desc-text">{{ Str::limit($item->descripcion, 75) }}</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @php
                                    $catSlug = strtolower($item->categoria);
                                @endphp
                                <span class="badge-category cat-{{ $catSlug }}">
                                    {{ ucfirst(strtolower($item->categoria)) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge-type">{{ ucfirst(strtolower($item->tipo)) }}</span>
                            </td>
                            <td style="text-align: center;">
                                <span class="level-badge" title="Nivel jerárquico {{ $item->nivel }}, Orden {{ $item->orden }}">
                                    N{{ $item->nivel }} <small class="order-sub">#{{ $item->orden }}</small>
                                </span>
                            </td>
                            <td style="text-align: center;">
                                @if($item->permite_hijos)
                                    <span class="badge-pill green" title="Permite dependientes"><i class="fa-solid fa-check"></i> Sí</span>
                                @else
                                    <span class="badge-pill gray" title="Nodo terminal"><i class="fa-solid fa-minus"></i> No</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <span class="status-pill {{ $item->estado === 'ACTIVO' ? 'active' : 'inactive' }}">
                                    <span class="dot"></span>
                                    {{ $item->estado }}
                                </span>
                            </td>
                            <td class="actions-cell">
                                <div class="action-buttons">
                                    {{-- Botón Editar que abre Modal --}}
                                    <button
                                        type="button"
                                        class="btn-action edit"
                                        title="Editar este elemento"
                                        onclick='openEditModal(@json($item))'
                                    >
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>

                                    {{-- Botón Desactivar / Eliminar --}}
                                    <form method="POST" action="{{ route('catalogos.estructura.destroy', $item) }}" onsubmit="return confirm('¿Confirma desactivar el elemento {{ $item->codigo }} - {{ addslashes($item->nombre) }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn-action danger" type="submit" title="Desactivar elemento">
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
                                    <div class="empty-icon"><i class="fa-solid fa-folder-open"></i></div>
                                    <div class="empty-title">No se encontraron elementos</div>
                                    <p class="empty-desc">No hay registros coincidentes con los filtros actuales en el catálogo organizacional.</p>
                                    @if(request()->hasAny(['search', 'categoria', 'estado']))
                                        <a href="{{ route('catalogos.estructura.index') }}" class="btn-secondary" style="margin-top: 12px;">
                                            <i class="fa-solid fa-rotate-left"></i> Restablecer filtros
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación limpia y estilizada --}}
        <div class="pagination-wrapper">
            {{ $items->links() }}
        </div>
    </section>

    {{-- Modal de Edición --}}
    <div id="editModal" class="modal-backdrop" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-header">
                <div class="modal-title-wrap">
                    <i class="fa-solid fa-pen-to-square modal-icon"></i>
                    <div>
                        <h3 class="modal-title">Editar Elemento de Estructura</h3>
                        <p class="modal-subtitle" id="editModalSubtitle">Actualice los datos de la unidad organizacional.</p>
                    </div>
                </div>
                <button type="button" class="modal-close" onclick="closeEditModal()" aria-label="Cerrar modal">&times;</button>
            </div>

            <form id="editForm" method="POST" action="" class="modal-body modern-form">
                @csrf
                @method('PUT')

                <div class="form-row grid-3">
                    <div class="field-group">
                        <label for="edit_codigo">Código <span class="required">*</span></label>
                        <input id="edit_codigo" name="codigo" required maxlength="50">
                    </div>
                    <div class="field-group">
                        <label for="edit_codigo_padre">Código Padre</label>
                        <input id="edit_codigo_padre" name="codigo_padre" maxlength="50" placeholder="Opcional">
                    </div>
                    <div class="field-group">
                        <label for="edit_nombre">Nombre <span class="required">*</span></label>
                        <input id="edit_nombre" name="nombre" required>
                    </div>
                </div>

                <div class="form-row grid-4">
                    <div class="field-group">
                        <label for="edit_categoria">Categoría <span class="required">*</span></label>
                        <select id="edit_categoria" name="categoria" required>
                            <option value="GOBIERNO">🏛️ Gobierno</option>
                            <option value="CONTROL">🛡️ Control</option>
                            <option value="ASESORIA">⚖️ Asesoría</option>
                            <option value="LINEA">⚡ Línea</option>
                            <option value="COORDINACION">🔄 Coordinación</option>
                            <option value="CONSULTIVO">👥 Consultivo</option>
                        </select>
                    </div>
                    <div class="field-group">
                        <label for="edit_tipo">Tipo <span class="required">*</span></label>
                        <select id="edit_tipo" name="tipo" required>
                            <option value="NATURALEZA">Naturaleza</option>
                            <option value="TIPO">Tipo</option>
                            <option value="UNIDAD">Unidad</option>
                            <option value="PUESTO">Puesto</option>
                        </select>
                    </div>
                    <div class="field-group">
                        <label for="edit_nivel">Nivel <span class="required">*</span></label>
                        <input id="edit_nivel" type="number" name="nivel" min="1" max="20" required>
                    </div>
                    <div class="field-group">
                        <label for="edit_orden">Orden</label>
                        <input id="edit_orden" type="number" name="orden" min="0">
                    </div>
                </div>

                <div class="form-row grid-3">
                    <div class="field-group checkbox-card">
                        <label class="toggle-label" for="edit_permite_hijos">
                            <input type="checkbox" id="edit_permite_hijos" name="permite_hijos" value="1">
                            <div class="toggle-content">
                                <span class="toggle-title">Permite Subunidades / Hijos</span>
                                <span class="toggle-desc">Puede contener unidades dependientes</span>
                            </div>
                        </label>
                    </div>
                    <div class="field-group">
                        <label for="edit_estado">Estado <span class="required">*</span></label>
                        <select id="edit_estado" name="estado" required>
                            <option value="ACTIVO">🟢 ACTIVO</option>
                            <option value="INACTIVO">🔴 INACTIVO</option>
                        </select>
                    </div>
                    <div class="field-group full-width-sm">
                        <label for="edit_descripcion">Descripción</label>
                        <textarea id="edit_descripcion" name="descripcion" rows="2"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeEditModal()">
                        <i class="fa-solid fa-xmark"></i> Cancelar
                    </button>
                    <button type="submit" class="btn-primary">
                        <i class="fa-solid fa-check"></i> Actualizar Elemento
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('styles')
<style>
    /* =========================================================
       ESTADÍSTICAS RÁPIDAS
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
        padding: 18px 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: var(--shadow-sm);
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }

    .stat-box:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .stat-box-icon {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    .stat-box-icon.blue { background: #eff6ff; color: #2563eb; }
    .stat-box-icon.green { background: #dcfce7; color: #16a34a; }
    .stat-box-icon.amber { background: #fef3c7; color: #d97706; }
    .stat-box-icon.red { background: #fee2e2; color: #dc2626; }

    .stat-box-data {
        display: flex;
        flex-direction: column;
    }

    .stat-box-value {
        font-size: 22px;
        font-weight: 800;
        color: var(--text);
        line-height: 1.2;
    }

    .stat-box-label {
        font-size: 11px;
        font-weight: 600;
        color: var(--muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 2px;
    }

    /* =========================================================
       TARJETAS Y CABECERAS
    ========================================================= */
    .card {
        background: #ffffff;
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: var(--shadow-sm);
    }

    .card-header-flex {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding-bottom: 16px;
        border-bottom: 1px solid var(--border);
        margin-bottom: 20px;
    }

    .card-title {
        font-size: 17px;
        font-weight: 700;
        color: var(--text);
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 0;
    }

    .title-icon {
        color: var(--secondary);
        font-size: 18px;
    }

    .card-subtitle {
        font-size: 12px;
        color: var(--muted);
        margin-top: 4px;
        margin-bottom: 0;
    }

    .btn-toggle-form {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid var(--border);
        padding: 7px 14px;
        border-radius: var(--radius-md);
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.15s ease;
    }

    .btn-toggle-form:hover {
        background: #e2e8f0;
        color: var(--text);
    }

    /* =========================================================
       FORMULARIO MODERNO
    ========================================================= */
    .form-section-title {
        font-size: 12px;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 18px 0 12px;
        padding-bottom: 6px;
        border-bottom: 1px dashed #e2e8f0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .form-section-title:first-child {
        margin-top: 0;
    }

    .form-section-title i {
        color: var(--secondary);
        font-size: 13px;
    }

    .form-row {
        display: grid;
        gap: 16px;
        margin-bottom: 12px;
    }

    .grid-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    .grid-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }

    .field-group {
        display: flex;
        flex-direction: column;
    }

    .field-group label {
        font-size: 12px;
        font-weight: 600;
        color: #334155;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .field-group label .required {
        color: var(--danger);
        font-weight: 700;
    }

    .field-group input,
    .field-group select,
    .field-group textarea {
        width: 100%;
        padding: 9px 13px;
        border: 1px solid #cbd5e1;
        border-radius: var(--radius-md);
        background: #ffffff;
        font-family: inherit;
        font-size: 13px;
        color: var(--text);
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
        outline: none;
    }

    .field-group input:focus,
    .field-group select:focus,
    .field-group textarea:focus {
        border-color: var(--secondary);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }

    .field-hint {
        font-size: 11px;
        color: var(--muted);
        margin-top: 4px;
    }

    /* Checkbox Card / Switch */
    .checkbox-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: var(--radius-md);
        padding: 10px 14px;
        display: flex;
        justify-content: center;
    }

    .toggle-label {
        display: flex !important;
        flex-direction: row !important;
        align-items: flex-start !important;
        gap: 12px !important;
        cursor: pointer;
        width: 100%;
        margin-bottom: 0 !important;
    }

    .toggle-label input[type="checkbox"] {
        width: 18px !important;
        height: 18px !important;
        margin-top: 2px !important;
        cursor: pointer;
        flex-shrink: 0;
        accent-color: var(--secondary);
    }

    .toggle-content {
        display: flex;
        flex-direction: column;
    }

    .toggle-title {
        font-size: 12px;
        font-weight: 700;
        color: var(--text);
    }

    .toggle-desc {
        font-size: 11px;
        color: var(--muted);
        margin-top: 1px;
    }

    .form-actions {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-top: 22px;
        padding-top: 16px;
        border-top: 1px solid var(--border);
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--secondary) 0%, #1d4ed8 100%);
        color: #ffffff;
        border: none;
        padding: 10px 20px;
        border-radius: var(--radius-md);
        font-size: 13px;
        font-weight: 600;
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
        padding: 10px 18px;
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

    /* =========================================================
       CABECERA Y FILTROS DE TABLA
    ========================================================= */
    .table-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 20px;
    }

    .search-filter-form {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .search-box {
        position: relative;
        min-width: 250px;
    }

    .search-box input {
        width: 100%;
        padding: 8px 32px 8px 34px;
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        font-size: 12px;
        background: #f8fafc;
        transition: all 0.15s ease;
    }

    .search-box input:focus {
        background: #ffffff;
        border-color: var(--secondary);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        outline: none;
    }

    .search-icon {
        position: absolute;
        left: 11px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--muted);
        font-size: 12px;
        pointer-events: none;
    }

    .clear-search {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 12px;
        text-decoration: none;
    }

    .clear-search:hover { color: var(--danger); }

    .filter-select {
        padding: 8px 12px;
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        font-size: 12px;
        font-weight: 500;
        background: #f8fafc;
        color: var(--text);
        cursor: pointer;
        outline: none;
    }

    .filter-select:focus {
        border-color: var(--secondary);
        background: #ffffff;
    }

    .btn-clear-all {
        font-size: 11px;
        font-weight: 600;
        color: var(--danger);
        background: #fee2e2;
        padding: 7px 10px;
        border-radius: var(--radius-md);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: background 0.15s ease;
    }

    .btn-clear-all:hover {
        background: #fecaca;
    }

    /* =========================================================
       TABLA ELEGANTE Y MODERNA
    ========================================================= */
    .table-wrap {
        overflow-x: auto;
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        background: #ffffff;
    }

    .modern-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
        text-align: left;
    }

    .modern-table thead {
        background: #f8fafc;
        border-bottom: 1px solid var(--border);
    }

    .modern-table th {
        padding: 12px 14px;
        font-size: 11px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        white-space: nowrap;
    }

    .modern-table td {
        padding: 13px 14px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        color: var(--text);
    }

    .modern-table tbody tr {
        transition: background-color 0.15s ease;
    }

    .modern-table tbody tr:hover {
        background-color: #f8fafc;
    }

    .modern-table tbody tr:last-child td {
        border-bottom: none;
    }

    /* Badges y Chips */
    .code-badge-wrap {
        display: flex;
        flex-direction: column;
        gap: 4px;
        align-items: flex-start;
    }

    .code-badge {
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-size: 12px;
        font-weight: 700;
        background: #f1f5f9;
        color: #0f172a;
        padding: 3px 8px;
        border-radius: 6px;
        border: 1px solid #cbd5e1;
        letter-spacing: 0.5px;
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

    /* Categorías */
    .badge-category {
        display: inline-block;
        font-size: 11px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 999px;
        white-space: nowrap;
    }

    .badge-category.cat-gobierno { background: #ede9fe; color: #5b21b6; }
    .badge-category.cat-control { background: #fef3c7; color: #92400e; }
    .badge-category.cat-asesoria { background: #dbeafe; color: #1e40af; }
    .badge-category.cat-linea { background: #d1fae5; color: #065f46; }
    .badge-category.cat-coordinacion { background: #cffafe; color: #155e75; }
    .badge-category.cat-consultivo { background: #ffe4e6; color: #9f1239; }

    .badge-type {
        font-size: 11px;
        font-weight: 600;
        color: #475569;
        background: #f1f5f9;
        padding: 3px 8px;
        border-radius: 6px;
        white-space: nowrap;
    }

    .level-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        padding: 3px 8px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 700;
        color: #334155;
    }

    .level-badge .order-sub {
        font-size: 10px;
        color: var(--muted);
        font-weight: 500;
    }

    .badge-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 11px;
        font-weight: 600;
        padding: 3px 8px;
        border-radius: 999px;
    }

    .badge-pill.green { background: #dcfce7; color: #166534; }
    .badge-pill.gray { background: #f1f5f9; color: #64748b; }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 11px;
        font-weight: 700;
        padding: 4px 10px;
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
        width: 32px;
        height: 32px;
        border-radius: 7px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.15s ease;
        font-size: 13px;
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

    /* Estado Vacío */
    .empty-state {
        padding: 40px 20px;
        text-align: center;
    }

    .empty-icon {
        font-size: 38px;
        color: #cbd5e1;
        margin-bottom: 12px;
    }

    .empty-title {
        font-size: 15px;
        font-weight: 700;
        color: #334155;
    }

    .empty-desc {
        font-size: 12px;
        color: var(--muted);
        max-width: 400px;
        margin: 6px auto 0;
    }

    /* =========================================================
       PAGINACIÓN: FIJACIÓN CRÍTICA DE ÍCONOS <> GIGANTES
    ========================================================= */
    .pagination-wrapper {
        margin-top: 18px;
    }

    /* Restricción absoluta para evitar que los SVG de Laravel Pagination se expandan */
    .pagination-wrapper nav[role="navigation"] svg,
    nav[role="navigation"] svg,
    .w-5.h-5,
    svg.w-5 {
        width: 16px !important;
        height: 16px !important;
        min-width: 16px !important;
        min-height: 16px !important;
        max-width: 16px !important;
        max-height: 16px !important;
        display: inline-block !important;
        vertical-align: middle !important;
    }

    /* =========================================================
       MODAL DE EDICIÓN
    ========================================================= */
    .modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.55);
        backdrop-filter: blur(4px);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        animation: fadeIn 0.15s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .modal-dialog {
        background: #ffffff;
        border-radius: var(--radius-lg);
        width: 100%;
        max-width: 820px;
        box-shadow: 0 20px 40px rgba(15, 23, 42, 0.22);
        overflow: hidden;
        animation: slideDown 0.2s ease-out;
    }

    @keyframes slideDown {
        from { transform: translateY(-16px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    .modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 20px 24px;
        background: #f8fafc;
        border-bottom: 1px solid var(--border);
    }

    .modal-title-wrap {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .modal-icon {
        font-size: 20px;
        color: var(--secondary);
    }

    .modal-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--text);
        margin: 0;
    }

    .modal-subtitle {
        font-size: 12px;
        color: var(--muted);
        margin: 2px 0 0;
    }

    .modal-close {
        background: transparent;
        border: none;
        font-size: 24px;
        line-height: 1;
        color: #94a3b8;
        cursor: pointer;
        padding: 4px;
        border-radius: 6px;
        transition: color 0.15s ease;
    }

    .modal-close:hover {
        color: var(--danger);
    }

    .modal-body {
        padding: 22px 24px;
        max-height: calc(85vh - 120px);
        overflow-y: auto;
    }

    .modal-footer {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 20px;
        padding-top: 16px;
        border-top: 1px solid var(--border);
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

    @media (max-width: 800px) {
        .grid-3 {
            grid-template-columns: 1fr;
        }
        .grid-4 {
            grid-template-columns: 1fr;
        }
        .stats-grid {
            grid-template-columns: 1fr;
        }
        .table-card-header {
            flex-direction: column;
            align-items: flex-start;
        }
        .search-filter-form {
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
    // Alternar visibilidad del formulario de registro
    function toggleFormCard() {
        const container = document.getElementById('formContainer');
        const icon = document.getElementById('toggleIcon');
        const text = document.getElementById('toggleText');

        if (container.style.display === 'none') {
            container.style.display = 'block';
            icon.className = 'fa-solid fa-chevron-up';
            text.textContent = 'Ocultar';
        } else {
            container.style.display = 'none';
            icon.className = 'fa-solid fa-chevron-down';
            text.textContent = 'Mostrar';
        }
    }

    // Modal de Edición
    const editModal = document.getElementById('editModal');
    const editForm = document.getElementById('editForm');
    const editModalSubtitle = document.getElementById('editModalSubtitle');

    function openEditModal(item) {
        // Asignar action del formulario dinámicamente: /catalogos/estructura-organizacional/{id}
        const updateUrl = "{{ route('catalogos.estructura.update', ':id') }}".replace(':id', item.id);
        editForm.action = updateUrl;

        // Cargar campos
        document.getElementById('edit_codigo').value = item.codigo || '';
        document.getElementById('edit_codigo_padre').value = item.codigo_padre || '';
        document.getElementById('edit_nombre').value = item.nombre || '';
        document.getElementById('edit_categoria').value = item.categoria || 'GOBIERNO';
        document.getElementById('edit_tipo').value = item.tipo || 'UNIDAD';
        document.getElementById('edit_nivel').value = item.nivel ?? 1;
        document.getElementById('edit_orden').value = item.orden ?? 0;
        document.getElementById('edit_permite_hijos').checked = Boolean(item.permite_hijos);
        document.getElementById('edit_estado').value = item.estado || 'ACTIVO';
        document.getElementById('edit_descripcion').value = item.descripcion || '';

        editModalSubtitle.textContent = `Editando elemento: [${item.codigo}] ${item.nombre}`;

        editModal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeEditModal() {
        editModal.style.display = 'none';
        document.body.style.overflow = '';
    }

    // Cerrar modal al hacer clic en el backdrop
    editModal.addEventListener('click', function (e) {
        if (e.target === editModal) {
            closeEditModal();
        }
    });

    // Cerrar con tecla Escape
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && editModal.style.display === 'flex') {
            closeEditModal();
        }
    });
</script>
@endpush
