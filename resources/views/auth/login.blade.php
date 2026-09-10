<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>CPIMuni | Iniciar sesión</title>

    <style>
        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            min-height: 100%;
            font-family: "Segoe UI", Arial, sans-serif;
        }

        body {
            min-height: 100vh;
            background:
                radial-gradient(circle at 15% 20%, rgba(28, 126, 184, .30), transparent 30%),
                radial-gradient(circle at 85% 80%, rgba(0, 180, 216, .18), transparent 30%),
                linear-gradient(135deg, #07111f 0%, #0b2944 50%, #0b3c6d 100%);

            display: flex;
            align-items: center;
            justify-content: center;
            padding: 25px;
            color: #fff;
        }

        .page {
            width: 100%;
            max-width: 1050px;
            min-height: 650px;

            display: grid;
            grid-template-columns: 1.05fr .95fr;

            overflow: hidden;

            border-radius: 26px;

            background: rgba(255, 255, 255, .075);

            border: 1px solid rgba(255, 255, 255, .14);

            box-shadow:
                0 35px 90px rgba(0, 0, 0, .45);

            backdrop-filter: blur(22px);
        }

        /* PANEL IZQUIERDO */

        .brand-panel {
            padding: 55px;

            display: flex;
            flex-direction: column;
            justify-content: space-between;

            background:
                linear-gradient(
                    145deg,
                    rgba(11, 60, 109, .92),
                    rgba(12, 86, 132, .68)
                );
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .brand-logo {
            width: 62px;
            height: 62px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 17px;

            background: rgba(255, 255, 255, .13);

            border: 1px solid rgba(255, 255, 255, .22);

            font-size: 20px;
            font-weight: 800;

            letter-spacing: -1px;
        }

        .brand-name {
            font-size: 28px;
            font-weight: 800;
        }

        .brand-name span {
            font-weight: 400;
        }

        .presentation {
            max-width: 440px;
        }

        .presentation h1 {
            margin: 0 0 20px;

            font-size: clamp(35px, 4vw, 50px);

            line-height: 1.08;

            letter-spacing: -1.5px;
        }

        .presentation p {
            margin: 0;

            color: #d6e7f5;

            font-size: 16px;

            line-height: 1.7;
        }

        .features {
            display: grid;
            grid-template-columns: 1fr 1fr;

            gap: 13px;

            margin-top: 35px;
        }

        .feature {
            padding: 14px;

            border-radius: 13px;

            background: rgba(255, 255, 255, .08);

            border: 1px solid rgba(255, 255, 255, .10);

            color: #e6f2fb;

            font-size: 13px;
        }

        .copyright {
            color: #a9c4d8;
            font-size: 12px;
        }

        /* PANEL LOGIN */

        .login-panel {
            background: rgba(255, 255, 255, .97);

            color: #172b3a;

            padding: 55px;

            display: flex;
            align-items: center;
        }

        .login-box {
            width: 100%;
            max-width: 390px;
            margin: auto;
        }

        .login-title {
            margin-bottom: 8px;

            color: #0b3c6d;

            font-size: 30px;
            font-weight: 800;
        }

        .login-subtitle {
            margin-bottom: 30px;

            color: #687b8d;

            font-size: 14px;
        }

        .alert {
            padding: 13px 15px;

            border-radius: 10px;

            margin-bottom: 20px;

            font-size: 13px;
        }

        .alert-error {
            color: #842029;

            background: #f8d7da;

            border: 1px solid #f1aeb5;
        }

        .alert-success {
            color: #0f5132;

            background: #d1e7dd;

            border: 1px solid #a3cfbb;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;

            margin-bottom: 8px;

            color: #243b53;

            font-size: 13px;
            font-weight: 600;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;

            left: 15px;
            top: 50%;

            transform: translateY(-50%);

            color: #7890a4;

            font-size: 16px;
        }

        .form-control {
            width: 100%;

            height: 50px;

            padding: 0 15px 0 44px;

            border-radius: 11px;

            border: 1px solid #d5dee7;

            background: #f8fafc;

            color: #172b3a;

            font-size: 14px;

            outline: none;

            transition: .2s;
        }

        .form-control:focus {
            border-color: #1687d4;

            background: #fff;

            box-shadow:
                0 0 0 3px rgba(22, 135, 212, .12);
        }

        .password-toggle {
            position: absolute;

            right: 14px;
            top: 50%;

            transform: translateY(-50%);

            border: 0;

            background: transparent;

            color: #71869a;

            cursor: pointer;

            font-size: 12px;
        }

        .password-control {
            padding-right: 55px;
        }

        .btn-login {
            width: 100%;

            height: 51px;

            margin-top: 7px;

            border: 0;

            border-radius: 11px;

            background:
                linear-gradient(
                    135deg,
                    #1687d4,
                    #0b5c98
                );

            color: #fff;

            font-size: 14px;
            font-weight: 700;

            cursor: pointer;

            box-shadow:
                0 8px 20px rgba(11, 92, 152, .25);

            transition: .2s;
        }

        .btn-login:hover {
            transform: translateY(-1px);

            box-shadow:
                0 12px 25px rgba(11, 92, 152, .32);
        }

        .security {
            margin-top: 25px;

            padding-top: 20px;

            border-top: 1px solid #e5eaf0;

            text-align: center;

            color: #8796a5;

            font-size: 11px;

            line-height: 1.6;
        }

        .security strong {
            color: #536779;
        }

        @media (max-width: 800px) {

            .page {
                grid-template-columns: 1fr;
                max-width: 500px;
            }

            .brand-panel {
                display: none;
            }

            .login-panel {
                min-height: 600px;
                padding: 40px 30px;
            }
        }

        @media (max-width: 420px) {

            body {
                padding: 12px;
            }

            .login-panel {
                padding: 35px 22px;
            }
        }
    </style>

</head>

<body>

<div class="page">

    <!-- PANEL INSTITUCIONAL -->

    <section class="brand-panel">

        <div>

            <div class="brand">

                <div class="brand-logo">
                    CPI
                </div>

                <div class="brand-name">
                    CPI<span>Muni</span>
                </div>

            </div>

        </div>

        <div class="presentation">

            <h1>
                Gestión municipal inteligente.
            </h1>

            <p>
                Plataforma integral para la gestión,
                organización y administración de los
                instrumentos de gestión municipal.
            </p>

            <div class="features">

                <div class="feature">
                    🏛️ Gestión institucional
                </div>

                <div class="feature">
                    📋 Instrumentos de gestión
                </div>

                <div class="feature">
                    👥 Organización municipal
                </div>

                <div class="feature">
                    📊 Reportes y seguimiento
                </div>

            </div>

        </div>

        <div class="copyright">
            CPIMuni · Plataforma de Gestión Municipal
        </div>

    </section>


    <!-- PANEL LOGIN -->

    <section class="login-panel">

        <div class="login-box">

            <div class="login-title">
                Iniciar sesión
            </div>

            <div class="login-subtitle">
                Accede a tu cuenta de CPIMuni
            </div>


            @if ($errors->any())

                <div class="alert alert-error">

                    {{ $errors->first() }}

                </div>

            @endif


            @if (session('success'))

                <div class="alert alert-success">

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

                    <div class="input-wrapper">

                        <span class="input-icon">
                            ✉
                        </span>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-control"
                            value="{{ old('email') }}"
                            placeholder="Ingrese su correo"
                            autocomplete="email"
                            required
                            autofocus
                        >

                    </div>

                </div>


                <div class="form-group">

                    <label for="password">
                        Contraseña
                    </label>

                    <div class="input-wrapper">

                        <span class="input-icon">
                            🔒
                        </span>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control password-control"
                            placeholder="Ingrese su contraseña"
                            autocomplete="current-password"
                            required
                        >

                        <button
                            type="button"
                            class="password-toggle"
                            onclick="togglePassword()"
                        >
                            Mostrar
                        </button>

                    </div>

                </div>


                <button
                    type="submit"
                    class="btn-login"
                >
                    Iniciar sesión
                </button>

            </form>


            <div class="security">

                🔐 Acceso protegido mediante autenticación segura.<br>

                <strong>CPIMuni</strong> · Gestión Municipal

            </div>

        </div>

    </section>

</div>


<script>

function togglePassword()
{
    const input = document.getElementById('password');
    const button = document.querySelector('.password-toggle');

    if (input.type === 'password') {

        input.type = 'text';
        button.textContent = 'Ocultar';

    } else {

        input.type = 'password';
        button.textContent = 'Mostrar';

    }
}

</script>

</body>

</html>