<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>CPIMuni | Iniciar sesión</title>

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            min-height: 100vh;
            font-family:
                "Segoe UI",
                Arial,
                sans-serif;

            background:
                radial-gradient(
                    circle at top left,
                    #1e5a91 0%,
                    transparent 35%
                ),
                linear-gradient(
                    135deg,
                    #07111f,
                    #0b2035,
                    #102d47
                );

            display: flex;
            align-items: center;
            justify-content: center;

            color: #fff;
        }

        .background {
            position: fixed;
            inset: 0;
            overflow: hidden;
            pointer-events: none;
        }

        .circle {
            position: absolute;
            border-radius: 50%;
            filter: blur(2px);
            opacity: .15;
        }

        .circle.one {
            width: 420px;
            height: 420px;
            background: #3498db;
            top: -180px;
            left: -120px;
        }

        .circle.two {
            width: 300px;
            height: 300px;
            background: #00b4d8;
            bottom: -120px;
            right: -80px;
        }

        .login-container {
            width: min(430px, 92%);
            position: relative;
            z-index: 2;
        }

        .login-card {
            padding: 42px 38px;
            border-radius: 24px;

            background:
                rgba(255,255,255,.08);

            border:
                1px solid rgba(255,255,255,.15);

            box-shadow:
                0 30px 80px rgba(0,0,0,.45);

            backdrop-filter:
                blur(20px);
        }

        .logo {
            width: 78px;
            height: 78px;
            margin: 0 auto 20px;

            border-radius: 20px;

            display: flex;
            align-items: center;
            justify-content: center;

            background:
                linear-gradient(
                    145deg,
                    #1976b9,
                    #0b3c6d
                );

            font-size: 30px;
            font-weight: 800;

            box-shadow:
                0 12px 30px rgba(0,0,0,.25);
        }

        h1 {
            text-align: center;
            font-size: 28px;
            letter-spacing: -.5px;
        }

        .subtitle {
            text-align: center;
            color: #b9c8d8;
            margin-top: 8px;
            margin-bottom: 32px;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #dce7f2;
            font-size: 14px;
        }

        input {
            width: 100%;
            padding: 14px 16px;

            border-radius: 12px;

            border:
                1px solid rgba(255,255,255,.14);

            background:
                rgba(0,0,0,.20);

            color: #fff;

            outline: none;

            font-size: 15px;

            transition: .2s;
        }

        input::placeholder {
            color: #8295a8;
        }

        input:focus {
            border-color: #3da9e8;

            box-shadow:
                0 0 0 3px rgba(61,169,232,.15);
        }

        .btn {
            width: 100%;

            padding: 14px;

            border: 0;
            border-radius: 12px;

            background:
                linear-gradient(
                    135deg,
                    #1687d4,
                    #0b5c98
                );

            color: #fff;

            font-size: 15px;
            font-weight: 700;

            cursor: pointer;

            transition: .2s;
        }

        .btn:hover {
            transform: translateY(-1px);

            box-shadow:
                0 10px 25px rgba(0,0,0,.25);
        }

        .error {
            background: rgba(220,53,69,.15);
            border: 1px solid rgba(220,53,69,.3);

            color: #ffb8bf;

            padding: 12px 14px;

            border-radius: 10px;

            margin-bottom: 20px;

            font-size: 13px;
        }

        .success {
            background: rgba(25,135,84,.15);
            border: 1px solid rgba(25,135,84,.3);

            color: #9ce3bf;

            padding: 12px 14px;

            border-radius: 10px;

            margin-bottom: 20px;

            font-size: 13px;
        }

        .footer {
            text-align: center;

            color: #8295a8;

            font-size: 12px;

            margin-top: 25px;
        }

        .footer strong {
            color: #b9d8ed;
        }

    </style>

</head>

<body>

<div class="background">

    <div class="circle one"></div>
    <div class="circle two"></div>

</div>

<div class="login-container">

    <div class="login-card">

        <div class="logo">
            CPI
        </div>

        <h1>CPIMuni</h1>

        <div class="subtitle">
            Plataforma de Gestión Municipal
        </div>

        @if ($errors->any())

            <div class="error">
                {{ $errors->first() }}
            </div>

        @endif

        @if (session('success'))

            <div class="success">
                {{ session('success') }}
            </div>

        @endif

        <form
            method="POST"
            action="{{ route('login.process') }}"
        >

            @csrf

            <div class="form-group">

                <label for="email">
                    Correo electrónico
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="usuario@municipalidad.gob.pe"
                    required
                    autofocus
                >

            </div>

            <div class="form-group">

                <label for="password">
                    Contraseña
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Ingrese su contraseña"
                    required
                >

            </div>

            <button
                type="submit"
                class="btn"
            >
                Iniciar sesión
            </button>

        </form>

        <div class="footer">

            Sistema desarrollado por
            <strong>CPIGestor</strong>

        </div>

    </div>

</div>

</body>

</html>