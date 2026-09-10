@extends('layouts.app')

@section('title', 'Dashboard | CPIMuni')

@section('page-title', 'Dashboard')

@section('content')

    <div class="welcome">

        <h2>
            Bienvenido,
            {{ $usuario->nombre }}
        </h2>

        <p>
            Panel principal de CPIMuni para la gestión
            de instrumentos y organización municipal.
        </p>

    </div>


    <div class="stats">

        <div class="card stat-card">

            <div class="stat-icon">
                <i class="fa-solid fa-building-columns"></i>
            </div>

            <div>
                <div class="stat-value">
                    {{ $municipalidad ? '1' : '0' }}
                </div>

                <div class="stat-label">
                    Municipalidad asignada
                </div>
            </div>

        </div>


        <div class="card stat-card">

            <div class="stat-icon">
                <i class="fa-solid fa-users"></i>
            </div>

            <div>

                <div class="stat-value">
                    {{ $usuarios }}
                </div>

                <div class="stat-label">
                    Usuarios registrados
                </div>

            </div>

        </div>


        <div class="card stat-card">

            <div class="stat-icon">
                <i class="fa-solid fa-file-lines"></i>
            </div>

            <div>

                <div class="stat-value">
                    0
                </div>

                <div class="stat-label">
                    Instrumentos de gestión
                </div>

            </div>

        </div>


        <div class="card stat-card">

            <div class="stat-icon">
                <i class="fa-solid fa-folder-open"></i>
            </div>

            <div>

                <div class="stat-value">
                    0
                </div>

                <div class="stat-label">
                    Documentos
                </div>

            </div>

        </div>

    </div>


    <div class="dashboard-grid">


        <div class="card">

            <div class="card-title">
                Información institucional
            </div>


            @if($municipalidad)

                <div class="municipality-info">

                    <div class="info-box">

                        <div class="info-label">
                            Municipalidad
                        </div>

                        <div class="info-value">
                            {{ $municipalidad->nombre }}
                        </div>

                    </div>


                    <div class="info-box">

                        <div class="info-label">
                            Código de entidad
                        </div>

                        <div class="info-value">
                            {{ $municipalidad->codigo_entidad ?? '—' }}
                        </div>

                    </div>


                    <div class="info-box">

                        <div class="info-label">
                            RUC
                        </div>

                        <div class="info-value">
                            {{ $municipalidad->ruc ?? '—' }}
                        </div>

                    </div>


                    <div class="info-box">

                        <div class="info-label">
                            Tipo
                        </div>

                        <div class="info-value">
                            {{ $municipalidad->tipo ?? '—' }}
                        </div>

                    </div>


                    <div class="info-box">

                        <div class="info-label">
                            Departamento
                        </div>

                        <div class="info-value">
                            {{ $municipalidad->departamento ?? '—' }}
                        </div>

                    </div>


                    <div class="info-box">

                        <div class="info-label">
                            Provincia
                        </div>

                        <div class="info-value">
                            {{ $municipalidad->provincia ?? '—' }}
                        </div>

                    </div>


                    <div class="info-box">

                        <div class="info-label">
                            Distrito
                        </div>

                        <div class="info-value">
                            {{ $municipalidad->distrito ?? '—' }}
                        </div>

                    </div>


                    <div class="info-box">

                        <div class="info-label">
                            Estado
                        </div>

                        <div class="info-value">
                            {{ $municipalidad->estado ?? '—' }}
                        </div>

                    </div>

                </div>

            @else

                <div class="info-box">

                    <div class="info-label">
                        Estado institucional
                    </div>

                    <div class="info-value">
                        Usuario global / sin municipalidad asignada
                    </div>

                </div>

            @endif

        </div>


        <div class="card">

            <div class="card-title">
                Accesos principales
            </div>


            @if(
                auth()->user()->tienePermiso('organizacion.ver')
                || auth()->user()->rol?->nombre === 'SUPERADMIN'
            )

                <div class="quick-item">

                    <div class="quick-icon">
                        <i class="fa-solid fa-sitemap"></i>
                    </div>

                    <div>

                        <strong>
                            Organización
                        </strong>

                        <span>
                            Estructura orgánica
                        </span>

                    </div>

                </div>

            @endif


            @if(
                auth()->user()->tienePermiso('instrumentos.ver')
                || auth()->user()->rol?->nombre === 'SUPERADMIN'
            )

                <div class="quick-item">

                    <div class="quick-icon">
                        <i class="fa-solid fa-file-lines"></i>
                    </div>

                    <div>

                        <strong>
                            Instrumentos
                        </strong>

                        <span>
                            ROF, PEI, POI y otros
                        </span>

                    </div>

                </div>

            @endif


            @if(
                auth()->user()->tienePermiso('normativa.ver')
                || auth()->user()->rol?->nombre === 'SUPERADMIN'
            )

                <div class="quick-item">

                    <div class="quick-icon">
                        <i class="fa-solid fa-scale-balanced"></i>
                    </div>

                    <div>

                        <strong>
                            Normativa
                        </strong>

                        <span>
                            Base normativa
                        </span>

                    </div>

                </div>

            @endif


            @if(
                auth()->user()->tienePermiso('reportes.ver')
                || auth()->user()->rol?->nombre === 'SUPERADMIN'
            )

                <div class="quick-item">

                    <div class="quick-icon">
                        <i class="fa-solid fa-chart-column"></i>
                    </div>

                    <div>

                        <strong>
                            Reportes
                        </strong>

                        <span>
                            Información de gestión
                        </span>

                    </div>

                </div>

            @endif

        </div>

    </div>

@endsection