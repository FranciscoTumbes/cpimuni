<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        @yield('title', 'CPIMuni')
    </title>

    <link
        rel="preconnect"
        href="https://fonts.googleapis.com"
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --primary: #0B3C6D;
            --primary-dark: #082F55;
            --secondary: #2563EB;
            --background: #F5F7FA;
            --surface: #FFFFFF;
            --sidebar: #0F172A;
            --sidebar-hover: #1E293B;
            --text: #1E293B;
            --muted: #64748B;
            --white: #FFFFFF;
            --border: #E2E8F0;
            --success: #16A34A;
            --warning: #D97706;
            --danger: #DC2626;
            --radius-sm: 6px;
            --radius-md: 10px;
            --radius-lg: 14px;
            --shadow-sm: 0 1px 3px rgba(15, 23, 42, 0.08);
            --shadow-md: 0 4px 12px rgba(15, 23, 42, 0.10);
            --icon-xs: 14px;
            --icon-sm: 16px;
            --icon-md: 18px;
            --icon-lg: 24px;
            --icon-xl: 32px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--background);
            color: var(--text);
        }

        .fa,
        .fas,
        .far,
        .fab,
        .fa-solid,
        .fa-regular,
        .fa-brands {
            font-size: inherit;
        }

        /* =========================================================
           SIDEBAR
        ========================================================= */

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 270px;
            height: 100vh;
            background:
                linear-gradient(
                    180deg,
                    #0f172a 0%,
                    #111827 100%
                );
            color: white;
            z-index: 1000;
            overflow-y: auto;
            transition: .3s;
        }

        .brand {
            height: 78px;
            display: flex;
            align-items: center;
            padding: 0 24px;
            border-bottom: 1px solid rgba(255,255,255,.08);
        }

        .brand-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: linear-gradient(
                135deg,
                var(--secondary),
                var(--primary)
            );
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: var(--icon-lg);
            margin-right: 12px;
        }

        .brand-title {
            font-size: 20px;
            font-weight: 800;
        }

        .brand-subtitle {
            display: block;
            font-size: 10px;
            color: #94a3b8;
            margin-top: 2px;
        }

        .menu {
            padding: 20px 12px;
        }

        .menu-section {
            color: #64748b;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1px;
            padding: 16px 12px 8px;
        }

        .menu-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            margin: 3px 0;
            border-radius: 10px;
            color: #cbd5e1;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: .2s;
        }

        .menu-item:hover {
            background: var(--sidebar-hover);
            color: white;
        }

        .menu-item.active {
            background: linear-gradient(
                90deg,
                #2563eb,
                #1d4ed8
            );
            color: white;
            box-shadow:
                0 6px 18px rgba(37,99,235,.25);
        }

        .menu-item i {
            width: 18px;
            height: 18px;
            font-size: var(--icon-sm);
            text-align: center;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        /* =========================================================
           MAIN
        ========================================================= */

        .main {
            margin-left: 270px;
            min-height: 100vh;
        }

        .topbar {
            height: 78px;
            background: white;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
        }

        .page-title h1 {
            font-size: 20px;
            font-weight: 700;
        }

        .page-title p {
            font-size: 12px;
            color: var(--muted);
            margin-top: 3px;
        }

        .user-area {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: 700;
        }

        .user-info strong {
            display: block;
            font-size: 13px;
        }

        .user-info span {
            display: block;
            font-size: 11px;
            color: var(--muted);
        }

        .logout-button {
            border: 0;
            background: #fee2e2;
            color: #b91c1c;
            width: 38px;
            height: 38px;
            border-radius: 9px;
            cursor: pointer;
        }

        /* =========================================================
           CONTENT
        ========================================================= */

        .content {
            padding: 30px;
        }

        .welcome {
            background:
                linear-gradient(
                    135deg,
                    #0B3C6D,
                    #2563eb
                );
            border-radius: 18px;
            color: white;
            padding: 28px;
            margin-bottom: 26px;
            box-shadow:
                0 12px 30px rgba(15,23,42,.12);
        }

        .welcome h2 {
            font-size: 24px;
            margin-bottom: 8px;
        }

        .welcome p {
            color: #dbeafe;
            font-size: 13px;
        }

        /* =========================================================
           CARDS
        ========================================================= */

        .stats {
            display: grid;
            grid-template-columns:
                repeat(4, minmax(0, 1fr));
            gap: 18px;
            margin-bottom: 26px;
        }

        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 22px;
            box-shadow: var(--shadow-sm);
        }

        .stat-card {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: rgba(37, 99, 235, 0.08);
            color: var(--secondary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: var(--icon-md);
        }

        .stat-value {
            font-size: 24px;
            font-weight: 800;
        }

        .stat-label {
            font-size: 11px;
            color: var(--muted);
            margin-top: 3px;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns:
                2fr 1fr;
            gap: 20px;
        }

        .card-title {
            font-size: 15px;
            font-weight: 700;
            margin-bottom: 18px;
        }

        .municipality-info {
            display: grid;
            grid-template-columns:
                repeat(2, 1fr);
            gap: 16px;
        }

        .info-box {
            background: #f8fafc;
            border-radius: 10px;
            padding: 15px;
        }

        .info-label {
            color: var(--muted);
            font-size: 10px;
            text-transform: uppercase;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .info-value {
            font-size: 13px;
            font-weight: 600;
        }

        .quick-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 13px 0;
            border-bottom: 1px solid var(--border);
        }

        .quick-item:last-child {
            border-bottom: none;
        }

        .quick-icon {
            width: 38px;
            height: 38px;
            border-radius: 9px;
            background: #eff6ff;
            color: var(--secondary);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .quick-item strong {
            font-size: 12px;
        }

        .quick-item span {
            display: block;
            color: var(--muted);
            font-size: 10px;
            margin-top: 2px;
        }

        /* =========================================================
           MOBILE
        ========================================================= */

        @media(max-width: 1000px) {

            .stats {
                grid-template-columns:
                    repeat(2, 1fr);
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
            }

        }

        @media(max-width: 700px) {

            .sidebar {
                transform: translateX(-100%);
            }

            .main {
                margin-left: 0;
            }

            .topbar {
                padding: 0 15px;
            }

            .content {
                padding: 18px;
            }

            .stats {
                grid-template-columns: 1fr;
            }

            .municipality-info {
                grid-template-columns: 1fr;
            }

            .user-info {
                display: none;
            }

        }

    </style>

    @stack('styles')

</head>

<body>

    <aside class="sidebar">

        <div class="brand">

            <div class="brand-icon">
                <i class="fa-solid fa-building-columns"></i>
            </div>

            <div>
                <div class="brand-title">
                    CPIMuni
                </div>

                <span class="brand-subtitle">
                    Gestión Municipal Inteligente
                </span>
            </div>

        </div>


        <nav class="menu">

            <div class="menu-section">
                PRINCIPAL
            </div>

            <a
                href="{{ route('dashboard') }}"
                class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}"
            >
                <i class="fa-solid fa-chart-pie"></i>
                <span>Dashboard</span>
            </a>


            @if(
                auth()->user()->tienePermiso('municipalidades.ver')
                || auth()->user()->rol?->nombre === 'SUPERADMIN'
            )

                <div class="menu-section">
                    MUNICIPALIDAD
                </div>

                <a href="{{ route('admin.municipalidades') }}" class="menu-item {{ request()->routeIs('admin.municipalidades') ? 'active' : '' }}">
                    <i class="fa-solid fa-city"></i>
                    <span>Mi Municipalidad</span>
                </a>

            @endif


            @if(
                auth()->user()->tienePermiso('organizacion.ver')
                || auth()->user()->rol?->nombre === 'SUPERADMIN'
            )

                <div class="menu-section">
                    ORGANIZACIÓN
                </div>

                <a href="{{ route('organizacion.index') }}" class="menu-item {{ request()->routeIs('organizacion.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-sitemap"></i>
                    <span>Organización</span>
                </a>

                <a href="{{ route('organizacion.index') }}#organigrama" class="menu-item">
                    <i class="fa-solid fa-diagram-project"></i>
                    <span>Organigrama</span>
                </a>

                <a href="{{ route('organizacion.index') }}#puestos" class="menu-item">
                    <i class="fa-solid fa-briefcase"></i>
                    <span>Puestos</span>
                </a>

                <a href="{{ route('organizacion.index') }}#funciones" class="menu-item">
                    <i class="fa-solid fa-list-check"></i>
                    <span>Funciones</span>
                </a>

            @endif


            @if(
                auth()->user()->tienePermiso('instrumentos.ver')
                || auth()->user()->rol?->nombre === 'SUPERADMIN'
            )

                <div class="menu-section">
                    INSTRUMENTOS
                </div>

                <a href="{{ route('instrumentos.index') }}" class="menu-item {{ request()->routeIs('instrumentos.index') && request('tipo') === null ? 'active' : '' }}">
                    <i class="fa-solid fa-file-lines"></i>
                    <span>Instrumentos de Gestión</span>
                </a>

                <a href="{{ route('instrumentos.index', ['tipo' => 'ROF']) }}" class="menu-item {{ request()->routeIs('instrumentos.index') && request('tipo') === 'ROF' ? 'active' : '' }}">
                    <i class="fa-solid fa-book"></i>
                    <span>ROF</span>
                </a>

                <a href="{{ route('instrumentos.index', ['tipo' => 'PEI,POI']) }}" class="menu-item {{ request()->routeIs('instrumentos.index') && in_array(request('tipo'), ['PEI', 'POI', 'PEI,POI'], true) ? 'active' : '' }}">
                    <i class="fa-solid fa-bullseye"></i>
                    <span>PEI / POI</span>
                </a>

            @endif


            @if(
                auth()->user()->tienePermiso('normativa.ver')
                || auth()->user()->rol?->nombre === 'SUPERADMIN'
            )

                <div class="menu-section">
                    NORMATIVA
                </div>

                <a href="{{ route('normativa.index') }}" class="menu-item {{ request()->routeIs('normativa.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-scale-balanced"></i>
                    <span>Normativa</span>
                </a>

            @endif


            @if(
                auth()->user()->tienePermiso('documentos.gestionar')
                || auth()->user()->rol?->nombre === 'SUPERADMIN'
            )

                <div class="menu-section">
                    DOCUMENTOS
                </div>

                <a href="#" class="menu-item">
                    <i class="fa-solid fa-folder-open"></i>
                    <span>Documentos</span>
                </a>

            @endif


            @if(
                auth()->user()->tienePermiso('reportes.ver')
                || auth()->user()->rol?->nombre === 'SUPERADMIN'
            )

                <div class="menu-section">
                    REPORTES
                </div>

                <a href="#" class="menu-item">
                    <i class="fa-solid fa-chart-column"></i>
                    <span>Reportes</span>
                </a>

            @endif


            @if(auth()->user()->rol?->nombre === 'SUPERADMIN')

                <div class="menu-section">
                    ADMINISTRACIÓN
                </div>

                <a href="{{ route('admin.municipalidades') }}" class="menu-item {{ request()->routeIs('admin.municipalidades*') ? 'active' : '' }}">
                    <i class="fa-solid fa-building"></i>
                    <span>Municipalidades</span>
                </a>

                <a href="{{ route('admin.usuarios') }}" class="menu-item {{ request()->routeIs('admin.usuarios*') ? 'active' : '' }}">
                    <i class="fa-solid fa-users"></i>
                    <span>Usuarios</span>
                </a>

                <a href="{{ route('admin.roles') }}" class="menu-item {{ request()->routeIs('admin.roles*') ? 'active' : '' }}">
                    <i class="fa-solid fa-user-shield"></i>
                    <span>Roles y permisos</span>
                </a>

                <a href="{{ route('catalogos.estructura.index') }}" class="menu-item {{ request()->routeIs('catalogos.estructura.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-network-wired"></i>
                    <span>Catálogo de estructura</span>
                </a>

                <a href="{{ route('admin.auditoria') }}" class="menu-item {{ request()->routeIs('admin.auditoria') ? 'active' : '' }}">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                    <span>Auditoría</span>
                </a>

            @endif

        </nav>

    </aside>


    <main class="main">

        <header class="topbar">

            <div class="page-title">

                <h1>
                    @yield('page-title', 'Dashboard')
                </h1>

                <p>
                    Sistema de Gestión de Instrumentos Municipales
                </p>

            </div>


            <div class="user-area">

                <div class="user-avatar">

                    {{
                        strtoupper(
                            substr(
                                auth()->user()->nombre ?? 'U',
                                0,
                                1
                            )
                        )
                    }}

                </div>

                <div class="user-info">

                    <strong>
                        {{ auth()->user()->nombre }}
                        {{ auth()->user()->apellido }}
                    </strong>

                    <span>
                        {{ auth()->user()->rol->nombre ?? 'Usuario' }}
                    </span>

                </div>


                <form
                    method="POST"
                    action="{{ route('logout') }}"
                >

                    @csrf

                    <button
                        type="submit"
                        class="logout-button"
                        title="Cerrar sesión"
                    >
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </button>

                </form>

            </div>

        </header>


        <section class="content">

            @yield('content')

        </section>

    </main>


    @stack('scripts')

</body>

</html>