<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>CPIMuni | Dashboard</title>

    <style>

        body {
            margin: 0;
            font-family: "Segoe UI", Arial, sans-serif;
            background: #f4f7fa;
            color: #172b3a;
        }

        .topbar {
            height: 70px;
            background: #0b3c6d;
            color: white;

            display: flex;
            align-items: center;
            justify-content: space-between;

            padding: 0 30px;
        }

        .brand {
            font-size: 23px;
            font-weight: 800;
        }

        .user {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .logout {
            background: rgba(255,255,255,.12);
            border: 1px solid rgba(255,255,255,.2);
            color: white;
            padding: 9px 15px;
            border-radius: 8px;
            cursor: pointer;
        }

        .container {
            padding: 35px;
        }

        .welcome {
            background: white;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 5px 25px rgba(0,0,0,.06);
        }

        .welcome h1 {
            margin-top: 0;
            color: #0b3c6d;
        }

        .cards {
            display: grid;
            grid-template-columns:
                repeat(auto-fit, minmax(220px, 1fr));

            gap: 20px;
            margin-top: 25px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,.05);
        }

        .card h3 {
            color: #0b3c6d;
        }

    </style>

</head>

<body>

<header class="topbar">

    <div class="brand">
        CPIMuni
    </div>

    <div class="user">

        <span>
            {{ auth()->user()->nombre }}
            {{ auth()->user()->apellido }}
        </span>

        <form
            method="POST"
            action="{{ route('logout') }}"
        >

            @csrf

            <button
                class="logout"
                type="submit"
            >
                Cerrar sesión
            </button>

        </form>

    </div>

</header>

<main class="container">

    <section class="welcome">

        <h1>
            Bienvenido a CPIMuni
        </h1>

        <p>
            Plataforma de Gestión Municipal.
        </p>

        <p>
            <strong>Usuario:</strong>
            {{ auth()->user()->email }}
        </p>

        <p>
            <strong>Rol:</strong>
            {{ auth()->user()->rol->nombre ?? 'Sin rol' }}
        </p>

        <p>
            <strong>Municipalidad:</strong>

            @if(auth()->user()->municipalidad)

                {{ auth()->user()->municipalidad->nombre }}

            @else

                Administración global CPIMuni

            @endif

        </p>

    </section>

    <section class="cards">

        <div class="card">
            <h3>🏛️ Municipalidad</h3>
            <p>Información institucional.</p>
        </div>

        <div class="card">
            <h3>📋 Organización</h3>
            <p>Órganos, unidades y puestos.</p>
        </div>

        <div class="card">
            <h3>📚 Instrumentos</h3>
            <p>ROF, PEI, POI y otros instrumentos.</p>
        </div>

        <div class="card">
            <h3>📊 Reportes</h3>
            <p>Indicadores y reportes de gestión.</p>
        </div>

    </section>

</main>

</body>

</html>